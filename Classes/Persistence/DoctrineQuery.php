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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

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
        $this->queryBuilder = $em->createQueryBuilder();
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    public function getSource()
    {
        return $this->queryBuilder;
    }

    public function execute($returnRawQueryResult = false)
    {
        return $this->objectManager->get(
            DoctrineQueryResult::class,
            $this->queryBuilder->select('e')->from($this->type, 'e')->getQuery(),
            $returnRawQueryResult
        );
    }

    public function setOrderings(array $orderings)
    {
        foreach ($orderings as $field => $dir) {
            $this->queryBuilder->addOrderBy('e.' . $field, $dir);
        }
        return $this;
    }

    public function setLimit($limit)
    {
        $this->queryBuilder->setMaxResults($limit);
        return $this;
    }

    public function setOffset($offset)
    {
        $this->queryBuilder->setFirstResult($offset);
        return $this;
    }

    public function matching($constraint)
    {
        $this->queryBuilder->where($constraint);
        return $this;
    }

    public function logicalAnd($constraint1)
    {
        if (!is_array($constraint1)) {
            $constraint1 = func_get_args();
        }

        return $this->queryBuilder->expr()->andX(...$constraint1);
    }

    public function logicalOr($constraint1)
    {
        if (!is_array($constraint1)) {
            $constraint1 = func_get_args();
        }

        return $this->queryBuilder->expr()->orX(...$constraint1);
    }

    public function logicalNot(ConstraintInterface $constraint)
    {
        throw new NotImplementedException('This method only exists to satisfy the interface. Use ->not instead');
    }

    public function not($expr)
    {
        return $this->queryBuilder->expr()->not($expr);
    }

    public function equals($propertyName, $operand, $caseSensitive = true)
    {
        return $this->queryBuilder->expr()->eq('e.' . $propertyName, $operand);
    }

    public function like($propertyName, $operand, $caseSensitive = true)
    {
        return $this->queryBuilder->expr()->like('e.' . $propertyName, $operand);
    }

    public function contains($propertyName, $operand)
    {
        return $this->queryBuilder->expr()->isMemberOf('e.' . $propertyName, $operand);
    }

    public function in($propertyName, $operand)
    {
        return $this->queryBuilder->expr()->in('e.' . $propertyName, $operand);
    }

    public function lessThan($propertyName, $operand)
    {
        return $this->queryBuilder->expr()->lt('e.' . $propertyName, $operand);
    }

    public function lessThanOrEqual($propertyName, $operand)
    {
        return $this->queryBuilder->expr()->lte('e.' . $propertyName, $operand);
    }

    public function greaterThan($propertyName, $operand)
    {
        return $this->queryBuilder->expr()->gt('e.' . $propertyName, $operand);
    }

    public function greaterThanOrEqual($propertyName, $operand)
    {
        return $this->queryBuilder->expr()->gte('e.' . $propertyName, $operand);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setQuerySettings(QuerySettingsInterface $querySettings)
    {
        throw new NotImplementedException('Doctrine ORM does not support query settings');
    }

    public function getQuerySettings()
    {
        throw new NotImplementedException('Doctrine ORM does not support query settings');
    }

    public function count()
    {
        return $this->getQueryBuilderCopy()
            ->select('COUNT(e)')
            ->from($this->type, 'e')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOrderings()
    {
        throw new NotImplementedException('Doctrine ORM does not support fetching orderings');
    }

    public function getLimit()
    {
        return $this->queryBuilder->getMaxResults();
    }

    public function getOffset()
    {
        return $this->queryBuilder->getFirstResult();
    }

    /**
     * @return \Doctrine\DBAL\Query\Expression\ExpressionBuilder|\Doctrine\ORM\Query\Expr
     */
    public function getConstraint()
    {
        return $this->queryBuilder->expr();
    }

    /**
     * @param string $propertyName
     * @return string
     */
    public function isEmpty($propertyName)
    {
        return $this->logicalOr([
            $this->queryBuilder->expr()->isNull('e.' . $propertyName),
            $this->equals($propertyName, ''),
            $this->equals($propertyName, 0),
        ]);
    }

    public function setSource(SourceInterface $source)
    {
        throw new NotImplementedException('Doctrine ORM does not support settings a source');
    }

    public function getStatement()
    {
        return $this->getQueryBuilderCopy()->getDQL();
    }

    protected function getQueryBuilderCopy()
    {
        return clone $this->queryBuilder;
    }
}
