# StillAlive

Stillalive is the best way to monitor the live functionality of your web application.
## Adding or removing StillAlive
StillAlive can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add stillalive.developer
~~~
When added, StillAlive automatically creates a new account and login configuration including access token. You can access StillAlive for your deployment within the [web console](https://console.cloudcontrolled.com) (go to the specific deployment, choose "Add-Ons" tab and click StillAlive login).
## Removing StillAlive
Similarily, an Add-on can also be removed from the deployment easily:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove stillalive.developer

~~~

# Add-on Credentials

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-ons) in the general documentation.
