#!/bin/bash
read -p 'Would you like to export your database? (y/N)? ' answer
case ${answer:0:1} in
    y|Y )
        sh ./bin/export.sh
    ;;
    * )
        :
    ;;
esac

echo '\033[0;31mKilling containers. Please wait...\033[0m'
docker-compose down

read -p 'Would you like to delete Docker volumes? (y/N)? ' answer
case ${answer:0:1} in
    y|Y )
        docker volume prune
        echo '\033[0;32mVolumes delete.\033[0m'
    ;;
    * )
        :
    ;;
esac

echo '\033[0;32mKilling complete.\033[0m'