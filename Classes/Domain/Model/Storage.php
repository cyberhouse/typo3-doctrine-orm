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

/**
 * Model of a FAL storage
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_file_storage")
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class Storage extends AbstractDoctrineEntity
{
    /**
     * @ORM\Column(name="tstamp", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $lastModified = 0;

    /**
     * @ORM\Column(name="crdate", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $createdDate = 0;

    /**
     * @ORM\Column(name="cruser_id", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $createdBy = 0;

    /**
     * @ORM\Column(name="deleted", type="boolean")
     * @var bool
     */
    private $deleted = false;

    /**
     * @ORM\Column(name="name", type="string")
     * @var bool
     */
    private $name = '';

    /**
     * @ORM\Column(name="description", type="text")
     * @var bool
     */
    private $description = '';

    /**
     * Returns LastModified
     *
     * @return int
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Sets LastModified
     *
     * @param int $lastModified
     * @return Storage
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * Returns CreatedDate
     *
     * @return int
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Sets CreatedDate
     *
     * @param int $createdDate
     * @return Storage
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * Returns CreatedBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Sets CreatedBy
     *
     * @param int $createdBy
     * @return Storage
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Returns Deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Sets Deleted
     *
     * @param bool $deleted
     * @return Storage
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * Returns Name
     *
     * @return bool
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets Name
     *
     * @param bool $name
     * @return Storage
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns Description
     *
     * @return bool
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets Description
     *
     * @param bool $description
     * @return Storage
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}
