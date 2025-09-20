#!/bin/bash

host=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' $(docker ps -f name=docker_cs_mysql_1 -q))

echo "MariaDB is at $host"

mysql -h $host -u <db_user> -p<db_user_password> microcontrollis
