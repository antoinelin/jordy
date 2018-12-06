#!/bin/bash
echo '\033[0;36mBuilding web-server, database, wordpress and redis containers. Please wait...\033[0m'
docker-compose up -d --build web-server database wordpress redis

echo '\033[0;36mBuilding proxy container. Please wait...\033[0m'
docker-compose up -d --build proxy-dev

read -p 'Would you like to install Jordy WordPress theme? (recommanded) (y/N)? ' answer
case ${answer:0:1} in
    y|Y )
        docker-compose run --rm wordpress-cli install
    ;;
    * )
        :
    ;;
esac

echo '\033[0;32mBuilding complete.\033[0m'