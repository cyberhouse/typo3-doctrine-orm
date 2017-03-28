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

use Doctrine\Common\Collections\ArrayCollection;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\ObjectStorageConverter;

/**
 * Overload the storage converter to use Doctrine collections
 * instead of extbase ObjectStorage objects
 *
 * @author Georg Großberger <georg.grossberger@cyberhouse.at>
 */
class ObjectStorageMapper extends ObjectStorageConverter
{
    protected $targetType = ArrayCollection::class;

    public function convertFrom(
        $source,
        $targetType,
        array $convertedChildProperties = [],
        PropertyMappingConfigurationInterface $configuration = null
    ) {
        $objectStorage = new ArrayCollection();

        foreach ($convertedChildProperties as $subProperty) {
            $objectStorage->add($subProperty);
        }

        return $objectStorage;
    }
}
