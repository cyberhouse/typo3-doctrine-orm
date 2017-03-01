<?php
namespace Cyberhouse\DoctrineORM\Persistence;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

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
     * DoctrineQueryResult constructor.
     * @param \Doctrine\ORM\Query $query
     * @param bool $useRaw
     */
    public function __construct(Query $query, bool $useRaw)
    {
        $this->query = $query;
        $this->result = $query->getResult($useRaw ? Query::HYDRATE_ARRAY : Query::HYDRATE_OBJECT);
    }

    public function current()
    {
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
        return count($this->result) < $this->pointer;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function offsetExists($offset)
    {
        return isset($this->result[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->result[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->result[$offset]);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFirst()
    {
        return $this->result[0];
    }

    public function toArray()
    {
        return $this->result;
    }

    public function count()
    {
        return count($this->result);
    }
}
