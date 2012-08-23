# Shared MySQL Add-on

Every deployment can access a highly available shared MySQL add-on based on [Amazon RDS](http://aws.amazon.com/rds/). The shared MySQL add-on is recommended for development and low traffic apps only. For medium to high traffic apps we recommend one of the dedicated [MySQLd add-on](https://www.cloudcontrol.com/add-ons/mysqld) plans.

## Adding or removing the shared MySQL Add-on

The database comes in different sizes and prices. It can be added using the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add mysqls.OPTION
~~~
Replace `mysqls.OPTION` with a valid option, e.g. `mysqls.free`.

## Upgrading the MySQL Add-on

To upgrade from one plan to another use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Downgrade the MySQL Add-on

To downgrade from the current plan to a smaller one use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade mysqls.OPTION_OLD mysqls.OPTION_NEW
~~~

## Removing the MySQL add-on

Similarily, an add-on can also be removed from the deployment by using the addon.remove command.

**Attention:** Removing the MySQLs add-on deletes all data in the database.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove mysqls.OPTION
~~~

## Replication and Failover

All instances are master-slave replicated accross two different availability zones. In case of a failure of the master, an automatic failover to the slave will trigger to restore availability. This failover process takes usually between 3 and 10 minutes.

## Database credentials

### Internal access credentials

For the add-on credentials we provide a json file. You can read out the location of the file from the env variable `CRED_FILE`. The benefit of using the environment variable is that you don't need to hardcode the add-on credentials, so if the add-on vendor changes the credentials, your app still works without your intervention. See an example of code reading the `CRED_FILE` variable here.

 

The JSON file has the following structure:
~~~
{
   "MYSQLS":{
      "MYSQLS_DATABASE":"depx11xxx22",
      "MYSQLS_PASSWORD":"asdfasdfasdf",
      "MYSQLS_PORT":"3306",
      "MYSQLS_HOSTNAME":"mysqlsdb.asdf.eu-1.rds.amazonaws.com",
      "MYSQLS_USERNAME":"depx11xxx22"
   }
}
~~~

### External access

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

MYSQLS_PASSWORD    : kIYUZGknx6cy
MYSQLS_USERNAME    : dep12345678
MYSQLS_HOSTNAME    : mysqlddb.io9si2var48.eu-west-1.rds.amazonaws.com:3306
MYSQLS_DATABASE    : dep12345678
~~~

Likewise imports and exports are equally simple.

**Export**
Use mysqldump to create a dump of your database.
~~~
$ mysqldump -u MYSQLS_USERNAME -p --host=MYSQLS_HOSTNAME --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem MYSQL_DATABASE > MYSQL_DATABASE.sql
~~~

**Import**
To import that file into a MySQL database use the following command.
~~~
$ mysql -u MYSQLS_USER -p --host=MYSQLS_SERVER --ssl-ca=PATH_TO_CERTIFICATE/mysql-ssl-ca-cert.pem MYSQL_DATABASE < MYSQL_DATABASE.sql
~~~

