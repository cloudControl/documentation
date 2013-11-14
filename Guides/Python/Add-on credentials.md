# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can
change these credentials at any time, so they shouldn't be hard-coded in the
source code. If the credentials are not in the source code, they also won't
appear in the version control and cause potential security issues.

There are two ways to get the [Add-on credentials] in a Python app.

## Reading the Credentials from the Credentials File

By default, all the Add-on credentials can be found in a provided JSON file,
which path is exposed in the `CRED_FILE` environment variable. You can see
the format of that file locally with the command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.creds
~~~

You can use the following code wherever you want to get the credentials in your
Python app:
~~~python
import os
import json

try:
  cred_file = open(os.environ['CRED_FILE'])
  creds = json.load(cred_file)

  config = {
    'var1_name': creds['ADDON_NAME']['ADDON_NAME_PARAMETER1'],
    'var2_name': creds['ADDON_NAME']['ADDON_NAME_PARAMETER2'],
    'var3_name': creds['ADDON_NAME']['ADDON_NAME_PARAMETER3']
    # e.g. for MYSQLS: 'hostname': creds['MYSQLS']['MYSQLS_HOSTNAME']
  }
except IOError:
  print 'Could not open the creds.json file'
~~~

Some examples for database Add-ons can be seen in the last section.

## Reading the Credentials from Environment Variables

The default for Python is to not expose Add-on credentials as environment
variables. To overwrite this default use the following command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME config.add SET_ENV_VARS
~~~

You can look up the individual environment variable names in the respective
Add-on documentation. To read them, simply access Python's `os.environ`
dictionary.

Note that there are some other interesting [environment variables][env-vars]
available in your deployment containers.

# Examples

cloudControl offers a number of data storage solutions via the [Add-on Marketplace].
Below you can see how to access Add-on credentials on two examples for MySQL
and PostgreSQL.

## MySQL

To add a MySQL database, use the [MySQL Dedicated Add-on] or [MySQL Shared Add-on].

Here's a Python snippet that reads the database settings from the credentials
file and stores them in the `db_config` dictionary:
~~~python
import os
import json

cred_file = open(os.environ['CRED_FILE'])
creds = json.load(cred_file)

db_config = {
    'database': creds['MYSQLD']['MYSQLD_DATABASE'],
    'host': creds['MYSQLD']['MYSQLD_HOSTNAME'],
    'port': creds['MYSQLD']['MYSQLD_PORT'],
    'username': creds['MYSQLD']['MYSQLD_USERNAME'],
    'password': creds['MYSQLD']['MYSQLD_PASSWORD']
}
~~~

The example used the MySQLd Add-on. Variable names for MySQLs differ. Remember,
you can always refer to the `addon.creds` command to see the actual variable
names and values.

## PostgreSQL

To add a PostgreSQL database, use the [ElephantSQL Add-on].

With this Python snippet you can read the PostgreSQL settings and store them in
the `db_config` dictionary:
~~~python
import os
import json

cred_file = open(os.environ['CRED_FILE'])
creds = json.load(cred_file)
elephant_uri = urlparse(creds['ELEPHANTSQL']['ELEPHANTSQL_URL'])

db_config = {
    'database': elephant_uri.path[1:],
    'host': elephant_uri.hostname,
    'port': elephant_uri.port,
    'username': elephant_uri.username,
    'password': elephant_uri.password
}
~~~

[env-vars]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables
[Add-on credentials]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-on-credentials
[Add-on Marketplace]: https://www.cloudcontrol.com/add-ons/
[Custom Config Add-on]: https://www.cloudcontrol.com/add-ons/config
[MySQL Dedicated Add-on]: https://www.cloudcontrol.com/add-ons/mysqld
[MySQL Shared Add-on]: https://www.cloudcontrol.com/add-ons/mysqls
[ElephantSQL Add-on]: https://www.cloudcontrol.com/add-ons/elephantsql
