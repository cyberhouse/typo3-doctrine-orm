<?php
namespace Cyberhouse\DoctrineORM\Domain\Model;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * Base for page entites that appear in the frontend
 *
 * @ORM\Entity
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
abstract class PageFrontend extends Page
{
    /**
     * @ORM\Column(name="subtitle")
     * @var string
     */
    private $subtitle = '';

    /**
     * @ORM\Column(name="nav_title")
     * @var string
     */
    private $navigationTitle = '';

    /**
     * @ORM\Column(name="nav_hide")
     * @var bool
     */
    private $hideInMenu = false;

    /**
     * Returns Subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Sets Subtitle
     *
     * @param string $subtitle
     * @return PageFrontend
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * Returns NavigationTitle
     *
     * @return string
     */
    public function getNavigationTitle()
    {
        return $this->navigationTitle;
    }

    /**
     * Sets NavigationTitle
     *
     * @param string $navigationTitle
     * @return PageFrontend
     */
    public function setNavigationTitle($navigationTitle)
    {
        $this->navigationTitle = $navigationTitle;
        return $this;
    }

    /**
     * Returns HideInMenu
     *
     * @return bool
     */
    public function getHideInMenu()
    {
        return $this->hideInMenu;
    }

    /**
     * Sets HideInMenu
     *
     * @param bool $hideInMenu
     * @return PageFrontend
     */
    public function setHideInMenu($hideInMenu)
    {
        $this->hideInMenu = $hideInMenu;
        return $this;
    }
}
