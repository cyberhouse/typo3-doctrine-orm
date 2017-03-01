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

use Cyberhouse\DoctrineORM\Domain\Model\AbstractDoctrineEntity;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Persistence manager for extbase that uses
 * a Doctrine ORM entity manager as backend
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrinePersistenceManager implements PersistenceManagerInterface
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
        $em = $this->getEntityManager();

        if ($em->isOpen()) {
            $em->transactional(function () {
                // noop
            });
        }
        $this->em = null;
        $this->factory->reset($this->currentContext);
    }

    public function clearState()
    {
        $this->getEntityManager()->clear();
    }

    public function isNewObject($object)
    {
        return !($object instanceof AbstractDoctrineEntity && $object->getUid() > 0);
    }

    public function getIdentifierByObject($object)
    {
        $id = null;

        if ($object instanceof AbstractDoctrineEntity) {
            $id = $object->getUid();
        }

        return $id;
    }

    public function getObjectByIdentifier($identifier, $objectType = null, $useLazyLoading = false)
    {
        return $this->getEntityManager()->find($objectType, $identifier);
    }

    public function getObjectCountByQuery(QueryInterface $query)
    {
        return $query->count();
    }

    public function getObjectDataByQuery(QueryInterface $query)
    {
        return $query->execute(true);
    }

    public function registerRepositoryClassName($className)
    {
    }

    public function add($object)
    {
        $this->em->persist($object);
    }

    public function remove($object)
    {
        $this->em->remove($object);
    }

    public function update($object)
    {
        $this->em->persist($object);
    }

    public function injectSettings(array $settings)
    {
    }

    /**
     * Converts the given object into an array containing the identity of the domain object.
     *
     * @param object $object The object to be converted
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException
     * @return array
     * @api
     */
    public function convertObjectToIdentityArray($object)
    {
        throw new NotImplementedException(__METHOD__, 1476108103);
    }

    /**
     * Recursively iterates through the given array and turns objects
     * into arrays containing the identity of the domain object.
     *
     * @param array $array The array to be iterated over
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException
     * @return array
     * @api
     * @see convertObjectToIdentityArray()
     */
    public function convertObjectsToIdentityArrays(array $array)
    {
        throw new NotImplementedException(__METHOD__, 1476108111);
    }

    public function createQueryForType($type)
    {
        return new DoctrineQuery($this->em, $type);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        if (!$this->em) {
            $conf = $this->configuration->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $this->currentContext = $conf['extensionName'];
            $this->em = $this->factory->get($this->currentContext);
        }
        return $this->em;
    }
}
