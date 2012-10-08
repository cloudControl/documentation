# Adding MySQL database

In this tutorial we are going to show you how to add database to your applications.

To add MySQL database, use MySQL (Shared) addon. More information on how to add [MySQL Shared addon](https://www.cloudcontrol.com/documentation/add-ons/mysql-shared).

Database credentials are stored in configuration JSON file, and path to this file is available in $CRED_FILE enviroment variable. Here is ruby snippet that reads database settings and stores them in db_config hash:
~~~ruby
  require 'json'

  json_str = File.open(ENV["CRED_FILE"]).read
  mysqls_data = JSON.parse(json_str)["MYSQLS"]
  db_config = {
    :database => mysqls_data["MYSQLS_DATABASE"],
    :host => mysqls_data["MYSQLS_HOSTNAME"],
    :port => mysqls_data["MYSQLS_PORT"],
    :username => mysqls_data["MYSQLS_USERNAME"],
    :password => mysqls_data["MYSQLS_PASSWORD"]
  }
~~~

If database configurations are usually stored in a file, embed them via ERB. Here is an example Rails' database.yml file:
~~~erb
<%
  require 'json'

  json_str = File.open(ENV["CRED_FILE"]).read
  mysqls_data = JSON.parse(json_str)["MYSQLS"]
  db_config = {
    :database => mysqls_data["MYSQLS_DATABASE"],
    :host => mysqls_data["MYSQLS_HOSTNAME"],
    :port => mysqls_data["MYSQLS_PORT"],
    :username => mysqls_data["MYSQLS_USERNAME"],
    :password => mysqls_data["MYSQLS_PASSWORD"]
  }
%>

# NOTE: here goes your development configuration
# NOTE: here goes your test configuration

production:
  adapter: mysql2
  encoding: utf8
  pool: 5
  database: <%= db_config[:database] %>
  host: <%= db_config[:host] %>
  port: <%= db_config[:port] %>
  username: <%= db_config[:username] %>
  password: <%= db_config[:password] %>
~~~
