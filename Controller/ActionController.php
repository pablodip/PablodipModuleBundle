<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * ActionController.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ActionController extends Controller
{
    /**
     * Executes an action.
     */
    public function executeAction()
    {
        $moduleId = $this->get('request')->get('_pablodip_module.module');
        $actionFullName = $this->get('request')->get('_pablodip_module.action');

        $module = $this->container->get($moduleId);
        foreach ($module->getControllerPreExecutes() as $controllerPreExecute) {
            $controllerPreExecute($this->container);
        }

        $action = $module->getAction($actionFullName);

        return $action->executeController();
    }
}
