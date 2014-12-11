# MySQLs: Shared MySQL Add-on

Every deployment can access a highly available shared MySQL Add-on based on [Google Cloud SQL](https://cloud.google.com/sql/).
The shared MySQL Add-on is recommended for development and low-traffic apps only. For medium to high-traffic apps we
recommend one of the dedicated [MySQLd Add-on](https://next.dotcloud.com/add-ons/mysqld) plans.

## Adding the MySQLs Add-on

The database comes in different sizes and prices. It can be added using the addon.add command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.add mysqls.OPTION
~~~
Replace `mysqls.OPTION` with a valid option, e.g. `mysqls.free`.

## Upgrading the MySQLs Add-on

To upgrade from one plan to another use the addon.upgrade command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.upgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Downgrading the MySQLs Add-on

To downgrade from the current plan to a smaller one use the addon.downgrade command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.downgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Removing the MySQLs Add-on

Similarily, an Add-on can also be removed from the deployment by using the addon.remove command.

**Attention:** Removing the MySQLs Add-on deletes all data in the database.

~~~
$ dcapp APP_NAME/DEP_NAME addon.remove mysqls.OPTION
~~~

## Replication and Failover

Your data is replicated in many geographic locations as standard. Failover between them is handled automatically by us. Your data is safe and your database is available even in the event of a major failure in one location

## Database Credentials

### Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available
in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always
using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the
section about [Add-on Credentials](https://next.dotcloud.com/dev-center/platform-documentation#add-ons) in
the general documentation.

### External Access

External access to the MySQLs Add-on is available through an SSL encrypted connection by following these simple steps.

 1. Download the [certificate file](https://console.developers.google.com/m/cloudstorage/b/dotcloudapp-ca/o/addon_mysqls_ca.pem) to your local machine.
 1. Connect to the database using an SSL encrypted connection.

The following example uses the MySQL command line tool.

~~~
$ mysql -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/addon_mysqls_ca.pem
~~~

Replace the uppercase variables with the corresponding values shown by the addon command.

~~~
$ dcapp APP_NAME/DEP_NAME addon mysqls.OPTION
Addon : mysqls.512mb

Settings

MYSQLS_PASSWORD    : SOME_SECRET_PASSWORD
MYSQLS_USERNAME    : SOME_SECRET_USERNAME
MYSQLS_HOSTNAME    : SOME_HOSTNAME
MYSQLS_DATABASE    : SOME_DATABASE_NAME
~~~

Likewise imports and exports are equally simple.

To **export** your data use the mysqldump command.
~~~
$ mysqldump -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/addon_mysqls_ca.pem MYSQLS_DATABASE > MYSQLS_DATABASE.sql
~~~

To **import** an sql file into a MySQL database use the following command.
~~~
$ mysql -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/addon_mysqls_ca.pem MYSQLS_DATABASE < MYSQLS_DATABASE.sql
~~~

