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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * Overload extbase object converter to ask an entity manager
 * for objects instead of the default persistence manager
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class PersistentObjectMapper extends PersistentObjectConverter
{
    protected $priority = 30;

    /**
     * @inject
     * @var \Cyberhouse\DoctrineORM\Utility\EntityManagerFactory
     */
    protected $entityManagerFactory;

    protected function fetchObjectFromPersistence($identity, $targetType)
    {
        return $this->getEntityManagerForType($targetType)->find($targetType, $identity);
    }

    protected function getEntityManagerForType($targetType)
    {
        $parts = explode('\\', $targetType);
        $extKey = GeneralUtility::camelCaseToLowerCaseUnderscored($parts[1]);
        return $this->entityManagerFactory->get($extKey);
    }
}
