# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [2.5.0](https://github.com/sonata-project/SonataEasyExtendsBundle/compare/2.4.0...2.5.0) - 2018-02-11
### Added
- Added  `DoctrineCollector::clear` to clear properties of class.

## [2.4.0](https://github.com/sonata-project/SonataEasyExtendsBundle/compare/2.3.1...2.4.0) - 2018-01-20
### Added
- Added new `namespace_prefix` option to generate command

### Fixed
- commands are now usable with Symfony 3.2 - 3.3

## [2.3.1](https://github.com/sonata-project/SonataEasyExtendsBundle/compare/2.3.0...2.3.1) - 2018-01-16
### Fixed
- Register commands explicitly in `commands.xml`
- Generator services are now public

## [2.3.0](https://github.com/sonata-project/SonataEasyExtendsBundle/compare/2.2.0...2.3.0) - 2017-11-30
### Changed
- Changed internal folder structure to `src`, `tests` and `docs`

### Fixed
- It is now allowed to install Symfony 4

### Removed
- Support for old versions of PHP and Symfony.

## [2.2.0](https://github.com/sonata-project/SonataEasyExtendsBundle/compare/2.1.10...2.2.0) - 2017-04-25
### Added
- Added optional `--namespace` option to `GenerateCommand`
- Added new functionality to add ORM overrides
