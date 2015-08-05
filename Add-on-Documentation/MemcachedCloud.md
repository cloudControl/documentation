# Memcached Cloud
[Memcached Cloud] is a fully-managed service for hosting and running your Memcached
in a reliable and fail-safe manner.

## Adding the Memcached Cloud Add-on

To add the Memcached Cloud Add-on use the addon.add command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.add memcachedcloud.OPTION
~~~
Replace `memcachedcloud.OPTION` with a valid option, e.g. `memcachedcloud.25mb`.

When added, Memcached Cloud automatically creates a new user account with your email address. You can
manage the Add-on within the [web console](/console) (go to the specific deployment and click the link "memcachedcloud.OPTION").

## Upgrading the Memcached Cloud Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.upgrade memcachedcloud.OPTION_OLD memcachedcloud.OPTION_NEW
~~~

## Downgrading the Memcached Cloud Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.downgrade memcachedcloud.OPTION_OLD memcachedcloud.OPTION_NEW
~~~

## Removing the Memcached Cloud Add-on

The Memcached Cloud Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.remove memcachedcloud.OPTION
~~~

### Internal Access

It's recommended to the read database credentials from the *creds.json* file. The location of the
file is available in the `CRED_FILE` environment variable. Reading the credentials from the
*creds.json* file ensures your app is always using the correct credentials. For detailed
instructions on how to use the *creds.json* file please refer to the section about
[Add-on Credentials] in the general documentation.


[Memcached Cloud]: https://redislabs.com/memcached-cloud
[Add-on Credentials]: /dev-center/platform-documentation#add-ons
