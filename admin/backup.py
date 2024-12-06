#!/usr/bin/python3
# -*- coding: utf-8 -*-
import pymysql
import sys
import subprocess
import datetime
import socket
import time

mariadb_connection = None
cursor = None
db_host = None
db_user = '<db_user>'
db_password = '<db_user_password>'
db_container_name = 'cs_mysql'

def connect2localDB():
    global cursor
    global mariadb_connection
    global db_host
    global db_user
    global db_password
    mariadb_connection = pymysql.connect(host=db_host, port=3306, user=db_user, password=db_password, database='microcontrollis', charset='utf8mb4')
    mariadb_connection.encoding = 'utf_8'
    # pymysql.set_character_set('utf8')
    #mariadb_connection.set_character_set('utf8')
    cursor = mariadb_connection.cursor()
    cursor.execute('SET NAMES utf8mb4;')
    cursor.execute('SET CHARACTER SET utf8mb4;')
    cursor.execute('SET character_set_connection=utf8mb4;')
    print('connected to database...')

def closeDB():
    global cursor
    global mariadb_connection
    mariadb_connection.commit()
    cursor.close()
    mariadb_connection.close()

def detectHost():
    container_id = subprocess.check_output(['docker', 'ps', '-f', 'name=' + db_container_name, '-q'])
    container_id = container_id.decode("utf-8")
    container_id = container_id.rstrip()
    print('container id : ' + container_id)
    container_ip = subprocess.check_output(["docker", "inspect", "-f", "'{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}'", str(container_id)])
    container_ip = container_ip.decode("utf-8")
    container_ip = container_ip.rstrip()
    container_ip = container_ip.replace("'", "")
    print('container ip : ' + str(container_ip))
    global db_host
    db_host = str(container_ip)

def getTables():
    global cursor
    sql = 'SHOW tables'
    cursor.execute(sql)
    result = cursor.fetchall()
    res = []
    for row in result:
        res = res + [row[0]]
    return res

def backup_structure():
    global db_host
    global db_user
    global db_password
    now = datetime.datetime.now()
    hostname = socket.gethostname()
    dateStr = now.strftime('%Y%m%d-%H%M%S')
    f = open('dump_' + hostname + '_structure_' + dateStr + '.sql.bz2', 'w')
    sqldump = subprocess.Popen( ['mysqldump', '-h', db_host, '-u', db_user, '-p'+ db_password, '--lock-tables', '--tables', '--no-data', '--databases', 'microcontrollis'],
                         stdout=subprocess.PIPE,
                         stderr=subprocess.PIPE)
    compress = subprocess.Popen( ['bzip2', '-c', '-9'],
                         stdin=sqldump.stdout,
                         stdout=f,
                         stderr=subprocess.PIPE)
    o, e = compress.communicate()
    print('output: ' + str(o))
    print('error: ' + str(e))
    returnCode = compress.returncode
    print('returnCode : ' + str(returnCode))


def backup_table(tableName):
    global db_host
    global db_user
    global db_password

    startTime = time.time()
    now = datetime.datetime.now()
    hostname = socket.gethostname()
    print("now storing " + tableName)

    dateStr = now.strftime('%Y%m%d-%H%M%S')
    f = open('dump_' + tableName + '-' + hostname + '_' + dateStr + '.sql.bz2', 'w')
    sqldump = subprocess.Popen( ['mysqldump', '-h', db_host, '-u', db_user, '-p'+ db_password, '--single-transaction', 'microcontrollis', tableName],
                         stdout=subprocess.PIPE,
                         stderr=subprocess.PIPE)
    compress = subprocess.Popen( ['bzip2', '-c', '-9'],
                         stdin=sqldump.stdout,
                         stdout=f,
                         stderr=subprocess.PIPE)
    o, e = compress.communicate()
    print('output: ' + str(o))
    print('error: ' + str(e))
    returnCode = compress.returncode
    print('returnCode : ' + str(returnCode))
    endTime = time.time()
    print('time : ' + str(endTime - startTime))

if __name__ == '__main__':
    startTime = time.time()
    detectHost()
    connect2localDB()
    allTables = getTables()
    closeDB()
    #print('received the list : ' + str(allTables))
    backup_structure()
    print("Structure done, now data, that will take some time...")
    for table in allTables:
        backup_table(table)
    endTime = time.time()
    print('complete Backup took : ' + str(endTime - startTime))
    print('Done !')
