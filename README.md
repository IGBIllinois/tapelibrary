# Tape Library

- Keep track of Backup Tapes
- Use on https://www-app.igb.illinois.edu/tapelibrary/

# Installation

## Prerequisites
- PHP
- PHP Mysql
- PHP LDAP
- composer


1.  Create an alias in apache that points to html folder
2.  Run sql/tapelibrary.sql on the mysql server.
```mysql -u root -p tapelibrary < sql/tapelibrary.sql```
3.  Create a user/password on the mysql server which has select/insert/delete/update permissions on the cluster_accounting database.
4.  Edit /conf/settings.inc.php to reflect your settings.
5.  Run composer to install php dependencies
```composer install```
6. Copy the /vendor/datatables directory to the  /html/vendor directory
```cp -rf vendor/datatables/datatables html/vendor/```

