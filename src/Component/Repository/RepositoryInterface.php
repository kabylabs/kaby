<?php

declare(strict_types=1);

namespace Kaby\Component\Repository;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface RepositoryInterface
{
    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;

    /**
     * Finds an object by its primary key / identifier
     *
     * @param int|string $id
     * @param int|null   $lockMode
     * @param int|null   $lockVersion
     *
     * @return mixed
     */
    public function find($id, $lockMode = null, $lockVersion = null);

    /**
     * Finds all objects in the repository
     *
     * @return array
     */
    public function findAll();

    /**
     * Finds objects by a set of criteria
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Finds a single object by a set of criteria
     *
     * @param array $criteria
     *
     * @return object
     */
    public function findOneBy(array $criteria);

    /**
     * Apply pagination to repository
     */
    public function withPagination(): void;

    /**
     * Apply criteria to repository
     *
     * @param array $criteria
     */
    public function withCriteria(array $criteria): void;

    /**
     * Apply sorting to repository
     *
     * @param array $sorting
     */
    public function withSorting(array $sorting): void;

    /**
     * Apply limit to repository
     *
     * @param int $limit
     */
    public function withLimit(int $limit): void;

    /**
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return $this
     */
    public function paginate($currentPage, $maxPerPage);

    /**
     * Save entity
     *
     * @param object $entity
     */
    public function save($entity): void;

    /**
     * @param $entity
     */
    public function remove($entity): void;
}