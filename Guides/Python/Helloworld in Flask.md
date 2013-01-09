# Deploying a Flask application
[Flask][flask] is a microframework for Python based on Werkzeug, Jinja 2 and good
intentions.

In this tutorial we're going to show you how to deploy a Hello World Flask
application on [cloudControl]. Check out the [Python buildpack] for
supported features.

## Prerequisites
*   [cloudControl user account][cloudControl-doc-user]
*   [cloudControl command line client][cloudControl-doc-cmdline]
*   [git]

## Cloning a Hello World application
First, clone the hello world app from our repository:
~~~bash
$ git clone git@github.com:cloudControl/python-flask-example-app.git
$ cd python-flask-example-app
~~~

### Dependency declaraion with pip
Pip requirements are read from `requirements.txt` in your project's root directory.
For this simple app the only requirement is Flask itself:
~~~pip
Flask==0.9
~~~
You should always specify the versions of your dependencies, if you want your builds to
be reproducable and to prevent unexpected errors caused by version changes.

### Process type definitions
cloudControl uses a [Procfile] to know how to start your processes.

There must be a file called `Procfile` at the top level of your repository, with the following content:
~~~
web: python server.py
~~~
The web process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and deploying your app
Choose a unique name (from now on called APP_NAME) for your application and create it on the cloudControl platform: 
~~~bash
$ cctrlapp APP_NAME create python
~~~

Push your code to the application's repository, which creates a deployment image:
~~~bash
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
~~~
Deploy your app: 
~~~bash
$ cctrlapp APP_NAME/default deploy 
~~~

Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.


[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[git]: https://help.github.com/articles/set-up-git
[flask]: http://flask.pocoo.org/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-user]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#user-accounts
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api "documentation of the cloudControl-command-line-client"
[Python buildpack]: https://github.com/cloudControl/buildpack-python
