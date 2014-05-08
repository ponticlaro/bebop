#!/usr/bin/env bash

# Usage: sh ci/setup-theme

echo "-----> Setting up development theme"

# Symlink test theme into theme directory
echo "     > Symlinking test theme"
if [[ -L $(pwd)/wp-core/wp-content/themes/dev-theme ]]; then
	rm $(pwd)/wp-core/wp-content/themes/dev-theme
fi
ln -s $(pwd)/ci/dev-theme/ $(pwd)/wp-core/wp-content/themes/dev-theme

# Select testing theme
echo "     > Activating testing theme"
./vendor/bin/wp theme activate dev-theme