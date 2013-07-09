# Treasure Data



Treasure Data is the Big Data analytics and data warehousing platform.

## Adding or removing the Treasure Data Add-on

The Add-on is free during Beta. It can be added by executing the command addon.add:



~~~

$ cctrlapp APP_NAME/DEP_NAME addon.add treasure_data.beta

~~~

When added, Treasure Data automatically creates a new account and login configuration including access token. You can access Treasure Data for your deployment within the [web console](https://console.cloudcontrolled.com) (go to the specific deployment, choose "Add-Ons" tab and click Treasure Data login).

## Removing the Treasure Data Add-on

Similarily, an Add-on can also be removed from the deployment easily:



~~~

$ cctrlapp APP_NAME/DEP_NAME addon.remove treasure_data.beta


~~~



# Add-on Credentials



## Internal access credentials



It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.
