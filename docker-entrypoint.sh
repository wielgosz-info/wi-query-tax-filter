#!/bin/sh
set -euo pipefail

# This script is used to configure the container after starting.
# It needs an access to the mounted volumes.

git fetch
yarn install

exec "$@"
