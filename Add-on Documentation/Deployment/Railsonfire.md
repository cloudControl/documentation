# Railsonfire: Railsonfire is a simple to use Continuous Integration and Deployment service.

Whenever you make changes to your application and push your Code we take the latest version of your code, run all your tests and, if you want, push to your staging and/or production application. Test and deploy your applications without the headache of setting up your own test server. Getting started takes less than two minutes and is fully integrated with cloudControl.

## Adding the Railsonfire Add-on

To add the Railsonfire Add-on use the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add railsonfire.OPTION
~~~
Replace `railsonfire.OPTION` with a valid option, e.g. `railsonfire.test`.

When added, Railsonfire automatically creates a new user account with your email adress. You can manage the Add-on within the [web console](https://console.cloudcontrolled.com/) (go to the specific deployment and click the link "railsonfire.OPTION").

## Upgrading the Railsonfire Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade railsonfire.OPTION_OLD railsonfire.OPTION_NEW
~~~

## Downgrading the Railsonfire Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade railsonfire.OPTION_OLD railsonfire.OPTION_NEW
~~~

## Removing the Railsonfire Add-on

The Railsonfire Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove railsonfire.OPTION
~~~

### Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.
