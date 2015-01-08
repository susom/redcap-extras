Using Console Tools in the REDCap VM
====================================

Purpose
-------

Once the REDCap VM is started, you can interact with it through your web
browser of your host computer or at the shell of the guest.  This guide
describes the commands that work within the guest to simplify interaction with
the REDCap server.

Manipulating Services
---------------------

REDCap depends on properly running Apache and MySQL servers.  If they did not
start correctly when you started the or need to restart, these commands might
be helpful.

- restart_httpd
- restart_mysql

REDCap Database
--------------------------

These commands simplify interaction with the REDCap database:

- db - Start a mysql client and connect to the redcap database
- remove_ban - Remove all data from the redcap_ip_banned table that records IP address bans on the API interface
- show_columns - Display all columns on all REDCap tables
- show_logs - Display records in the redcap_log_event table
- redcap_version - Display the redcap version string from the database.


Miscellaneous
-------------

- check_redcap - performs a simple status check on the REDCap application
