# Deploying a Ruby on Rails Application

In this tutorial we're going to show you how to deploy a [Ruby on Rails] application on [dotCloud]. You can find the [source code on Github][example-app] and check out the [Ruby buildpack][ruby buildpack] for supported features. The application is a fork of Michael Hartl's [Rails tutorial] sample app which is a Twitter clone.

## The Rails Application Explained

### Get the App

First, clone the Rails application from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/ruby-rails-example-app.git
$ cd ruby-rails-example-app
~~~

### Dependency Tracking

The Ruby buildpack tracks dependencies with [Bundler]. Those are defined in the `Gemfile` which is placed in the root directory of your repository. In this example app it contains:

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
  gem 'cloudcontrol-rails', '0.0.6'
end

group :assets do
  gem 'sass-rails'
  gem 'coffee-rails'
  gem 'uglifier'
end

gem 'jquery-rails'
~~~

### Testing

The app has an exhaustive set of tests. Check that all the tests are passing locally:

~~~bash
$ bundle install
$ bundle exec rake db:migrate
$ bundle exec rake db:test:prepare
$ bundle exec rspec spec/
~~~

Now that the app is working, lets have a look at changes we have made to deploy it on dotCloud.

### Process Type Definition

dotCloud uses a [Procfile] to know how to start your processes. The example code already includes a file called Procfile in the root of your repository. It looks like this:

~~~
web: bundle exec rails s -p $PORT
~~~

Left from the colon we specified the required process type called `web` followed by the command that starts the app and listens on the port specified by the environment variable `$PORT`.

### Configuring the Asset Pipeline

We have added following code into the `Application` class defined in the `config/application.rb`:

~~~ruby
module SampleApp
  class Application < Rails::Application

    ...

    # Do not initialize on precompile in the build process
    # as this can fail, e.g. if database is being accessed in the process
    # and there is no benefit in doing it in build process anyway
    config.assets.initialize_on_precompile = false if ENV['BUILDPACK_RUNNING']

  end
end
~~~

### Production Database

By default, Rails 3 uses SQLite for all the environments. However, it is not recommended to use SQLite on dotCloud because the filesystem is [not persistent][filesystem]. To use a database, you should choose an Add-on from [Data Storage category][data-storage-addons].

In this tutorial we use PostgresSQL with the [ElephantSQL Add-on][postgres-addon]. This is why we have modified the `Gemfile` by moving the `sqlite3` line to ":development, :test" block and added a new ":production" group with "pg" and ["cloudcontrol-rails"][gem itself] gems.

Additionally we have changed the "production" section of `config/database.yml` to use the postgresql adapter:
~~~
production:
  adapter: postgresql
  encoding: utf8
  pool: 5
~~~
The 'cloudcontrol-rails' gem will provide the database credentials.


## Pushing and Deploying your App

Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the dotCloud platform:

~~~bash
$ cctrlapp APP_NAME create ruby
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 62, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (51/51), done.
Writing objects: 100% (62/62), 15.14 KiB, done.
Total 62 (delta 2), reused 0 (delta 0)

-----> Receiving push
-----> Using Ruby version: ruby-2.0.0
-----> Installing dependencies using Bundler version 1.3.2
       Running: bundle install --without development:test --path vendor/bundle --binstubs vendor/bundle/bin --deployment
       Fetching gem metadata from https://rubygems.org/..........
       Fetching gem metadata from https://rubygems.org/..
       Installing rake (10.0.3)
       ...
       Installing rails (3.1.10)
       ...
       Installing uglifier (1.3.0)

       Your bundle is complete! It was installed into ./vendor/bundle
       Post-install message from rdoc:
       Depending on your version of ruby, you may need to install ruby rdoc/ri data:
       <= 1.8.6 : unsupported
       = 1.8.7 : gem install rdoc-data; rdoc-data --install
       = 1.9.1 : gem install rdoc-data; rdoc-data --install
       >= 1.9.2 : nothing to do! Yay!

       Cleaning up the bundler cache.
-----> Preparing app for Rails asset pipeline
       Running: rake assets:precompile
       Asset precompilation completed (6.34s)
       Cleaning assets
-----> WARNINGS:
       You have not declared a Ruby version in your Gemfile.
       To set your Ruby version add this line to your Gemfile:
       ruby '2.0.0'
-----> Building image
-----> Uploading image (34M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Add ElephantSQL Add-on with `turtle` plan to your deployment and deploy it:

~~~bash
$ cctrlapp APP_NAME/default addon.add elephantsql.turtle
$ cctrlapp APP_NAME/default deploy
~~~

Finally, prepare the database by running migrations using the [Run command][run command]:

~~~bash
$ cctrlapp APP_NAME/default run "rake db:migrate"
~~~

Congratulations, you can now access the app at http://APP_NAME.cloudcontrolled.com.

For additional information take a look at [Ruby on Rails notes][rails-notes] and
other [ruby-specific documents][ruby-guides].

[Ruby on Rails]: http://rubyonrails.org/
[dotCloud]: http://next.dotcloud.com
[example-app]: https://github.com/cloudControl/ruby-rails-example-app
[ruby buildpack]: https://github.com/cloudControl/buildpack-ruby
[Rails tutorial]: http://ruby.railstutorial.org/
[Bundler]: http://bundler.io/
[Procfile]: https://next.dotcloud.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[filesystem]: https://next.dotcloud.com/dev-center/Platform%20Documentation#non-persistent-filesystem
[data-storage-addons]: https://next.dotcloud.com/dev-center/Add-on%20Documentation/Data%20Storage/
[postgres-addon]: https://next.dotcloud.com/dev-center/Add-on%20Documentation/Data%20Storage/ElephantSQL
[run command]: https://next.dotcloud.com/dev-center/Guides/Ruby/RunCommand
[rails-notes]: https://next.dotcloud.com/dev-center/Guides/Ruby/RailsNotes
[ruby-guides]: https://next.dotcloud.com/dev-center/Guides/Ruby
[gem itself]: http://rubygems.org/gems/cloudcontrol-rails
