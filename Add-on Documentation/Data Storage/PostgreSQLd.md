# PostgreSQLd: Dedicated PostgreSQL Add-on

High-availability, dedicated PostgreSQL databases are available for
mission-critical production deployments. The dedicated PostgreSQL Add-on is
based on [Amazon RDS] and uses master-slave replicated Multi-AZ instances. Read
slaves or reserved instances are currently not supported via the Add-on, but
you can always create a custom RDS instance in the EU region and connect your
app to it. We recommend using the [Config Add-on] to make the credentials of
the self-managed RDS instance available to your app.

## Features of the cloudControl PostgreSQLd Add-on

The PostgreSQLd Add-on comes with the following features:

1. Easy, Managed Deployments
    - Pre-configured Parameters - You can simply launch a PostgreSQL Instance
    and connect your application within minutes without additional
    configuration.
    - Automatic Software Patching - cloudControl will make sure that the
    PostgreSQL software powering your deployment stays up-to-date with the
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

4. PostgreSQL Features Supported
    - PostGIS - PostGIS is a spatial database extender for PostgreSQL
    object-relational database. It adds support for geographic objects
    allowing you to run location queries to be run in SQL.
    - Language Extensions - PostgreSQL allows procedural languages to be loaded
    into the database through extensions. Three language extensions are
    included with PostgreSQL to support Perl, pgSQL and Tcl.
    - Full Text Search Dictionaries - PostgreSQL supports Full Text Searching
    that provides the capability to identify natural-language documents that
    satisfy a query, and optionally to sort them by relevance to the query.
    Dictionaries, besides improving search quality, normalization and removal
    of stop words also improve performance of queries.
    - HStore, JSON Data Types - PostgreSQL includes support for ‘JSON’ data
    type and two JSON functions. These allow return of JSON directly from the
    database server. PostgreSQL has an extension that implements the ‘hstore’
    data type for storing sets of key/value pairs within a single PostgreSQL
    value.
    - Core PostgreSQL engine features - For a detailed list of PostgreSQL core
    engine features, please refer here.

5. Dashboard
    - View key operational metrics like CPU/ Memory/ Storage/ Connections for your DB Instance deployments via [Webconsole].

## Adding the PostgreSQLd Add-on

To add the PostgreSQLd Add-on, use the `addon.add` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.add postgresqld.OPTION
~~~
Replace `postgresqld.OPTION` with a valid option, e.g. `postgresqld.small`. See
[PostgreSQLd] in the Add-on Marketplace for pricing and options.

Please note: After adding a dedicated PostgreSQL database, it can take up to 30
minutes before the instance is available. Also, the credentials will only be
available after the instance is up and running.

## Upgrading the PostgreSQLd Add-on

To upgrade from a smaller to a more powerful plan, use the `addon.upgrade` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade postgresqld.OPTION_OLD postgresqld.OPTION_NEW
~~~

Please note: Upgrading the instance types is a multi-step process that first
upgrades the secondary, then promotes the secondary to the new master. After
this, the old master is updated and becomes the new secondary. This process
can take up to 30 minutes and can involve a 3 to 10 minute downtime.

## Downgrading the PostgreSQLd Add-on

To downgrade to a smaller plan, use the `addon.downgrade` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade postgresqld.OPTION_OLD postgresqld.OPTION_NEW
~~~

Please note: It is only possible to downgrade to plans with matching storage
sizes.

## Removing the PostgreSQLd Add-on

The PostgreSQLd Add-on can be removed from the deployment by using the
`addon.remove` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.remove postgresqld.OPTION
~~~

**Attention:** Removing the PostgreSQLd Add-on deletes all data in the database.

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

External access to the PostgreSQLd Add-on is available through an SSL encrypted
connection by using the `psql` command line client:
~~~bash
$ psql "host=POSTGRESQLD_HOST dbname=POSTGRESQLD_DATABASE sslmode=require" -U POSTGRESQLD_USERNAME
~~~

Or alternatively using URL:
~~~bash
$ psql POSTGRESQLD_URL
~~~

Replace the uppercase variables with the corresponding values shown by the `addon` command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon postgresqld.OPTION
Addon                    : postgresqld.small
 Settings
   POSTGRESQLD_PASSWORD          : SOME_SECRET_PASSWORD
   POSTGRESQLD_USER              : SOME_SECRET_USER
   POSTGRESQLD_HOST              : SOME_HOST.eu-west-1.rds.amazonaws.com
   POSTGRESQLD_DATABASE          : SOME_DATABASE_NAME
   POSTGRESQLD_PORT              : 5432
   POSTGRESQLD_URL               : SOME_DATABASE_URL
~~~

Similarly, imports and exports are equally simple.

To **export** your data use the `pg_dump` command:
~~~bash
$ pg_dump "host=POSTGRESQLD_HOST dbname=POSTGRESQLD_DATABASE sslmode=require" -U POSTGRESQLD_USERNAME > PG_DUMP
~~~
Or export your data using URL:
~~~bash
$ pg_dump POSTGRESQLD_URL > PG_DUMP
~~~

To **import** an sql file into a PostgreSQL database use the following command:
~~~bash
$ psql "host=POSTGRESQLD_HOST dbname=POSTGRESQLD_DATABASE sslmode=require" -U POSTGRESQLD_USERNAME < PG_DUMP
~~~
Or import your data using URL:
~~~bash
$ psql POSTGRESQLD_URL < PG_DUMP
~~~

[Amazon RDS]: http://aws.amazon.com/rds/
[Config Add-on]: https://www.cloudcontrol.com/add-ons/config
[PostgreSQLd]: https://www.cloudcontrol.com/add-ons/postgresqld
[Add-on Credentials]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons
[Email us]: mailto:support@cloudcontrol.de
[Webconsole]: https://www.cloudcontrol.com/console/login