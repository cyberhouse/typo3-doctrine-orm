.. include:: Includes.txt

====================
Further integrations
====================

For an easier workflow, doctrine_orm integrates the following Doctrine ORM functions into the TYPO3 core.

Migrations
==========

There are two entry points for migrating the database schema to the current domain model

Install Tool
------------

doctrine_orm injects all ``CREATE TABLE`` statements of the entites into the result of the SqlReader, used by the install tool and others, like the `typo3-console`_ package.

.. _typo3-console: https://github.com/TYPO3-Console/typo3_console

While this is convinient and sufficient in most cases, there are two limitations that must be kept in mind:

1. Doctrine ORM creates FOREIGN KEY constraints. The way these are defined is, currently at least, not supported by TYPO3. So they are left out.
2. The schema of TYPO3 and other extensions is merged with the one of the Doctrine domain model. For better compatibility, the former take precedence. So if a domain model uses a TYPO3 table (like *pages*), the definition in the ext_tables.sql file is used.

CLI Commands
------------

There are several commands available for (automated) migrations. They all use the new CLI of TYPO3 8, so they must be invoked with:

.. code-block:: bash

   ./typo3/sysext/core/bin/typo3 command -options

+------------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Command          | Description                                                                                                                                                                                                               |
+------------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| doctrine:migrate | This command migrates the Doctrine ORM schema to its currently needed layout. Unlike the install tool integrations, this commands ignores all ext_tables.sql statements and migrates the schema of the domain model only. |
+------------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| doctrine:fks     | Migrate FOREIGN KEY constraints only. This is useful if the fields and tables themselves have been migrated via core functionalities but foreign key constraints are needed by the domain model.                          |
+------------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| doctrine:showsql | Pretty print the CREATE TABLE statements of the domain model to stdout. This is useful for generating ext_tables.sql files for a core based schema migration.                                                             |
+------------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| doctrine:proxies | Generate the proxy classes for the domain model. Running this command is required if TYPO3 does not run in Development context                                                                                            |
+------------------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

All four commands know two options:

+-------------+-------+-----------------------------------------------------------+
| Switch      | Short | Description                                               |
+-------------+-------+-----------------------------------------------------------+
| --extension | -e    | Limit the actions to the given extension                  |
+-------------+-------+-----------------------------------------------------------+
| --dry-run   | -d    | Do not execute any changes, just print what would be done |
+-------------+-------+-----------------------------------------------------------+

Example: Generate an ext_tables.sql file for the previous *todolist* extension:

.. code-block:: bash

   ./typo3/sysext/core/bin/typo3 doctrine:showsql -e todolist > typo3conf/ext/todolist/ext_tables.sql

Other
=====

doctrine_orm integrates two more settings:

Application Context
-------------------

The Doctrine ORM configuration factory has the so called ``devMode`` - switch. doctrine_orm automatically enables it if the TYPO3 context is not Development. This means:

- In Development context, regardless of a possible sub-context, all caching is done in-memory, runtime only, using an ArrayCache and proxy classes are generated on demand.
- In any other context, proxies must be present before running the code, and cached information is actually persistent.

Caching
-------

TYPO3 and Doctrine ORM use similar caching interfaces, but not the same. doctrine_orm registers a TYPO3 cache, that belongs to the group ``system`` and uses the frontend ``DoctrineCapableFrontend``. This cache is then passed to every entity manager created. So when clearing "All Caches", the Doctrine caches are cleared as well. The backend can be configured using any TYPO3 cache backend in the cacheConfiguration settings of ``doctrine_orm``

Example: Let doctrine use a redis Backend:

.. code-block:: php

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['doctrine_orm']['backend'] =
        \TYPO3\CMS\Core\Cache\Backend\RedisBackend::class
