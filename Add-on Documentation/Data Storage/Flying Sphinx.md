# Flying Sphinx (Alpha)

Flying Sphinx is an Add-on for cloudControl which lets you use Thinking Sphinx (and thus, Sphinx) for all your search needs.

## Adding or removing the Flying Sphinx Add-on

The Add-on comes in different sizes and prices. It can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add flying_sphinx.OPTION
~~~
".option" represents the plan size, e.g. flying_sphinx.wooden

## Upgrade the Flying Sphinx Add-on

Upgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade flying_sphinx.OPTION_OLD flying_sphinx.OPTION_NEW
~~~
## Downgrade the Flying Sphinx Add-on

Downgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade flying_sphinx.OPTION_OLD flying_sphinx.OPTION_NEW
~~~

## Removing the Flying Sphinx Add-on

Similarily, an Add-on can also be removed from the deployment easily. The costs only apply for the time the Add-on was active:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove flying_sphinx.OPTION
~~~

# Add-on Credentials

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-ons) in the general documentation.

