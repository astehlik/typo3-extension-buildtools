#!/usr/bin/env bash

set -e

version="${1:?Usage: t3_get_tag_comment.sh <version>}"

subject="$(git tag -l --format='%(contents:subject)' "v${version}")"
body="$(git tag -l --format='%(contents:body)' "v${version}")"

# Join single line-breaks within a paragraph into one line, but keep
# blank-line-separated paragraphs intact (TER accepts multi-line comments).
joined_body="$(
  printf '%s' "$body" | awk '
      BEGIN { para = ""; first = 1 }
      {
        line = $0
        sub(/\r$/, "", line)
        if (line ~ /^[[:space:]]*$/) {
          if (para != "") {
            if (!first) printf "\n\n"
            printf "%s", para
            first = 0
            para = ""
          }
        } else {
          para = (para == "" ? line : para " " line)
        }
      }
      END {
        if (para != "") {
          if (!first) printf "\n\n"
          printf "%s", para
        }
      }
    '
)"

if [[ -n "$joined_body" ]]; then
    comment="${subject}"$'\n\n'"${joined_body}"
else
    comment="$subject"
fi

if [[ -z "${comment// }" ]]; then
    echo "Tag v${version} has no message; TER requires a release comment." >&2
    exit 1
fi

echo "$comment"
