# DBInsights

DbInsights provides you with business intelligence insights and database analytics with minimum efforts on your part.

## Adding DBInsights

The DBInsights Add-on can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add dbinsights.PLAN
~~~

When added, DBInsights automatically creates a new user account with your email adress. You can access the insights for the database within the [web console](https://www.cloudcontrol.com/console) (go to the specific deployment and click the link "dbinsights.OPTION").

## Upgrade DBInsights

Upgrading to another version of DBInsights is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade dbinsights.OPTION_OLD dbinsights.OPTION_NEW 
~~~

## Downgrade DBInsights

Downgrading to another version of DBInsights is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade dbinsights.OPTION_OLD dbinsights.OPTION_NEW 
~~~

## Removing DBInsights

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove dbinsights.OPTION
~~~

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "DBINSIGHTS":{
      "DBINSIGHTS_URL":"https://dbinsights.com/accounts/youraccount",
   }
}
~~~

