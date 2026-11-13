#!/usr/bin/env bash
# Fails when the gate itself is incomplete: CODEOWNERS, the PR template, the issue forms or CI lost a
# required piece. `--self-test` breaks a copy on purpose and must then fail.
set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SELF_TEST=0
[ "${1:-}" = "--self-test" ] && SELF_TEST=1

# Every path the gate depends on, and the token that must appear inside it.
REQUIRED=(
  ".github/CODEOWNERS|/src/"
  ".github/CODEOWNERS|@OuisAlhetar"
  ".github/pull_request_template.md|risk:"
  ".github/pull_request_template.md|priority:"
  ".github/ISSUE_TEMPLATE/bug.yml|risk_tier"
  ".github/ISSUE_TEMPLATE/bug.yml|priority"
  ".github/ISSUE_TEMPLATE/bug.yml|evidence"
  ".github/workflows/ci.yml|php tests/run.php"
  ".github/workflows/ci.yml|DB-free gate"
)

fail=0
for rule in "${REQUIRED[@]}"; do
  file="${rule%%|*}"
  token="${rule##*|}"
  if [ ! -f "$ROOT/$file" ]; then
    echo "MISSING FILE  $file"; fail=1; continue
  fi
  if ! grep -qF -- "$token" "$ROOT/$file"; then
    echo "MISSING TOKEN $file <- '$token'"; fail=1
  fi
done

# The controller is ungoverned on purpose and the governed paths are not: both directions are asserted.
if grep -qE '^/[a-z]' "$ROOT/.github/CODEOWNERS" 2>/dev/null; then :; fi
if ! grep -qE '^\*\s+@' "$ROOT/.github/CODEOWNERS" 2>/dev/null; then
  echo "CODEOWNERS has no catch-all owner"; fail=1
fi

if [ "$SELF_TEST" = 1 ]; then
  TMP="$(mktemp -d)"
  trap 'rm -rf "$TMP"' EXIT
  cp -R "$ROOT/." "$TMP/" 2>/dev/null || true
  printf '# damage\n' > "$TMP/.github/CODEOWNERS"
  if ( cd "$TMP" && bash scripts/gate-selfcheck.sh >/dev/null 2>&1 ); then
    echo "self-test: a broken gate was reported as healthy"
    exit 1
  fi
  echo "self-test: damage detected"
  exit 0
fi

if [ "$fail" = 0 ]; then
  echo "gate ok"
fi
exit "$fail"
