# MySQLs: Shared MySQL Add-on

Every deployment can access a highly available shared MySQL add-on.

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

All instances are master-slave replicated. In case of a failure of the master, 
an automatic failover to the slave will trigger to restore availability. 
This failover process takes usually between 3 and 10 minutes.

## Database Credentials

### Internal Access

It's recommended to the read database credentials from the creds.json file. The
location of the file is available in the `CRED_FILE` environment variable.
Reading the credentials from the creds.json file ensures your app is always
using the correct credentials. For detailed instructions on how to use the
creds.json file please refer to the section about
[Add-on Credentials](https://www.exoscale.ch/dev-center/Platform%20Documentation#add-ons)
in the general documentation.
