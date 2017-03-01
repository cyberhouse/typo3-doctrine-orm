=====
Setup
=====

Using this extension requires two steps of configuration:

1. Register the models of your extension
2. Configure alternate interface implementations for extbase


Configure extension
===================

This is mainly one call: the method ``register`` of the singleton ``Cyberhouse\DoctrineORM\Utility\ExtensionRegistry``. This should be done in the file ``ext_localconf.php`` like this:

.. code-block:: php

   $registry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Cyberhouse\DoctrineORM\Utility\ExtensionRegistry::class);
   $registry->register($_EXTKEY);

The path to the models defaults to the extbase standard of ``EXT:EXT_KEY/Classes/Domain/Model``. You can set several custom paths instead. If one path is present, the default path will not be added by the registry, so, if necessary, it must be set manually.

.. code-block:: php

   $paths = [
       '/my/entity/library/src', // Some external source
       'EXT:other_ext/Classes/Entities', // Entities of another extension
       'EXT:' . $_EXTKEY . '/Classes/Domain/Model', // Entites in extbase like namespace
   ];
   $registry->register($_EXTKEY, ...$paths); // $path is variadic, so use unpacking for an array of paths

In any case, the path ``EXT:doctrine_orm/Classes/Domain/Model`` is added, which provides several doctrine entites for TYPO3 core tables, like pages and files.

Configure Plugin / Module
=========================

Extbase uses implementations of several interfaces to provide persistence management within an MVC request. It comes with default implementations that use the TYPO3 core options (TCA and TypoScript) to configure models and storing them.

When using Doctrine ORM, we need alternate implementations, so add the following lines to your plugins TypoScript:

.. code-block:: typoscript

   plugin.tx_myext {
       settings {} # The well known settings array
       objects {
           Cyberhouse\DoctrineORM\Persistence\DoctrinePersistenceManager {
               implements = TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
           }
           Cyberhouse\DoctrineORM\Persistence\DoctrineQuery {
               implements = TYPO3\CMS\Extbase\Persistence\QueryInterface
           }
           Cyberhouse\DoctrineORM\Persistence\DoctrineQueryResult {
               implements = TYPO3\CMS\Extbase\Persistence\QueryResultInterface
           }
       }
   }

For a backend module use ``module.`` instead

.. code-block:: typoscript

   module.tx_myext.objects {
      # Same options as above
   }
   # If you already have a plugin configured, simply copy its options
   module.tx_myext.objects < plugin.tx_myext.objects

.. important::
   The ``objects.`` directives are more commonly known in the context of ``config.tx_extbase.``. Do not set them
   there as this would break all plugins and modules that need the core functionality.
