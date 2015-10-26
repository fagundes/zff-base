<?php

/**
 * @license http://opensource.org/licenses/MIT MIT  
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Service;

use Doctrine\ORM\EntityManager;
use Zend\Db\Adapter\Adapter;

/**
 * AbstractService
 *
 * Uma Service que herda desta Classe Abstrata tem:
 *  - vinculo com uma Entity, por meio do atributo $entityName
 *  - vinculo com uma ou mais Services, por meio do atributo $services
 *  - vinculo com a TableHandler que cria Tables com o mesmo nome da Service
 * Inclui diversos metodos facilitadores.
 * 
 * @todo create services with __get magic method, so we can creating lazy using $serviceLocator
 * @todo do the same above to entityManager and tableHandler
 *
 * @package ZffBase
 * @subpackage ZffBase_Service
 */
abstract class AbstractService
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Adapter 
     */
    protected $dbAdapter;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $entityManagerName;

    /**
     * @var string 
     */
    protected $dbAdapterName;

    /**
     * @var array
     */
    protected $services;

    /**
     * @var Table\TableHandler 
     */
    protected $tableHandler;

    /**
     * @var string 
     */
    protected $tableClassName;

    public function __construct(EntityManager $entityManager = null)
    {
        if ($entityManager) {
            $this->setEntityManager($entityManager);
        }
    }

    protected function checkIfClassExists($class)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('Class %s does not exist.', $class));
        }
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    public function setDbAdapter(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityManagerName()
    {
        return $this->entityManagerName? : 'doctrine.entitymanager.orm_default';
    }

    public function setEntityManagerName($entityManagerName)
    {
        $this->entityManagerName = $entityManagerName;
    }

    public function getDbAdapterName()
    {
        return $this->dbAdapterName ? : 'zfdb_adapter';
    }

    public function setDbAdapterName($dbAdapterName)
    {
        $this->dbAdapterName = $dbAdapterName;
    }
    
    public function getTableClassName()
    {
        if (!$this->tableClassName) {
            $reflectionFinalClass = new \ReflectionClass($this);
            $this->tableClassName      = $reflectionFinalClass->getNamespaceName() . '\\Table\\' . $reflectionFinalClass->getShortName();
        }
        return $this->tableClassName;
    }

    public function setTableClassName($tableClassName)
    {
        $this->tableClassName = $tableClassName;
        return $this;
    }

    public function getTableHandler()
    {
        if (!$this->tableHandler) {
            $this->tableHandler = new Table\TableHandler();
            $this->tableHandler->setEntityManager($this->getEntityManager());
            $this->tableHandler->setDbAdapter($this->getDbAdapter());
        }
        return $this->tableHandler;
    }

    public function setTableHandler(Table\TableHandler $tableHandler)
    {
        $this->tableHandler = $tableHandler;
    }

    /**
     * Metodo proxy EntityManager#getRepository.
     * @return \Doctrine\ORM\EntityRepository A classe de repositorio
     */
    public function getRepository()
    {
        $this->checkIfClassExists($this->entityName);
        return $this->getEntityManager()->getRepository($this->entityName);
    }

    /**
     * @return array Array de services utilizadas pelas classes concretas
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Metodo proxy.
     *
     * @param mixed $id
     * @return \Base\Entity\AbstractEntity
     */
    public function getReference($id)
    {
        return $this->entityManager->getReference($this->entityName, $id);
    }

    /**
     * @param array|\Base\Entity\AbstractEntity $entity
     * @return \Zff\Base\Entity\AbstractEntity
     */
    public function insert($entity)
    {

        $this->checkIfClassExists($this->entityName);
        if (is_array($entity)) {
            $entity = new $this->entityName($entity);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    /**
     * @param array|\Base\Entity\AbstractEntity $entity
     * @return \Gestor\Entity\AbstractEntity
     */
    public function update($entity)
    {

        $this->checkIfClassExists($this->entityName);
        if (is_array($entity)) {
            $entity = $this->entityManager->getReference($this->entityName, $entity['id']);
            $entity->exchangeArray($entity);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    /**
     * Atualiza o array de entidades.
     *
     * @param array $entities
     * @return array Entities updated
     */
    public function updateAll($entities)
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
        return $entities;
    }

    /**
     *
     */
    public function delete($id)
    {
        $this->checkIfClassExists($this->entityName);
        $entity = $this->entityManager->getReference($this->entityName, $id);

        if ($entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    /**
     * Metodo proxy EntityRepository#findBy.
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findBy($criteria, (array) $orderBy);
    }

    /**
     * Metodo proxy EntityRepository#find
     * @param int $id
     * @return \Gestor\Entity\AbstractEntity
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Como findBy mais retorna todos
     * @param array $orderBy
     * @return array
     */
    public function findAll(array $orderBy = null)
    {
        return $this->getRepository()->findBy(array(), (array) $orderBy);
    }

    /**
     * Retorna o total de resultados,
     * $equal serão incluidos como criterios utilizando: '=' ou 'in'
     * $different serão incluidos como criterios utilizando: '<>' ou 'not in'
     *
     * @param array $equal Criterios iguais.
     * @param array $different Criterios diferentes.
     * @return int total
     */
    public function count(array $equal = array(), array $different = array())
    {

        $this->checkIfClassExists($this->entityName);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(e)')
                ->from($this->entityName, 'e');

        $i = 0;
        foreach ($equal as $field => $value) {
            $fieldParam  = $field . $i++;
            $whereClause = is_array($value) ? "e.$field in (:$fieldParam)" : "e.$field = :$fieldParam";

            $qb->andWhere($whereClause);
            $qb->setParameter($fieldParam, $value);
        }

        foreach ($different as $field => $value) {
            $fieldParam  = $field . $i++;
            $whereClause = is_array($value) ? "e.$field not in (:$fieldParam)" : "e.$field <> :$fieldParam";

            $qb->andWhere($whereClause);
            $qb->setParameter($fieldParam, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFindAllQueryBuilder()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')->from($this->entityName, 'e');
        return $qb;
    }

    /**
     * @param array|mixed $data
     * @return Table\AbstractTable
     */
    public function createTable($data)
    {
        $tableHandler = $this->getTableHandler();

        $table = $tableHandler->createTable($this->getTableClassName());
        $form  = $table->getForm();

        $form->setData($data);

        return $table;
    }

    /**
     * @param \Zff\Base\Service\Table\AbstractTable $table
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return string the html resulting
     */
    public function renderTable(Table\AbstractTable $table, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $tableHandler = $this->getTableHandler();

        $tableHandler->prepareTable($table, $queryBuilder);

        return $table->render();
    }

    public function executeTable($data, \Doctrine\ORM\QueryBuilder $queryBuilder)
    {

        $table = $this->createTable($data);
        $form = $table->getForm();

        if ($form->isValid()) {
            return $this->renderTable($table, $queryBuilder);
        }
        return false;
    }

}
