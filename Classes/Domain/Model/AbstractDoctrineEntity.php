<?php
namespace Cyberhouse\DoctrineORM\Domain\Model;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 * Base for Doctrine ORM managed entities for TYPO3
 *
 * Extends the default abstract domain object to
 * ensure extbase handles it like an extbase entity
 *
 * @ORM\MappedSuperclass
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
abstract class AbstractDoctrineEntity extends AbstractDomainObject
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="uid", type="integer")
     * @var int
     */
    protected $uid;

    /**
     * @ORM\Column(name="pid", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    protected $pid = 0;

    /**
     * Returns Uid
     *
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Returns Pid
     *
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Sets Pid
     *
     * @param mixed $pid
     * @return AbstractDoctrineEntity
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }

    public function _isDirty($propertyName = null)
    {
        return false;
    }
}
