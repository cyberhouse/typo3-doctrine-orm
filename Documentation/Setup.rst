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

   config.tx_extbase {
     objects {
       TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface {
         className = Cyberhouse\DoctrineORM\Persistence\DoctrinePersistenceManager
       }
       # The Bootstrap class for extbase plugins asks for this class 
       # explicitly, so we need to set an implementation for an implementation
       # That's basically an "extbase - XClass"
       TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager {
         className = Cyberhouse\DoctrineORM\Persistence\DoctrinePersistenceManager
       }
     }
   }

For extensions not registering at the ExtensionRegistry, every call to an interface function is passed through to the core PersistenceManager
