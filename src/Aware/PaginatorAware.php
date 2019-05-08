<?php

namespace App\Aware;

use Knp\Component\Pager\PaginatorInterface;

trait PaginatorAware
{
    /** @var PaginatorInterface */
    private $paginator;

    /**
     * @required
     *
     * @param PaginatorInterface $paginator
     */
    public function setPaginator(PaginatorInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @return PaginatorInterface
     */
    protected function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }
}