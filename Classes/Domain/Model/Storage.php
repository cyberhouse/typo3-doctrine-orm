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
     * @ORM\Column(name="tstamp", type="int")
     * @var int
     */
    private $lastModified = 0;

    /**
     * @ORM\Column(name="crdate", type="int")
     * @var int
     */
    private $createdDate = 0;

    /**
     * @ORM\Column(name="tstamp", type="int")
     * @var int
     */
    private $createdBy = 0;

    /**
     * @ORM\Column(name="deleted", type="bool")
     * @var bool
     */
    private $deleted = false;

    /**
     * @ORM\Column(name="deleted", type="string")
     * @var bool
     */
    private $name = '';

    /**
     * @ORM\Column(name="deleted", type="text")
     * @var bool
     */
    private $description = '';
}
