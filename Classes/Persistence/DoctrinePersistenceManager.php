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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Persistence manager for extbase that uses
 * a Doctrine ORM entity manager as backend
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrinePersistenceManager extends PersistenceManager
{
    /**
     * @inject
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configuration;

    /**
     * @inject
     * @var \Cyberhouse\DoctrineORM\Utility\EntityManagerFactory
     */
    protected $factory;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $currentContext;

    public function persistAll()
    {
        try {
            $em = $this->getEntityManager();

            if ($em->isOpen()) {
                $em->transactional(function () {
                    // noop
                });
            }
            $this->em = null;
            $this->factory->reset($this->currentContext);
        } catch (\UnexpectedValueException $ignored) {
            parent::persistAll();
        }
    }

    public function clearState()
    {
        try {
            $this->getEntityManager()->clear();
        } catch (\UnexpectedValueException $ignored) {
            parent::clearState();
        }
    }

    public function isNewObject($object)
    {
        return !($object instanceof AbstractEntity && $object->getUid() > 0);
    }

    public function getIdentifierByObject($object)
    {
        $id = null;

        if ($object instanceof AbstractEntity) {
            $id = $object->getUid();
        }

        return $id;
    }

    public function getObjectByIdentifier($identifier, $objectType = null, $useLazyLoading = false)
    {
        try {
            return $this->getEntityManager()->find($objectType, $identifier);
        } catch (\UnexpectedValueException $ignored) {
            return parent::getObjectByIdentifier($identifier, $objectType, $useLazyLoading);
        }
    }

    public function getObjectCountByQuery(QueryInterface $query)
    {
        return $query->count();
    }

    public function getObjectDataByQuery(QueryInterface $query)
    {
        return $query->execute(true);
    }

    public function add($object)
    {
        try {
            $this->getEntityManager()->persist($object);
        } catch (\UnexpectedValueException $ignored) {
            parent::add($object);
        }
    }

    public function remove($object)
    {
        try {
            $this->getEntityManager()->remove($object);
        } catch (\UnexpectedValueException $ignored) {
            parent::remove($object);
        }
    }

    public function update($object)
    {
        try {
            $this->getEntityManager()->persist($object);
        } catch (\UnexpectedValueException $ignored) {
            parent::update($object);
        }
    }

    public function createQueryForType($type)
    {
        try {
            return GeneralUtility::makeInstance(DoctrineQuery::class, $this->getEntityManager(), $type);
        } catch (\UnexpectedValueException $ignored) {
            return parent::createQueryForType($type);
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        if (!$this->em) {
            $conf = $this->configuration->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $this->currentContext = GeneralUtility::camelCaseToLowerCaseUnderscored($conf['extensionName']);
            $this->em = $this->factory->get($this->currentContext);
        }
        return $this->em;
    }
}
