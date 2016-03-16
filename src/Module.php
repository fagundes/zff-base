<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * We dont need constant ROOT to PHP includes.
     *
     * @reference http://samminds.com/2012/08/why-root_path-is-not-needed/
     * @reference http://stackoverflow.com/questions/11969925/how-to-get-applications-root-directory
     *
     * But if we need it for something else, just put line below in /public/index.php.
     *
     * define('ROOT', dirname(__DIR__));
     */

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'formGroupClasses' => function ($sm) {
                    return new Form\View\Helper\FormInputClasses('error', 'form-group', [
                        'error' => 'has-error',
                        'warning' => 'has-warning',
                        'info' => 'has-info',
                        'success' => 'has-success',
                    ]);
                },
                'formControlClasses' => function ($sm) {
                    return new Form\View\Helper\FormInputClasses('error', 'form-control', [
                        'error' => 'form-control-error',
                        'warning' => 'form-control-warning',
                        'info' => 'form-control-info',
                        'success' => 'form-control-success',
                    ]);
                }
            ],
        ];
    }
}
