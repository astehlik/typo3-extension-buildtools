#!/usr/bin/env bash
# Checks out a tag into a worktree and publishes it to TER, mirroring the
# steps of .github/workflows/extension-publish.yml.
#
# Usage: t3_deploy_to_ter.sh <tag>
# Example: t3_deploy_to_ter.sh v12.1.0

set -eo pipefail

THIS_SCRIPT_DIR="$( cd "$( dirname "$(readlink -f "${BASH_SOURCE[0]}")" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

. script.inc.sh

cd "${ROOT_DIR}"

tag="${1:?Usage: t3_deploy_to_ter.sh <tag>}"

if [[ -z "${TYPO3_EXTENSION_KEY:-}" ]]; then
    echo "The TYPO3_EXTENSION_KEY env var is not set." >&2
    exit 1
fi

if [[ -z "${TYPO3_API_TOKEN:-}" ]]; then
    echo "The TYPO3_API_TOKEN env var is not set." >&2
    exit 1
fi

version="$("${THIS_SCRIPT_DIR}/t3_get_tag_version.sh" "$tag")"

worktreeDir="${ROOT_DIR}/work/publish-worktree-${tag}"
distDir="${ROOT_DIR}/work/publish-dist-${tag}"

cleanup() {
    if git -C "${ROOT_DIR}" worktree list --porcelain | grep -qF "$worktreeDir"; then
        git -C "${ROOT_DIR}" worktree remove --force "$worktreeDir"
    fi
}
trap cleanup EXIT

echo "==> Checking out ${tag} into a worktree"
rm -rf "$worktreeDir"
git -C "${ROOT_DIR}" worktree add --detach "$worktreeDir" "$tag"

echo "==> Copying worktree into a plain (non-git) build directory"
rm -rf "$distDir"
mkdir -p "$distDir"
rsync -a --exclude='.git' "$worktreeDir"/ "$distDir"/

echo "==> Removing worktree"
git -C "${ROOT_DIR}" worktree remove --force "$worktreeDir"
trap - EXIT

echo "==> Determining release comment from tag ${tag}"
comment="$("${THIS_SCRIPT_DIR}/t3_get_tag_comment.sh" "$version")"
echo "    comment:"
echo "$comment" | sed 's/^/    | /'

cd "$distDir"

echo "==> Setting version in composer.json"
composer config version "$version"

echo "==> Cleaning up build directory for TER upload"
touch ready_for_release.txt
bash "${THIS_SCRIPT_DIR}/t3_cleanup_for_ter.sh"

echo "==> Ensuring typo3/tailor is installed"
if ! command -v tailor >/dev/null 2>&1 && [[ ! -x "$HOME/.composer/vendor/bin/tailor" ]]; then
    composer global require typo3/tailor --prefer-dist --no-progress --no-suggest
fi
tailorBin="$(command -v tailor || echo "$HOME/.composer/vendor/bin/tailor")"

read -r -p "About to publish version ${version} of ${TYPO3_EXTENSION_KEY} to TER from ${distDir}. Continue? [y/N] " confirm
if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
    echo "Aborted."
    exit 1
fi

echo "==> Publishing to TER"
php "$tailorBin" ter:publish --comment "$comment" "$version"

echo "==> Done. Build directory left at: $distDir"
