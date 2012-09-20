# MySQLs: Shared MySQL Add-on

Every deployment can access a highly available shared MySQL add-on based on [Amazon RDS](http://aws.amazon.com/rds/). The shared MySQL add-on is recommended for development and low traffic apps only. For medium to high traffic apps we recommend one of the dedicated [MySQLd add-on](https://www.cloudcontrol.com/add-ons/mysqld) plans.

## Adding the MySQLs Add-on

The database comes in different sizes and prices. It can be added using the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add mysqls.OPTION
~~~
Replace `mysqls.OPTION` with a valid option, e.g. `mysqls.free`.

## Upgrading the MySQLs Add-on

To upgrade from one plan to another use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Downgrading the MySQLs Add-on

To downgrade from the current plan to a smaller one use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Removing the MySQLs Add-on

Similarily, an add-on can also be removed from the deployment by using the addon.remove command.

**Attention:** Removing the MySQLs add-on deletes all data in the database.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove mysqls.OPTION
~~~

## Replication and Failover

All instances are master-slave replicated accross two different availability zones. In case of a failure of the master, an automatic failover to the slave will trigger to restore availability. This failover process takes usually between 3 and 10 minutes.

## Database Credentials

### Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.

### External Access

External access to the MySQLs add-on is available through an SSL encrypted connection by following these simple steps.

 1. Download the [certificate file](http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem) to your local machine.
 1. Connect to the database using an SSL encrypted connection.

The following example uses the MySQL command line tool.

~~~
$ mysql -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem
~~~

Replace the uppercase variables with the corresponding values shown by the addon command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon mysqls.OPTION
Addon : mysqls.512mb

Settings

MYSQLS_PASSWORD    : SOME_SECRET_PASSWORD
MYSQLS_USERNAME    : SOME_SECRET_USERNAME
MYSQLS_HOSTNAME    : SOME_HOST.eu-west-1.rds.amazonaws.com:3306
MYSQLS_DATABASE    : SOME_DATABASE_NAME
~~~

Likewise imports and exports are equally simple.

To **export** your data use the mysqldump command.
~~~
$ mysqldump -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem MYSQLS_DATABASE > MYSQLS_DATABASE.sql
~~~

To **import** an sql file into a MySQL database use the following command.
~~~
$ mysql -u MYSQLS_USER -p --host=MYSQLS_SERVER --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem MYSQLS_DATABASE < MYSQLS_DATABASE.sql
~~~

