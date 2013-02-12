# Deploying a Sinatra application
[Sinatra][sinatra] is a DSL for quickly creating web applications in Ruby with minimal effort.

In this tutorial we're going to show you how to deploy a Sinatra application on
[cloudControl]. You can find the [source code on Github][example-app].
Check out the [Ruby buildpack] for supported features.


## Prerequisites
*   [cloudControl user account][cloudControl-doc-user]
*   [cloudControl command line client][cloudControl-doc-cmdline]
*   [git]


## Cloning a Hello World application
First, clone the hello world app from our repository:
~~~bash
$ git clone git://github.com/cloudControl/ruby-sinatra-example-app.git
$ cd ruby-sinatra-example-app
~~~

Now you have a small but fully functional Sinatra application.


## Dependency declaration with Bundler
[Bundler] requirements are read from the `Gemfile` (and `Gemfile.lock`) in the project's root directory.

For this simple app the only requirement is Sinatra itself:
~~~ruby
source :rubygems
gem 'sinatra'
~~~

Note that there is also the `Gemfile.lock`. When you change the dependencies,
you should run the `bundle install` command to update the `Gemfile.lock`.

This file must be in your repository and ensures that all the developers always
use the same versions of all the gems. It also makes the changes visible in git.

## Process type definitions
cloudControl uses a [Procfile] to know how to start your processes.

There must be a file called `Procfile` at the top level of your repository, with the following content:
~~~
web: bundle exec ruby server.rb -p $PORT
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and deploying your app
Choose a unique name (from now on called APP_NAME) for your application and
create it on the cloudControl platform:
~~~bash
$ cctrlapp APP_NAME create ruby
~~~

Push your code to the application's repository. This will create a deployment image:
~~~
$ cctrlapp APP_NAME/default push
    Counting objects: 6, done.
    Delta compression using up to 8 threads.
    Compressing objects: 100% (4/4), done.
    Writing objects: 100% (6/6), 650 bytes, done.
    Total 6 (delta 0), reused 0 (delta 0)

    -----> Receiving push
    -----> Installing dependencies using Bundler version 1.2.1
        Running: bundle install --without development:test --path vendor/bundle --binstubs bin/ --deployment
        Fetching gem metadata from http://rubygems.org/..........
        Fetching gem metadata from http://rubygems.org/..
        Installing rack (1.4.4)
        Installing rack-protection (1.3.2)
        Installing tilt (1.3.3)
        Installing sinatra (1.3.3)
        Using bundler (1.2.1)
        Your bundle is complete! It was installed into ./vendor/bundle
        Cleaning up the bundler cache.
    -----> Building image
    -----> Uploading image (748K)

    To ssh://APP_NAME@cloudcontrolled.com/repository.git
    * [new branch]      master -> master
~~~
Deploy the app:
~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.


[sinatra]: http://www.sinatrarb.com/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-user]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#user-accounts
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[ruby buildpack]: https://github.com/cloudControl/buildpack-ruby
[procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[git]: https://help.github.com/articles/set-up-git
[bundler]: http://gembundler.com/
[example-app]: https://github.com/cloudControl/ruby-sinatra-example-app
