# PablodipModuleBundle

[![Build Status](https://secure.travis-ci.org/pablodip/PablodipModuleBundle.png)](http://travis-ci.org/pablodip/PablodipModuleBundle) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/pablodip/PablodipModuleBundle/badges/quality-score.png?s=286d142e9401b751797de720e4b8847e49175dd9)](https://scrutinizer-ci.com/g/pablodip/PablodipModuleBundle/)

  1. [Introduction](#introduction)
  1. [Creating modules](#creating-modules)
  1. [Making modules reusable](#making-modules-reusable)
  1. [Extensions](#extensions)
  1. [Reusing modules](#reusing-modules)

<a name="introduction"></a>
## Introduction

The aim of a Symfony2 application is to transform HTTP requests in HTTP responses. This is done by the controllers through the routes, so that you can say the controllers are the most important part of a Symfony2 application, as everything else is managed by them.

Symfony2 offers you flexibility for working with controllers and routes. A controller can be any PHP callable, and a route can be defined in many formats, like YAML, XML, PHP, annotations. This flexibility is good, but sometimes your applications become mess and also it's difficult to reuse things between applications.

The PablodipModuleBundle allows you to organize your controllers and routes in modules, so that they become organized and reusable. Organized controllers are those who follow some order rule and keep related things together. Reusable modules are those which you can easily make customizable and use them again in the same project or in other project.

The common rule for organizing controllers is to put them into the `Controller` directory inside the bundles. Their names sometimes coincide with model names and also you usually have some controllers for static actions.

How should you organize a blog, by putting all its controllers in the same controller class or by splitting them in different classes? If you have a bundle dedicated only to the blog, it would make sense to split the controllers. But then, how would you make that bundle reusable? How would you allow to customize its behavior? By putting parameters in the container? By creating fields in a database? Would not be better to allow the user to customize them the way he wants, through the container, a database or whatever? Also how would you create reusable themes for the blog? The template overriding provided by Symfony2 is great, but it doesn't allow you to create themes. Also, how would you allow to use the blog several times in the same project? It would be difficult due to it uses only one set of routes and they are hardcoded in the controllers and templates.

We'll see how to use the PablodipModuleBundle to solve all these problems.

<a name="creating-modules"></a>
## Creating modules

In order to convert an HTTP request in an HTTP response you need a controller (also called action) and a route to access to that controller. A module is a group of related actions. Both actions and modules are represented by classes.

    <?php

    namespace Pablodip\BlogModuleBundle\Module;

    use Pablodip\ModuleBundle\Module\Module;
    use Pablodip\ModuleBundle\Action\RouteAction;
    use Symfony\Component\HttpFoundation\Response;

    class BlogModule extends Module
    {
        protected function defineConfiguration()
        {
            $this->addActions(array(
                new RouteAction('blog_list', '/blog', 'GET', function () {
                    // ...
                    return new Response($content);
                }),
                new RouteAction('blog_post', '/blog/{id}', 'GET', function ($id) {
                    // ...
                    return new Response($content);
                }),
                // ...
            ));
        }
    }

We can see some things in this code:

  * A module extends from the `Pablodip\ModuleBundle\Module\Module` class.
  * A module configuration is defined in its `defineConfiguration` protected method.
  * A module can have actions, which you can add to the module through its `addActions` method.
  * An action with route extends from the `Pablodip\ModuleBundle\Action\RouteAction` class.
  * An action with route receives four arguments: the route name, pattern, method, and the controller. The method can be a method or `ANY` for any method. The controller can be any PHP callable.
  * The controller receives the route parameters as arguments, as in a normal Symfony2 controller.
  * The purpose of a controller is to return a `Response` Symfony2 class, the same than in Symfony2.
  * The convention for placing modules is to put them inside the `Module` directory of bundles.
  * The convection for naming modules is to use the `Module` suffix.

Also all configuration methods implement a fluent interface, so that you can chain them.

### Action names

An action must have a name. Action names have to be unique by module. Route actions use the route name as action name by default (as route names must be unique by module too), but you can also customize it:

    $action->setName('my_preferred_name');

### Route prefixes

The previous example has an issue when defining routes: the route names and patterns prefixes are similar in all the actions. So that you could define them in only one place, thus you'll be able to customize them easier.

    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('blog_')
            ->setRoutePatternPrefix('/blog')
        ;

        $this->addActions(array(
            new RouteAction('list', '/', 'GET', function () {
                // ...
                return new Response($content);
            }),
            new RouteAction('post', '/{id}', 'GET', function ($id) {
                // ...
                return new Response($content);
            }),
            // ...
        ));
    }

Note how the action routes are more concise this way. The routes finally defined are the same than before as the module joins the module route prefixes with the actions route names.

You can also use a shortcut for defining the route prefixes.

    $this->setRoutePrefixes('blog_', '/blog');

### Generating Module URLs

Since you can define the route name prefix for a module in one place, you should be able to generate URLs for the module without writing all the time the prefix. Otherwise you'd have to change all of the hardcoded prefixes if you decide to change the route name prefix.

In order to do this you have to use the `generateModuleUrl` method from modules:

    $url = $module->generateModuleUrl($actionName, $parameters, $absolute);

    // normal
    $listUrl = $module->generateModuleUrl('list'); // route generated: blog_list
    // with parameters
    $postUrl = $module->generateModuleUrl('post', array('id' => $id));
    // absolute
    $module->generateModuleUrl('list', array(), true);

### Container access

You can access to the container at any moment in the module, so that you can use it for whatever you want, including configuring things. We'll see some examples later.

    $container = $module->getContainer();

Actions can access to the module, so that they can also access to the container.

    $module = $action->getModule();

    // container
    $container = $action->getModule()->getContainer();
    $container = $action->getContainer(); // shortcut

    // services
    $validator = $action->getContainer()->get('validator);
    $validator = $action->get('validator'); // shortcut

### Controllers

Controllers receive the parameters of the route as arguments. They can also receive the request (like in normal Symfony2 controllers) and the action.

    new RouteAction('post', '/{id}', 'GET', function ($id, Request $request, RouteAction $action) {
    })

Receiving the action is useful as you can access to the container through it.

Actions implement the same shortcuts than the Symfony2 default controller:

    // shorcuts
    $url = $action->generateUrl($routeName, $routeParameters);
    $response = $action->redirect($url);
    $response = $action->render($templateName, $templateParameters);
    // ...
    // (view full list in the Symfony2 default controller)

The `render` method is a bit improved. The templates you normally use have this format: `%bundle_name%:%module_name%:%action_name%.html.twig`, so that it's easy to guess them. The `render` method will try to guess the template name by passing the array of parameters as the first argument.

    namespace Pablodip\BlogModuleBundle\Module;

    class BlogModule extends Module
    {
        protected function defineConfiguration()
        {
            $this->addAction(new RouteAction('list', '/', 'GET', function (RouteAction $action) {
                // using full template
                return $action->render('PablodipModuleBundle:Blog:list.html.twig', array());

                // guessing template
                return $action->render(array()); // template guessed: PablodipModuleBundle:Blog:list.html.twig
            }));
        }
    }

You should only use this guessing system for your own modules, not for reusable modules.

You can also generate module urls from actions:

    $url = $action->generateModuleUrl($actionName, $parameters, $absolute);

### Module View

Sometimes you need to access to the module from the views, for instance for generating module URLs. You shouldn't pass the module to the view directly as you'd be able to access to things you don't need in the view (the container for instance). Instead you should pass a reduced module object for the view, which you can generate with the `createView` method.

    $moduleView = $module->createView();

    // rendering a template
    $container->get('templating')->render($templateName, array('module' => $module->createView()));

You can call the module parameter however you want, but `module` is a good convention.

Then you can use the module in the views to generate URLs:

    {# relative #}
    {{ module.path('list') }}
    {{ module.path('post', {'id': post.id}) }}

    {# absolute #}
    {{ module.url('list') }}

When you use the `render` method from actions, the module is passed automatically to the view.

We'll see some other methods from the module view later.

### Module Manager

The module manager creates and returns modules. It's useful when you need to access to other modules.

    $moduleManager = $container->get('module_manager');

    $blogModule = $moduleManager->get('PablodipBlogModuleBundle:Blog');
    // PablodipBlogModuleBundle/Module/BlogModule

    $helpModule = $moduleManager->get('PablodipHelpModuleBundle:Help');
    // PablodipHelpModuleBundle/Module/HelpModule

As you can see you can use a friendly syntax for accessing to modules, just by writing the bundle and name.

Then you can generate URLs or do whatever you want:

    $url = $blogModule->generateModuleUrl('list');

When you want to use a module in the view you can access to it though the `get_module` twig function, which returns the view module object directly:

    {{ get_module('PablodipBlogModuleBundle:Blog').path('list') }}

### Importing module routes

In order to use the routes defined in the modules you have to import them into the routing file configuration:

    pablodip_blog_module:
        resource: "@PablodipBlogModuleBundle/Module/BlogModule.php"
        type:     module

You can also import a directory, thus all modules inside it will be imported:

    pablodip_blog_bundle_modules:
        resource: "@PablodipBlogBundle/Module"
        type:     module

In both cases you can use a prefix like in normal routes:

    pablodip_blog_bundle_modules:
        resource: "@PablodipBlogBundle/Module"
        type:     module
        prefix:   /pablodip-blog-module

### Forwarding

You can forward with the `forward` method from modules:

    $response = $module->forward($actionName, $attributes);

    $response = $module->forward('post', array('id' => $id));

See more about forwarding in the Symfony2 documentation [here](http://symfony.com/doc/current/book/controller.html#forwarding).

### Actions without route

Sometimes you need to process controllers without accessing directly to them through routes. In order to do this you have to use the `Action` class instead.

    use Pablodip\ModuleBundle\Action\Action;

    new Action($actionName, $controller);

    new Action('recentPosts', function ($max) {
        // process

        return Response($content);
    });

Note that here you have to indicate the action name as there is no route name.

These actions are useful for embedding controllers, which we'll see now.

### Embedding controllers

Sometimes you need to embed controllers like you can see in the Symfony2 documentation [here](http://symfony.com/doc/current/book/templating.html#templating-embedding-controller).

You can do this with the `render` method from modules:

    {{ module.render(actionName, attributes) }}

    {{ module.render(post, {'id': post.id}) }}

    {{ get_module('PablodipBlogModuleBundle:Blog').render('post', {'id': post.id}) }}

### Independent actions

Sometimes modules become big, so that since actions are just classes we can create independent actions.

    <?php

    namespace Pablodip\BlogBundle\Action;

    use Pablodip\ModuleBundle\Action\BaseRouteAction;
    use Symfony\Component\HttpFoundation\Response;

    class PostAction extends BaseRouteAction
    {
        protected function defineConfiguration()
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

We can see some things in this code:

  * Independent actions extend from the `BaseRouteAction` class instead from the `RouteAction` class.
  * Independent actions configuration is defined in its `defineConfiguration` protected method.
  * Independent actions require the same things than normal route actions: a route name, pattern, method and a controller.

The controller can be any PHP callable, so that you can do something like this:

    class ShowAction extends BaseAction
    {
        protected function defineConfiguration()
        {
            $this
                ->setRoute('show', '/{id}', 'GET')
                ->setController(array($this, 'controller'))
            ;
        }

        public function controller($id)
        {
            // ...

            return new Response($content);
        }
    }

This way you use a normal method in the action instead of a closure, so that you don't need to receive the action as an argument:

    public function controller($id)
    {
        // action
        $this;

        // container
        $container = $this->getContainer();

        // render
        return $this->render(array());

        // you can use any action method this way
    }

### Conclusion

We've seen how to create modules and actions in an organized and flexible way, and also how to import their routes. We've seen how to reuse the route names and patterns prefixes and how to access to the container.

You can use this way to create your controllers, which is nice because your code will be organized. But it's even better because you can also make them reusable and customizable easily, which we'll see now.

<a name="making-modules-reusable"></a>
## Making modules reusable

In order to reuse modules you should be able to customize them. You should be able to customize three things: options, functions and routes.

  * Options. Any parameter, booleans for logic decisions, string for values, arrays for several options, etc.
  * Functions. Used inside a module.
  * Routes. Both route names and patterns for modules and actions.

### Configure method

When you define your configuration, you have to do it in the `defineConfiguration` method, but when a user configure a module, it must be done in the `configure` method. So that the `defineConfiguration` method must be used for your own modules and for making reusable modules, and the `configure` method for configuring third part modules.

    class MyBlogModule extends BlogModule
    {
        protected function configure()
        {
        }
    }

### Action access

You can access to actions through the name:

    class MyBlogModule extends BlogModule
    {
        protected function configure()
        {
            $listAction = $this->getAction('list');
            $postAction = $this->getAction('post');
        }
    }

### Options

An option can be any parameter. You can use them for customizing logic decisions as well as for variable values or any other thing.

You can add options to modules and actions through both the `addOption` and `addOptions` methods. An option requires a name and a default value.

Defining option in modules:

    class BlogModule extends Module
    {
        protected function defineConfiguration()
        {
            // ...

            $this->addOptions(arrray(
                'show_date'  => true,
                'email_from' => 'noreply@blogmodule.dev',
            ));
        }
    }

Defining options in actions:

    class ListAction extends BaseAction
    {
        protected function defineConfiguration()
        {
            // ...

            $this->addOption('max_per_page', 10);
        }
    }

Then you can use the options in your controllers:

    // module options
    $optionValue = $action->getModule()->getOption($optionName);
    $optionValue = $action->getModuleOption($optionName) // shortcut

    if ($this->getModuleOption('show_date')) {
        // ...
    }

    $email->setFrom($action->getModuleOption('email_from'));

    // action options
    $pagerfanta->setMaxPerPage($action->getOption('max_per_page'));

Also in your templates:

    {% if module.option('show_data') %}
        ...
    {% endif %}

    {{ module.actionOption('list', 'max_per_page') }}

The user can configure the options in his module through both the `setOption` and `setOptions` methods.

    class MyBlogModule extends BlogModule
    {
        protected function configure()
        {
            $this->setOptions(array(
                'show_date'  => false,
                'email_from' => 'my@email.com',
            ));
            $this->getAction('list')->setOption('max_per_page', 20);
            $this->setActionOption('list', 'max_per_page', 20); // shortcut
        }
    }

### Customizing templates with options

You can allow the user to customize the templates of actions just by putting the template in a option.

    class ListAction extends BaseAction
    {
        protected function defineConfiguration()
        {
            $this
                ->setRoute('list', '/', 'GET')
                ->setController(array($this, 'controller'))
                ->addOption('template', 'PablodipBlogModuleBundle:Blog:list.html.twig')
            ;
        }

        public function controller()
        {
             // ...

            return $this->render($this->getTemplate());
        }
    }

This way the user only has to change the `template` option in order to use a different template in the action:

    class MyBlogModule extends BlogModule
    {
        protected function configure()
        {
            $this->setActionOption('list', 'template', 'MyBlogModuleBundle:Blog:list.html.twig');
        }
    }

### Functions

When a module does some common tasks, it usually calls functions. These functions can be from third part libraries or own functions, but they are called from the module. So why not to allow the user to be able to customize those functions if he needs that?

Let's see an example. You create a module which serializes things. You can create your own functions for serializing data, you can use the Symfony2 Serializer component, the JMSSerializerBundle, or you can use any other way to serialize. You'll end up using an interface like this:

    $serialized = serializeFunction($data, $format);
    $unserialized = unserializeFunction($data, $type, $format);

So why not to do your module to be able to use any implementation for that interface?

You can do this just by using functions in your modules:

    class BlogModule extends Module
    {
        // ...

        public function serialize($data, $format)
        {
            // implement your serialization or use an external serialization library
        }

        public function deserialize($data, $type, $format)
        {
            // implementation
        }
    }

You can call those functions from the controllers:

    $serialized = $module->serialize($data, $format);
    $unserialized = $module->deserialize($data, $type, $format);

The user can change the implementation just by overriding the functions.

    class MyBlogModule extends BlogModule
    {
        public function serialize($data, $format)
        {
            // my implementation
        }
    }

### Parsing configuration

Modules have a protected method called `parseConfiguration`, which is called after defining their configuration and configuring them. This method should be used for checking the configuration is ok and also for parsing it, both things if needed.

    class BlogModule extends Module
    {
         // ...

        protected function parseConfiguration()
        {
            // checking configuration
            if (!$this->getOption('email_from')) {
                throw new \RuntimeException('The module requies the option "email_from".');
            }
        }
    }

### Routes

The user can customize the route name and pattern prefixes:

    class MyBlogBundle extends BlogBundle
    {
        protected function configure()
        {
            $this->setRoutePrefixes('my_blog_', '/my/blog');
        }
    }

The user can also customize the action route name and pattern:

    $action->setRouteName('my_list')
    $action->setRoutePattern('/my-list);

### Conclusion

We've seen how to use options and functions in our modules. We've also seen how to use the modules route system in order to allow the user to customize the routes as much as he wants. So in conclusion, our modules have become completely reusable.

<a name="extensions"></a>
## Extensions

Extensions allow you to modify modules, so that you can get more reusability thanks to them.

An extension is just a class which extends from the `BaseExtension` class. An extension must implement the `getName` method and can implement the same three configuration methods that modules have.

    <?php

    namespace Pablodip\BlogModuleBundle\Extension;

    use Pablodip\ModuleBundle\Extension\BaseExtension;

    class FeedExtension extends BaseExtension
    {
        public function getName()
        {
            return 'feed';
        }

        public function defineConfiguration()
        {
        }

        public function configure()
        {
        }

        public function parseConfiguration()
        {
        }
    }

The configuration methods are called before the same methods in the modules. You only need to implement the methods you use, the same than in modules.

Note how the configuration methods are public in the extensions.

An extension can access to the module it is being used in through the `getModule` method. This way the extension can modify anything of the module. But as an extension configuration methods are called before the module ones, a module can always modify an extension.

    class FeedExtension extends BaseExtension
    {
        public function defineConfiguration()
        {
            $this->getModule()->addAction(new FeedAction());
        }
    }

The module which will use this extension will have a new action.

In order to use extensions in your modules you have to register them by using the `registerExtensions` protected method.

    class MyBlogModule extends BlogModule
    {
        protected function registerExtensions()
        {
            $extensions = parent::registerExtensions();
            $extensions[] = new FeedExtension();

            return $extensions;
        }
    }

You can see how the code calls the method from the parent and then adds more extensions. This is to continue registering the extensions the parent class is registering.

You can access to extensions by their names in modules:

    $feedExtension = $module->getExtension('feed');

You can modify modules from extensions the way you want, so that you can get great things. Let's see some examples.

### Themes

A theme is just a set of different templates for some actions. You can customize the templates used in actions just by putting the templates as options. So that you can create themes just by customizing those templates, and this can be done in a good way through extensions.

    class CoolBlogTheme extends BaseExtension
    {
        protected function configure()
        {
            $this->getModule()->getAction('list')->setOption('template', 'CoolBlogModuleThemeBundle:Blog:list.html.twig');
            $this->getModule()->getAction('post')->setOption('template', 'CoolBlogModuleThemeBundle:Blog:post.html.twig');
        }
    }

You can use this theme in your module just by registering the extension.

    class MyBlogModule extends BlogModule
    {
        protected function registerExtensions()
        {
            $extensions = parent::registerExtensions();
            $extensions[] = new CoolBlogTheme();

            return $extensions;
        }
    }

### Reusing common tasks

We talked about how to reuse common tasks in modules by putting them in functions. We saw an example when we needed to serialize data. But if we need to use serialization in several modules, do we need to implement those functions for each of them? And if we implement those functions, why not to reuse those implementations in the different modules?

We can do this things by putting those functions in extensions.

    class SerializerExtension extends BaseExtension
    {
        public function getName()
        {
            return 'serializer';
        }

        public function serialize($data, $format)
        {
            // implementation
        }

        public function unserialize($data, $type, $format)
        {
            // implementation
        }
    }

This way you can use these functions in several modules just by registering the extension and using it.

    $serialized = $module->getExtension('serializer')->serialize($data, $format);

### Extensions for the same task

Some of the extensions you use are for the same task, so that it would be good to allow to make more implementations for that task as well as to use those extensions appropriately.

Let's allow to make more implementations for the serializer extension, for instance with the Symfony2 Serializer component and with the JMSSerializerBundle. In order to do this you need a base serializer extension:

    abstract class BaseSerializerExtension extends BaseExtension
    {
        public function getName()
        {
            return 'serializer';
        }

        abstract public function serialize($data, $format);

        abstract public function unserialize($data, $type, $format);
    }

And the implementations:

    class Symfony2SerializerExtension extends BaseSerializerExtension
    {
        public function serialize($data, $format)
        {
            // implementation
        }

        public function unserialize($data, $type, $format)
        {
            // implementation
        }
    }

    class JMSSerializerBundleSerializerExtension extends BaseSerializerExtension
    {
        public function serialize($data, $format)
        {
            // implementation
        }

        public function unserialize($data, $type, $format)
        {
            // implementation
        }
    }

Note that we don't override the extension name as we need to access to the extension from the module through its name.

You can do the same with themes:

    abstract class BaseBlogThemeExtension extends BaseExtension
    {
        protected function configure()
        {
            $this->getModule()->setActionOption('list', 'template', $this->getListTemplate());
            $this->getModule()->setActionOption('post', 'template', $this->getPostTemplate());
        }

        abstract protected function getListTemplate();

        abstract protected function getPostTemplate();
    }

    class CoolBlogThemeExtension extends BaseBlogThemeExtension
    {
        protected function getListTemplate()
        {
            return 'CoolBlogModuleThemeBundle:Blog:list.html.twig';
        }

        protected function getPostTemplate()
        {
            return 'CoolBlogModuleThemeBundle:Blog:post.html.twig';
        }
    }

Now you have a module which uses a serializer extension, the Symfony2SerializerExtension by default, but you want to allow the user to choose the implementation he prefers. If we register the extension directly the user will have to filter the extensions registered and register his serializer preferred extension, which is not a good way. A better way is to create a function in the module for registering the serializer extension and registering that extension.

The module with the Symfony2Serializer extension by default:

    class BlogModule extends Module
    {
        // ...

        protected function registerExtensions()
        {
            $extensions = parent::registerExtensions();
            $extensions[] = $this->registerSerializerExtension();

            return $extensions;
        }

        protected function registerSerializerExtension()
        {
            return new Symfony2SerializerExtension();
        }
    }

This way the user can change the serializer extension easily:

    class MyBlogModule extends BlogModule
    {
        protected function registerSerializerExtension()
        {
            return new JMSSerializerBundleSerializerExtension();
        }
    }

If you don't want to provide a default extension you can make abstract both the module and the register extension method:

    abstract class BlogModule extends Module
    {
        // ...

        protected function registerExtensions()
        {
            $extensions = parent::registerExtensions();
            $extensions[] = $this->registerSerializerExtension();

            return $extensions;
        }

        abstract protected function registerSerializerExtension();
    }

### Molino

Molino is a library for making tools persistence backend agnostic. This is great for making modules reusable, as if you use the Molino API your modules will work with any molino: Mandango, Doctrine ORM, Doctrine ODM MongoDB, and so on.

In order to do this you need to allow the user register a molino:

    abstract class BlogModule extends Module
    {
        private $molino;

        public function getMolino()
        {
            return $this->molino;
        }

        protected function defineConfiguration()
        {
            $this->molino = $this->registerMolino();

            // ...
        }

        abstract protected function registerMolino();
    }

This way the user can choose the molino he wants:

    use Molino\Mandango\Molino;

    class MyBlogModule extends BlogModule
    {
        protected function registerMolino()
        {
            return new Molino($this->getContainer()->get('mandango'));
        }
    }

You can use the molino from your controllers:

    $molino = $module->getMolino();

But as working with Molino is a common task in reusable modules, let's see the official extensions there are for this. The base abstract class is `Pablodip\ModuleBundle\Extension\Molino\BaseMolinoExtension`, which has the abstract protected method `registerMolino`, and the `getMolino` method to access to the molino. There is a molino extension for each molino implementation.

    Pablodip\ModuleBundle\Extension\Molino\MandangoMolinoExtension
    Pablodip\ModuleBundle\Extension\Molino\DoctrineORMMolinoExtension

These extensions register the molino using the container for building it.

You can use them with events:

    $extension = new MandangoMolinoExtension(true);
    $eventDispatcher = $extension->getEventDispatcher();

You can get the molino from the extension, and also use the shortcut from actions:

    $molino = $action->getModule()->getExtension('molino')->getMolino();

    // shortcut
    $molino = $action->getMolino();

When working with Molino you have to use the model classes, so it's a good idea to put it as an option.

    abstract class BlogModule extends MolinoModule
    {
        protected function defineConfiguration()
        {
            $this->addOption('postClass', null);

            // ...
        }

        protected function parseConfiguration()
        {
            if (null === $this->getOption('postClass')) {
                throw new \RuntimeException('The option "postClass" is required in a BlogModule.');
            }
        }
    }

This way you can use Molino like this in your controllers:

    class PostAction extends BaseAction
    {
        // ...

        public function controller($id)
        {
            $post = $molino->findOneById($this->getModuleOption('postClass'), $id);

            // ...
        }
    }

<a name="reusing-modules"></a>
## Reusing modules

Modules are just classes, so that extending modules is as simple as extending classes. You can have as many modules as you want in your project, the only ones really used are those which you import in the routing. So you can use the same module several times in the same project, customizing each of them the way you want.

Let's create some blogs.

A blog with Mandango and with the Cool theme.

    class MyMandangoBlogModule extends BlogModule
    {
        protected function registerExtensions()
        {
            $extensions = parent::registerExtensions();
            $extensions[] = new CoolBlogModuleThemeExtension();

            return $extensions;
        }

        protected configure()
        {
            $this
                ->setRoutePrefixes('my-mandango-blog_', '/my/mandango/blog')
                ->setOption('postClass', 'Model\Mandango\Post')
            ;
        }

        protected function registerMolinoExtension()
        {
            return new MandangoMolinoExtension();
        }
    }

A Doctine ORM blog with feed.

    class MyDoctrineORMBlogModule extends BlogModule
    {
        protected function registerExtensions()
        {
            $extensions = parent::registerExtensions();
            $extensions[] = new FeedExtension();

            return $extensions;
        }

        protected configure()
        {
            $this
                ->setRoutePrefixes('my-doctrine-orm-blog_', '/my/doctrine-orm/blog')
                ->setOption('postClass', 'Model\Doctrine\ORM\Post')
            ;
        }

        protected function registerMolino()
        {
            return new DoctrineORMMolinoExtension();
        }
    }

### Conclusion

You can customize modules the way you want and the times you want. You can create many things on top of this system, they can work persistence backend agnostic thanks to Molino, they can have themes, they can be as customizable as you want.

Reusing things is good for everybody. You can share things with others and you can use things from others.

## Author

Pablo Díez - <pablodip@gmail.com>

## License

PablodipModuleBundle is licensed under the MIT License. See the LICENSE file for full details.
