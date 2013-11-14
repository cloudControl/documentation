# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can
change these credentials at any time, so they shouldn't be hard-coded in the
source code. If the credentials are not in the source code, they also won't
appear in the version control and cause potential security issues.

There are two ways to get the [Add-on credentials] in a Ruby app.


## Reading the Credentials from Environment Variables

By default, each Add-on exposes its credentials in the environment. You can
look up the individual environment variable names in the respective Add-on
documentation. To read them, simply access Ruby's `ENV` hash. Some examples for
database Add-ons can be seen in the last section.

In case you don't want to expose these credentials in the environment, you can
disable them by executing:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME config.add SET_ENV_VARS=false
~~~

The Add-on credentials can still be read from the credentials file, as explained in the next section.

Note that there are some other interesting [environment variables]
available in your deployment containers.


## Reading the Credentials from the Credentials File

All the [Add-on credentials] can be found in a provided JSON file as well, which path is exposed in
the `CRED_FILE` environment variable. You can see the format of that file locally with the command:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.creds
~~~

You can use the following code wherever you want to get the credentials in your
Ruby app:
~~~ruby
require 'json'

begin
  cred_file = File.open(ENV["CRED_FILE"]).read
  creds = JSON.parse(cred_file)["ADDON_NAME"]
  config = {
    :var1_name => creds["ADDON_NAME_PARAMETER1"],
    :var2_name => creds["ADDON_NAME_PARAMETER2"],
    :var3_name => creds["ADDON_NAME_PARAMETER3"]
    # e.g. for MYSQLS: :hostname => creds[MYSQLS_HOSTNAME]
  }
rescue
  puts "Could not open the creds.json file"
end
~~~


# Examples

cloudControl offers a number of data storage solutions via the [Add-on Marketplace].
Below you can see how to access Add-on credentials on two examples for MySQL and PostgreSQL.

## MySQL

To add a MySQL database, use the [MySQL Dedicated Add-on] or [MySQL Shared Add-on].

Here's a Ruby snippet that reads the database settings and stores them in the
`db_config` hash:
~~~ruby
db_config = {
  database: ENV["MYSQLD_DATABASE"],
  host: ENV["MYSQLD_HOST"],
  port: ENV["MYSQLD_PORT"],
  username: ENV["MYSQLD_USER"],
  password: ENV["MYSQLD_PASSWORD"]
}
~~~

The example used the MySQLd Add-on. Variable names for MySQLs differ. Remember, you can always refer to the `addon.creds` command to see the actual variable names and values.

## PostgreSQL

To add a PostgreSQL database, use the [ElephantSQL Add-on].

With this Ruby snippet you can read the PostgreSQL settings and store them in the
`db_config` hash:
~~~ruby
require 'uri'

elephant_uri = URI.parse ENV['ELEPHANTSQL_URL']
db_config = {
  database: elephant_uri.path[1 .. -1],
  host: elephant_uri.host,
  port: elephant_uri.port,
  username: elephant_uri.user,
  password: elephant_uri.password
}
~~~

You can also find a working example application on [Github][ruby-postgresql-example].

[Add-on credentials]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-on-credentials
[environment variables]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables
[Add-on Marketplace]: https://www.cloudcontrol.com/add-ons/?c=1
[MySQL Dedicated Add-on]: https://www.cloudcontrol.com/add-ons/mysqld
[MySQL Shared Add-on]: https://www.cloudcontrol.com/add-ons/mysqls
[ElephantSQL Add-on]: https://www.cloudcontrol.com/add-ons/elephantsql
[ruby-postgresql-example]: https://github.com/ElephantSQL/ruby-postgresql-example
