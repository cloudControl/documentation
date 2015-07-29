# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can
change these credentials at any time, so they shouldn't be hard-coded in the
source code. If the credentials are not in the source code, they also won't
appear in the version control and cause potential security issues.

## Reading the Credentials from the Credentials File

All the [Add-on credentials] can be found in a provided JSON file, which path
is exposed in the `CRED_FILE` environment variable. You can see the format of that file locally with the command:
~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT_NAME addon.creds
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

cloudControl offers a number of data storage solutions via the [Add-on Marketplace].
Below you can see how to access Add-on credentials on two examples for MySQL and PostgreSQL.

## MySQL

To add a MySQL database, use the [MySQL Dedicated Add-on] or [MySQL Shared Add-on].

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

The example used the MySQLs Add-on. Variable names for MySQLd differ. Remember, you can always refer to the `addon.creds` command to see the actual variable names and values.

## PostgreSQL

To add a PostgreSQL database, use the [ElephantSQL Add-on].

With this PHP snippet you can read the PostgreSQL settings and store them in
the `$db_config` array:
~~~php
$creds_string = file_get_contents($_ENV['CRED_FILE'], false);
if ($creds_string == false) {
    die('FATAL: Could not read credentials file');
}
$creds = json_decode($creds_string, true);
$elephant_uri = parse_url($creds['ELEPHANTSQL']['ELEPHANTSQL_URL']);
$db_config = array(
     "database"  => substr($elephant_uri["path"], 1),
     "host"      => $elephant_uri["host"],
     "port"      => $elephant_uri["port"],
     "username"  => $elephant_uri["user"],
     "password"  => $elephant_uri["pass"]
);
~~~

[env-vars]: https://www.cloudcontrol.com/dev-center/platform-documentation#environment-variables
[Add-on credentials]: https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials
[Add-on Marketplace]: https://www.cloudcontrol.com/add-ons/
[MySQL Dedicated Add-on]: https://www.cloudcontrol.com/add-ons/mysqld
[MySQL Shared Add-on]: https://www.cloudcontrol.com/add-ons/mysqls
[ElephantSQL Add-on]: https://www.cloudcontrol.com/add-ons/elephantsql
