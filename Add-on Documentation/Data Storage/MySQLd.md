# MySQLd: Dedicated MySQL Add-on

High-availability, dedicated MySQL databases are available for
mission-critical production deployments. The dedicated MySQL Add-on is
based on [Amazon RDS] and uses master-slave replicated Multi-AZ instances. Read
slaves or reserved instances are currently not supported via the Add-on, but
you can always create a custom RDS instance in the EU region and connect your
app to it. We recommend using the [Config Add-on] to make the credentials
of the self-managed RDS instance available to your app.

## Features of the cloudControl MySQLd Add-on

The MySQLd Add-on comes with the following features:

1. Easy, Managed Deployments
    - Pre-configured Parameters - You can simply launch a MySQL Instance
    and connect your application within minutes without additional
    configuration.
    - Automatic Software Patching - cloudControl will make sure that the
    MySQL software powering your deployment stays up-to-date with the
    latest patches.

2. Backup & Recovery
    - Automated Backups - Turned on by default, the automated backup feature
    enables point-in-time recovery for your instance.
    - DB Snapshots - DB Snapshots are available. [Email us] for more details.

3. High Availability
    - Multi-AZ Deployments - Once you create or modify your DB Instance, we
    will automatically provision and manage a “standby” replica in a
    different Availability Zone (independent infrastructure in a physically
    separate location). Database updates are made concurrently on the primary
    and standby resources to prevent replication lag.

4. Dashboard
    - View key operational metrics like CPU/ Memory/ Storage/ Connections for your DB Instance deployments via [Webconsole].

## Adding the MySQLd Add-on

To add the MySQLd Add-on use the `addon.add` command:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add mysqld.OPTION
~~~
Replace `mysqld.OPTION` with a valid option, e.g. `mysqld.small`. See
[MySQLd] in the Add-on Marketplace for pricing and options.

Please note: After adding a dedicated MySQL database, it can take up to 30 minutes before the instance is available. Also the credentials will only be available after the instance is up and running.

## Upgrading the MySQLd Add-on

To upgrade from a smaller to a more powerful plan use the `addon.upgrade` command:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade mysqld.OPTION_OLD mysqld.OPTION_NEW
~~~

Please note: Upgrading the instance types is a multi step process that first upgrades the secondary, then promotes the secondary to the new master and after that upgrades also the old master and makes it the new secondary. This process can take up to 30 minutes and can involve a 3 to 10 minute downtime.

## Downgrading the MySQLd Add-on

To downgrade to a smaller plan, use the `addon.downgrade` command:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade mysqld.OPTION_OLD mysqld.OPTION_NEW
~~~

Please note: It is only possible to downgrade to plans with matching storage
sizes.


## Removing the MySQLd Add-on

The MySQLd Add-on can be removed from the deployment by using the `addon.remove` command:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove mysqld.OPTION
~~~

**Attention:** Removing the MySQLd Add-on deletes all data in the database.

## Replication and Failover

All instances are master-slave replicated across two different availability
zones. In case of a failure of the master, an automatic failover to the slave
will trigger to restore availability. This failover process takes usually
between 3 and 10 minutes.

## Database Credentials

### Internal Access

It's recommended to the read database credentials from the creds.json file. The
location of the file is available in the `CRED_FILE` environment variable.
Reading the credentials from the creds.json file ensures your app is always
using the correct credentials. For detailed instructions on how to use the
creds.json file, please refer to the section about [Add-on Credentials] in the
general documentation.

### External Access

External access to the MySQLd Add-on is available through an SSL-encrypted connection by following these simple steps:

 1. Download the [certificate file] to your local machine.
 1. Connect to the database using an SSL encrypted connection.

The following example uses the MySQL command line tool:

~~~
$ mysql -u MYSQLD_USERNAME -p --host=MYSQLD_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem
~~~

Replace the uppercase variables with the corresponding values shown by the `addon` command:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon mysqld.OPTION
Addon                    : mysqld.small
 Settings
   MYSQLD_PASSWORD          : SOME_SECRET_PASSWORD
   MYSQLD_USER              : SOME_SECRET_USER
   MYSQLD_HOST              : SOME_HOST.eu-west-1.rds.amazonaws.com
   MYSQLD_DATABASE          : SOME_DATABASE_NAME
   MYSQLD_PORT              : 3306
   MYSQLD_URL               : SOME_DATABASE_URL
~~~

Similarly, imports and exports are equally simple.

To **export** your data use the `mysqldump` command:
~~~
$ mysqldump -u MYSQLD_USERNAME -p --host=MYSQLD_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem MYSQLD_DATABASE > MYSQLD_DATABASE.sql
~~~

To **import** an sql file into a MySQL database use the following command:
~~~
$ mysql -u MYSQLD_USER -p --host=MYSQLD_SERVER --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem MYSQLD_DATABASE < MYSQLD_DATABASE.sql
~~~


[Amazon RDS]: http://aws.amazon.com/rds/
[Config Add-on]: https://www.cloudcontrol.com/add-ons/config
[MySQLd]: https://www.cloudcontrol.com/add-ons/mysqld
[Add-on Credentials]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons
[Email us]: mailto:support@cloudcontrol.de
[certificate file]: http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem
[Webconsole]: https://www.cloudcontrol.com/console/login
