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
$ exoapp APP_NAME/DEP_NAME config.add SET_ENV_VARS=false
~~~

The Add-on credentials can still be read from the credentials file, as explained in the next section.

Note that there are some other interesting [environment variables]
available in your deployment containers.


## Reading the Credentials from the Credentials File

All the [Add-on credentials] can be found in a provided JSON file as well, which path is exposed in
the `CRED_FILE` environment variable. You can see the format of that file locally with the command:
~~~bash
$ exoapp APP_NAME/DEP_NAME addon.creds
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

exoscale offers a number of data storage solutions via the [Add-on Marketplace].
Below you can see how to access Add-on credentials for MySQL.

## MySQL

To add a MySQL database, use the [MySQL Shared Add-on].

Here's a Ruby snippet that reads the database settings and stores them in the
`db_config` hash:
~~~ruby
db_config = {
  database: ENV["MYSQLS_DATABASE"],
  host: ENV["MYSQLS_HOST"],
  port: ENV["MYSQLS_PORT"],
  username: ENV["MYSQLS_USER"],
  password: ENV["MYSQLS_PASSWORD"]
}
~~~

Remember, you can always refer to the `addon.creds` command to see the actual variable names and values.

[Add-on credentials]: https://www.exoscale.ch/dev-center/Platform%20Documentation#add-on-credentials
[environment variables]: https://www.exoscale.ch/dev-center/Platform%20Documentation#environment-variables
[Add-on Marketplace]: https://www.exoscale.ch/add-ons/?c=1
[MySQL Shared Add-on]: https://www.exoscale.ch/add-ons/mysqls
