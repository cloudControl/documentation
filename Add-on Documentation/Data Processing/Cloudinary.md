# Cloudinary (Beta)

Cloudinary is a cloud service that offers a solution to an applications's entire image management pipeline.

## Adding or removing the Cloudinary Add-on

The Add-on comes in different sizes and prices. It can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add cloudinary.OPTION
~~~
*.option* represents the plan size, e.g. cloudinary.test


## Upgrade the Cloudinary Add-on

Upgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade cloudinary.OPTION_OLD cloudinary.OPTION_NEW
~~~

##Downgrade the Cloudinary Add-on

Downgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade cloudinary.OPTION_OLD cloudinary.OPTION_NEW
~~~

## Removing the Cloudinary Add-on

Similarily, an Add-on can also be removed from the deployment easily. The costs only apply for the time the Add-on was active:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove cloudinary.OPTION
~~~

## Add-on credentials

Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.

For more detailed information please refer to the [documentation on Cloudinary](http://cloudinary.com/documentation/cloudcontrol_integration).