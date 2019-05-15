<?php

namespace App\Repository;

use App\Repository\Extra\Operator;
use App\Aware\PaginatorAware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseRepository extends ServiceEntityRepository
{
    use PaginatorAware;

    const PAGINATION_DEFAULT_PAGE = 1;
    const PAGINATION_DEFAULT_LIMIT = 10;

    private $placeholderCounter = 0;

    /**
     * @param array $params
     *
     * @return array
     */
    public function findByParams(array $params = []): array
    {
        $filters = $params['filters'] ?? [];
        $orders = $params['orders'] ?? [];
        $pagination = $params['pagination'] ?? [];

        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);

        if ($filters) {
            $this->applyFilters($filters, $qb, $alias, $this->getClassName());
        }

        $this->applyOrder($orders ?: ['id' => 'DESC'], $qb, $alias, $this->getClassName());


        return $this->applyPagination($pagination, $qb);
    }

    /**
     * @param array $filters
     *
     * @return int
     */
    public function getCount(?array $filters): int
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);

        if ($filters) {
            $this->applyFilters($filters, $qb, $alias, $this->getClassName());
        }

        $count = (int)$qb
            ->select($qb->expr()->count($this->getAlias() . '.id'))
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    protected function getAlias(): string
    {
        $entityNamespaceParts = \explode('\\', $this->getEntityName());
        $entityClassShortName = \end($entityNamespaceParts);

        return mb_strtolower($entityClassShortName);
    }

    /**
     * @param array|null $pagination
     * @param QueryBuilder $qb
     *
     * @return array
     */
    protected function applyPagination(?array $pagination, QueryBuilder $qb): array
    {
        $page = (int)($pagination['page'] ?? static::PAGINATION_DEFAULT_PAGE);
        $limit = (int)($pagination['limit'] ?? static::PAGINATION_DEFAULT_LIMIT);

        $paginationView = $this->getPaginator()->paginate($qb, $page, $limit);

        header('Pagination-Count: ' . $paginationView->getTotalItemCount());
        header('Pagination-Limit: ' . $limit);
        header('Pagination-Page: ' . $page);

        return $paginationView->getItems();
    }

    /**
     * @param array $order
     * @param QueryBuilder $qb
     * @param string $alias
     * @param string $className
     */
    protected function applyOrder(array $order, QueryBuilder $qb, string $alias, string $className): void
    {
        $metadata = $this->getEntityManager()->getClassMetadata($className);
        $columns = $metadata->getFieldNames();
        $relationColumns = $metadata->getAssociationMappings();
        foreach ($order as $field => $value) {
            $column = $alias . '.' . $field;
            if (isset($relationColumns[$field]) && !\in_array($field, $qb->getAllAliases(), true)) {
                $qb->leftJoin($column, $field);
                $this->applyOrder($value, $qb, $field, $relationColumns[$field]['targetEntity']);
                continue;
            }

            if (!\in_array($field, $columns, true)) {
                continue;
            }

            $qb->addOrderBy($column, $value);
        }
    }

    /**
     * @param iterable $filters
     * @param QueryBuilder $qb
     * @param string $alias
     * @param string $className
     */
    protected function applyFilters(iterable $filters, QueryBuilder $qb, string $alias, string $className): void
    {
        $metadata = $this->getEntityManager()->getClassMetadata($className);
        $columns = $metadata->getFieldNames();
        $relationColumns = $metadata->getAssociationMappings();

        foreach ($filters as $field => $value) {

            $column = $alias . '.' . $field;

            if (isset($relationColumns[$field]) && !\in_array($field, $qb->getAllAliases(), true)) {
                $qb->leftJoin($column, $field);
                $this->applyFilters($value, $qb, $field, $relationColumns[$field]['targetEntity']);
                continue;
            }

            if (!\in_array($field, $columns, true)) {
                continue;
            }

            if (\is_array($value) && !empty($value) && \in_array(\key($value), Operator::getConstants())) {
                foreach ($value as $operatorType => $operatorValue) {
                    $this->applyFilters([
                        $field => [
                            'operator' => $operatorType,
                            'value' => $operatorValue
                        ]
                    ], $qb, $alias, $className);
                }
                continue;
            }

            $operator = $value['operator'] ?? (\is_array($value) ? $operator = Operator::IN : Operator::EQUAL);
            $value = $value['value'] ?? $value;

            switch ($operator) {
                case (Operator::EQUAL):
                    $this->eq($qb, $column, $value);
                    break;
                case (Operator::NOT_EQUAL):
                    $this->neq($qb, $column, $value);
                    break;
                case (Operator::IN):
                    $this->in($qb, $column, $value);
                    break;
                case (Operator::NOT_IN):
                    $this->nin($qb, $column, $value);
                    break;
                case (Operator::LESS_THAN):
                    $this->lt($qb, $column, $value);
                    break;
                case (Operator::LESS_THAN_OR_EQUAL):
                    $this->lte($qb, $column, $value);
                    break;
                case (Operator::GREAT_THAN):
                    $this->gt($qb, $column, $value);
                    break;
                case (Operator::GREAT_THAN_OR_EQUAL):
                    $this->gte($qb, $column, $value);
                    break;
                case (Operator::IS_NULL):
                    $this->isnull($qb, $column, $value);
                    break;
            }
        }
    }

    private function gt(QueryBuilder $qb, string $column, $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' > :' . $placeholder)
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function gte(QueryBuilder $qb, string $column, $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' >= :' . $placeholder)
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function lt(QueryBuilder $qb, string $column, $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' < :' . $placeholder)
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function lte(QueryBuilder $qb, string $column, $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' <= :' . $placeholder)
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function eq(QueryBuilder $qb, string $column, $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' = :' . $placeholder)
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function neq(QueryBuilder $qb, string $column, $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' <> :' . $placeholder)
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function in(QueryBuilder $qb, string $column, array $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' IN (:' . $placeholder . ')')
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function nin(QueryBuilder $qb, string $column, array $value): self
    {
        $placeholder = $this->createPlaceholder($column);
        $qb->andWhere($column . ' NOT IN (:' . $placeholder . ')')
            ->setParameter($placeholder, $value);

        return $this;
    }

    private function isnull(QueryBuilder $qb, string $column, $value): self
    {
        $operator = $value ? 'IS NULL' : 'IS NOT NULL';
        $qb->andWhere($column . ' ' . $operator);

        return $this;
    }

    private function createPlaceholder(string $name): string
    {
        return (str_replace('.', '_', $name) . ++$this->placeholderCounter);
    }
}