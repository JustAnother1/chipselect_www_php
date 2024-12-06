#!/bin/bash

# change as needed
db_user=''
db_passwd=''

#regEx : https://pubs.opengroup.org/onlinepubs/9699919799/basedefs/V1_chap09.html#tag_09_04


# get IP of mariadb inside docker container
host=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' $(docker ps -f name=docker-cs_mysql-1 -q))

echo "MariaDB is at $host"

# start with structure
for filename in *.bz2; do
    #echo $filename
    [[ "$filename" =~ "_structure_" ]]
    if [ $? -eq 0 ]
    then
        echo "found $filename"
        bunzip2 -c -k $filename | mysql -h $host -u $db_user -p$db_passwd microcontrollis
        if [ $? -eq 0 ]
        then
          echo "OK"
        else
          echo "Failed !"
          exit 1
        fi

    fi
done

# and now the tables
for filename in *.bz2; do
    [[ "$filename" =~ "_structure_" ]]
    if [ $? != 0 ]
    then
        echo "now restoring $filename ..."
        bunzip2 -c -k $filename | mysql -h $host -u $db_user -p$db_passwd microcontrollis
        if [ $? -eq 0 ]
        then
          echo "OK"
        else
          echo "Failed !"
          exit 1
        fi
    fi
done
