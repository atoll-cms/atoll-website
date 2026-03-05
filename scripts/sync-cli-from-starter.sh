#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LOCAL_CLI_PATH="${LOCAL_CLI_PATH:-$ROOT_DIR/bin/atoll}"

MODE="sync"
if [[ "${1:-}" == "--check" ]]; then
  MODE="check"
  shift
fi

UPSTREAM_STARTER_PATH="${UPSTREAM_STARTER_PATH:-${1:-}}"
if [[ -z "$UPSTREAM_STARTER_PATH" ]]; then
  echo "Usage: $(basename "$0") [--check] <path-to-atoll-starter>" >&2
  exit 2
fi

UPSTREAM_CLI_PATH="$UPSTREAM_STARTER_PATH/bin/atoll"
if [[ ! -f "$UPSTREAM_CLI_PATH" ]]; then
  echo "Upstream starter CLI not found: $UPSTREAM_CLI_PATH" >&2
  exit 2
fi

if [[ ! -f "$LOCAL_CLI_PATH" ]]; then
  echo "Local CLI not found: $LOCAL_CLI_PATH" >&2
  exit 2
fi

if [[ "$MODE" == "check" ]]; then
  if cmp -s "$UPSTREAM_CLI_PATH" "$LOCAL_CLI_PATH"; then
    echo "bin/atoll is in sync with upstream starter CLI"
    exit 0
  fi

  echo "CLI drift detected: local bin/atoll differs from atoll-starter/bin/atoll" >&2
  echo "To sync locally:" >&2
  echo "  ./scripts/sync-cli-from-starter.sh $UPSTREAM_STARTER_PATH" >&2
  exit 1
fi

cp "$UPSTREAM_CLI_PATH" "$LOCAL_CLI_PATH"
chmod +x "$LOCAL_CLI_PATH"
echo "CLI synced from: $UPSTREAM_CLI_PATH"
