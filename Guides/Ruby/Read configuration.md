# Getting the Add-on credentials

Each deployment gets different credentials for each Add-on. Providers can
change these credentials at any time, so they shouldn't be hard-coded in the
source code. If the credentials are not in the source code, they also won't
appear in the version control and cause potential security issues.

There are two ways to get the Add-on credentials in a ruby app.

## Reading the credentials from creds file

All the Add-on credentials can be found in a provided JSON file.

The path to the JSON file can be found in the `CRED_FILE` environment variable.
To see the JSON data locally, use the
addon.creds command, `cctrlapp APP_NAME/DEP_NAME addon.creds`.

You can use the following code wherever you want to get the credentials in your Ruby app.

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

## Reading the credentials from environment variables

An alternative (and simpler) way of reading the Add-on credentials is reading them from
the environment variables. Each Add-on exposes some environment variables, the same
ones that are nested under specific sections in cred file. To read them, simply
access Ruby's `ENV` hash. An example for two database Add-ons can be seen in the next section.

Note that there are some other interesting [variables][env-vars] available in a deployment's environment.

# Adding relational databases

### MySQL

To add a MySQL database, use the [MySQL Dedicated Add-on](https://www.cloudcontrol.com/add-ons/mysqld) or [MySQL Shared Add-on](https://www.cloudcontrol.com/add-ons/mysqls).

Here's a ruby snippet that reads the database settings and stores them in 'db_config' hash:
~~~ruby
db_config = {
  database: ENV["MYSQLD_DATABASE"],
  host: ENV["MYSQLD_HOST"],
  port: ENV["MYSQLD_PORT"],
  username: ENV["MYSQLD_USER"],
  password: ENV["MYSQLD_PASSWORD"]
}
~~~
It the previous example, MySQLd Add-on was used. This can be seen in the names of the environment variables.

### PostgreSQL

To add a PostgreSQL database, use the [ElephantSQL Add-on](https://www.cloudcontrol.com/add-ons/elephantsql).

Here is a ruby snippet that reads the database settings and stores them in a `db_config` hash:
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

[env-vars]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables
