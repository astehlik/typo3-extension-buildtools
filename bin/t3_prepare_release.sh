#!/usr/bin/env bash

set -e

THIS_SCRIPT_DIR="$( cd "$( dirname "`readlink -f ${BASH_SOURCE[0]}`" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

. script.inc.sh

cd "${ROOT_DIR}"

release="$1"
version=${version%.*}

if [[ -z "$release" ]]; then
    echo "No release number provided!"
    exit 1
fi

echo "Preparing release for version $version"

echo "Replacing release number in ext_emconf.php..."

sed -i -E "s/'version' => '.+'/'version' => '$release'/" ext_emconf.php

git add ext_emconf.php

if [[ -e Documentation/Settings.cfg ]]; then
    echo "Replacing version numbers documentation settings..."

    sed -i -E "s/version     = .+/version     = $version/" Documentation/Settings.cfg
    sed -i -E "s/release     = .+/release     = $release/" Documentation/Settings.cfg

    git add Documentation/Settings.cfg
fi

git commit -m "[TASK] Release version $release"

git flow release start ${release}

git flow release finish ${release}

echo "Check if everything is OK, then push..."
