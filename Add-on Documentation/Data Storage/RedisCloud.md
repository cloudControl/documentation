# Redis Cloud
[Redis Cloud] is a fully-managed cloud service for hosting and running your Redis dataset in a
highly-available and scalable manner, with predictable and stable top performance.

## Adding the Redis Cloud Add-on

To add the Redis Cloud Add-on use the addon.add command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.add rediscloud.OPTION
~~~
Replace `rediscloud.OPTION` with a valid option, e.g. `rediscloud.25mb`.

When added, Redis Cloud automatically creates a new user account with your email address. You can
manage the Add-on within the [web console](/console) (go to the specific deployment and click the link "rediscloud.OPTION").

## Upgrading the Redis Cloud Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.upgrade rediscloud.OPTION_OLD rediscloud.OPTION_NEW
~~~

## Downgrading the Redis Cloud Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.downgrade rediscloud.OPTION_OLD rediscloud.OPTION_NEW
~~~

## Removing the Redis Cloud Add-on

The Redis Cloud Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ dcapp APP_NAME/DEP_NAME addon.remove rediscloud.OPTION
~~~

### Internal Access

It's recommended to the read database credentials from the *creds.json* file. The location of the
file is available in the `CRED_FILE` environment variable. Reading the credentials from the
*creds.json* file ensures your app is always using the correct credentials. For detailed
instructions on how to use the *creds.json* file please refer to the section about
[Add-on Credentials] in the general documentation.


[Redis Cloud]: https://redislabs.com/redis-cloud
[Add-on Credentials]: /dev-center/platform-documentation#add-ons
