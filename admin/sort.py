#!/usr/bin/python3
# -*- coding: utf-8 -*-

import shutil
import os
import re

# scan all files in current directory
for file in os.listdir('.'):
    print(str(file))
    FileNameExtractor = re.compile(r'\d\d\d\d\d\d\d\d')
    fileName = FileNameExtractor.search(file)
    print(str(fileName))
    if None != fileName:
        if os.path.isdir(file):
            pass
        else:
            # take that date from the file
            fileName = fileName.group()
            print(str(fileName))
            print('filename = ' + fileName)
            # make sure that a folder with that date as name exists
            if False == os.path.exists(fileName):
                os.makedirs(fileName)
            # move the file into that folder
            shutil.move(file, fileName + '/' + file)

