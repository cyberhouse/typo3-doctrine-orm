<?php
namespace Cyberhouse\DoctrineORM\Database;

/*
 * This file is (c) 2017 by Cyberhouse GmbH
 *
 * It is free software; you can redistribute it and/or
 * modify it under the terms of the GPLv3 license
 *
 * For the full copyright and license information see
 * <https://www.gnu.org/licenses/gpl-3.0.html>
 */

/**
 * Helper to remove quotes around identifiers
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class IdentifierQuotes
{
    public function remove($identifier)
    {
        return trim(trim(trim($identifier), '`'));
    }

    public function has($identifier)
    {
        return strpos($identifier, '`') !== false;
    }
}
