#!/usr/bin/env bash

set -e

THIS_SCRIPT_DIR="$( cd "$( dirname "$(readlink -f "${BASH_SOURCE[0]}")" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

. script.inc.sh

cd "${ROOT_DIR}"

testPaths=""
if [[ -d "Classes" ]]; then
    testPaths="Classes"
fi

if [[ -d "Configuration/TCA" ]]; then
    testPaths="${testPaths} Configuration/TCA"
fi

if [[ -d "Tests" ]]; then
    testPaths="${testPaths} Tests"
fi

if ls ext_*.php 1> /dev/null 2>&1; then
    testPaths="${testPaths} ext_*.php"
fi

phpcsCmd="bin/phpcs"
customRuleset="$1"
if [[ "$1" == "fix" ]]; then
    customRuleset="$2"
    phpcsCmd="bin/phpcbf"
fi

if [[ ! -d "${PWD}/Tests/CodeSniffer" ]]; then
    php bin/phpcs --config-set installed_paths ${PWD}/.Build/vendor/de-swebhosting/php-codestyle/PhpCodeSniffer/
    php ${phpcsCmd} --standard=PSRDefault ${testPaths}
else
    if [[ -z "${customRuleset}" ]]; then
        echo "Name of custom ruleset must be provided"
        exit 1
    fi
    php bin/phpcs --config-set installed_paths ${PWD}/.Build/vendor/de-swebhosting/php-codestyle/PhpCodeSniffer/,${PWD}/Tests/CodeSniffer
    php ${phpcsCmd} --standard=${customRuleset} ${testPaths}
fi
