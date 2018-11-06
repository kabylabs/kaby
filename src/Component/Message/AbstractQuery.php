<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractQuery extends AbstractMessage
{
    const CURRENT_PAGE = 1;
    const MAX_PER_PAGE = 50;

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
        return (int) $this->page ?: self::CURRENT_PAGE;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) $this->limit ?: self::MAX_PER_PAGE;
    }
}