#!/usr/bin/env bash

# Detect root directory, start from Package root
cd ..
while [[ "$PWD" != "/" ]] ; do
    cd ..

    if [[ -e composer.json ]]; then
        ROOT_DIR="$PWD"
        CORE_ROOT="$PWD"
        break
    fi
done

if [[ -z "${ROOT_DIR}" ]]; then
    echo "Could not detect root directory :("
    exit 1;
fi
