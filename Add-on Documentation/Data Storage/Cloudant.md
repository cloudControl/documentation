# Cloudant

With dotCloud, every deployment can feature a highly available hosted CouchDB provided by [Cloudant](https://cloudant.com/).

## Adding or removing the Cloudant Add-on

The database comes in different sizes and prices. It can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add cloudant.OPTION
~~~
*.option* represents the plan size, e.g. cloudant.basic


## Upgrade the Cloudant Add-on

Upgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade cloudant.OPTION_OLD cloudant.OPTION_NEW
~~~

##Downgrade the Cloudant Add-on

Downgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade cloudant.OPTION_OLD cloudant.OPTION_NEW
~~~

##Removing the Cloudant add-on

Similarily, an add-on can also be removed from the deployment easily. The costs only apply for the time the add-on was active:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove cloudant.OPTION
~~~
#Database credentials

##Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "CLOUDANT":{
      "CLOUDANT_DATABASE":"depx11xxx22",
      "CLOUDANT_PASSWORD":"asdfasdfasdf",
      "CLOUDANT_PORT":"3306",
      "CLOUDANT_HOSTNAME":"cloudantdb.asdf.eu-1.rds.amazonaws.com",
      "CLOUDANT_USERNAME":"depx11xxx22"
   }
}
~~~
