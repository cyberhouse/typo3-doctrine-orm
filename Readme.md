# Doctrine ORM for TYPO3

Extension doctrine_orm integrates the well known [Doctrine ORM](http://doctrine-project.org/projects/orm.html) 
using the [TYPO3 Doctrine DBAL API](https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Database/Index.html) 
exposed by TYPO3 8. It does not offer the full feature set of either extbase or Doctrine, simply because 
the conceptual differences are too big for a 100% seamless integration.

It's main goal is to provide a set of APIs to use Doctrine managed entites within extbase plugins.

## Installation

Install via the TER or use composer: `composer require cyberhouse/typo3-doctrine-orm`

## Usage

Using this package requires two steps:

1. Register and setup some Typoscript, as described in <Documentation/Setup.rst>
2. Create Doctrine ORM models with some changes applied <Documentation/Usage.rst>

## Development

This extension is developed and maintained publicly on <https://github.com/cyberhouse/typo3-doctrine-orm>

## License

This package is licensed under the GPL v3. Please see the file `LICENSE` or 
the official [FSF GPL Website](FSF GPL Website: https://www.gnu.org/licenses/gpl-3.0.html)
