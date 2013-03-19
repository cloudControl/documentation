# Deploying a Pyramid application
[Pyramid] is described as a "small, fast, down-to-earth, open-source Python web application development framework." And it is as fast and down-to-earth to make Pyramid work on clouControl! 
Within minutes, you are going to deploy a simple "Hello World!" Pyramid application on [cloudControl]. 
(To learn more about features, please consult our documentation on [Python buildpack].)

## Prerequisites
Feel free to simply read through this document... However, if you wish to finish this tutorial, you may need:
+ A [Git client]. Command-line or GUI, it's up to you to decide!
+ A [cloudControl] user account.
+ Installed [cloudControl-command-line-client]

## Cloning a simple application
We begin by cloning the python-pyramid-example-app folder from our repository:

~~~bash
$ git clone git://github.com/cloudControl/python-pyramid-example-app.git
$ cd python-pyramid-example-app
~~~

In addition to the original source code of the app, app.py, you can see there are two more interesting files in the folder:
+ requirements.txt
+ Procfile
Those files provide information on dependency requirements and process type, essential to cloudControl. Once you've got this part sorted, you are practically done!


### Dependency declaration with Pip
'requirements.txt' lists our dependency requirements. The file should always be saved in the project's root directory.
In our case, this Pyramid app is simple enough to have only one requirement: Pyramid!

~~~pip
Pyramid==1.3
~~~

It is strongly advised you specify the versions of your dependencies. This way, you will get reproducable builds and prevent unexpected errors caused by version changes.


### Process type definitions
cloudControl uses a [Procfile] to know how to start your processes.
Just create a file called `Procfile` at the top level of your repository, with the following content:

~~~
web: python server.py
~~~

The web process type specifies the command that will be executed when the app is deployed.

## Pushing and deploying your app
Almost there! Now choose a unique name (here: APP_NAME) for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create python
~~~

Push your code to the application's repository, which creates a deployment image:

~~~bash
$ cctrlapp APP_NAME/default push
    Counting objects: 8, done.
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
           Successfully installed Pyramid Jinja2 Werkzeug distribute
           Cleaning up...
    -----> Building image
    -----> Uploading image (3.2M)
        
    To ssh://APP_NAME@cloudcontrolled.com/repository.git
    460bdac..28dd4d5  master -> master
~~~

Now deploy and watch:

~~~bash
$ cctrlapp APP_NAME/default deploy 
~~~

And... that's it, really! You should now be able to reach your application at: 
`http://APP_NAME.cloudcontrolled.com`. However, you won't see much except for a 404 Error. To prompt "Hello world!" as explained in this [Pyramid tutorial], visit:
http://testpyramid.cloudcontrolled.com/hello/world


[Pyramid]: http://www.pylonsproject.org/projects/pyramid/about
[Pyramid tutorial]: http://docs.pylonsproject.org/projects/pyramid/en/1.3-branch/
[cloudControl]: http://www.cloudcontrol.com
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[cloudControl-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
