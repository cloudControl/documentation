# Deploying a Ruby on Rails Application

In this tutorial we're going to show you how to deploy a [Ruby on Rails] application on [cloudControl]. You can find the [source code on Github][example-app] and check out the [Ruby buildpack][ruby buildpack] for supported features. The application is a fork of Michael Hartl's [Rails tutorial] sample app which is a Twitter clone.

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

Now that the app is working, lets have a look at changes we have made to deploy it on cloudControl.

### Process Type Definition

cloudControl uses a [Procfile] to know how to start your processes. The example code already includes a file called Procfile in the root of your repository. It looks like this:

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

By default, Rails 3 uses SQLite for all the environments. However, it is not recommended to use SQLite on cloudControl because the filesystem is [not persistent][filesystem]. To use a database, you should choose an Add-on from [Data Storage category][data-storage-addons].

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

Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create ruby
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 5, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (2/2), done.
Writing objects: 100% (3/3), 296 bytes, done.
Total 3 (delta 1), reused 0 (delta 0)

-----> Receiving push
-----> Installing dependencies using Bundler version 1.2.1
       Running: bundle install --without development:test --path vendor/bundle --binstubs bin/ --deployment
       Using rake (10.1.0)
       Using multi_json (1.2.0)
       ...
       Using will_paginate (3.0.4)
       Your bundle is complete! It was installed into ./vendor/bundle
       Cleaning up the bundler cache.
-----> Preparing app for Rails asset pipeline
       Running: rake assets:precompile
       /usr/bin/ruby1.9.1 /srv/tmp/builddir/vendor/bundle/ruby/1.9.1/bin/rake assets:precompile:nondigest RAILS_ENV=production RAILS_GROUPS=assets
       Asset precompilation completed (3.73s)
-----> Rails plugin injection
       Injecting rails_log_stdout
       Injecting rails3_serve_static_assets
-----> Building image
-----> Uploading image (8.7M)

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
[cloudControl]: http://www.cloudcontrol.com
[example-app]: https://github.com/cloudControl/ruby-rails-example-app
[ruby buildpack]: https://github.com/cloudControl/buildpack-ruby
[Rails tutorial]: http://ruby.railstutorial.org/
[Bundler]: http://bundler.io/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[filesystem]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#non-persistent-filesystem
[data-storage-addons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/
[postgres-addon]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/ElephantSQL
[run command]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/RunCommand
[rails-notes]: https://www.cloudcontrol.com/dev-center/Guides/Ruby/RailsNotes
[ruby-guides]: https://www.cloudcontrol.com/dev-center/Guides/Ruby
[gem itself]: http://rubygems.org/gems/cloudcontrol-rails
