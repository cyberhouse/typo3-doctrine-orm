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
 * @ORM\Entity
 * @ORM\Table(name="pages")
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
}
