<?php
/**
 * Created by PhpStorm.
 * User: vinicius
 * Date: 15/03/16
 * Time: 21:43
 */

namespace Zff\Base\Controller;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ControllerAbstractFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (class_exists($requestedName)) {
            $reflect = new \ReflectionClass($requestedName);
            if ($reflect->isSubclassOf(AbstractController::class)) {
                return true;
            }
        }

        return false;
    }

    public function createServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName
    ) {
    
        if ($this->canCreateServiceWithName($serviceLocator, $name, $requestedName)) {
            $reflect = new \ReflectionClass($requestedName);
            $controller = $reflect->newInstance();

            $forms = (array)$controller->getForms();
            foreach ($forms as $customFormName => $formName) {
                $this->loadForm($serviceLocator, $controller, $formName, $customFormName);
            }

            $services = (array)$controller->getServices();
            foreach ($services as $customServiceName => $serviceName) {
                $this->loadService($serviceLocator, $controller, $serviceName, $customServiceName);
            }

            return $controller;
        }

        return null;
    }

    protected function loadService(
        ServiceLocatorInterface $serviceLocator,
        AbstractController $controller,
        $serviceNeededName,
        $customServiceName
    ) {

        if (is_int($customServiceName)) {
            $simpleServiceName = preg_replace('/(.*)\\\/', '', $serviceNeededName);
        } else {
            $simpleServiceName = ucfirst($customServiceName);
        }

        $methodSet = 'set' . $simpleServiceName . 'Service';

        if (!method_exists($controller, $methodSet)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected method %s::%s, to set the service %s.',
                    get_class($controller),
                    $methodSet,
                    $serviceNeededName
                )
            );
        }

        $serviceNeeded = $serviceLocator->get($serviceNeededName);
        call_user_func([$controller,$methodSet], $serviceNeeded);
    }

    protected function loadForm(
        ServiceLocatorInterface $serviceLocator,
        AbstractController $controller,
        $formNeededName,
        $customFormName
    ) {

        if (is_int($customFormName)) {
            $simpleServiceName = preg_replace('/(.*)\\\/', '', $formNeededName);
        } else {
            $simpleServiceName = ucfirst($customFormName);
        }

        $methodSet = 'set' . $simpleServiceName . 'Form';

        if (!method_exists($controller, $methodSet)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected method %s::%s, to set the form %s.',
                    get_class($controller),
                    $methodSet,
                    $formNeededName
                )
            );
        }

        $serviceNeeded = $serviceLocator->get($formNeededName);
        call_user_func([$controller,$methodSet], $serviceNeeded);
    }
}
