#!/usr/bin/env bash

set -e

if hash phpenv 2>/dev/null; then
    phpenv config-rm xdebug.ini
fi

THIS_SCRIPT_DIR="$( cd "$( dirname "`readlink -f ${BASH_SOURCE[0]}`" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

if [[ -z "${TYPO3_EXTENSION_KEY}" ]]; then
    echo "TYPO3_EXTENSION_KEY environment variable must be defined before running this script!"
    exit 1
fi

if [[ -z "${TRAVIS_TAG}" ]]; then
    echo "No Travis tag is available. Upload only runs for new tags."
    exit 0
fi

if [[ -z "${TYPO3_ORG_USERNAME}" ]]; then
    echo "The TYPO3_ORG_USERNAME env var is not set."
    exit 1
fi

if [[ -z "${TYPO3_ORG_PASSWORD}" ]]; then
    echo "The TYPO3_ORG_PASSWORD env var is not set."
    exit 1
fi

. script.inc.sh

cd "${ROOT_DIR}"

tagMessage=`git tag -n10 -l ${TRAVIS_TAG} | sed 's/^v[0-9.]*[ ]*//g'`

if [[ -z "$tagMessage" ]]; then
    echo "The tag message could not be detected or was empty."
    exit 1
fi

echo "Extracted tag message: $tagMessage"

buildDirectoryName=`basename $PWD`
echo "Detected build directory name $buildDirectoryName"

versionNumber="${TRAVIS_TAG#v}"
function assertVersionNumberInFile {
    file="${1}"
    assertStringInFile "'${versionNumber}'" "${file}"
}

function assertStringInFile {
    expectedString="${1}"
    file="${2}"
    if grep "${expectedString}" "${file}"; then
        echo "Expected string ${expectedString} found in ${file}"
    else
        echo "Expected string ${expectedString} not found in ${file}!"
        exit 1
    fi
}

echo "Making sure version number matches in ext_emconf.php"
assertVersionNumberInFile ext_emconf.php

if [[ -e Documentation/Settings.cfg ]]; then
    echo "Making sure version variable is used in documentation settings"
    assertStringInFile "{extension.version}" Documentation/Settings.cfg
fi

echo "Cleanup Git repository..."
git reset --hard HEAD && git clean -fx

EXTENSION_DIRECTORY="$PWD"

cd ..

if [[ "${buildDirectoryName}" != "${TYPO3_EXTENSION_KEY}" ]]; then
    echo "Renaming repository folder to match extension key..."
    mv "${buildDirectoryName}" "${TYPO3_EXTENSION_KEY}"
fi

echo "Installing TYPO3 repository client..."
rm typo3-repository-client -Rf
composer create-project --no-dev namelesscoder/typo3-repository-client typo3-repository-client

echo "Uploading release ${TRAVIS_TAG} to TER"
./typo3-repository-client/bin/upload "${EXTENSION_DIRECTORY}" "${TYPO3_ORG_USERNAME}" "${TYPO3_ORG_PASSWORD}" "${tagMessage}"
