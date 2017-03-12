.. include:: Includes.txt

=======================
Models and Repositories
=======================

Configuration of entities is done using the `AnnotationDriver`_ of Doctrine. For better compatiblity and readability, the SimpleAnnotationDriver is not used, so the namespace must be imported via a ``use`` statement.

.. _AnnotationDriver: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html

Entities / Domain Models
========================

When creating a mode, extend the class ``Cyberhouse\DoctrineORM\Domain\Model\AbstractDoctrineEntity``. The recommendations and best practices of naming tables in TYPO3 extensions apply as well.

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

All data types supported by Doctrine may be used. If the model contains a TCA declaration and can be edited via TCEforms in the TYPO3 backend, the annotation ``@ORM\Column(columnDefinition="")`` might be required to ensure a field configuration that is compatible with the FormEngine.

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
