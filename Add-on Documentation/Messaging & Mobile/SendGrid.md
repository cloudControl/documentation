# Sendgrid

Sendgrid's cloud-based email infrastructure relieves businesses of the cost and complexity of maintaining custom email systems.

## Adding Sendgrid

The Sendgrid Add-on can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add sendgrid.OPTION
~~~

When added, Sendgrid automatically creates a new user account with your email adress. You can manage your sendgrid Add-on easily within the web console (go to the specific deployment and click the link "sendgrid.OPTION").

## Upgrade Sendgrid

Upgrading to another version of Sendgrid is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade sendgrid.OPTION_OLD sendgrid.OPTION_NEW 
~~~

## Downgrade Sendgrid

Downgrading to another version of Sendgrid is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade sendgrid.OPTION_OLD sendgrid.OPTION_NEW 
~~~

## Removing Sendgrid

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove sendgrid.OPTION
~~~

For additional information please visit the SendGrid documentation.

# Add-on credentials

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
    "SENDGRID": {
        "SENDGRID_USERNAME": "SOME_NAME@example.com",
        "SENDGRID_PASSWORD": "SOME_SECRET_PASSWORD",
    }
}
~~~

