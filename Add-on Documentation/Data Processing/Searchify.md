# Searchify

Searchify is a hosted search Add-on that provides full-text search to your application, without the hassle of managing your own search infrastructure. Searchify makes it easy to tune your search results ranking using powerful custom scoring functions. And Searchify is fast - most search queries are answered in less than 100 milliseconds.

Searchify is 100% IndexTank-compatible, and is a drop-in replacement for IndexTank users

## Adding the Searchify Add-on

To add the Searchify Add-on use the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add searchify.OPTION
~~~
Replace `searchify.OPTION` with a valid option, e.g. `searchify.small`.

When added, Searchify automatically creates a new user account with your email adress. You can manage the Add-on within the [web console](https://console.cloudcontrolled.com/) (go to the specific deployment and click the link "searchify.OPTION").

## Upgrading the Searchify Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade searchify.OPTION_OLD searchify.OPTION_NEW
~~~

## Downgrading the Searchify Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade searchify.OPTION_OLD searchify.OPTION_NEW
~~~

## Removing the Searchify Add-on

The Searchify Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove searchify.OPTION
~~~

### Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.
