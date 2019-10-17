#!/usr/bin/env bash

set -ev

phpenv config-rm xdebug.ini

THIS_SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

extensionKey="$1"

if [[ -z "${extensionKey} " ]]; then
    echo "Extension name missing!"
    exit 1
fi

if [[ -z "$TRAVIS_TAG" ]]; then
    echo "No Travis tag is available. Upload only runs for new tags."
    exit 0
fi

if [[ -z "$TYPO3_ORG_USERNAME" ]]; then
    echo "The $TYPO3_ORG_USERNAME env var is not set."
    exit 1
fi

if [[ -z "$TYPO3_ORG_PASSWORD" ]]; then
    echo "The $TYPO3_ORG_PASSWORD env var is not set."
    exit 1
fi

cd ../..

echo "Cleanup Git repository..."
git reset --hard HEAD && git clean -fx

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
    if grep "${versionNumber}" "${1}"; then
        echo "Correct version number ${versionNumber} found in $1"
    else
        echo "Version number ${versionNumber} not found in $1!"
        exit 1
    fi
}

assertVersionNumberInFile ext_emconf.php
assertVersionNumberInFile Documentation/Settings.cfg

cd ..

if [[ "${buildDirectoryName}" != "${extensionKey}" ]]; then
    echo "Renaming repository folder to match extension key..."
    mv "${buildDirectoryName}" "${extensionKey}"
fi

echo "Installing TYPO3 repository client..."
composer create-project --no-dev namelesscoder/typo3-repository-client typo3-repository-client

cd ${extensionKey}

echo "Setting version to ${TRAVIS_TAG#"v"}"
../typo3-repository-client/bin/setversion ${TRAVIS_TAG#"v"}

echo "Uploading release ${TRAVIS_TAG} to TER"
../typo3-repository-client/bin/upload . "$TYPO3_ORG_USERNAME" "$TYPO3_ORG_PASSWORD" "$tagMessage"
