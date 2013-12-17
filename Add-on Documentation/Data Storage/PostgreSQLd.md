# PostgreSQLd: Dedicated PostgreSQL Add-on

High available dedicated PostgreSQL databases are available for mission
critical production deployments. The dedicated PostgreSQL Add-on is based on
[Amazon RDS](http://aws.amazon.com/rds/) and is using master-slave replicated
Multi-AZ instances. Read slaves or reserved instances are currently not
supported via the Add-on but you can always create a custom RDS instance in the
EU region and connect your app to that. We recommend using the [Config
add-on](https://www.cloudcontrol.com/add-ons/config) to make the credentials of
the self managed RDS instance available to your app.

## Adding the PostgreSQLd Add-on

To add the PostgreSQLd add-on use the `addon.add` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.add postgresqld.OPTION
~~~
Replace `postgresqld.OPTION` with a valid option, e.g. `postgresqld.small`.

Please note: After adding a dedicated PostgreSQL database it can take up to 30
minutes before the instance is available. Also the credentials will only be
available after the instance is up and running.

## Upgrading the PostgreSQLd Add-on

To upgrade from a smaller to a more powerful plan use the `addon.upgrade` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade postgresqld.OPTION_OLD postgresqld.OPTION_NEW
~~~

Please note: Upgrading the instance types is a multi step process that first
upgrades the secondary, then promotes the secondary to the new master and after
that upgrades also the old master and makes it the new secondary. This process
can take up to 30 minutes and can involve a 3 to 10 minute downtime.

## Downgrading the PostgreSQLd Add-on

To downgrade to a smaller plan use the `addon.downgrade` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade postgresqld.OPTION_OLD postgresqld.OPTION_NEW
~~~

Please note: Downgrading is only possible to plans with matching storage sizes.

## Removing the PostgreSQLd Add-on

The PostgreSQLd add-on can be removed from the deployment by using the `addon.remove` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.remove postgresqld.OPTION
~~~

**Attention:** Removing the PostgreSQLd add-on deletes all data in the database.

## Replication and Failover

All instances are master-slave replicated accross two different availability
zones. In case of a failure of the master, an automatic failover to the slave
will trigger to restore availability. This failover process takes usually
between 3 and 10 minutes.

## Database Credentials

### Internal Access

It's recommended to the read database credentials from the creds.json file. The
location of the file is available in the `CRED_FILE` environment variable.
Reading the credentials from the creds.json file ensures your app is always
using the correct credentials. For detailed instructions on how to use the
creds.json file please refer to the section about [Add-on
Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons)
in the general documentation.

### External Access

External access to the PostgreSQLd add-on is available through an SSL encrypted
connection by using the `psql` command line client:
~~~bash
$ psql "host=POSTGRESQLD_HOST dbname=POSTGRESQLD_DATABASE sslmode=require" -U POSTGRESQLD_USERNAME
~~~

Replace the uppercase variables with the corresponding values shown by the addon command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon postgresqld.OPTION
Addon                    : postgresqld.small
 Settings
   POSTGRESQLD_PASSWORD          : SOME_SECRET_PASSWORD
   POSTGRESQLD_USER              : SOME_SECRET_USER
   POSTGRESQLD_HOST              : SOME_HOST.eu-west-1.rds.amazonaws.com
   POSTGRESQLD_DATABASE          : SOME_DATABASE_NAME
   POSTGRESQLD_PORT              : 5432
~~~

Likewise imports and exports are equally simple.

To **export** your data use the `pg_dump` command:
~~~bash
$ pg_dump "host=POSTGRESQLD_HOST dbname=POSTGRESQLD_DATABASE sslmode=require" -U POSTGRESQLD_USERNAME > PG_DUMP
~~~

To **import** an sql file into a PostgreSQL database use the following command:
~~~bash
$ psql "host=POSTGRESQLD_HOST dbname=POSTGRESQLD_DATABASE sslmode=require" -U POSTGRESQLD_USERNAME < PG_DUMP
~~~

