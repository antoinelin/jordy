#!/bin/bash

# From https://github.com/nezhar/wordpress-docker-compose/blob/master/export.sh
_os="`uname`"
_now=$(date +"%m_%d_%Y")
_file="./wordpress/data/data_$_now.sql"

echo '\033[0;36mExporting database. Please wait...\033[0m'
docker exec mysql sh -c 'exec mysqldump -uroot -p$MYSQL_ROOT_PASSWORD "$MYSQL_DATABASE"' > $_file

if [[ $_os == "Darwin"* ]] ; then
  sed -i '.bak' 1,1d $_file
else
  sed -i 1,1d $_file # Removes the password warning from the file
fi

echo '\033[0;32mExport completed.\033[0m'