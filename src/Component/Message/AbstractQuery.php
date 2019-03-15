<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

use Kaby\Component\Repository\Repository;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractQuery extends AbstractMessage
{
    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @return int
     */
    public function getPage(): int
    {
        return (int) $this->page ?: Repository::CURRENT_PAGE;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) $this->limit ?: Repository::MAX_PER_PAGE;
    }
}