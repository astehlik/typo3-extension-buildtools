# TYPO3 Extension build tools

Test your TYPO3 Extensions with the utilities provided by the TYPO3 core using:

* the `typo3/testing-framework` package
* `docker` and `docker-compose`
* the `typo3gmbh/php*` docker images

**Hint 1:** currently each version of the tools only supports **one** TYPO3 core version.

**Hint 2:** currently the Composer dependencies must be installed in the `.Build` directory. See
[doc/composer-sample.json](doc/composer-sample.json) for the needed configuration.

## Install

### Edit `composer.json`

Adjust the settings in your `composer.json` file as described in [doc/composer-sample.json](doc/composer-sample.json).

After that run `composer update`.

### Require dynamically

If you want to test multiple core versions you can require different versions of the buildtools
dynamically during your build process.

For TYPO3 10: 

```bash
composer require --dev de-swebhosting/typo3-extension-buildtools:^10.0
```

For TYPO3 9 (not yet available!):

```bash
composer require --dev de-swebhosting/typo3-extension-buildtools:^9.0
```

## Run locally

Before you can execute a script locally, you need to set your Extension key as an environment variable:

```bash
export TYPO3_EXTENSION_KEY="<my_extension_key>"
```

After that you can run the different commands.

t3_check_codestyle.sh
t3_deploy_to_ter.sh
t3_prepare_release.sh
t3_run_tests.sh

## Run in travis

Have a look at the [doc/travis-sample.yml](doc/travis-sample.yml) file. You need to

1. replace `<your_extension_key>` with the Extension key of your extension
2. remove `[your_custom_codestyle]` or replace it with the name of the ruleset you want to use
   for the code style checker (see [Code style checking](#code-style-checking)).
   
The Example Travis CI config will

* validate the `composer.json` file
* check the code style of your PHP code
* run Unit Tests for PHP 7.2 and 7.3
* run functional Tests for PHP 7.2 and 7.3
* run PHP linting for PHP 7.2 and 7.3
* run acceptance tests for PHP 7.2 and 7.2

It will also try to deploy your Extension to TER when a tag is pushed. You need to set your TYPO3 login data
in Travis environment variables for this to work (see [Deploy to TER](#deploy-to-ter)).

* `TYPO3_ORG_USERNAME`
* `TYPO3_ORG_PASSWORD` 

## Write tests

### Unit test

To write a unit test create the folder `Tests/Unit` in your Extension  and add your first
test case by extending `TYPO3\TestingFramework\Core\Unit\UnitTestCase`:

```php
namespace Vendor\MyExt\Tests\Unit;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class MyFirstUnitTest extends UnitTestCase {
...
}
```

### Functional test

To write a functional test create the folder `Tests/Functional` in your Extension  and add your first functional
test case by extending `TYPO3\TestingFramework\Core\Functional\FunctionalTestCase`:

```php
namespace Vendor\MyExt\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class MyFirstFunctionalTest extends FunctionalTestCase {
...
}
```

### Acceptance test

## Code style checking

## Deploy to TER

## Credits

This work is based on the TYPO3 testing framework and the awesome documentation at
https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/Testing/ExtensionTesting.html
