<?php
namespace Cyberhouse\DoctrineORM\Domain\Traits;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Cyberhouse\DoctrineORM\Domain\Model\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * Properties for an entity inside a page.
 *
 * Basically, this uses the well known PID column to map
 * Page entites
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
trait WithinPage
{
    /**
     * @ORM\ManyToOne(targetEntity="Cyberhouse\DoctrineORM\Domain\Model\Page")
     * @ORM\JoinColumn(name="pid", referencedColumnName="uid", fieldName="pid", onDelete="CASCADE")
     * @var Page
     */
    private $parentPage;

    /**
     * Returns ParentPage
     *
     * @return \Cyberhouse\DoctrineORM\Domain\Model\Page
     */
    public function getParentPage()
    {
        return $this->parentPage;
    }

    /**
     * Sets ParentPage
     *
     * @param \Cyberhouse\DoctrineORM\Domain\Model\Page $parentPage
     * @return WithinPage
     */
    public function setParentPage(Page $parentPage)
    {
        $this->parentPage = $parentPage;
        return $this;
    }
}
