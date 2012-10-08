# Searchify (Alpha)

Searchify provides hosted real-time full-text search services available to all cloudControl apps. Searchify runs IndexTank, and is a drop-in replacement for IndexTank users.

## Adding or removing the Searchify Add-on

The Add-on comes in different sizes and prices. It can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add searchify.OPTION
~~~

".option" represents the plan size, e.g. searchify.developer. (The developer account is free during alpha and beta. Once out of beta, users can upgrade to one of the normal plans).

## Upgrade the Searchify Add-on

Upgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade searchify.OPTION_OLD searchify.OPTION_NEW
~~~

## Downgrade the Searchify Add-on

Downgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade searchify.OPTION_OLD searchify.OPTION_NEW
~~~

## Removing the Searchify Add-on

Similarily, an Add-on can also be removed from the deployment easily. The costs only apply for the time the Add-on was active:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove searchify.OPTION
~~~

# Add-on credentials

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.

