# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can change these credentials at any time, so they shouldn't be hard-coded in the source code. If the credentials are not in the source code, they also won't appear in the version control and cause potential security issues.

There are two ways to get the [Add-on credentials] in a Node.js app.

## Reading the Credentials from Environment Variables

By default, each Add-on exposes its credentials in the environment. You can look up the individual environment variable names in the respective Add-on documentation. To use a particular environment variable, you can refer to it using  `process.env.ENVIRONMENT_VARIABLE_NAME` in your code. Some examples for database Add-ons can be seen in the last section.

In case you don't want to expose these credentials in the environment, you can disable them by executing:

~~~bash
$ cctrlapp APP_NAME/DEP_NAME config.add SET_ENV_VARS=false
~~~

The Add-on credentials can still be read from the credentials file, as explained in the next section.

Note that there are some other interesting [environment variables] available in your deployment containers, such as the path to the credentials file.

## Reading the Credentials from a Credentials File

All the [Add-on credentials] can be found in a provided JSON file as well, which path is exposed in
the `CRED_FILE` environment variable. You can see the format of that file locally with the command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.creds
~~~

# Examples

cloudControl offers a number of data storage solutions via the [Add-on Marketplace]. Below is an example on how to access the credentials for MySQL from a credentials file.

##MySQL
To add a MySQL database, use the [MySQL Dedicated Add-on] or [MySQL Shared Add-on].

Here's a Node.js snippet that reads the database settings from the credentials file:

~~~javascript

var fs = require('fs');

var configuration = JSON.parse(
    fs.readFileSync(process.env.CRED_FILE)
);

var host = configuration.MYSQLD.MYSQLD_HOST;
var database = configuration.MYSQLD.MYSQLD_DATABASE;
var user = configuration.MYSQLD.MYSQLD_USER;
var password = configuration.MYSQLD.MYSQLD_PASSWORD;
var port = configuration.MYSQLD.MYSQLD_PORT;

~~~

The example used the MySQLd Add-on. Variable names for MySQLs differ. Remember, you can always refer to the addon.creds command to see the actual variable names and values.

Similarly, for other databases such as Postgres, credentials can be accessed from the credential file.

##PostgreSQL
To add a PostgreSQL database, use the [ElephantSQL Add-on].

This sets the `ELEPHANTSQL_URL` environment variable which can be used in your code as shown below:

~~~javascript

var fs = require('fs');
var url = require('url');

var configuration = JSON.parse(
    fs.readFileSync(process.env.CRED_FILE)
);
var elephantSQLUrl = url.parse(configuration.ELEPHANTSQL.ELEPHANTSQL_URL);

var host = elephantSQLUrl.hostname;
var database = elephantSQLUrl.pathname.substr(1);
var auth = elephantSQLUrl.auth.split(':');
var user = auth[0];
var password = auth[1];
var port = elephantSQLUrl.port;
~~~

[Add-on Marketplace]: https://www.cloudcontrol.com/add-ons
[environment variables]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables
[MySQL Dedicated Add-on]: https://www.cloudcontrol.com/add-ons/mysqld
[MySQL Shared Add-on]: https://www.cloudcontrol.com/add-ons/mysqls
[Add-on credentials]:https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-on-credentials
[ElephantSQL Add-on]: https://www.cloudcontrol.com/add-ons/elephantsql
