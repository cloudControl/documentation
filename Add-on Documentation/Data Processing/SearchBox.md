# SearchBox ElasticSearch

Don't bother with the administrative operations or reliability issues of a search platform. SearchBox is a hosted, managed and scalable search as a service powered by ElasticSearch, the final frontier of search engines.

## Adding the SearchBox ElasticSearch Add-on

To add the SearchBox ElasticSearch Add-on use the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add searchbox.OPTION
~~~
Replace `searchbox.OPTION` with a valid option, e.g. `searchbox.micro`.

When added, SearchBox ElasticSearch automatically creates a new user account. You can manage the Add-on within the [web console](https://console.cloudcontrolled.com/) (go to the specific deployment and click the link "searchbox.OPTION").

## Upgrading the SearchBox ElasticSearch Add-on

To upgrade from a smaller to a more powerful plan use the addon.upgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade searchbox.OPTION_OLD searchbox.OPTION_NEW
~~~

## Downgrading the SearchBox ElasticSearch Add-on

To downgrade to a smaller plan use the addon.downgrade command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade searchbox.OPTION_OLD searchbox.OPTION_NEW
~~~

## Removing the SearchBox ElasticSearch Add-on

The SearchBox ElasticSearch Add-on can be removed from the deployment by using the addon.remove command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove searchbox.OPTION
~~~

## Internal Access

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

## SearchBox ElasticSearch Code Examples

You will find examples on how to use SearchBox ElasticSearch within your app at [dev center](http://dev.searchbox.io).