#!bin/bash

thisScriptDir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
t3RunTestScript="${thisScriptDir}/../bin/t3_run_tests.sh"

if [ ! -f "${t3RunTestScript}" ]; then
    echo "Error: t3_run_tests.sh not found at ${t3RunTestScript}"
    exit 1
fi

# Replace "cd ../../ || exit 1" with "cd ../../../../../ || exit 1"
sed -i 's/cd ..\/..\/ || exit 1/cd ..\/..\/..\/..\/..\/ || exit 1/g' "${t3RunTestScript}"

# Remove directory from php-cs-fixer commands
sed -i 's/Build\/php-cs-fixer\/\(.*\).php typo3\//Build\/php-cs-fixer\/\1.php/g' "${t3RunTestScript}"

# Replace Build/phpunit/UnitTests.xml with .Build/vendor/de-swebhosting/typo3-extension-buildtools/phpunit/UnitTests.xml
sed -i 's/Build\/phpunit\/UnitTests.xml/.Build\/vendor\/de-swebhosting\/typo3-extension-buildtools\/phpunit\/UnitTests.xml/g' "${t3RunTestScript}"

# Replace Build/phpunit/FunctionalTests.xml with .Build/vendor/de-swebhosting/typo3-extension-buildtools/phpunit/FunctionalTests.xml
sed -i 's/Build\/phpunit\/FunctionalTests.xml/.Build\/vendor\/de-swebhosting\/typo3-extension-buildtools\/phpunit\/FunctionalTests.xml/g' "${t3RunTestScript}"
