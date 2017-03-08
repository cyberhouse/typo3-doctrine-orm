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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model of a FAL resource
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_file")
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class File extends AbstractDoctrineEntity
{
    /**
     * @var Collection
     */
    private $metaData;

    /**
     * @var Collection
     */
    private $processedFiles;

    /**
     * @ORM\JoinColumn(name="storage", referencedColumnName="uid", nullable=true, onDelete="SET NULL")
     * @ORM\ManyToOne(targetEntity="Storage")
     * @var Storage
     */
    private $storage;

    private $lastChanged = 0;

    private $lastIndexed = 0;

    private $type = 0;

    private $identifier = '';

    private $identifierHash = '';

    private $extension = '';

    private $mimeType = '';

    private $name = '';

    private $contentHash = '';

    private $size = 0;

    private $creationDate = 0;

    private $modifiationDate = 0;

    public function __construct()
    {
        $this->metaData = new ArrayCollection();
        $this->processedFiles = new ArrayCollection();
    }

    /**
     * Returns MetaData
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * Sets MetaData
     *
     * @param \Doctrine\Common\Collections\Collection $metaData
     * @return File
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
        return $this;
    }

    /**
     * Returns ProcessedFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProcessedFiles()
    {
        return $this->processedFiles;
    }

    /**
     * Sets ProcessedFiles
     *
     * @param \Doctrine\Common\Collections\Collection $processedFiles
     * @return File
     */
    public function setProcessedFiles($processedFiles)
    {
        $this->processedFiles = $processedFiles;
        return $this;
    }

    /**
     * Returns Storage
     *
     * @return \Cyberhouse\DoctrineORM\Domain\Model\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Sets Storage
     *
     * @param \Cyberhouse\DoctrineORM\Domain\Model\Storage $storage
     * @return File
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Returns LastChanged
     *
     * @return int
     */
    public function getLastChanged()
    {
        return $this->lastChanged;
    }

    /**
     * Sets LastChanged
     *
     * @param int $lastChanged
     * @return File
     */
    public function setLastChanged($lastChanged)
    {
        $this->lastChanged = $lastChanged;
        return $this;
    }

    /**
     * Returns LastIndexed
     *
     * @return int
     */
    public function getLastIndexed()
    {
        return $this->lastIndexed;
    }

    /**
     * Sets LastIndexed
     *
     * @param int $lastIndexed
     * @return File
     */
    public function setLastIndexed($lastIndexed)
    {
        $this->lastIndexed = $lastIndexed;
        return $this;
    }

    /**
     * Returns Type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets Type
     *
     * @param int $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets Identifier
     *
     * @param string $identifier
     * @return File
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Returns IdentifierHash
     *
     * @return string
     */
    public function getIdentifierHash()
    {
        return $this->identifierHash;
    }

    /**
     * Sets IdentifierHash
     *
     * @param string $identifierHash
     * @return File
     */
    public function setIdentifierHash($identifierHash)
    {
        $this->identifierHash = $identifierHash;
        return $this;
    }

    /**
     * Returns Extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Sets Extension
     *
     * @param string $extension
     * @return File
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * Returns MimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Sets MimeType
     *
     * @param string $mimeType
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Returns Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets Name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns ContentHash
     *
     * @return string
     */
    public function getContentHash()
    {
        return $this->contentHash;
    }

    /**
     * Sets ContentHash
     *
     * @param string $contentHash
     * @return File
     */
    public function setContentHash($contentHash)
    {
        $this->contentHash = $contentHash;
        return $this;
    }

    /**
     * Returns Size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets Size
     *
     * @param int $size
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Returns CreationDate
     *
     * @return int
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Sets CreationDate
     *
     * @param int $creationDate
     * @return File
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * Returns ModifiationDate
     *
     * @return int
     */
    public function getModifiationDate()
    {
        return $this->modifiationDate;
    }

    /**
     * Sets ModifiationDate
     *
     * @param int $modifiationDate
     * @return File
     */
    public function setModifiationDate($modifiationDate)
    {
        $this->modifiationDate = $modifiationDate;
        return $this;
    }
}
