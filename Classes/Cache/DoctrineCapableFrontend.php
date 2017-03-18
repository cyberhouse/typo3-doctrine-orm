<?php
namespace Cyberhouse\DoctrineORM\Cache;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

use Doctrine\Common\Cache\Cache;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

/**
 * A TYPO3 cache frontend that can be used by doctrine
 *
 * It simply extends the VariableFrontend with the interface
 * required by Doctrine, the function is the same
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class DoctrineCapableFrontend extends VariableFrontend implements Cache
{

    /**
     * Fetch / get a cache entry
     *
     * @param string $id
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->get($this->fixKey($id));
    }

    /**
     * Check if an entry is available
     *
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        return $this->has($this->fixKey($id));
    }

    /**
     * Save / set a cache entry
     *
     * lifetime is the TTL in seconds
     *
     * @param string $id
     * @param mixed $data
     * @param int $lifeTime
     * @return bool|void
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->set($this->fixKey($id), $data, [], $lifeTime);
    }

    /**
     * Remove / delete a cache entry
     *
     * @param string $id
     * @return bool|void
     */
    public function delete($id)
    {
        $this->remove($this->fixKey($id));
    }

    /**
     * The TYPO3 cache API does not support statistics so
     * we always return null, ignoring the actual backend
     * might be able to get some
     *
     * @return null
     */
    public function getStats()
    {
        return null;
    }

    /**
     * Ensure a string that is a valid TYPO3 cache key
     *
     * @param string $key
     * @return string
     */
    private function fixKey($key)
    {
        $fixed = preg_replace('/[^a-zA-Z0-9_%\\-&]+/', '-', $key);

        if (strlen($fixed) > 250) {
            return substr($fixed, 0, 250);
        }

        return $fixed;
    }
}
