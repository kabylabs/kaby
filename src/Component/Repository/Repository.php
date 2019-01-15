<?php

declare(strict_types=1);

namespace Kaby\Component\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use function Funct\Strings\chompRight;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use ReflectionClass;
use ReflectionException;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class Repository extends ServiceEntityRepository implements RepositoryInterface
{
    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * @var array
     */
    protected $sorting = [];

    /**
     * @var bool
     */
    protected $paginated = false;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int
     */
    protected $maxPerPage;

    /**
     * Repository constructor.
     *
     * @param ManagerRegistry $registry
     *
     * @throws ReflectionException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getEntityClass());
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    private function getEntityClass(): string
    {
        $shortClassName = (new ReflectionClass(get_called_class()))->getShortName();
        $entityClassName = str_replace('Repository', 'Entity', __NAMESPACE__ . '\\' . chompRight($shortClassName, 'Repository'));

        return $entityClassName;
    }

    public function beginTransaction(): void
    {
        $this->_em->beginTransaction();
    }

    public function commit(): void
    {
        $this->_em->commit();
    }

    public function rollback(): void
    {
        $this->_em->rollback();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (!empty($criteria)) {
            $this->criteria = $criteria;
        }

        if (!empty($orderBy)) {
            $this->sorting = $orderBy;
        }

        $query = $this->createQueryBuilder($this->getAlias());

        if (null !== $limit && null !== $offset) {
            $query->setFirstResult($offset);
            $query->setMaxResults($limit);
        }

        return $this->execute($query);
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (!empty($criteria)) {
            $this->criteria = $criteria;
        }

        if (!empty($orderBy)) {
            $this->sorting = $orderBy;
        }

        $query = $this->createQueryBuilder($this->getAlias());

        return $this->executeOneOrNullResult($query);
    }

    /**
     * {@inheritdoc}
     */
    public function withPagination(): void
    {
        $this->paginated = true;
    }

    /**
     * {@inheritdoc}
     */
    public function withCriteria(array $criteria): void
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function withSorting(array $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * {@inheritdoc}
     */
    public function withLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return $this
     */
    public function paginate($currentPage, $maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
        $this->currentPage = $currentPage;
        $this->withPagination();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity): void
    {
        if (null !== $this->find($entity->getId())) {
            $this->_em->remove($entity);
            $this->_em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = [], array $sorting = []): iterable
    {
        $this->withPagination();
        $this->withCriteria($criteria);
        $this->withSorting($sorting);

        return $this->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function add($resource): void
    {
        $this->save($resource);
    }

    /**
     * Execute query
     *
     * @param QueryBuilder $query
     *
     * @return mixed
     */
    protected function execute(QueryBuilder $query)
    {
        $query = $this->modifyQuery($query);
        if ($this->paginated) {
            $this->paginated = false;

            $factory = new PagerfantaFactory();
            $pagerFanta = new Pagerfanta(new DoctrineORMAdapter($query, false, false));
            $pagerFanta->setMaxPerPage($this->maxPerPage);
            $pagerFanta->setCurrentPage($this->currentPage);

            $collection = $factory->createRepresentation($pagerFanta, new Route('route'));

            return $collection;
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Execute one or null result
     *
     * @param QueryBuilder $query
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    protected function executeOneOrNullResult(QueryBuilder $query)
    {
        $stmt = $this->modifyQuery($query)->getQuery();

        return $stmt->getOneOrNullResult();
    }

    /**
     * Execute single scalar result
     *
     * @param QueryBuilder $query
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    protected function executeSingleScalarResult(QueryBuilder $query)
    {
        $stmt = $this->modifyQuery($query)->getQuery();

        return $stmt->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $query
     *
     * @return QueryBuilder
     */
    protected function modifyQuery(QueryBuilder $query)
    {
        $this->applyCriteria($query, $this->criteria);
        $this->applySorting($query, $this->sorting);

        if ($this->limit) {
            $query->setMaxResults($this->limit);
        }

        $this->criteria = [];
        $this->sorting = [];
        $this->limit = null;

        return $query;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = [])
    {
        foreach ($criteria as $property => $value) {
            $associateNames = $this->_class->getAssociationNames();
            $fieldNames = $this->_class->getFieldNames();

            if (!in_array($property, array_merge($associateNames, $fieldNames))) {
                continue;
            }

            $name = $this->getPropertyName($property);

            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
            } else if (is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in($name, $value));
            } else if ('' !== $value) {
                $parameter = str_replace('.', '_', $property);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($name, ':' . $parameter))
                    ->setParameter($parameter, $value)
                ;
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = [])
    {
        foreach ($sorting as $property => $order) {
            $associateNames = $this->_class->getAssociationNames();
            $fieldNames = $this->_class->getFieldNames();

            if (!in_array($property, array_merge($associateNames, $fieldNames))) {
                continue;
            }

            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property), $order);
            }
        }
    }

    /**
     * @param string $name
     *
     * @param array  $aliases
     *
     * @return string
     */
    protected function getPropertyName($name, array $aliases = []): string
    {
        $parts = explode('.', $name);
        foreach ($parts as $n) {
            if (in_array($n, $aliases)) {
                return $name;
            }
        }

        return $this->getAlias() . '.' . $name;
    }

    /**
     * @return string
     */
    protected function getAlias(): string
    {
        $entityName = $this->_entityName;
        $pos = strrpos($this->_entityName, '\\') + 1;

        return strtolower(substr($entityName, $pos));
    }
}