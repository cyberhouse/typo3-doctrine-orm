Doctrine ORM for TYPO3
======================

Extension doctrine_orm integrates the well known `Doctrine ORM`_ using the `TYPO3 Doctrine DBAL API`_ exposed by TYPO3 8. It does not offer the full feature set of either extbase or Doctrine, simply because the conceptual differences are too big for a 100% seamless integration.

It's main goal is to provide a set of APIs to use Doctrine managed entites within extbase plugins.

.. _Doctrine ORM: http://doctrine-project.org/projects/orm.html
.. _TYPO3 Doctrine DBAL API: https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Database/Index.html

Installation
------------

Install via the TER or use composer: ``composer require cyberhouse/typo3-doctrine-orm``.

Usage
-----

Using this package requires two steps:

1. Register your extension model classes path and configure extbase two use alternate implementations for various objects.
   This is explained in `Setup`_
2. Create a domain model with the classes of this package as a base instead the default once.
   Details and examples can be found in `Modelling`_

.. _Setup: Setup
.. _Modelling: Modelling

License
-------

This package is licensed under the GPL v3. Please see the file `License.rst`_ or the official `FSF GPL Website`_.

.. _License.rst: :doc: Documentation/License.rst
.. _FSF GPL Website: https://www.gnu.org/licenses/gpl-3.0.html
