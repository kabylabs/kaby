<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractQuery extends AbstractMessage
{
    /**
     * @var int
     */
    const CURRENT_PAGE = 1;

    /**
     * @var int
     */
    const MAX_PER_PAGE = 50;

    /**
     * @var int
     */
    private $page = self::CURRENT_PAGE;

    /**
     * @var int
     */
    private $limit = self::MAX_PER_PAGE;

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}