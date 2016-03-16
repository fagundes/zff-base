<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2015 Vinicius Fagundes
 */

namespace Zff\Base\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Zend\Db\Adapter\Adapter;
use Zff\Base\Exception;

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

    /**
     * @var boolean
     */
    protected $autocommit;

    /**
     * @var integer
     */
    protected $maxResults;

    /**
     * @var integer
     */
    protected $firstResult;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager = null)
    {
        $this->autocommit = true;
        $this->maxResults = 20;
        $this->firstResult = 1;
        if ($entityManager) {
            $this->setEntityManager($entityManager);
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
        return $this->entityManagerName ?: 'doctrine.entitymanager.orm_default';
    }

    public function setEntityManagerName($entityManagerName)
    {
        $this->entityManagerName = $entityManagerName;
    }

    public function getDbAdapterName()
    {
        return $this->dbAdapterName ?: 'zfdb_adapter';
    }

    public function setDbAdapterName($dbAdapterName)
    {
        $this->dbAdapterName = $dbAdapterName;
    }

    public function getTableHandler()
    {
        return $this->tableHandler;
    }

    public function setTableHandler(Table\TableHandler $tableHandler)
    {
        $this->tableHandler = $tableHandler;
    }

    public function getTableClassName()
    {
        if (!$this->tableClassName) {
            $reflectionFinalClass = new \ReflectionClass($this);
            $this->tableClassName = $reflectionFinalClass->getNamespaceName()
                . '\\Table\\'
                . $reflectionFinalClass->getShortName();
        }
        return $this->tableClassName;
    }

    public function setTableClassName($tableClassName)
    {
        $this->tableClassName = $tableClassName;
        return $this;
    }

    public function getAutocommit()
    {
        return $this->autocommit;
    }

    public function setAutocommit($autocommit)
    {
        $this->autocommit = $autocommit;
    }

    /**
     * @return array list of service's names
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Proxy Method.
     * @return \Doctrine\ORM\EntityRepository repository object
     */
    public function getRepository()
    {
        $this->checkIfClassExists($this->entityName);
        return $this->getEntityManager()->getRepository($this->entityName);
    }

    /**
     * Proxy Method.
     *
     * @param mixed $id
     *
     * @return \Zff\Base\Entity\AbstractEntity
     */
    public function getReference($id)
    {
        return $this->entityManager->getReference($this->entityName, $id);
    }

    /**
     * @param array|\Zff\Base\Entity\AbstractEntity $entity
     *
     * @return \Zff\Base\Entity\AbstractEntity
     */
    public function insert($entity)
    {
        if (is_array($entity)) {
            $this->checkIfClassExists($this->entityName);
            $entity = new $this->entityName($entity);
        }

        $this->entityManager->persist($entity);
        if ($this->getAutocommit()) {
            $this->entityManager->flush();
        }
        return $entity;
    }

    /**
     * @param array|\Zff\Base\Entity\AbstractEntity $entity
     *
     * @return \Zff\Base\Entity\AbstractEntity
     */
    public function update($entity)
    {

        if (is_array($entity)) {
            $this->checkIfClassExists($this->entityName);
            $entity = $this->getReference($this->entityName, $entity['id']);
            $entity->exchangeArray($entity);
        }

        $this->entityManager->persist($entity);
        if ($this->getAutocommit()) {
            $this->entityManager->flush();
        };
        return $entity;
    }

    /**
     * Update all entities given.
     *
     * @param array $entities
     *
     * @return array Entities updated
     */
    public function updateAll($entities)
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        if ($this->getAutocommit()) {
            $this->entityManager->flush();
        }
        return $entities;
    }

    /**
     * Delete entity with the $id given.
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $this->checkIfClassExists($this->entityName);
        $entity = $this->getReference($id);

        if ($entity) {
            $this->entityManager->remove($entity);
            if ($this->getAutocommit()) {
                $this->entityManager->flush();
            }
            return true;
        }
        return false;
    }

    /**
     * Delete all entities from this entity.
     *
     * @return boolean|array
     */
    public function deleteAll()
    {
        $dql = "DELETE from " . $this->entityName;
        $qb = $this->entityManager->createQuery($dql);
        $result = $qb->getResult();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Finds entities by a set of criteria.
     * Method proxy to EntityRepository::findBy
     *
     * @param array $criteria
     * @param array $orderBy
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findBy($criteria, (array)$orderBy);
    }

    /**
     * Finds a single entity by a set of criteria.
     * Method proxy to EntityRepository::findOneBy
     *
     * @param array $criteria
     * @param array $orderBy
     *
     * @return object|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, (array)$orderBy);
    }

    /**
     * Finds an entity by its primary key / identifier.
     * Method proxy to EntityRepository::find
     *
     * @param int $id
     *
     * @return \Zff\Base\Entity\AbstractEntity
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Finds all entities WIHOUT a set of criteria.
     *
     * @param array $orderBy
     *
     * @return array
     */
    public function findAll(array $orderBy = null)
    {
        return $this->getRepository()->findBy([], (array)$orderBy);
    }

    /**
     * Retorna o total de resultados,
     * $equal serão incluidos como criterios utilizando: '=' ou 'in'
     * $different serão incluidos como criterios utilizando: '<>' ou 'not in'
     *
     * @param array $equal Criterios iguais.
     * @param array $different Criterios diferentes.
     *
     * @return int total
     */
    public function count(array $equal = [], array $different = [])
    {

        $this->checkIfClassExists($this->entityName);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(e)')
            ->from($this->entityName, 'e');

        $i = 0;
        foreach ($equal as $field => $value) {
            $fieldParam = $field . $i++;
            $whereClause = is_array($value) ? "e.$field in (:$fieldParam)" : "e.$field = :$fieldParam";

            $qb->andWhere($whereClause);
            $qb->setParameter($fieldParam, $value);
        }

        foreach ($different as $field => $value) {
            $fieldParam = $field . $i++;
            $whereClause = is_array($value) ? "e.$field not in (:$fieldParam)" : "e.$field <> :$fieldParam";

            $qb->andWhere($whereClause);
            $qb->setParameter($fieldParam, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $currentPageNumber the doctrine object query
     * @param int $itemCountPerPage items per page
     *
     * @return \Zff\Base\Service\AbstractService
     */
    public function setPagination($currentPageNumber = null, $itemCountPerPage = null)
    {
        $this->maxResults = $itemCountPerPage !== null ? $itemCountPerPage : $this->maxResults;
        $this->firstResult = $currentPageNumber !== null ? ($this->maxResults - 1)
            * $this->maxResults : $this->firstResult;
        return $this;
    }

    /**
     * Create and returns a doctrine pagination.
     *
     * @param Query $query the doctrine object query
     * @param int   $currentPageNumber current page
     * @param int   $itemCountPerPage items per page
     *
     * @return Paginator
     */
    public function getPaginator(
        Query $query,
        $currentPageNumber = null,
        $itemCountPerPage
        = null
    ) {
    

        $this->setPagination($currentPageNumber, $itemCountPerPage);
        $query->setFirstResult($this->firstResult)
            ->setMaxResults($this->maxResults);
        return new Paginator($query);
    }

    public function getFindAllQueryBuilder()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')->from($this->entityName, 'e');
        return $qb;
    }

    /**
     * @param array|mixed $data
     *
     * @return Table\AbstractTable
     */
    public function createTable($data)
    {
        $tableHandler = $this->getTableHandler();

        $table = $tableHandler->createTable($this->getTableClassName());
        $form = $table->getForm();

        $form->setData($data);

        return $table;
    }

    /**
     * @param \ZfTable\AbstractTable     $table
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return string the html resulting
     */
    public function renderTable(\ZfTable\AbstractTable $table, \Doctrine\ORM\QueryBuilder $queryBuilder)
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

    protected function checkIfClassExists($class)
    {
        if (!class_exists($class)) {
            throw new Exception\RuntimeException(sprintf('Class %s does not exist.', $class));
        }
    }
}
