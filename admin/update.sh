#!/bin/bash
echo "update Images..."
docker pull nginx:latest
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
docker pull mariadb:latest
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
docker compose build
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
echo "stopping containers..."
docker compose down --remove-orphans
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
echo "restarting container..."
docker compose up -d
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
docker image prune -a --force
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
echo "wait for containers to start..."
sleep 5
docker ps
returnCode=$?
if [ $? -eq 0 ]
then
  echo "OK"
else
  echo "res: $returnCode"
  echo "Failed !"
  exit 1
fi
echo "$(date --rfc-3339=date) - Done!"
