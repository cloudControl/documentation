# Adding databases

In this tutorial we are going to show you how to add different databases to your application.

There are generally two ways to access credentials and other database-related information:
* reading them from the relevant entry in "creds" JSON file
* reading them from the environment variables specific to the chosen database/addon

Path to "creds" file in saved in CRED_FILE environment variable.

## MySQL

To add MySQL database, use MySQL (Shared) addon. More information can be found [here](https://www.cloudcontrol.com/add-ons/mysqls).

Here is a ruby snippet that reads database settings and stores them in db_config hash:
~~~ruby
db_config = {
    database: ENV["MYSQLS_DATABASE"],
    host: ENV["MYSQLS_HOSTNAME"],
    port: ENV["MYSQLS_PORT"],
    username: ENV["MYSQLS_USERNAME"],
    password: ENV["MYSQLS_PASSWORD"],
    adapter: "mysql2"
}
~~~

## PostgreSQL

To add PostgreSQL database, use ElephantSQL addon. More information can be found [here](https://www.cloudcontrol.com/add-ons/elephantsql).

Here is a ruby snippet that reads database settings and stores them in db_config hash:
~~~ruby
re = /.+\/\/([^:]+):([^@]+)@([^:]+):([^\/]+)\/(.+)/
elephantsql_url = ENV["ELEPHANTSQL_URL"]
_, username, password, host, port, database = re.match(elephantsql_url).to_a

db_config = {
    database: database,
    host: host,
    port: port,
    username: username,
    password: password,
    adapter: "postgresql"
}
~~~
