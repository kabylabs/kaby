<?php

declare(strict_types=1);

namespace Kaby\Component\Specification;

use Doctrine\ORM\QueryBuilder;
use Kaby\Component\Repository\RepositoryInterface;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
interface SpecificationInterface
{
    /**
     * @param QueryBuilder        $query
     * @param RepositoryInterface $repository
     */
    public function match(QueryBuilder $query, RepositoryInterface $repository);
}