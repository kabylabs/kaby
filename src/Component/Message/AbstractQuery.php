<?php

declare(strict_types=1);

namespace Kaby\Component\Message;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author  Arif Setianto <arifsetiantoo@gmail.com>
 */
abstract class AbstractQuery extends AbstractMessage
{
    /**
     * @var int
     */
    const MAX_PER_PAGE = 50;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $maxPerPage;

    /**
     * @var bool
     */
    private $paginated = false;

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function paginateFromRequest(Request $request)
    {
        return $this->paginate(
            $request->get('page', 1),
            $request->get('limit', self::MAX_PER_PAGE)
        );
    }

    /**
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return $this
     */
    public function paginate($currentPage, $maxPerPage = self::MAX_PER_PAGE)
    {
        if ($maxPerPage > 100) {
            throw new InvalidArgumentException('Maximum per page is 100 rows');
        }

        $this->currentPage = $currentPage;
        $this->maxPerPage = $maxPerPage;

        if ($this->currentPage && $this->maxPerPage) {
            $this->paginated = true;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPaginated()
    {
        return $this->paginated;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }
}