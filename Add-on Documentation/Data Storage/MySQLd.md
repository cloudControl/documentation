# MySQLd: Dedicated MySQL Add-on

High-availability, dedicated MySQL databases are available for
mission-critical production deployments. The dedicated MySQL Add-on is
based on [Google Cloud SQL] and synchronous replication in multiple zones.

## Features of the dotCloud MySQLd Add-on

The MySQLd Add-on comes with the following features:

1. Easy, Managed Deployments
    - Pre-configured Parameters - You can simply launch a MySQL Instance
    and connect your application within minutes without additional
    configuration.
    - Automatic Software Patching - dotCloud will make sure that the
    MySQL software powering your deployment stays up-to-date with the
    latest patches.

2. Backup & Recovery
    - Automated Backups - Turned on by default, the automated backup feature
    enables point-in-time recovery for your instance.
    - DB Snapshots - DB Snapshots are available. [Email us] for more details.

3. High Availability
    - Multiple Zones Data Replication - Your data is replicated in many geographic locations
    as standard. Failover between them is handled automatically by us. Your data is
    safe and your database is available even in the event of a major failure in one location.

4. Dashboard
    - View key operational metrics like Storage or Connections for your DB Instance deployments via [Webconsole]. 
    - Download the SSL Certificate for encrypted external connections. 

5. Security
    - All data is encrypted when on internal networks and when stored in database
    tables and temporary files.
    - Connections can be encrypted using SSL.

## Adding the MySQLd Add-on

To add the MySQLd Add-on use the `addon.add` command:

~~~
$ dcapp APP_NAME/DEP_NAME addon.add mysqld.OPTION
~~~
Replace `mysqld.OPTION` with a valid option, e.g. `mysqld.d0`. See
[MySQLd] in the Add-on Marketplace for pricing and options.

Please note: After adding a dedicated MySQL database, it can take up to 5 minutes before the instance is available. Also the credentials will only be available after the instance is up and running.

## Upgrading the MySQLd Add-on

To upgrade from a smaller to a more powerful plan use the `addon.upgrade` command:

~~~
$ dcapp APP_NAME/DEP_NAME addon.upgrade mysqld.OPTION_OLD mysqld.OPTION_NEW
~~~

Please note: Upgrading the instance types is a multi step process that first upgrades the secondary, then promotes the secondary to the new master and after that upgrades also the old master and makes it the new secondary. This process can take up to 65 minutes and can involve a 3 to 10 minute downtime.

## Downgrading the MySQLd Add-on

To downgrade to a smaller plan, use the `addon.downgrade` command:

~~~
$ dcapp APP_NAME/DEP_NAME addon.downgrade mysqld.OPTION_OLD mysqld.OPTION_NEW
~~~

Please note: It is only possible to downgrade to plans with matching storage
sizes.


## Removing the MySQLd Add-on

The MySQLd Add-on can be removed from the deployment by using the `addon.remove` command:

~~~
$ dcapp APP_NAME/DEP_NAME addon.remove mysqld.OPTION
~~~

**Attention:** Removing the MySQLd Add-on deletes all data in the database.

## Replication and Failover

All instances replicated in multiple zones. In the unlikely event of a zone outage,
instances fail over to another, available, zone automatically. Failover is designed to
be transparent to your applications, so that after failover, an instance has the same
instance name, IP address, and firewall rules. During the failover there will typically
be a few seconds downtime as the instance starts up in a new zone.

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

 1. Download the SSL certificate file from the Dashboard via [Webconsole].
 1. Connect to the database using an SSL encrypted connection.

The following example uses the MySQL command line tool:

~~~
$ mysql -u MYSQLD_USERNAME -p --host=MYSQLD_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/ca-cert.pem
~~~

Replace the uppercase variables with the corresponding values shown by the `addon` command:

~~~
$ dcapp APP_NAME/DEP_NAME addon mysqld.OPTION
Addon                    : mysqld.d0
 Settings
   MYSQLD_PASSWORD          : SOME_SECRET_PASSWORD
   MYSQLD_USER              : SOME_SECRET_USER
   MYSQLD_HOST              : SOME_HOST
   MYSQLD_DATABASE          : SOME_DATABASE_NAME
   MYSQLD_PORT              : 3306
   MYSQLD_URL               : SOME_DATABASE_URL
~~~

Similarly, imports and exports are equally simple.

To **export** your data use the `mysqldump` command:
~~~
$ mysqldump -u MYSQLD_USERNAME -p --host=MYSQLD_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/ca-cert.pem MYSQLD_DATABASE > MYSQLD_DATABASE.sql
~~~

To **import** an sql file into a MySQL database use the following command:
~~~
$ mysql -u MYSQLD_USER -p --host=MYSQLD_SERVER --ssl-ca=PATH_TO_CERTIFICATE/ca-cert.pem MYSQLD_DATABASE < MYSQLD_DATABASE.sql
~~~


[Google Cloud SQL]: https://developers.google.com/cloud-sql/
[Config Add-on]: https://next.dotcloud.com/add-ons/config
[MySQLd]: https://next.dotcloud.com/add-ons/mysqld
[Add-on Credentials]: https://next.dotcloud.com/dev-center/platform-documentation#add-ons
[Email us]: mailto:support@dotcloud.com
[Webconsole]: https://next.dotcloud.com/console/login
