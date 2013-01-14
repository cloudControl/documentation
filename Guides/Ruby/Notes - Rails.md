# Notes for Rails developers

This document contains some information that can be useful to Rails programmers.

## Deploying on cloudControl

Check out the [Quickstart] too see how to create and deploy the application on the platform.

## Procfile

To run rails server, create a file named "Procfile" with the following content:
~~~
web: bundle exec rails s -p $PORT
~~~
Note that instead of the `rails s`, you could use anything else you want to run when you
container starts, e.g. unicorn server. For more details about the Procfile, check
the [Platform Documentation][procfile].

## Adding Database

To use a database you should choose an Add-on from [Data Storage category][data-storage-addons].
You can chose between MySQL and PostgreSQL databases.

Modify the file `config/database.yml` so that desired environment has the host, port, database, username and password fields left blank, e.g.
~~~
production:
  adapter: myslq2
  encoding: utf8
  pool: 5
  host:
  port:
  database:
  username:
  password:
~~~
And add the 'cloudcontrol-rails' gem to your Gemfile. It will automatically populate blank fields with the correct information if appropriate Add-on is used.

For more information, check the [gem itself].
If you have specific database requirements, check out [Adding databases][database-conf].

## Asset pipeline

If asset pipeline is used, "config/application.rb" file should contain the following line:
~~~ruby
config.assets.initialize_on_precompile = false if ENV['BUILDPACK_RUNNING']
~~~
This line disables the intialization on precompile only during the build process (while in the buildpack), but does not affect the normal code executions, e.g. running a web server or a run command.


## Environments

Rails server can be run in different environments. To specify the environment
different than default 'production' environment, use the free
[Custom Config addon](https://www.cloudcontrol.com/add-ons/config) to override
the content of RAILS_ENV and RAKE_ENV environment variables. For example:
~~~
cctrlapp APP_NAME/DEPLOYMENT addon.add config.free --RACK_ENV=some_env --RAILS_ENV=some_env
~~~

NOTE: Gems in development and test environments are excluded from the bundle install process.

## Further reading

For additional information, check out the following resources:
* [Platform documentation][platform-doc] for the general information about the platform
* [Read configuration][rb-conf] to see how to read deployment-specific configuration, e.g. Add-on credentials
* [SSH session][run-cmd] for the information on how to directly access deplyoment environment on the platform


[data-storage-addons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/
[gem itself]: http://rubygems.org/gems/cloudcontrol-rails
[database-conf]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/Read%20configuration#adding-relational-databases
[procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[quickstart]: https://www.cloudcontrol.com/dev-center/Quickstart
[platform-doc]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation
[rb-conf]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/Read%20configuration
[run-cmd]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/SSH%20session
