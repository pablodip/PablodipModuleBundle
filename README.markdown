# PablodipModuleBundle

[![Build Status](https://secure.travis-ci.org/pablodip/PablodipModuleBundle.png)](http://travis-ci.org/pablodip/PablodipModuleBundle)

## Introduction

The PablodipModuleBundle allows you to organize your controllers in modules, and also to make those modules flexible and reusable.

Before starting to see what this bundle offers you, let's remember how Symfony2 works. According to the Symfony2 documentation, creating pages in Symfony2 is a two-step process: create a route and create a controller.

A route is simply a link between a URL (through a pattern) and a controller, thus when the pattern matches the controller is executed. A controller is just a PHP callable, and it's purpose is to transform a HTTP request in a HTTP response. So when developing with Symfony2 you're most of the time creating routes and controller. This is why by making this process as much organized and reusable as possible your developments will become more organized and reusable as well.

Symfony2 offers you several ways to define routes: YAML, XML, PHP, annotation through the SensioFrameworkExtraBundle, and also any way you want by implementing an interface. Also we said that a controller is any PHP callable. These two things mean Symfony2 offers you much flexibility.

But how should you use such as flexibility to make your controllers more reusable? If you want to make things customizable in your controllers, should you use parameters in the container, options in the database, or what? But why to use external things and not to put the options in the own controller, allowing the user customize them the way he wants? If you want to offer different extensions or themes for your controllers, how should you do that? Also if you have reusable controllers (like a blog), can you use them more than once in the same project? Also customize them in different ways?

The PablodipModuleBundle takes advantage of the Symfony2 flexibility and allows you to create organized, flexible and reusable modules. This is why it's good for both your own and shared modules.

## Creating modules and actions

A module is a group of related actions. An action defines a route and a controller. Both modules and actions are represented by classes.

    <?php

    namespace Vendor\BlogBundle\Module;

    use Pablodip\ModuleBundle\Module\Module;
    use Pablodip\ModuleBundle\Action\Action;
    use Symfony\Component\HttpFoundation\Response;

    class BlogModule extends Module
    {
        protected function configure()
        {
            $this->addActions(array(
                new Action('blog_list', '/blog', 'GET', function () {
                    // ...
                    return new Response($content);
                }),
                new Action('blog_show', '/blog/{id}', 'GET', function ($id) {
                    // ...
                    return new Response($content);
                }),
                // ...
            ));
        }
    }

The modules are configured through their protected `configure` method. The actions have four basic things: the route name, pattern, method, and the controller. The controller receives the route parameters as arguments, the same than in a normal Symfony2 controller. All of the configure methods implement a fluent interface.

### Route prefixes

The previous example looks good, but there is an issue when defining routes: the route names and patterns prefixes are similar in all actions, so why not to define them in some place in order to make it easily customizable?

    protected function configure()
    {
        protected function configure()
        {
            $this
                ->setRouteNamePrefix('blog)
                ->setRoutePatternPrefix('/blog')
            ;

            $this->addActions(array(
                new Action('list', '/', 'GET', function () {
                    // ...
                    return new Response($content);
                }),
                new Action('show', '/{id}', 'GET', function ($id) {
                    // ...
                    return new Response($content);
                }),
                // ...
            ));
        }
    }

You can also use a shortcut:

    $this->defineRoutePrefixes('blog', '/blog');

### Container

You can access to the container at any moment in the module, so that you can use it even for configuring things. We'll see some examples later.

    $container = $module->getContainer();

The actions can access to the module, so that they can also access to the container.

    $module = $action->getModule();

    // container
    $container = $action->getModule()->getContainer();
    $container = $action->getContainer(); // shortcut

    // services
    $validator = $action->getContainer()->get('validator);
    $validator = $action->get('validator'); // shortcut

### Controllers

The controllers receive the parameters of the route as arguments. They can also receive the request (like in normal Symfony2 controllers) and also the action.

    new Action('show', '/{id}', 'GET', function ($id, Request $request, Action $action) {
    })

Receiving the action is useful because you can access to the container through it. It also implements the same shortcuts than the Symfony2 default controller.

    // shorcuts
    $url = $action->generateUrl($routeName, $routeParameters);
    $response = $action->redirect($url);
    $response = $action->render($templateName, $templateParameters);
    // ...
    // (view full list in the Symfony2 default controller)

### Importing the routes

In order to use the routes defined in the modules you have to import them into the routing file configuration:

    blog_module:
        resource: "@VendorBlogBundle/Module/BlogModule.php"
        type:     pablodip_module

You can also import a directory:

    vendor_blog_bundle_modules:
        resource: "@VendorBlogBundle/Module"
        type:     pablodip_module

In both cases you can use a prefix:

    vendor_blog_bundle_modules:
        resource: "@VendorBlogBundle/Module"
        type:     pablodip_module
        prefix:   /vendor-blog-bundle

### Organizing actions

We've seen how to create actions directly in the modules, but if there are many actions or if the actions are big, the module will become too big. The actions are just classes, so you can also define them in files:

    <?php

    namespace Vendor\BlogBundle\Action;

    use Pablodip\ModuleBundle\Action\BaseAction;

    class ShowAction extends BaseAction
    {
        protected function configure()
        {
            $this
                ->setRoute('show', '/{id}', 'GET')
                ->setController(function ($id) {
                    // ...

                    return new Response($content);
                })
            ;
        }
    }

Notice that the independent actions extend from the `BaseAction` instead from the `Action` class. You have to configure them through the protected `configure` method. The configuration is the same than in a normal action.

The controller can be any PHP callable, so that you can do something like this:

    class ShowAction extends BaseAction
    {
        protected function configure()
        {
            $this
                ->setRoute('show', '/{id}', 'GET')
                ->setController(array($this, 'execute'))
            ;
        }

        public function execute($id)
        {
            // ...

            return new Response($content);
        }
    }

This way you don't need to receive the action as an argument since the controller method is in the action.

    public function execute($id)
    {
        // container
        $container = $this->getContainer();

        // you can use like this any action method
    }

### Conclusion

So far we've seen how to create modules and actions in an organized and flexible way, and how to import their routes as well. We've seen how to reuse the routes names and patterns prefixes and also we use the method in all the routes, which is a good practice.

You can use this way to create your controllers, which is nice because you're code is well organized. But it's even better because you can also make them reusable easily, which we'll see now.

## Making modules reusable

In order to make modules reusable we need to define options and allow the user to customize them. We also need to allow customizing the routes.

First let's see a couple of methods you should use instead of the `configure` method in order to make your modules reusable. The `configure` method should be used by the people that configure the module, not by the people that define the module configuration. However in your own modules it's fine to use the configure method as you're also who define the configuration.

These two methods are `defineConfiguration` and `parseConfiguration`. They are quite descriptive, but let's describe them a bit further:

  * `defineConfiguration`: Here you should start all the configuration, that is, adding actions, options, callbacks.
  * `parseConfiguration`: Here you should check and parse the configuration (both if needed).

This only applies to the modules, the actions continue using the `configure` method to define options as the user configure them in the module.

### Options

You can add options to the modules and actions.

    class BlogModule extends Module
    {
        protected function defineConfiguration()
        {
            // ...

            $this->addOptions(arrray(
                'emailFrom' => 'noreply@blogmodule.dev',
            ));
        }
    }

    class ListAction extends BaseAction
    {
        protected function configure()
        {
            // ...

            $this->addOptions(array(
                'maxPerPage' => 10,
            ));
        }
    }

Then you can use the options in your controllers:

    // module option
    $email->setFrom($action->getModule()->getOption('emailFrom'));

    // action option
    $pagerfanta->setMaxPerPage($action->getOption('maxPerPage'));

And the user can configure the options in the modules if he wants:

    class MyBlogModule extends BlogModule
    {
        protected function configure()
        {
            $this->setOption('emailFrom');
            $this->getAction('list')->setOption('maxPerPage', 20);
        }
    }

### Routes

The route names and patterns prefixes can be customized by the user:

    class MyBlogBundle extends BlogBundle
    {
        protected configure function()
        {
            $this->setRoutePrefixes('my_blog', '/my/blog');
        }
    }

Due to the route names can change, you have to use the `generateModuleUrl` method from the module in order to being able to generate the routes.

    $url = $module->generateModuleUrl($actionRouteNameSuffix, $parameters);

The user can also customize any action route pattern:

    $this->getAction('list')->setPattern('/list');

What the user can't do is to change the route names from the actions, otherwise the modules won't be able to generate the previous routes.

### Views

You can also access to some parts of the modules from the views, thus you can do things such as generating module urls and access to options.

    $action->get('templating')->render($template, array('_module' => $action->getModule()->createView()))

This is done automatically when using the render method from the actions:

    $action->render($template);

Then in your templates you can use the `_module` variable:

    {{ _module.path(routeName, routeParameters) }}
    {{ _module.url(routeName, routeParameters) }}

    {{ _module.getOption(optionName) }}
    {{ _module.getActionOption(optionName) }}

## Extensions

## Author

Pablo Díez - <pablodip@gmail.com>

## License

PablodipModuleBundle is licensed under the MIT License. See the LICENSE file for full details.
