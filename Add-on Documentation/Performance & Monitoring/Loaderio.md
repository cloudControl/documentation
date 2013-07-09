# Loader.io


Loader.io is a simple cloud-based load testing tool for your apps - by SendGrid Labs. It allows you to stress test your web-apps/apis with thousands of concurrent connections.

## Adding or removing the Loader.io Add-on

The Add-on is free. It can be added by executing the command addon.add:



~~~

$ cctrlapp APP_NAME/DEP_NAME addon.add loaderio.test

~~~

When added, Loader.io automatically creates a new account and login configuration including access token. You can access Loader.io for your deployment within the [web console](https://console.cloudcontrolled.com) (go to the specific deployment, choose "Add-Ons" tab and click Loader.io login).

## Removing the Loader.io Add-on



Similarily, an Add-on can also be removed from the deployment easily:


~~~

$ cctrlapp APP_NAME/DEP_NAME addon.remove loaderio.test

~~~



# Add-on Credentials



## Internal access credentials



It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.
