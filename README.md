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
composer require --dev de-swebhosting/typo3-extension-buildtools:dev-master
```

For TYPO3 9:

```bash
composer require --dev de-swebhosting/typo3-extension-buildtools:dev-TYPO3_9
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

## Acceptance testing

### Frontend

You can find some boilerplate code for acceptance testing in the [doc/acceptance](doc/acceptance) folder.

Copy the [codeception.yml](doc/acceptance/codeception.yml) file into your Extension's `Tests` folder and

adjust the configured namespace.

The [frontend](doc/acceptance/frontend) subfolder contains a skeletton for creating a Frontend test suite.

Copy all files and folders from this directory to the `Tests/Acceptance` folder of your Extension and adjust

the namespaces in all files.

After that you should be able to run your first acceptance test (located in the
[Frontend](doc/acceptance/frontend/Frontend) subdirectory).

```bash
.Build/bin/t3_run_tests.sh -s acceptance -a Frontend
```

### Backend

The setup for Backend acceptance testing is quite similar to the Frontend.

Copy the contents of the [backend](doc/acceptance/backend) folder into the `Tests/Acceptance` folder of
your Extension and you are good to go.

The acceptance test command executes the Backend testsuite by default:

```bash
.Build/bin/t3_run_tests.sh -s acceptance
```

### Debugging

Add these to the config of the `web` service in the `docker-compose.yml` to temporarily
enable Development context for better debug output in the Frontend and the possibility
to access the webserver from your local machine

Important! For this to work you need to add the `--service-ports` flat to the `docker-compose` command

in `t3_run_tests.sh`. The final command looks like this:

```bash
docker-compose run --service-ports acceptance_backend_mariadb10
```

This config needs to be added to the `docker-compose.yml` file:

```yaml
  web:
    ...
    ports:
      - "8000:8000"
    ...
    environment:
      TYPO3_CONTEXT: Development
```

## Code style checking

## Deploy to TER

## Credits

This work is based on the TYPO3 testing framework and the awesome documentation at
https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/Testing/ExtensionTesting.html
