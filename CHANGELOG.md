# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2023-02-28

### Added
- Compatibility with JobRouter® 2023.1

### Changed
- On instantiation of the RestClient no authentication is done automatically anymore
- Incident class accepts as a priority only a Priority enum
- Incident class must be instantiated with a step number
- Step number in Incident class is always an integer
- Pool number in Incident class is always an integer
- Simulation in Incident class is always a boolean

### Removed
- Compatibility with PHP < 8.1
- Compatibility with JobRouter® < 2022.1

## [1.4.0] - 2022-08-21

### Added
- Compatibility with JobRouter® 2022.3
- Compatibility with PHP 8.2
- Hide sensitive parameters in back traces for PHP versions >= 8.2

## [1.3.0] - 2022-05-26

### Added
- Configuration of client options (#3)
- Compatibility with JobRouter® 2022.2

## [1.2.0] - 2022-03-05

### Added
- Compatibility with JobRouter® 2022.1

### Removed
- Compatibility with PHP 7.3

## [1.1.2] - 2021-10-30

### Fixed
- Annotation for data argument in request method of decorators

## [1.1.1] - 2021-10-24

### Fixed
- Return type of FileStorage::key()

## [1.1.0] - 2021-10-02

### Added
- Compatibility with PHP 8.0 and 8.1
- Compatibility with JobRouter® 5.2

### Removed
- Compatibility with PHP 7.2

## [1.0.1] - 2020-12-05

### Fixed
- Allow integer and boolean subtable values in IncidentsClientDecorator (#1)

## [1.0.0] - 2020-07-23

First stable release


[Unreleased]: https://github.com/brotkrueml/jobrouter-client/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/brotkrueml/jobrouter-client/compare/v1.4.0...v2.0.0
[1.4.0]: https://github.com/brotkrueml/jobrouter-client/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/brotkrueml/jobrouter-client/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/brotkrueml/jobrouter-client/compare/v1.1.2...v1.2.0
[1.1.2]: https://github.com/brotkrueml/jobrouter-client/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/brotkrueml/jobrouter-client/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/brotkrueml/jobrouter-client/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/brotkrueml/jobrouter-client/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/brotkrueml/jobrouter-client/releases/tag/v1.0.0
