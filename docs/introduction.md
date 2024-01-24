# Introduction

[JobRouter®](https://www.jobrouter.com/) is a scalable digitalisation platform
which links processes, data and documents. This JobRouter REST Client eases the
access to the REST API.

The library can be used to automate tasks in PHP scripts like importing or
synchronising data in the JobData module, start processes or working with
archive documents. The authentication is done in the background, so you
concentrate on your business domain.

The [PSR-7 standard](https://www.php-fig.org/psr/psr-7/) is used, so you'll
get in touch with objects which implements the `ResponseInterface`.
Currently, the JobRouter REST Client uses
[nyholm/psr7](https://github.com/nyholm/psr7>) for the PSR-7 implementation
and [Buzz](https://github.com/kriswallsmith/buzz>) as HTTP client. But that
shouldn't bother you.

The library is available under the
[GNU General Public License v2.0](https://github.com/jobrouter/php-rest-client/blob/main/LICENSE.txt).
You can also have a look into the
[source code](https://github.com/jobrouter/php-rest-client) on GitHub or
[file an issue](https://github.com/jobrouter/php-rest-client/issues).


## Release Management

This library uses [semantic versioning](https://semver.org/) which basically
means for you, that

- Bugfix updates (e.g. `1.0.0` => `1.0.1`) just includes small bug fixes or
  security relevant stuff without breaking changes.
- Minor updates (e.g. `1.0.0` => `1.1.0`) includes new features and smaller
  tasks without breaking changes.
- Major updates (e.g. `1.0.0` => `2.0.0`) includes breaking changes which can be
  refactorings, features or bug fixes.

The changes are recorded in the
[changelog](https://github.com/jobrouter/php-rest-client/blob/main/CHANGELOG.md).
