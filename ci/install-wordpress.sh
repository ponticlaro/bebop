#!/usr/bin/env bash

# Usage: sh ci/install-wordpress VERSION

VERSION=$1

if [[ -z $VERSION ]]; then
	
	echo "-----> You need to provide a WordPress version as the first parameter. Example:"
	echo "     > sh ci/install-wordpress 3.9"
	exit
fi

echo "-----> Setting up WordPress $VERSION"

echo "     > Downloading"
./vendor/bin/wp core download --version=$VERSION

echo "     > Creating configuration file"
./vendor/bin/wp core config

echo "     > Installing"
./vendor/bin/wp core install

echo "     > Configuring"
./vendor/bin/wp rewrite structure '/%postname%/'

echo "     > Done\n"