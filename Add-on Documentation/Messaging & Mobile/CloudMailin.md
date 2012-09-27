# CloudMailin

CloudMailIn allows you to receive any volume of incoming email via a Webhook. You are given an email address that will forward any incoming message to your app, as an HTTP POST request, within milliseconds. You can also seamlessly check the delivery status of each of your incoming emails via the dashboard, bounce emails that you do not wish to receive and use your own domain name.

## Adding CloudMailin

CloudMailin can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add cloudmailin.OPTION
~~~

## Upgrade CloudMailin

Upgrading to another version of CloudMailin is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade cloudmailin.OPTION_OLD cloudmailin.OPTION_NEW 
~~~

## Downgrade CloudMailin

Downgrading to another version of CloudMailin is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade cloudmailin.OPTION_OLD cloudmailin.OPTION_NEW 
~~~

## Removing CloudMailin

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove cloudmailin.OPTION
~~~

## Internal Access Credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.

The JSON file has the following structure:

~~~
{
   "CLOUDMAILIN":{
      "CLOUDMAILIN_SECRET":"12341337asdf1335asdfqwert",
      "CLOUDMAILIN_USERNAME":"depxasdfqwert@cloudcontrolled.com",
      "CLOUDMAILIN_PASSWORD":"1337asdf1234",
      "CLOUDMAILIN_FORWARD_ADDRESS":"12345asdf73@cloudmailin.net"
   }
}
~~~

## CloudMailin Code Example

You will find an example on how to use CloudMailIn within your app at [Github](https://github.com/cloudControl/CloudMailInAddonUsage).

