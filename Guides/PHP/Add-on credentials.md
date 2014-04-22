# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can
change these credentials at any time, so they shouldn't be hard-coded in the
source code. If the credentials are not in the source code, they also won't
appear in the version control and cause potential security issues.

## Reading the Credentials from the Credentials File

All the [Add-on credentials] can be found in a provided JSON file, which path
is exposed in the `CRED_FILE` environment variable. You can see the format of that file locally with the command:
~~~bash
$ exoapp APP_NAME/DEP_NAME addon.creds
~~~

You can use the following code wherever you want to get the credentials in your
PHP app:
~~~php
# read the credentials file
$creds_string = file_get_contents($_ENV['CRED_FILE'], false);
if ($creds_string == false) {
    die('FATAL: Could not read credentials file');
}

# the file contains a JSON string, decode it and return an associative array
$creds = json_decode($creds_string, true);

# now use the $creds array to configure your app
$var1_name = $creds['ADDON_NAME']['ADDON_NAME_PARAMETER1'];
$var2_name = $creds['ADDON_NAME']['ADDON_NAME_PARAMETER2'];
$var3_name = $creds['ADDON_NAME']['ADDON_NAME_PARAMETER3'];

# e.g. for MySQLs $hostname = $creds['MYSQLS']['MYSQLS_HOSTNAME'];
~~~

# Examples

exoscale offers a number of data storage solutions via the [Add-on Marketplace].
Below you can see how to access Add-on credentials for MySQL.

## MySQL

To add a MySQL database, use the [MySQL Shared Add-on].

Here's a PHP snippet that reads the database settings from the credentials file:
~~~php
$creds_string = file_get_contents($_ENV['CRED_FILE'], false);
if ($creds_string == false) {
    die('FATAL: Could not read credentials file');
}
$creds = json_decode($creds_string, true);
$database 	= $creds['MYSQLS']['MYSQLS_DATABASE'];
$host     	= $creds['MYSQLS']['MYSQLS_HOSTNAME'];
$port     	= $creds['MYSQLS']['MYSQLS_PORT'];
$username   = $creds['MYSQLS']['MYSQLS_USERNAME'];
$password   = $creds['MYSQLS']['MYSQLS_PASSWORD'];
~~~

Remember, you can always refer to the `addon.creds` command to see the actual variable names and values.

[env-vars]: https://www.exoscale.ch/dev-center/Platform%20Documentation#environment-variables
[Add-on credentials]: https://www.exoscale.ch/dev-center/Platform%20Documentation#add-on-credentials
[Add-on Marketplace]: https://www.exoscale.ch/add-ons/
[MySQL Shared Add-on]: https://www.exoscale.ch/add-ons/mysqls
