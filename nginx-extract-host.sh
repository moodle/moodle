#!/bin/sh

# Extract hostname from MOODLE_URL
# - Remove http:// or https://
# - Remove path/query components
export NGINX_HOST=$(echo "$MOODLE_URL" | sed -e 's|^[^/]*//||' -e 's|/.*$||')

echo "Configuration: Parsed MOODLE_URL='$MOODLE_URL' -> NGINX_HOST='$NGINX_HOST'"
