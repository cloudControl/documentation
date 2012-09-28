# Deploying a simple ruby-app with Sinatra
Sinatra is a [DSL](http://en.wikipedia.org/wiki/Domain-specific_language) for quickly creating web applications in Ruby with minimal effort.

In this tutorial we're going to show you how to deploy a simple ruby-web-app
using the example of [Sinatra][sinatra]. We will cover defining
the requirements, creating the Procfile and deploying your app on 
[the cloudControl platform][cloudControl].


## Prerequisites
*   Installed [Ruby](http://www.ruby-lang.org/). For this tutorial we'll use 1.9 version.
* [Rubygems][rubygems] installed. See [this guide](http://rubygems.org/pages/download) for install and get your gems up to date.
*   A [cloudControl][cloudControl] user account.
*   Installed [cloudControl-command-line-client][cloudControl-doc-cmdline]
*   A [Git client](http://git-scm.com/), whether command-line or GUI. 


## Getting started
We create a small hello-world-app, but you can still take your own app and
adapt our tutorial.

First create an empty directory for your application and switch to it:

	$ mkdir hello-ruby && cd hello-ruby

Now we install our dependencies. In this case we need to install two gems, [Bundler](http://rubygems.org/gems/bundler), which will manage our app dependencias, and [Sinatra](http://rubygems.org/gems/sinatra), which contains the Sinatra DSL itself.

	$ gem install bundler
	[sudo] password for fa: 
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

## Declare dependencies with Pip
As you are building a Rack-based app, the [cloudControl-platform][cloudControl] recognizes your dependencies by the file called Gemfile and your settings by the file called config.ru, both in your project-root. 

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

    web: python server.py 


## Store your app in Git
First we need to ignore the virtualenv and compiled python-files: 
Create a File called `.gitignore` with these contents: 

    venv
    *.pyc

Now there are the three major components of a python-app left. 
*   `requirements.txt`
    containing our requirements
*   `Procfile`
    declaration of our web-process
*   `server.py`
    our web-server itself

Let's put them into git: 

    $ git init
    $ git add .
    $ git commit -m "init"


## Deploy to cloudControl
Create the app: 

    $ cctrlapp APP_NAME create python
    
**Note:** App names have to be unique because they are used as the `.cloudcontrolled.com` subdomain. Choose one for your name and replace the APP_NAME placeholder accordingly.

Push your code to [cloudControl][cloudControl]:

    $ cctrlapp APP_NAME/default push
        Counting objects: 7, done.
        Delta compression using up to 4 threads.
        Compressing objects: 100% (5/5), done.
        Writing objects: 100% (6/6), 614 bytes, done.
        Total 6 (delta 1), reused 0 (delta 0)
            
        -----> Receiving push
        -----> Preparing Python interpreter (2.7.2)
        -----> Creating Virtualenv version 1.7.2
               New python executable in .heroku/venv/bin/python2.7
               Also creating executable in .heroku/venv/bin/python
               Installing distribute.........done.
               Installing pip................done.
               Running virtualenv with interpreter /usr/bin/python2.7
        -----> Activating virtualenv
        -----> Installing dependencies using pip version 1.1
               ...
               Successfully installed Flask Jinja2 Werkzeug distribute
               Cleaning up...
        -----> Building image
        -----> Uploading image (3.2M)
            
        To ssh://APP_NAME@cloudcontrolled.com/repository.git
        460bdac..28dd4d5  master -> master


To allow your app to be accessed via web you have to deploy it: 

    $ cctrlapp APP_NAME/default deploy 


We can see what's going on by looking at the deploy- and error-logs.
*   The deploy-log shows us how often, when and where our application was deployed.
*   The error-log shows us all output (stdout and stderr) of our declared web-process 

~~~
$ cctrlapp APP_NAME/default log deploy 
    [Thu Dec 24 12:31:34] repo-1 INFO Initialized deployment
    [Thu Dec 24 12:46:57] lxc-199 INFO Deploying ...
    [Thu Dec 24 12:46:59] lxc-199 INFO Deployed version: 28dd4d532c54b87e8f06e6ddbaa076be5579269d
    [Thu Dec 24 12:47:12] lb-1 INFO Routing requests to new version
    [Thu Dec 24 12:47:41] lb-1 INFO Routing requests to new version
    [Thu Dec 24 12:47:54] lb-4-new INFO Routing requests to new version
    [Thu Dec 24 12:48:38] lb-4-new INFO Routing requests to new version
~~~

~~~
$ cctrlapp APP_NAME/default log error 
    [Thu Dec 24 12:46:59 2012] deploy ***** Deployed 28dd4d532c54b87e8f06e6ddbaa076be5579269d *****
    [Thu Dec 24 12:47:01 2012] info  * Running on http://0.0.0.0:21692/
~~~


You can now reach your application via `http://APP_NAME.cloudcontrolled.com`.

## scaling 
Scaling on [cloudControl][cloudControl] is easy. 
Just pick the number of clones and the desired amount of memory per clone and redeploy: 

    $ cctrlapp APP_NAME/default deploy --min 4 --max 2

Which means "run 4 clones which get 256 MB RAM each".


[sinatra]: http://http://www.sinatrarb.com/
[rubygems]: http://rubygems.org/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api "documentation of the cloudControl-command-line-client"
[virtualenv]: http://pypi.python.org/pypi/virtualenv
[virtualenvwrapper]: http://www.doughellmann.com/projects/virtualenvwrapper/
