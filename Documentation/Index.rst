.. include:: Includes.txt

=============================================================
Doctrine ORM for TYPO3
=============================================================
.. only:: html

	:Classification:
    		orm

    	:Version:
    		|release|

	:Language:
    		en

    	:Keywords:
    		orm, doctrine

    	:Copyright:
    		2017

    	:Author:
    		Georg Großberger

    	:License:
    		This document is published under the Open Content License
    		available from http://www.opencontent.org/opl.shtml

	:Rendered:
    		|today|

	The content of this document is related to TYPO3,
	a GNU/GPL CMS/Framework available from `www.typo3.org <http://www.typo3.org/>`_.


	**Table of Contents**

.. toctree::
    :hidden:

    Setup
    Usage
    Integrations

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

1. :ref:`setup-registry` and :ref:`setup-typoscript` are the main steps to enable this package.
2. :ref:`setup-usage` describes how to use it for a Doctrine ORM based domain model.

Development
-----------

This extension is developed and maintained publicly on `github.com/cyberhouse/typo3-doctrine-orm`_

.. _github.com/cyberhouse/typo3-doctrine-orm: https://github.com/cyberhouse/typo3-doctrine-orm

License
-------

This package is licensed under the GPL v3. Please see the file `LICENSE` or the official `FSF GPL Website`_.

.. _FSF GPL Website: https://www.gnu.org/licenses/gpl-3.0.html
.. _LICENSE: LICENSE

