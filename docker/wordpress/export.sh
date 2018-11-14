#!/bin/bash

# From https://github.com/nezhar/wordpress-docker-compose/blob/master/export.sh
_os="`uname`"
_now=$(date +"%m_%d_%Y")
_file="./data/data_$_now.sql"
docker-compose -f ../docker-compose.yml run --rm database sh -c 'exec mysqldump "$MYSQL_DATABASE" -uroot -p"$MYSQL_ROOT_PASSWORD"' > $_file
if [[ $_os == "Darwin"* ]] ; then
  sed -i '.bak' 1,1d $_file
else
  sed -i 1,1d $_file # Removes the password warning from the file
fi