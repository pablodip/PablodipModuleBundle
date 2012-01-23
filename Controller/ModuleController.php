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
 * ModuleController.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ModuleController extends Controller
{
    /**
     * Executes an action.
     */
    public function executeAction()
    {
        $moduleClass = $this->get('request')->get('_pablodip_module.module');
        $actionName = $this->get('request')->get('_pablodip_module.action');

        $module = $this->get('module_manager')->get($moduleClass);
        foreach ($module->getControllerPreExecutes() as $controllerPreExecute) {
            if (null !== $retval = $controllerPreExecute($module)) {
                return $retval;
            }
        }

        return $module->getAction($actionName)->executeController();
    }
}
