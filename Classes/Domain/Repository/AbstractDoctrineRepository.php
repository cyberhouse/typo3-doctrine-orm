<?php
namespace Cyberhouse\DoctrineORM\Domain\Repository;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

/**
 * Base for Doctrine ORM based repositories
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
abstract class AbstractDoctrineRepository implements RepositoryInterface, SingletonInterface
{

    /**
     * Key of the extension this class is part of
     *
     * Defaults to the second part of the classes namespace
     * Override to set a custom
     *
     * @var string
     */
    protected $extensionKey;

    /**
     * Class name of the entity
     *
     * Default is the same name, within the "Model" sub namespace and
     * without the "Repository" suffix
     * Override to set to custom
     *
     * @var string
     */
    protected $modelClassName;
    /**
     * @inject
     * @var \Cyberhouse\DoctrineORM\Utility\EntityManagerFactory
     */
    private $emFactory;

    public function initializeObject()
    {
        if (empty($this->extensionKey)) {
            $parts = explode('\\', get_class($this));
            $this->extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($parts[1]);
        }

        if (empty($this->modelClassName)) {
            $this->modelClassName = substr(str_replace('\\Repository\\', '\\Model\\', get_class($this)), 0, -10);
        }
    }

    public function add($object)
    {
        $this->getEntityManager()->persist($object);
    }

    public function remove($object)
    {
        $this->getEntityManager()->remove($object);
    }

    public function update($modifiedObject)
    {
        $this->getEntityManager()->persist($modifiedObject);
    }

    public function findAll()
    {
        return $this->getEntityManager()->getRepository($this->modelClassName)->findAll();
    }

    public function countAll()
    {
        $dql = 'SELECT COUNT(e.uid) FROM ' . $this->modelClassName . ' e';
        return $this->createDqlQuery($dql)->getSingleScalarResult();
    }

    public function removeAll()
    {
        $dql = 'DELETE ' . $this->modelClassName . ' e';
        return $this->createDqlQuery($dql)->execute();
    }

    public function findByUid($uid)
    {
        return $this->findByIdentifier($uid);
    }

    public function findByIdentifier($identifier)
    {
        return $this->getEntityManager()->find($this->modelClassName, $identifier);
    }

    public function setDefaultOrderings(array $defaultOrderings)
    {
        throw new NotImplementedException('Default orderings via repository settings are not supported');
    }

    public function setDefaultQuerySettings(QuerySettingsInterface $defaultQuerySettings)
    {
        throw new NotImplementedException('Query settings for Doctrine ORM are not supported');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQuery()
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    /**
     * @param string $name
     * @return \Doctrine\ORM\Query
     */
    public function createNamedQuery(string $name)
    {
        return $this->getEntityManager()->createNamedQuery($name);
    }

    /**
     * @param string $dql
     * @return \Doctrine\ORM\Query
     */
    public function createDqlQuery(string $dql)
    {
        return $this->getEntityManager()->createQuery($dql);
    }

    protected function getEntityManager()
    {
        return $this->emFactory->get($this->extensionKey);
    }
}
