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
 * Basic model of a page
 *
 * @ORM\DiscriminatorColumn(name="doktype", type="integer")
 * @ORM\DiscriminatorMap({
 *     1   = "PageDefault",
 *     3   = "PageLink",
 *     4   = "PageShortcut",
 *     6   = "PageBackendUserSection",
 *     7   = "PageMountpoint",
 *     199 = "PageSpacer",
 *     254 = "PageSysFolder",
 *     255 = "PageRecycler"
 * })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Entity
 * @ORM\Table(name="pages", indexes={
 *     @ORM\Index(name="parent", columns={"pid", "deleted", "sorting"}),
 *     @ORM\Index(name="t3ver_oid", columns={"t3ver_oid", "t3ver_wsid"}),
 *     @ORM\Index(name="alias", columns={"alias"}),
 *     @ORM\Index(name="determineSiteRoot", columns={"is_siteroot"}),
 * })
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
abstract class Page extends AbstractDoctrineEntity
{
    /**
     * @ORM\Column(name="title")
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="t3ver_oid", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionOverlayUid = 0;

    /**
     * @ORM\Column(name="t3ver_id", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionUid = 0;

    /**
     * @ORM\Column(name="t3ver_wsid", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionWorkspaceUid = 0;

    /**
     * @ORM\Column(name="t3ver_state", type="smallint", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionState = 0;

    /**
     * @ORM\Column(name="t3ver_stage", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionStage = 0;

    /**
     * @ORM\Column(name="t3ver_label", type="string", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var string
     */
    private $versionLabel = '';

    /**
     * @ORM\Column(name="t3ver_count", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionCount = 0;

    /**
     * @ORM\Column(name="t3ver_tstamp", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionLastModified = 0;

    /**
     * @ORM\Column(name="t3ver_move_id", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionMoveUid = 0;

    /**
     * @ORM\Column(name="t3_origuid", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $versionOriginalUid = 0;

    /**
     * @ORM\Column(name="tstamp", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $lastModified = 0;

    /**
     * @ORM\Column(name="sorting", type="integer", columnDefinition="int(11) unsigned DEFAULT 0 NOT NULL")
     * @var int
     */
    private $sorting = 0;

    /**
     * @ORM\Column(name="deleted", type="boolean")
     * @var bool
     */
    private $deleted = false;

    /**
     * @ORM\Column(name="hidden", type="boolean")
     * @var bool
     */
    private $hidden = false;

    /**
     * @ORM\Column(name="alias")
     * @var string
     */
    private $alias = '';

    /**
     * @ORM\Column(name="is_siteroot", type="boolean")
     * @var bool
     */
    private $siteroot = false;

    /**
     * Returns Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets Title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns VersionOverlayUid
     *
     * @return int
     */
    public function getVersionOverlayUid()
    {
        return $this->versionOverlayUid;
    }

    /**
     * Sets VersionOverlayUid
     *
     * @param int $versionOverlayUid
     * @return Page
     */
    public function setVersionOverlayUid($versionOverlayUid)
    {
        $this->versionOverlayUid = $versionOverlayUid;
        return $this;
    }

    /**
     * Returns VersionUid
     *
     * @return int
     */
    public function getVersionUid()
    {
        return $this->versionUid;
    }

    /**
     * Sets VersionUid
     *
     * @param int $versionUid
     * @return Page
     */
    public function setVersionUid($versionUid)
    {
        $this->versionUid = $versionUid;
        return $this;
    }

    /**
     * Returns VersionWorkspaceUid
     *
     * @return int
     */
    public function getVersionWorkspaceUid()
    {
        return $this->versionWorkspaceUid;
    }

    /**
     * Sets VersionWorkspaceUid
     *
     * @param int $versionWorkspaceUid
     * @return Page
     */
    public function setVersionWorkspaceUid($versionWorkspaceUid)
    {
        $this->versionWorkspaceUid = $versionWorkspaceUid;
        return $this;
    }

    /**
     * Returns VersionState
     *
     * @return int
     */
    public function getVersionState()
    {
        return $this->versionState;
    }

    /**
     * Sets VersionState
     *
     * @param int $versionState
     * @return Page
     */
    public function setVersionState($versionState)
    {
        $this->versionState = $versionState;
        return $this;
    }

    /**
     * Returns VersionStage
     *
     * @return int
     */
    public function getVersionStage()
    {
        return $this->versionStage;
    }

    /**
     * Sets VersionStage
     *
     * @param int $versionStage
     * @return Page
     */
    public function setVersionStage($versionStage)
    {
        $this->versionStage = $versionStage;
        return $this;
    }

    /**
     * Returns VersionLabel
     *
     * @return string
     */
    public function getVersionLabel()
    {
        return $this->versionLabel;
    }

    /**
     * Sets VersionLabel
     *
     * @param string $versionLabel
     * @return Page
     */
    public function setVersionLabel($versionLabel)
    {
        $this->versionLabel = $versionLabel;
        return $this;
    }

    /**
     * Returns VersionCount
     *
     * @return int
     */
    public function getVersionCount()
    {
        return $this->versionCount;
    }

    /**
     * Sets VersionCount
     *
     * @param int $versionCount
     * @return Page
     */
    public function setVersionCount($versionCount)
    {
        $this->versionCount = $versionCount;
        return $this;
    }

    /**
     * Returns VersionLastModified
     *
     * @return int
     */
    public function getVersionLastModified()
    {
        return $this->versionLastModified;
    }

    /**
     * Sets VersionLastModified
     *
     * @param int $versionLastModified
     * @return Page
     */
    public function setVersionLastModified($versionLastModified)
    {
        $this->versionLastModified = $versionLastModified;
        return $this;
    }

    /**
     * Returns VersionMoveUid
     *
     * @return int
     */
    public function getVersionMoveUid()
    {
        return $this->versionMoveUid;
    }

    /**
     * Sets VersionMoveUid
     *
     * @param int $versionMoveUid
     * @return Page
     */
    public function setVersionMoveUid($versionMoveUid)
    {
        $this->versionMoveUid = $versionMoveUid;
        return $this;
    }

    /**
     * Returns VersionOriginalUid
     *
     * @return int
     */
    public function getVersionOriginalUid()
    {
        return $this->versionOriginalUid;
    }

    /**
     * Sets VersionOriginalUid
     *
     * @param int $versionOriginalUid
     * @return Page
     */
    public function setVersionOriginalUid($versionOriginalUid)
    {
        $this->versionOriginalUid = $versionOriginalUid;
        return $this;
    }

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
     * @return Page
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * Returns Sorting
     *
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Sets Sorting
     *
     * @param int $sorting
     * @return Page
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
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
     * @return Page
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * Returns Hidden
     *
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets Hidden
     *
     * @param bool $hidden
     * @return Page
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Returns Alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Sets Alias
     *
     * @param string $alias
     * @return Page
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Returns Siteroot
     *
     * @return bool
     */
    public function getSiteroot()
    {
        return $this->siteroot;
    }

    /**
     * Sets Siteroot
     *
     * @param bool $siteroot
     * @return Page
     */
    public function setSiteroot($siteroot)
    {
        $this->siteroot = $siteroot;
        return $this;
    }
}
