# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can change these credentials at any time, so they shouldn't be hard-coded in the source code. If the credentials are not in the source code, they also won't appear in the version control and cause potential security issues.

There are two ways to get the [Add-on credentials] in a Node.js app.

## Reading the Credentials from Environment Variables

By default, each Add-on exposes its credentials in the environment. You can look up the individual environment variable names in the respective Add-on documentation. To use a particular environment variable, you can refer to it using  `process.env.ENVIRONMENT_VARIABLE_NAME` in your code. Some examples for database Add-ons can be seen in the last section.

In case you don't want to expose these credentials in the environment, you can disable them by executing:

~~~bash
$ exoapp APP_NAME/DEP_NAME config.add SET_ENV_VARS=false
~~~

The Add-on credentials can still be read from the credentials file, as explained in the next section.

Note that there are some other interesting [environment variables] available in your deployment containers, such as the path to the credentials file.

## Reading the Credentials from a Credentials File

All the [Add-on credentials] can be found in a provided JSON file as well, which path is exposed in
the `CRED_FILE` environment variable. You can see the format of that file locally with the command:

~~~bash
$ exoapp APP_NAME/DEP_NAME addon.creds
~~~

You can use the following code wherever you want to get the credentials in your Node.js app:

~~~javascript
var fs = require('fs');

var creds = JSON.parse(
    fs.readFileSync(process.env.CRED_FILE)
);

var param1 = creds.ADDON_NAME.ADDON_NAME_PARAMETER1;
var param2 = creds.ADDON_NAME.ADDON_NAME_PARAMETER2;
var param3 = creds.ADDON_NAME.ADDON_NAME_PARAMETER3;
// e.g. for MYSQLS: var hostname = creds.MYSQLS.MYSQLS_HOSTNAME
~~~

# Examples

exoscale offers a number of data storage solutions via the [Add-on Marketplace]. Below you can see how to access Add-on credentials for MySQL.

##MySQL
To add a MySQL database, use the [MySQL Shared Add-on].

Here's a Node.js snippet that reads the database settings from the credentials file:

~~~javascript
var fs = require('fs');

var creds = JSON.parse(
    fs.readFileSync(process.env.CRED_FILE)
);

var host = creds.MYSQLS.MYSQLS_HOST;
var database = creds.MYSQLS.MYSQLS_DATABASE;
var user = creds.MYSQLS.MYSQLS_USER;
var password = creds.MYSQLS.MYSQLS_PASSWORD;
var port = creds.MYSQLS.MYSQLS_PORT;

~~~

Remember, you can always refer to the `addon.creds` command to see the actual variable names and values.

[Add-on Marketplace]: https://www.exoscale.ch/add-ons
[environment variables]: https://www.exoscale.ch/dev-center/Platform%20Documentation#environment-variables
[MySQL Shared Add-on]: https://www.exoscale.ch/add-ons/mysqls
[Add-on credentials]:https://www.exoscale.ch/dev-center/Platform%20Documentation#add-on-credentials
