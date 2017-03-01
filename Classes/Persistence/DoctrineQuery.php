<?php
namespace Cyberhouse\DoctrineORM\Persistence;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * A QOM - like query using the Doctrine ORM query builder
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineQuery implements QueryInterface
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @var string
     */
    private $type;

    /**
     * DoctrineQuery constructor.
     *
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, string $class)
    {
        $this->type = $class;
        $this->qb = $em->createQueryBuilder();
    }

    public function getSource()
    {
        return $this->qb;
    }

    public function execute($returnRawQueryResult = false)
    {
        return new DoctrineQueryResult(
            $this->qb->select('e')->from($this->type, 'e')->getQuery(),
            $returnRawQueryResult
        );
    }

    public function setOrderings(array $orderings)
    {
        foreach ($orderings as $field => $dir) {
            $this->qb->addOrderBy($field, $dir);
        }
        return $this;
    }

    public function setLimit($limit)
    {
        $this->qb->setMaxResults($limit);
    }

    public function setOffset($offset)
    {
        $this->qb->setFirstResult($offset);
    }

    public function matching($constraint)
    {
        $this->qb->where($constraint);
    }

    public function logicalAnd($constraint1)
    {
        if (!is_array($constraint1)) {
            $constraint1 = func_get_args();
        }

        return $this->qb->expr()->andX(...$constraint1);
    }

    public function logicalOr($constraint1)
    {
        if (!is_array($constraint1)) {
            $constraint1 = func_get_args();
        }

        return $this->qb->expr()->orX(...$constraint1);
    }

    public function logicalNot(ConstraintInterface $constraint)
    {
        throw new NotImplementedException('This method only exists to satisfy the interface. Use ->not instead');
    }

    public function not($expr)
    {
        return $this->qb->expr()->not($expr);
    }

    public function equals($propertyName, $operand, $caseSensitive = true)
    {
        return $this->qb->expr()->eq($propertyName, $operand);
    }

    public function like($propertyName, $operand, $caseSensitive = true)
    {
        return $this->qb->expr()->like($propertyName, $operand);
    }

    public function contains($propertyName, $operand)
    {
        return $this->qb->expr()->isMemberOf($propertyName, $operand);
    }

    public function in($propertyName, $operand)
    {
        return $this->qb->expr()->in($propertyName, $operand);
    }

    public function lessThan($propertyName, $operand)
    {
        return $this->qb->expr()->lt($propertyName, $operand);
    }

    public function lessThanOrEqual($propertyName, $operand)
    {
        return $this->qb->expr()->lte($propertyName, $operand);
    }

    public function greaterThan($propertyName, $operand)
    {
        return $this->qb->expr()->gt($propertyName, $operand);
    }

    public function greaterThanOrEqual($propertyName, $operand)
    {
        return $this->qb->expr()->gte($propertyName, $operand);
    }

    public function getType()
    {
        return $this->qb->getType();
    }

    public function setQuerySettings(QuerySettingsInterface $querySettings)
    {
        // Noop
    }

    public function getQuerySettings()
    {
        // Noop
    }

    public function count()
    {
        $qb = clone $this->qb;
        return $qb->select('COUNT(e)')->from($this->type, 'e')->getQuery()->getSingleScalarResult();
    }

    public function getOrderings()
    {
        return [];
    }

    public function getLimit()
    {
        return $this->qb->getMaxResults();
    }

    public function getOffset()
    {
        return $this->qb->getFirstResult();
    }

    /**
     * @return \Doctrine\DBAL\Query\Expression\ExpressionBuilder|\Doctrine\ORM\Query\Expr
     */
    public function getConstraint()
    {
        return $this->qb->expr();
    }

    /**
     * @param string $propertyName
     * @return string
     */
    public function isEmpty($propertyName)
    {
        return $this->logicalAnd([
            $this->qb->expr()->isNotNull($propertyName),
            $this->not($this->equals($propertyName, '')),
            $this->not($this->equals($propertyName, 0)),
        ]);
    }

    public function setSource(SourceInterface $source)
    {
        // noop
    }

    public function getStatement()
    {
        $qb = clone $this->qb;
        return $qb->getSQL();
    }
}
