.. include:: Includes.txt

.. _setup-usage:

=======================
Models and Repositories
=======================

Configuration of entities is done using the `AnnotationDriver`_ of Doctrine. For better compatiblity and readability, the SimpleAnnotationDriver is not used, so the namespace must be imported via a ``use`` statement.

.. _AnnotationDriver: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html

Entities / Domain Models
========================

When creating a model, extend the class ``Cyberhouse\DoctrineORM\Domain\Model\AbstractDoctrineEntity``. The recommendations and best practices of naming tables in TYPO3 extensions apply as well.

A simple ``Task`` model of a Todo list could look like this:

.. code-block:: php

   namespace Vendor\Todolist\Domain\Model;

   use Cyberhouse\DoctrineORM\Domain\Model\AbstractDoctrineEntity;
   use Doctrine\ORM\Mapping as ORM;

   /**
    * A task entity
    *
    * @ORM\Entity
    * @ORM\Table(name="tx_todolist_task")
    */
    class Task extends AbstractDoctrineEntity {
        /**
         * @ORM\Column(type="string")
         * @var string
         */
        private $name;
    }

All data types supported by Doctrine may be used. If the model contains a TCA declaration and can be edited via TCEforms in the TYPO3 backend, the annotation ``@ORM\Column(columnDefinition="")`` might be useful to ensure a field configuration that is compatible with the FormEngine.

Repositories
============

Repositiories can be used and added like Extbase repositories. The repository class for the previous ``Task`` example simply looks like this:

.. code-block:: php

   namespace Vendor\Todolist\Domain\Repository;

   use Cyberhouse\DoctrineORM\Domain\Repository\AbstractDoctrineRepository;

   class TaskRepository extends AbstractDoctrineRepository
   {
   }

This class can then be injected into a controller like a common Extbase repository.

.. warning::
   Do NOT use the annotation ``@ORM\Entity(repositoryClass="TaskRepository")`` with the ``AbstractDoctrineRepository``. These are not compatible. The DoctrineRepository is mainly a wrapper around an EntityManager instance which is the other way around Doctrine usually handles repositories.

Query Objects
=============

There are three ways to query objects via a doctrine repository:

1. :ref:`query-extbase`
2. :ref:`query-orm`
3. :ref:`query-dql`

.. _query-extbase:

Extbase QOM objects
-------------------

These work exactly like their extbase counter parts of TYPO3. Use the ``createQuery`` method 
to create one and add constraints.

.. code-block:: php

   $query = $taskRepository->createQuery();
   $query->matching($query->like('title', '%important%'));
   $result = $query->execute();

Because of the very different interfaces of Doctrine ORM and extbase in this regard, there are several important 
differences to the standard query API that need to be considered:

- The ``logicalNot`` function does not work. Use the replacement ``not`` instead. The former requires 
  a ``ConstraintInterface`` object as an argument that is not compatible with Doctrine ORM expressions.
- Query settings are not supported
- Method ``getSource`` will return the underlying ``QueryBuilder`` instead of a ``SourceInterface``.
- Previously set orderings can't be retrieved with ``getOrderings``. If this information is required, it must be 
  kept somewhere else because the QueryBuilder does not return this information.
- The ``getStatement`` method will return a DQL query.

.. _query-orm:

The Doctrine query builder
--------------------------

Use the repository method ``createOrmQuery`` to get a query builder of Doctine ORM itself. Its interface 
is almost identical to the QueryBuilder of the Doctrine DBAL query builder, which is already part of the TYPO3 core.

The main difference is, that it works on entities rather than tables. 
Please see the `Doctrine documentation`_ for details.

.. _Doctrine documentation: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html#working-with-querybuilder

.. code-block:: php

   $query = $taskRepository->createOrmQuery();
   $query->select('t')
      ->from(Task::class, 't')
      ->where($query->expr()->like('t.title', '%important%'))
      ->getResult();

.. _query-dql:

DQL queries
-----------

Doctrine ORM has its own query language: *Doctrine Query Language* or *DQL*. Use the repository 
method ``createDqlQuery`` to fetch objects via DQL:

.. code-block:: php

   $query = $taskRepository->createDqlQuery('SELECT t FROM Task t WHERE t.title LIKE \'%important%\'');
   $tasks = $query->getResult();

Please see the `DQL documentation`_ for details.

.. _DQL documentation: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html#select-queries
