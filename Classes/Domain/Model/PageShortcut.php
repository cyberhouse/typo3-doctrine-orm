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
 * A shortcut page
 *
 * @ORM\Entity
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class PageShortcut extends PageFrontend
{
    /**
     * @ORM\Column(name="shortcut_mode", type="smallint")
     * @var int
     */
    private $mode;

    /**
     * @ORM\OneToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="shortcut", referencedColumnName="uid", onDelete="SET NULL")
     * @var Page
     */
    private $shortcut;
}
