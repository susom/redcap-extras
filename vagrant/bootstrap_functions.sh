#!/bin/bash

# LICENSE
# Copyright (c) 2015, University of Florida
# All rights reserved.
#
# Distributed under the BSD 3-Clause License
# For full text of the BSD 3-Clause License see http://opensource.org/licenses/BSD-3-Clause

function install_prereqs() {
    # Install the REDCap prerequisites:
    #   https://iwg.devguard.com/trac/redcap/wiki/3rdPartySoftware

    apt-get update

    apt-get install -y \
        apache2 \
        mysql-server \
        php5 php-pear php5-mysql php5-curl

    # configure MySQL to start every time
    update-rc.d mysql defaults

    # restart apache
    service apache2 restart
}

# Setup REDCap
function install_redcap() {
    rm -rf /var/www/*

    # extract a standard REDCap zip file as downloaded from Vanderbilt.
    unzip -q $REDCAP_ZIP -d /var/www/

    # adjust ownership so apache can write to the temp folders
    chown -R www-data.root /var/www/redcap/edocs/
    chown -R www-data.root /var/www/redcap/temp/

    REDCAP_VERSION_DETECTED=`ls /var/www/redcap | grep redcap_v | cut -d 'v' -f2 | sort -n | tail -n 1`
    echo "$REDCAP_ZIP content indicates REDCap version: $REDCAP_VERSION_DETECTED"

    # STEP 1: Create a MySQL database/schema and user
    create_redcap_database
    # STEP 2: Add MySQL connection values to 'database.php'
    update_redcap_connection_settings
    # STEP 3: Customize values
    #   do nothing
    # STEP 4: Create the REDCap database tables
    create_redcap_tables
    # STEP 5: Configuration Check
}

function create_redcap_database() {
    mysql -uroot <<SQL
DROP DATABASE IF EXISTS redcap;
CREATE DATABASE redcap;

GRANT
    SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, EXECUTE, CREATE VIEW, SHOW VIEW
ON
    redcap.*
TO
    'redcap'@'localhost'
IDENTIFIED BY
    'password';
SQL
}

function update_redcap_connection_settings() {
    # edit redcap database config file (This needs to be done after extraction of zip files)
    echo "Setting the connection variables in: /var/www/redcap/database.php"
    echo '$hostname   = "localhost";' >> /var/www/redcap/database.php
    echo '$db         = "redcap";'    >> /var/www/redcap/database.php
    echo '$username   = "redcap";'    >> /var/www/redcap/database.php
    echo '$password   = "password";'  >> /var/www/redcap/database.php
    echo '$salt   = "abc";'  >> /var/www/redcap/database.php
}

# Create tables from sql files distributed with redcap under
#  redcap_vA.B.C/Resources/sql/
#
# @see install.php for details
function create_redcap_tables() {
    SQL_DIR=/var/www/redcap/redcap_v$REDCAP_VERSION_DETECTED/Resources/sql
    mysql -uredcap -ppassword redcap < $SQL_DIR/install.sql
    mysql -uredcap -ppassword redcap < $SQL_DIR/install_data.sql
    mysql -uredcap -ppassword redcap -e "UPDATE redcap.redcap_config SET value = '$REDCAP_VERSION_DETECTED' WHERE field_name = 'redcap_version' "

    files=$(ls -v $SQL_DIR/create_demo_db*.sql)
    for i in $files; do
        echo "Executing sql file $i"
        mysql -uredcap -ppassword redcap < $i
    done
}

# Check if the Apache server is actually serving the REDCap files
function check_redcap_status() {
    echo "Checking if redcap application is running..."
    curl -s http://localhost/redcap/ | grep -i 'Welcome\|Critical Error'
    echo "Please try to login to REDCap as user 'admin' and password: 'password'"
}

function install_utils() {
    cp $SHARED_FOLDER/aliases /home/vagrant/.bash_aliases
}
