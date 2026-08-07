#!/usr/bin/env bash

set -e

ref="${1:?Usage: t3_get_tag_version.sh <git-ref-or-tag>}"
tag="${ref#refs/tags/}"

if ! [[ "$tag" =~ ^v[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
    echo "Tag '${tag}' does not look like v<major>.<minor>.<patch>, e.g. v12.1.0" >&2
    exit 1
fi

echo "${tag#v}"
