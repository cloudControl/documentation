# Ruby Notes


## Procfile

The exoscale platform uses a file named `Procfile` to determine how to run your
application. This `Procfile` uses the YAML format to specify the desired
configuration.

The command specified under the `web` entry will be used to start the web
server.

If you have a `Procfile` with the following content:
~~~
web: ruby my_server.rb
~~~
the `ruby my_server.rb` will be executed upon deployment of a new web
container.

Example `Procfiles` that can be used to start the Rails application can be found
[later in this document][rails-procfile].

For more context, visit the [Platform documentation][procfile].


## Ruby version

The Ruby version can be specified in the Gemfile. If no version is specified,
the default one is used. Currently, the default version is 2.0.0.

To specify the version, put the `ruby` directive as the first line in the
`Gemfile`, e.g.:
~~~
ruby "1.9.3"
~~~

On the next push, the desired Ruby version will be used.

To see all the supported versions, check the [Ruby buildpack][ruby-buildpack]
documentation.


# Rails Notes


## Rails Procfile

To run a Rails server, create a file named `Procfile` with the following content:

~~~
web: bundle exec rails s -p $PORT
~~~


## Asset Pipeline

If the asset pipeline is used, the `config/application.rb` file should contain the following line:

~~~ruby
config.assets.initialize_on_precompile = false if ENV['BUILDPACK_RUNNING']
~~~

This disables the intialization on precompile only during the build process (while in the buildpack), but does not affect the normal code executions, e.g. running a web server or a run command.


## Database

To use a database in a Rails application, the `config/database.yml` file needs to be modified. Credentials and other database-related information should be embedded in ERB snippets. However, the file extension should remain `.yml`.

Here is an example of a `database.yml` file that is going to be used in with a MySQL database production environment.

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
  port: <%= ENV["MYSQLS_PORT"] %>
  username: <%= "'#{ ENV['MYSQLS_USERNAME'] }'" %>
  password: <%= "'#{ ENV['MYSQLS_PASSWORD'] }'" %>
~~~

NOTE: As YAML markup characters can be used in the password, strings in the embedded ruby snippets are enclosed in single quotes. Since the port is required to be an integer, it's not enclosed in quotes here.

Alternatively you can use the [cloudcontrol-rails] gem.


## Environments

Rails servers can be run in different environments. Production is the default one but you can change it by setting the `RAILS_ENV` and `RAKE_ENV` environment variables with the [Custom Config addon](https://www.exoscale.ch/add-ons/config). For example:

~~~
exoapp APP_NAME/DEP_NAME config.add RACK_ENV=some_env RAILS_ENV=some_env
~~~

NOTE: Gems in development and test environments are excluded from bundle install process.



[cloudcontrol-rails]: https://rubygems.org/gems/cloudcontrol-rails
[procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#version-control--images
[rails-procfile]: #rails-procfile
[ruby-buildpack]: https://github.com/cloudControl/buildpack-ruby
