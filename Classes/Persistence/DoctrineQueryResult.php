<?php
namespace Cyberhouse\DoctrineORM\Persistence;

/*
 * This file is (c) 2018 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Result of a Doctrine query
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineQueryResult implements QueryResultInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var array
     */
    private $result;

    /**
     * @var int
     */
    private $pointer = 0;

    /**
     * @var int
     */
    private $hydrationMode = 0;

    /**
     * DoctrineQueryResult constructor.
     * @param AbstractQuery $query
     * @param bool $useRaw
     */
    public function __construct(AbstractQuery $query, bool $useRaw)
    {
        $this->query = $query;
        $this->hydrationMode = $useRaw ? AbstractQuery::HYDRATE_ARRAY : AbstractQuery::HYDRATE_OBJECT;
    }

    public function current()
    {
        $this->init();
        return $this->result[$this->pointer];
    }

    public function next()
    {
        $this->pointer++;
    }

    public function key()
    {
        return $this->pointer;
    }

    public function valid()
    {
        $this->init();
        return count($this->result) > $this->pointer;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function offsetExists($offset)
    {
        $this->init();
        return isset($this->result[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->init();
        return $this->result[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->init();
        $this->result[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $this->init();
        unset($this->result[$offset]);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFirst()
    {
        $this->init();
        return $this->result[0];
    }

    public function toArray()
    {
        $this->init();
        return $this->result;
    }

    public function count()
    {
        $this->init();
        return count($this->result);
    }

    protected function init()
    {
        if (!is_array($this->result)) {
            $this->result = $this->query->getResult($this->hydrationMode);
        }
    }
}
