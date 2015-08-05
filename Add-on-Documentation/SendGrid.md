# SendGrid

SendGrid's cloud-based email infrastructure relieves businesses of the cost and complexity of maintaining custom email systems.

**Note**: *dotCloud operates on GCE, therefore the standard SMTP-Ports (i.e., 25, 465, 587) are blocked. Please make sure that you have set the SMTP-Port in your application to port: **2525** *

## Adding SendGrid

The SendGrid Add-on can be added to every deployment with:

~~~
$ dcapp APP_NAME/DEP_NAME addon.add sendgrid.OPTION
~~~

When added, SendGrid automatically creates a new user account with your email adress. You can manage your SendGrid Add-on easily within the web console (go to the specific deployment and click the link "sendgrid.OPTION").

## Upgrade SendGrid

Upgrading to another version of SendGrid is easily done:

~~~
$ dcapp APP_NAME/DEP_NAME addon.upgrade sendgrid.OPTION_OLD sendgrid.OPTION_NEW
~~~

## Downgrade SendGrid

Downgrading to another version of SendGrid is easily done:

~~~
$ dcapp APP_NAME/DEP_NAME addon.downgrade sendgrid.OPTION_OLD sendgrid.OPTION_NEW
~~~

## Removing SendGrid

~~~
$ dcapp APP_NAME/DEP_NAME addon.remove sendgrid.OPTION
~~~

Once you added a SendGrid plan, you need to login once at http://sendgrid.com with your Add-on credentials (see below) and complete their setup wizard to activate your account. For additional information please visit the SendGrid documentation.

# Add-on credentials

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://next.dotcloud.com/dev-center/platform-documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
    "SENDGRID": {
        "SENDGRID_USERNAME": "SOME_NAME@example.com",
        "SENDGRID_PASSWORD": "SOME_SECRET_PASSWORD",
    }
}
~~~
