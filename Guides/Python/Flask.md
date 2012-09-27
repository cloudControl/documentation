# Deploying a simple python-app with flask
Flask is a microframework for Python based on Werkzeug, Jinja 2 and good
intentions.

In this tutorial we're going to show you how to deploy a simple python-web-app
using the example of [flask][flask]. We will cover defining
the requirements, creating the Procfile and deploying your server on 
[the cloudControl platform][cloudControl].


## Prerequisites
*   Basic Python knowledge.
*   Installed [Python](http://python.org/) and [virtualenv][virtualenv].
    See [this guide](http://install.python-guide.org/) for guidance.
*   Your application must run on Python 2.7.
*   Your application must use Pip to resolve dependencies.
*   A [cloudControl][cloudControl] user account.
*   Installed [cloudControl-command-line-client][cloudControl-doc-cmdline]
*   A [Git client](http://git-scm.com/), whether command-line or GUI. 
    If you're a GUI fan, there are some excellent options available. 
    These include:
    *   [GitX](http://gitx.frim.nl/)
    *   [Github for Mac](http://mac.github.com/)
    *   [Github for Windows](http://windows.github.com/)
    *   [Gitbox](http://www.gitboxapp.com/)
    *   [git-cola](http://git-cola.github.com/)
    *   [Tower](http://www.git-tower.com/)
    *   [TortoiseGit](http://code.google.com/p/gitextensions/)


## Start Flask app inside a Virtualenv
We create a small hello-world-app, but you can still take your own app and
adapt our tutorial. 

First create an empty directory for your application and switch to it:

    $ mkdir hello-python && cd hello-python

Create a new virtualenv to use with this application and activate it

    $ virtualenv venv --distribute
      New python executable in venv/bin/python
      Installing distribute...............done.
      Installing pip...............done.
    $ source venv/bin/activate

Now you will be inside your separate python-environment for your application.
You will see the current active environment at the beginning of your prompt. 
Each time you want to work on it you have to activate your environment. An
alternative for advanced usage is [virtualenvwrapper][virtualenvwrapper].

Now we install our dependencies: 

    $ pip install Flask

    Downloading/unpacking Flask
      Downloading Flask-0.9.tar.gz (481kB): 481kB downloaded
      Running setup.py egg_info for package Flask

    Downloading/unpacking Werkzeug>=0.7 (from Flask)
      Downloading Werkzeug-0.8.3.tar.gz (1.1MB): 1.1MB downloaded
      Running setup.py egg_info for package Werkzeug

    Downloading/unpacking Jinja2>=2.4 (from Flask)
      Downloading Jinja2-2.6.tar.gz (389kB): 389kB downloaded
      Running setup.py egg_info for package Jinja2

    Installing collected packages: Flask, Werkzeug, Jinja2
      Running setup.py install for Flask
      Running setup.py install for Werkzeug
      Running setup.py install for Jinja2

    Successfully installed Flask Werkzeug Jinja2
    Cleaning up...

### server.py 
Since flask is installed we can create our application, `server.py`: 

    import os
    from flask import Flask

    app = Flask(__name__)


    @app.route('/')
    def hello():
        return 'Hello World!'


    if __name__ == '__main__':
        port = int(os.environ.get('PORT', 5000))
        app.run(host='0.0.0.0', port=port)
   

## Declare dependencies with Pip
The [cloudControl-platform][cloudControl] recognizes your dependencies by the
content of a file called `requirements.txt` in your project-root. 

Let's create one: 

    $ pip freeze > requirements.txt

Now our installed packages are declared explicitly in the `requirements.txt`: 

    $ cat requirements.txt
        Flask==0.9
        Jinja2==2.6
        Werkzeug==0.8.3
        distribute==0.6.28


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
    containing out requirements
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

    $ cctrlapp pythontest create python

Push your code to [cloudControl][cloudControl]:

    $ cctrlapp pythontest/default push
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
            
        To ssh://pythontest@cloudcontrolled.com/repository.git
        460bdac..28dd4d5  master -> master


To allow your app to be accessed via web you have to deploy it: 

    $ cctrlapp pythontest/default deploy 


Now we see some deploy- and error-logs in our output. 
*   The deploy-log shows us how often, when and where our application was deployed.
*   The error-log shows us all output (stdout and stderr) of our declared web-process 


    $ cctrlapp pythontest/default log deploy 
        [Thu Dec 24 12:31:34] repo-1 INFO Initialized deployment
        [Thu Dec 24 12:46:57] lxc-199 INFO Deploying ...
        [Thu Dec 24 12:46:59] lxc-199 INFO Deployed version: 28dd4d532c54b87e8f06e6ddbaa076be5579269d
        [Thu Dec 24 12:47:12] lb-1 INFO Routing requests to new version
        [Thu Dec 24 12:47:41] lb-1 INFO Routing requests to new version
        [Thu Dec 24 12:47:54] lb-4-new INFO Routing requests to new version
        [Thu Dec 24 12:48:38] lb-4-new INFO Routing requests to new version

    $ cctrlapp pythontest/default log error 
        [Thu Dec 24 12:46:59 2012] deploy ***** Deployed 28dd4d532c54b87e8f06e6ddbaa076be5579269d *****
        [Thu Dec 24 12:47:01 2012] info  * Running on http://0.0.0.0:21692/



You can now reach your application via [http://pythontest.cloudcontrolled.com](http://pythontest.cloudcontrolled.com)

## scaling 
Scaling on [cloudControl][cloudControl] is easy. 
Just pick the number of clones and the size of then and redeploy: 

    $ cctrlapp pythontest/default deploy --min 4 --max 2

Which means "run 4 web-processes with get 256 MB RAM each". 


[flask]: http://flask.pocoo.org/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/documentation/getting-started/command-line-client "documentation of the cloudControl-command-line-client"
[virtualenv]: http://pypi.python.org/pypi/virtualenv
[virtualenvwrapper]: http://www.doughellmann.com/projects/virtualenvwrapper/
