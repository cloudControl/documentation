# Rails notes

This document contains some information that can be useful to Rails programmers.

## Procfile

To run rails server, create a file named "Procfile" with the following content:
~~~
web: bundle exec rails s -p $PORT
~~~

## Asset pipeline

If asset pipeline is used, "config/application.rb" file should contain the following line:
~~~ruby
config.assets.initialize_on_precompile = false
~~~

## Database

To use a database in a Rails applications, "config/database.yml" file needs to be modified.
Credentials and other database related information should be embedded with ERB snippets.
File extension should stay ".yml".

Here is an example "database.yml" file that is going to be used in production environment with MySQL database.
~~~erb
development:
  adapter: sqlite3
  database: db/development.sqlite3
  pool: 5
  timeout: 5000

test:
  adapter: sqlite3
  database: db/test.sqlite3
  pool: 5
  timeout: 5000

production:
  adapter: mysql2
  encoding: utf8
  pool: 5
  database: <%= "'#{ ENV['MYSQLS_DATABASE'] }'" %>
  host: <%= "'#{ ENV['MYSQLS_HOSTNAME'] }'" %>
  port: <%= "'#{ ENV['MYSQLS_PORT'] }'" %>
  username: <%= "'#{ ENV['MYSQLS_USERNAME'] }'" %>
  password: <%= "'#{ ENV['MYSQLS_PASSWORD'] }'" %>
~~~

NOTE: Strings in the embedded ruby snippet are enclosed in single quotes because YAML markup characters can be used in the password.

## Environments

Rails server can be run in different environments. To specify environment different then default "production" environment, use the free [Custom Config addon](https://www.cloudcontrol.com/add-ons/config) to override the content of RAILS_ENV and RAKE_ENV environment variables. For example:
~~~
cctrlapp APP_NAME/DEPLOYMENT addon.add config.free --RACK_ENV=some_env --RAILS_ENV=some_env
~~~

NOTE: Gems in development and test environments are excluded from bundle install process.

For more information refer to other guides in Ruby section.

