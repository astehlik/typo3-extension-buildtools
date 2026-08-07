# TYPO3 Extension build tools

Test your TYPO3 Extensions with the utilities provided by the TYPO3 core using:

- the `typo3/testing-framework` package
- `docker` and `docker-compose`
- the `typo3gmbh/php*` docker images

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

- `t3_run_tests.sh` - For running Unit, Functional and Acceptance tests
- `t3_check_codestyle.sh` - For checking / fixing PHP code style via
  [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)
- `t3_prepare_release.sh` - Prepare docs and `ext_emconf.php` for a release
- `t3_deploy_to_ter.sh` - Check out a tag and publish it to TER, see [Deploy to TER](#deploy-to-ter)

## Run in GitHub Actions

Call the reusable workflows from a workflow file in your extension's `.github/workflows` directory:

```yaml
name: CI

on:
  push:
    branches: [main]
    tags: ['v*']
  pull_request:

jobs:
  test:
    uses: astehlik/typo3-extension-buildtools/.github/workflows/extension-test.yml@TYPO3_14

  publish:
    needs: test
    uses: astehlik/typo3-extension-buildtools/.github/workflows/extension-publish.yml@TYPO3_14
    with:
      extension-key: '<your_extension_key>'
    secrets:
      TYPO3_API_TOKEN: ${{ secrets.TYPO3_API_TOKEN }}
```

[`extension-test.yml`](.github/workflows/extension-test.yml) will

- validate and normalize the `composer.json` file
- check the code style of your PHP code (PHP_CodeSniffer and PHP CS Fixer)
- run PHP unit, functional and acceptance tests across the configured PHP versions
- lint your PHP code
- run PHPStan
- scan for deprecated and breaking code using `typo3scan`

[`extension-publish.yml`](.github/workflows/extension-publish.yml) publishes your extension to TER whenever
a tag matching `v<major>.<minor>.<patch>` is pushed (see [Deploy to TER](#deploy-to-ter)). It needs a
`TYPO3_API_TOKEN` secret with a valid TER API token.

## Write tests

### Unit test

To write a unit test create the folder `Tests/Unit` in your Extension and add your first
test case by extending `TYPO3\TestingFramework\Core\Unit\UnitTestCase`:

```php
namespace Vendor\MyExt\Tests\Unit;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class MyFirstUnitTest extends UnitTestCase {
...
}
```

### Functional test

To write a functional test create the folder `Tests/Functional` in your Extension and add your first functional
test case by extending `TYPO3\TestingFramework\Core\Functional\FunctionalTestCase`:

```php
namespace Vendor\MyExt\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class MyFirstFunctionalTest extends FunctionalTestCase {
...
}
```

## Execute tests

For executing Unit tests, run this command:

```bash
.Build/bin/t3_run_tests.sh -s unit -p "<PHP version>"
```

For executing functional tests, run this command:

```bash
.Build/bin/t3_run_tests.sh -s functional -d "<database type>" -p "<PHP version>"
```

`<database type>` can be:

- `mariadb`
- `mssql`
- `postgres`
- `sqlite`

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

To execute code style checks, you can use this command:

```bash
.Build/bin/t3_check_codestyle.sh
```

It checks all known locations of PHP files in TYPO3 Extensions with some default configuration based on the
TYPO3 core coding guidelines.

You can adjust the ruleset, by adding `Tests/CodeSniffer/MyCodingStandard/ruleset.xml` to your Extension.

This is an example to use the default rules and disable line length checking for TCA configuration files:

```xml
<?xml version="1.0"?>
<ruleset name="MyCodingStandard">
	<description>Based on PSRDefault. Adjust only if REALLY neccessary!</description>
	<rule ref="PSRDefault"/>
	<rule ref="Generic.Files.LineLength">
		<exclude-pattern>Configuration/TCA/*</exclude-pattern>
	</rule>
</ruleset>
```

After you created the ruleset, you _must_ provide its name to the code style checker:

```bash
.Build/bin/t3_check_codestyle.sh MyCodingStandard
```

To automatically fix code style errors, you can pass the `fix` keyword as first parameter:

```bash
.Build/bin/t3_check_codestyle.sh fix [MyCodingStandard]
```

## Deploy to TER

Use the `t3_prepare_release.sh` script, to prepare a release:

```bash
bash .Build/bin/t3_prepare_release.sh "<semantic_version>"
```

This will set the provided version number in `ext_emconf.php` and `Documentation/Settings.cfg` and create
a new release using the `git flow release` commands.

After that you can push all branches and tags:

```bash
git push && git push --tags && git checkout develop && git push
```

Pushing a tag that matches `v<major>.<minor>.<patch>` (e.g. `v12.1.0`) triggers the reusable
[`extension-publish.yml`](.github/workflows/extension-publish.yml) GitHub Actions workflow, which publishes
that version to TER. It needs a `TYPO3_API_TOKEN` secret with a valid TER API token.

To publish a tag manually instead (e.g. from your local machine), use `t3_deploy_to_ter.sh`:

```bash
export TYPO3_EXTENSION_KEY="<my_extension_key>"
export TYPO3_API_TOKEN="<ter_api_token>"

bash .Build/bin/t3_deploy_to_ter.sh v12.1.0
```

This checks out the given tag into a temporary git worktree, copies it into a plain (non-git) build
directory under `work/`, determines the release comment from the tag message, and publishes it to TER
after you confirm the prompt. Consumer extensions no longer need their own `Build/cleanup_for_ter.sh` —
the cleanup step is now provided by buildtools itself.

## Credits

This work is based on the TYPO3 testing framework and the awesome documentation at
https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/Testing/ExtensionTesting.html
