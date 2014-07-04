# MySQLs: Shared MySQL Add-on

Every deployment can access a highly available shared MySQL add-on with
databases guaranteed to be located in exoscale datacenters in Switzerland.

## Adding the MySQLs Add-on

The database comes in different sizes and prices. It can be added using the addon.add command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.add mysqls.OPTION
~~~
Replace `mysqls.OPTION` with a valid option, e.g. `mysqls.free`.

## Upgrading the MySQLs Add-on

To upgrade from one plan to another use the addon.upgrade command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.upgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Downgrading the MySQLs Add-on

To downgrade from the current plan to a smaller one use the addon.downgrade command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.downgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Removing the MySQLs Add-on

Similarily, an add-on can also be removed from the deployment by using the addon.remove command.

**Attention:** Removing the MySQLs add-on deletes all data in the database.

~~~
$ exoapp APP_NAME/DEP_NAME addon.remove mysqls.OPTION
~~~

## Replication and Failover

All data is synchronously replicated in our robust multi master 
[MariaDB](https://mariadb.org/) [Galera](http://galeracluster.com/) cluster. No slave lag or lost transactions. We provide high available access with our smart load balancers. By periodically checks, nodes in maintenance or failure state are automatically excluded from the load balancers database backend pool. That assures that requests are routed to healthy nodes only.

## Database Credentials

### Internal Access

It's recommended to the read database credentials from the creds.json file. The
location of the file is available in the `CRED_FILE` environment variable.
Reading the credentials from the creds.json file ensures your app is always
using the correct credentials. For detailed instructions on how to use the
creds.json file please refer to the section about
[Add-on Credentials](https://community.exoscale.ch/apps/Platform%20Documentation#add-ons)
in the general documentation.

Most database drivers provide a reconnect on connection issues when you add **autoReconnect=true** parameter to your database uri. This should be enabled to have the most stable setup. For example with Java:
~~~
jdbc:mysql://{MYSQLS_HOSTNAME}:{MYSQLS_PORT}/{MYSQLS_DATABASE}?autoReconnect=true
~~~


### External Access

External access to the MySQLs add-on is available through an SSL encrypted connection by following these simple steps.

 1. Download the [certificate file](https://community.exoscale.ch/static/apps/ca-cert.pem) to your local machine.
 1. Connect to the database using an SSL encrypted connection.

The following example uses the MySQL command line tool.

~~~
$ mysql -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/ca-cert.pem
~~~

Replace the uppercase variables with the corresponding values shown by the addon command.

~~~
$ exoapp APP_NAME/DEP_NAME addon mysqls.OPTION
Addon : mysqls.512mb

Settings

MYSQLS_DATABASE    : SOME_DATABASE_NAME
MYSQLS_HOSTNAME    : mysql.app.exo.io
MYSQLS_PORT        : 3306
MYSQLS_PASSWORD    : SOME_SECRET_PASSWORD
MYSQLS_USERNAME    : SOME_SECRET_USERNAME
~~~

Likewise imports and exports are equally simple.

To **export** your data use the mysqldump command.
~~~
$ mysqldump -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/ca-cert.pem MYSQLS_DATABASE > MYSQLS_DATABASE.sql
~~~

To **import** an sql file into a MySQL database use the following command.
~~~
$ mysql -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/ca-cert.pem MYSQLS_DATABASE < MYSQLS_DATABASE.sql
~~~
