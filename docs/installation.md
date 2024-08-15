# Installation

## Requirements

The JobRouter REST Client in the recent version requires at least PHP 8.1;
using the latest version of PHP is highly recommended.

The library requires the `curl` and `filter` PHP extensions, which are normally
enabled by default.

### Version matrix

| JobRouter REST Client | JobRouter®                  | PHP       |
|-----------------------|-----------------------------|-----------|
| 3.1                   | 2022.1 - 2024.3             | 8.1 - 8.3 |
| 3.0                   | 2022.1 - 2024.3             | 8.1 - 8.3 |
| 2.0                   | 2022.1 - 2024.2             | 8.1 - 8.3 |
| 1.4                   | 4.2 - 5.2 / 2022.1 - 2023.2 | 7.4 - 8.2 |
| 1.2 / 1.3             | 4.2 - 5.2 / 2022.1 - 2022.2 | 7.4 - 8.1 |
| 1.1                   | 4.2 - 5.2                   | 7.3 - 8.1 |
| 1.0                   | 4.2 - 5.1                   | 7.2 - 7.4 |

You can use, for example, JobRouter REST Client version 2.0 on JobRouter® version
5.2 at your own risk. However, new REST API resources may not be usable.


## Composer-based installation

Add a dependency on `jobrouter/rest-client` to your project's
`composer.json` file, if you use [Composer](https://getcomposer.org/) to
manage the dependencies of your project:

```shell
composer require jobrouter/rest-client
```

**Note:** The JobRouter REST Client *before* version 3.0 uses another dependency:

```shell
composer require jobrouter/rest-client
```

This is the preferred way: You can track new releases of the JobRouter REST Client
and the underlying libraries and update them yourself independently.


## Manual Installation

Download the recent version of the JobRouter REST Client from GitHub:

https://github.com/jobrouter/rest-client/releases

Expand `Assets` and select the appropriate package (`zip`, `tar.gz`).
It is advised to check the integrity of the package:

### Linux

```shell
sha256sum -c jobrouter-rest-client-<version>.tar.gz.sha256.txt
```

It should output:

```text
jobrouter-rest-client-<version>.tar.gz: OK
```


### Windows

Windows is shipped with the
[certutil](https://docs.microsoft.com/en-us/windows-server/administration/windows-commands/certutil)
program. You can check the hash of the file with:

```shell
CertUtil -hashfile jobrouter-rest-client-<version>.zip sha256
```

and compare it with the hash value in the corresponding
`.sha256.txt` file.

**Important:**
With the manual installation, the underlying libraries are only updated on new
releases of the JobRouter REST Client.
