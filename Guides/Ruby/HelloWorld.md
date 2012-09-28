# Deploying a simple ruby-app with Sinatra
Sinatra is a [DSL](http://en.wikipedia.org/wiki/Domain-specific_language) for quickly creating web applications in Ruby with minimal effort.

In this tutorial we're going to show you how to deploy a simple ruby-web-app
using the example of [Sinatra][sinatra]. We will cover defining
the requirements, creating the Procfile and deploying your app on 
[the cloudControl platform][cloudControl].


## Prerequisites
* Basic Ruby knowledge
* Installed [Ruby](http://www.ruby-lang.org/). For this tutorial we'll use 1.9 version.
* [Rubygems][rubygems] installed. See [this guide](http://rubygems.org/pages/download) to install and get your gems up to date.
*   A [cloudControl][cloudControl] user account.
*   Installed [cloudControl-command-line-client][cloudControl-doc-cmdline]
*   A [Git client](http://git-scm.com/), whether command-line or GUI. 


## Getting started
We create a small hello-world-app, but you can still take your own app and
adapt our tutorial.

First create an empty directory for your application and switch to it:

	$ mkdir hello-ruby && cd hello-ruby

Now we install our dependencies. In this case we need to install two gems, [Bundler](http://rubygems.org/gems/bundler), which will manage our app dependencies, and [Sinatra](http://rubygems.org/gems/sinatra), which contains the Sinatra DSL itself.

	$ gem install bundler 
	Fetching: bundler-1.2.1.gem (100%)
	Successfully installed bundler-1.2.1
	1 gem installed


	$ gem install sinatra
	Fetching: rack-1.4.1.gem (100%)
	Fetching: rack-protection-1.2.0.gem (100%)
	Fetching: tilt-1.3.3.gem (100%)
	Fetching: sinatra-1.3.3.gem (100%)
	Successfully installed rack-1.4.1
	Successfully installed rack-protection-1.2.0	
	Successfully installed tilt-1.3.3
	Successfully installed sinatra-1.3.3
	4 gems installed
	Installing ri documentation for rack-1.4.1...
	Installing ri documentation for rack-protection-1.2.0...
	Installing ri documentation for tilt-1.3.3...
	Installing ri documentation for sinatra-1.3.3...
	Installing RDoc documentation for rack-1.4.1...
	Installing RDoc documentation for rack-protection-1.2.0...
	Installing RDoc documentation for tilt-1.3.3...
	Installing RDoc documentation for sinatra-1.3.3...

### helloworld.rb 
Since Sinatra is installed we can create our application, `helloworld.rb`: 

~~~ruby
# helloworld.rb
require 'sinatra'

get '/' do
  'Hello world!'
end
~~~

## Declare dependencies and configuration
As you are building a [Rack](http://rack.github.com/)-based app, the [cloudControl-platform][cloudControl] recognizes your dependencies by the file called `Gemfile` and your server settings by the file called `config.ru`, both in your project-root. 

Let's create them: 

### config.ru 

~~~ruby
require './helloworld'
run Sinatra::Application
~~~

### Gemfile 

~~~ruby
source 'http://rubygems.org'
gem 'sinatra'
~~~

## Declare process types with Procfile
To run your application, [cloudControl][cloudControl] needs to know how to run
your server-process. 
For this case you have to define a `Procfile`, which is simply a YAML-File
with this name in your project-root.

Here is the one for our test-application: 

    web: bundle exec ruby helloworld.rb -p $PORT 


## Prepare your app and store it in Git
First we need to install and lock all dependencies using Bundler:
    
    $ bundle install
    Fetching gem metadata from http://rubygems.org/.....
    Using rack (1.4.1) 
    Using rack-protection (1.2.0) 
    Using tilt (1.3.3) 
    Using sinatra (1.3.3) 
    Using bundler (1.2.1) 
    Your bundle is complete! Use `bundle show [gemname]` to see where a bundled gem is installed.

Let's put the app files into git: 

    $ git init
    $ git add .
    $ git commit -m "first commit"


## Deploy to cloudControl
Create the app: 

    $ cctrlapp APP_NAME create ruby
    
**Note:** App names have to be unique because they are used as the `.cloudcontrolled.com` subdomain. Choose one for your name and replace the APP_NAME placeholder accordingly.

Push your code to [cloudControl][cloudControl]:

    $ cctrlapp APP_NAME/default push
      Counting objects: 4, done.
      Delta compression using up to 4 threads.
      Compressing objects: 100% (3/3), done.
      Writing objects: 100% (3/3), 391 bytes, done.
      Total 3 (delta 1), reused 0 (delta 0)
     
      -----> Receiving push
      -----> Installing dependencies using Bundler version 1.2.1
             Running: bundle install --without development:test --path vendor/bundle --binstubs bin/ --deployment
             Fetching gem metadata from http://rubygems.org/.....
             Installing rack (1.4.1)
             Installing rack-protection (1.2.0)
             Installing tilt (1.3.3)
             Installing sinatra (1.3.3)
             Using bundler (1.2.1)
             Your bundle is complete! It was installed into ./vendor/bundle
             Cleaning up the bundler cache.
      -----> Building image
      -----> Uploading image (712K)
       
      To ssh://APP_NAME@cloudcontrolled.com/repository.git
         65aefe5..0a71d5b  master -> master

To allow your app to be accessed via web you have to deploy it: 

    $ cctrlapp APP_NAME/default deploy 


We can see what's going on by looking at the deploy- and error-logs.
*   The deploy-log shows us how often, when and where our application was deployed.
*   The error-log shows us all output (stdout and stderr) of our declared web-process 

~~~
$ cctrlapp APP_NAME/default log deploy 
    [Fri Sep 28 11:11:16 2012] repo-dev-1 INFO Initialized deployment
    [Fri Sep 28 11:14:21 2012] lxc-dev-134 INFO Deploying ...
    [Fri Sep 28 11:14:24 2012] lxc-dev-134 INFO Deployed version: 65aefe5e7f3cae422c7747c24052d1a466c7b5e9
    [Fri Sep 28 11:14:24 2012] ip-10-53-143-27 INFO Routing requests to new version
    [Fri Sep 28 11:14:26 2012] ip-10-234-178-109 INFO Routing requests to new version

~~~

~~~
$ cctrlapp APP_NAME/default log error 
    [Fri Sep 28 11:14:24 2012] deploy ***** Deployed 65aefe5e7f3cae422c7747c24052d1a466c7b5e9 *****
    [Fri Sep 28 11:14:25 2012] info [2012-09-28 11:14:25] INFO  WEBrick 1.3.1
    [Fri Sep 28 11:14:25 2012] info [2012-09-28 11:14:25] INFO  ruby 1.9.3 (2011-10-30) [x86_64-linux]
    [Fri Sep 28 11:14:25 2012] info == Sinatra/1.3.3 has taken the stage on 12605 for production with backup from WEBrick
    [Fri Sep 28 11:14:25 2012] info 
    [Fri Sep 28 11:14:25 2012] info [2012-09-28 11:14:25] INFO  WEBrick::HTTPServer#start: pid=15 port=12605
    [Fri Sep 28 11:14:32 2012] info 178.19.208.122 - - [28/Sep/2012 11:14:32] "GET / HTTP/1.1" 200 12 0.0039

~~~

You can now reach your application via `http://APP_NAME.cloudcontrolled.com`.

## Scaling 
Scaling on [cloudControl][cloudControl] is easy. 
Just pick the number of clones and the desired amount of memory per clone and redeploy: 

    $ cctrlapp APP_NAME/default deploy --min 4 --max 2

Which means "run 4 clones which get 256 MB RAM each".


[sinatra]: http://http://www.sinatrarb.com/
[rubygems]: http://rubygems.org/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api "documentation of the cloudControl-command-line-client"
