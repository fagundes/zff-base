<?php

/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceAbstractFactory
 *
 * AbstractFactory para todas as Services que
 * herdam de Zff\Base\Service\AbstractService.
 *
 * @package ZffBase
 * @subpackage ZffBase_Service
 */
class ServiceAbstractFactory implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (class_exists($requestedName)) {
            $reflect = new \ReflectionClass($requestedName);
            if ($reflect->isSubclassOf(AbstractService::class)) {
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
            $service = $reflect->newInstance();

            $entityManager = $serviceLocator->get($service->getEntityManagerName());
            $service->setEntityManager($entityManager);
            
            $dbAdapter = $serviceLocator->get($service->getDbAdapterName());
            $service->setDbAdapter($dbAdapter);

            $tableHandler = $serviceLocator->get(Table\TableHandler::class);
            $tableHandler->setEntityManager($entityManager);
            $tableHandler->setDbAdapter($dbAdapter);
            $service->setTableHandler($tableHandler);

            $services = (array) $service->getServices();
            foreach ($services as $customServiceName => $serviceName) {
                $this->loadService($serviceLocator, $service, $serviceName, $customServiceName);
            }

            return $service;
        }
        return null;
    }

    protected function loadService(
        ServiceLocatorInterface $serviceLocator,
        AbstractService $service,
        $serviceNeededName,
        $customServiceName
    ) {
    
        if (is_int($customServiceName)) {
            $simpleServiceName = preg_replace('/(.*)\\\/', '', $serviceNeededName);
        } else {
            $simpleServiceName = ucfirst($customServiceName);
        }

        $methodSet = 'set' . $simpleServiceName . 'Service';

        if (!method_exists($service, $methodSet)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected method %s::%s, to set the service %s.',
                    get_class($service),
                    $methodSet,
                    $serviceNeededName
                )
            );
        }

        $serviceNeeded = $serviceLocator->get($serviceNeededName);
        call_user_func([$service,$methodSet], $serviceNeeded);
    }
}
