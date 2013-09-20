# Blitz.io: Performance Testing

Blitz makes load and performance testing of your web site, API, iPhone and Android and Facebook apps a fun sport. Instantly launch 1,000,000 users from around the world against your app to see if it can hold up. Integrate blitz into your continuous deployment with their Ruby Gem. Can you take the hits?

## Adding the Blitz.io Add-on

To add the Blitz.io Add-on use the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add blitz.OPTION
~~~
Replace `blitz.OPTION` with a valid option, e.g. `blitz.250`.

When added, Blitz.io automatically creates a new user account with your email adress. You can manage the load tests for your deployment within the [web console](https://www.cloudcontrol.com/console) (go to the specific deployment and click the link "blitz.OPTION").

## Upgrading the Blitz.io Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade blitz.OPTION_OLD blitz.OPTION_NEW
~~~

## Downgrading the Blitz.io Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade blitz.OPTION_OLD blitz.OPTION_NEW
~~~

## Removing the Blitz.io Add-on

The Blitz.io Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove blitz.OPTION
~~~

### Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

