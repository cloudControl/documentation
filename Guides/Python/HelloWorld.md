# Deploying a Flask application
[Flask] is a microframework for Python based on Werkzeug, Jinja 2 and good
intentions.

In this tutorial we're going to show you how to deploy a Flask
application on [cloudControl]. You can find the [source code on Github][example_app] and check out the [Python buildpack] for
supported features.

## The Flask App Explained

### Get the App
First, let's clone the Flask App from our repository on Github:

~~~bash
$ git clone git://github.com/cloudControl/python-flask-example-app.git
$ cd python-flask-example-app
~~~

Now you have a small but fully functional Flask application.

### Dependency Tracking
The Python buildpack tracks dependencies via pip and the `requirements.txt` file. It needs to be placed in the root directory of your repository. The example app specifies only Flask itself as a dependency and looks like this:

~~~pip
Flask==0.9
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes a file called `Procfile` at the top level of your repository. It looks like this:

~~~
web: python server.py
~~~

Left from the colon we specified the **required** process type called `web` followed by the command that starts the app and listens on the port specified by the environment variable `$PORT`.

## Pushing and Deploying the App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create python
~~~

Push your code to the application's repository, which triggers the deployment image build process:

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

Last but not least deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Flask app running at `http[s]://APP_NAME.cloudcontrolled.com`.

[Flask]: http://flask.pocoo.org/
[cloudControl]: http://www.cloudcontrol.com
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[example_app]: https://github.com/cloudControl/python-flask-example-app.git
