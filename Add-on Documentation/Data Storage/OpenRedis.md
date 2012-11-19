# OpenRedis (Beta)

OpenRedis provides hosted Redis services available to all cloudControl apps.

## Adding or removing the OpenRedis Add-on

The Add-on comes in different sizes and prices. It can be added by executing the command addon.add:


~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add openredis.OPTION
~~~
".option" represents the plan size, e.g. openredis.test

## Upgrade the OpenRedis Add-on

Upgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade openredis.OPTION_OLD openredis.OPTION_NEW
~~~

## Downgrade the OpenRedis Add-on

Downgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade openredis.OPTION_OLD openredis.OPTION_NEW
~~~

## Removing the OpenRedis Add-on

Similarily, an Add-on can also be removed from the deployment easily. The costs only apply for the time the Add-on was active:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove openredis.OPTION
~~~

# Add-on credentials

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

