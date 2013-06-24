# Deploying a Ruby on Rails application
[Ruby on Rails][rails] is an open-source web framework that's optimized for programmer happiness and sustainable productivity.

In this tutorial we're going to show you how to migrate an existing Rails
application to the [cloudControl] platform. You can find the complete
[source code][example-app] of the application on Github.

All the steps described in this tutorial can be followed in the git repository via commit history.


## Prerequisites
*   [cloudControl user account][cloudControl-doc-user]
*   [cloudControl command line client][cloudControl-doc-cmdline]
*   [git]
*   familiarity with the Ruby on Rails framework


## Original application

The goal of this tutorial is to migrate a fully functional Rails application to
the cloudControl Platform. The application in question is a fork of Michael Hartl's
[Rails tutorial]'s Sample App and is a Twitter clone.

To start, first clone the application from the previously mentioned git repository:
~~~bash
$ git clone git://github.com/cloudControl/ruby-rails-example-app.git
$ cd ruby-rails-example-app
~~~

Now you have the original version of the app. This version should work locally
on you machine, but is still not ready to be deployed on the platform.

Install all the necessary dependencies via `bundle install` command.

The app has exhaustive set of tests. Check that all the tests are
passing locally.

~~~bash
$ bundle exec rake db:migrate
$ bundle exec rake db:test:prepare
$ bundle exec rspec spec/
~~~

All the test should pass at this point, so run a server locally to get acquainted
with the app.
~~~bash
$ rails s
~~~

Now that the app is working, it's time to prepare it for deployment on the platform.

### Creating the app

Choose a unique name (from now on called APP_NAME) for your application and create
it on the platform. Be sure that you're inside of the git repository when
running the command:
~~~bash
$ cctrlapp APP_NAME create ruby
~~~

### Defining the process type

cloudControl uses a [Procfile] to know how to start your processes.

Create a file called `Procfile` with the following content:
~~~
web: bundle exec rails s -p $PORT
~~~

This file specifies a `web` command that will be executed once the app is
deployed. The $PORT environment variable contains the port your app needs to
listen on.


### Configuring the asset pipeline

Now add the following code to the `config/application.rb`:
~~~ruby

    # Do not initialize on precompile in the build process
    # as this can fail, e.g. if database is being accessed in the process
    # and there is no benefit in doing it in build process anyway
    config.assets.initialize_on_precompile = false if ENV['BUILDPACK_RUNNING']
~~~


### Production database
Now it's time to configure the production database.

By default, Rails 3 uses SQLite for all the databases, even the production one.
It is not possible to use SQLite in production environment on the platform,
reason being the [Non Persistent Filesystem][filesystem].

To use a database you should choose an Add-on from [Data Storage category][data-storage-addons].

You can use MySQL or PostgreSQL databases. The following section shows how to use
PostgreSQL, and the next one how to use MySQL. If you want, you can skip one of them.


#### PostgreSQL database
Let's use PostgresSQL with the [ElephantSQL Add-on][postres-addon].

First add the free option of the Add-on to the "default" deployment:
~~~bash
$ cctrlapp APP_NAME/default addon.add elephantsql.turtle
~~~

Second, modify the `Gemfile` by moving the `sqlite3` line to ":development, :test" block
and add a new ":production" group with 'pg' and 'cloudcontrol-rails' gems.

Now the `Gemfile` should have the following content:
~~~ruby
source 'http://rubygems.org'

gem 'rails', '3.1.10'
gem 'gravatar_image_tag', '1.0.0.pre2'
gem 'will_paginate', '3.0.4'

group :development do
  gem 'annotate', '2.4.0'
  gem 'faker', '0.3.1'
end

group :test do
  gem 'webrat', '0.7.1'
  gem 'spork', '0.9.0.rc8'
  gem 'factory_girl_rails', '1.0'
end

group :development, :test do
  gem 'rspec-rails', '2.6.1'
  gem 'sqlite3', '1.3.4'
end

group :production do
  gem 'pg'
  gem 'cloudcontrol-rails', '0.0.5'
end

group :assets do
  gem 'sass-rails'
  gem 'coffee-rails'
  gem 'uglifier'
end

gem 'jquery-rails'
~~~
Don't forget to run the `bundle install` command.

Finally, change the "production" section of `config/database.yml` file to:
~~~
production:
  adapter: postgresql
  encoding: utf8
  pool: 5
  host:
  port:
  database:
  username:
  password:
~~~

Now the app is ready to be deployed on the platform.
To do this, run the following command:
~~~bash
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~
This pushes the code to the application's repository, which creates a deployment image.

You also need to run the migrations on the database, to do so, use the [run command]:
~~~bash
$ cctrlapp APP_NAME/default run "rake db:migrate"
~~~


Now deploy the app:
~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you should now be able to reach the app at http://APP_NAME.cloudcontrolled.com.


#### MySQL database

Now you're going to change the production database to MySQL.

To do so, first add the Add-on. For this app, your are going to use the
[Shared MySQL Add-on][mysqls] with the free option:
~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.free
~~~

You need to add the 'mysql2' gem in the Gemfile.  If you followed the previous
section and used the PostgreSQL database, you can also remove the 'pg' gem.
Do not forget to run the `bundle install` command.

The `database.yml` needs to be modified as well. Set the production adapter to
'mysql2'.

As a final step, you can compare your working directory with the `migrated` branch
to be sure you didn't make any mistakes along the way:
~~~bash
$ git diff migrated
~~~

Commit the code and push it, run the migrations and deploy the app:
~~~bash
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~

Don't forget to run the migrations on the database:
~~~bash
$ cctrlapp APP_NAME/default run "rake db:migrate"
~~~

That's all, now you app is using the MySQL database.


For additional information take a look at [Ruby on Rails notes][rails-notes] and
other [ruby-specific documents][ruby-guides].


[rails]: http://rubyonrails.org/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-user]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#user-accounts
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[ruby buildpack]: https://github.com/cloudControl/buildpack-ruby
[procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[git]: https://help.github.com/articles/set-up-git
[bundler]: http://gembundler.com/
[filesystem]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#non-persistent-filesystem
[data-storage-addons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/
[mysqls]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs
[gem itself]: http://rubygems.org/gems/cloudcontrol-rails
[database-conf]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/Read%20configuration#adding-relational-databases
[example-app]: https://github.com/cloudControl/ruby-rails-example-app
[rails-notes]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/Ruby%20on%20Rails%20notes
[Rails tutorial]: http://ruby.railstutorial.org/
[postres-addon]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/ElephantSQL
[ruby-guides]: https://www.cloudcontrol.com/dev-center/Guides/Ruby
[run command]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/SSH%20session
