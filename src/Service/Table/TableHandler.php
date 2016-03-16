<?php

/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Service\Table;

use Zend\Stdlib\Parameters;
use ZfTable\AbstractTable;

class TableHandler
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    public function setDbAdapter(\Zend\Db\Adapter\Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     *
     * @param string $tableName
     * @return AbstractTable
     */
    public function createTable($tableName)
    {
        if ($this->getServiceLocator() && $this->getServiceLocator()->has($tableName)) {
            $table = $this->getServiceLocator()->get($tableName);
        } else {
            $table  = new $tableName;
        }
        
        $form   = $table->getForm();
        $filter = $table->getFilter();
        $form->setInputFilter($filter);

        return $table;
    }

    /**
     * @param AbstractTable $table
     * @param $queryBuilder
     */
    public function prepareTable(AbstractTable $table, $queryBuilder)
    {
        $table->setAdapter($this->getDbAdapter())
                ->setSource($queryBuilder)
                ->setParamAdapter(new Parameters($table->getForm()->getData()));
    }
}
