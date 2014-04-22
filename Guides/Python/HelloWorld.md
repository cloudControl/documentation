# Deploying a Flask application
[Flask] is a microframework for Python based on Werkzeug, Jinja 2 and good
intentions.

In this tutorial we're going to show you how to deploy a Flask
application on [exoscale]. You can find the [source code on Github][example_app] and check out the [Python buildpack] for
supported features.

## The Flask App Explained

### Get the App
First, let's clone the Flask App from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/python-flask-example-app.git
$ cd python-flask-example-app
~~~

Now you have a small but fully functional Flask application.

### Dependency Tracking
The Python buildpack tracks dependencies via pip and the `requirements.txt` file. It needs to be placed in the root directory of your repository. The example app specifies only Flask itself as a dependency and looks like this:

~~~pip
Flask==0.9
~~~

### Process Type Definition
exoscale uses a [Procfile] to know how to start your processes.

The example code already includes a file called `Procfile` at the top level of your repository. It looks like this:

~~~
web: python server.py
~~~

Left from the colon we specified the **required** process type called `web` followed by the command that starts the app and listens on the port specified by the environment variable `$PORT`.

## Pushing and Deploying the App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the exoscale platform:

~~~bash
$ exoapp APP_NAME create python
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ exoapp APP_NAME/default push
Counting objects: 16, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (10/10), done.
Writing objects: 100% (16/16), 258.30 KiB, done.
Total 16 (delta 2), reused 16 (delta 2)
       
-----> Receiving push
-----> No runtime.txt provided; assuming python-2.7.3.
-----> Preparing Python runtime (python-2.7.3)
-----> Installing Distribute (0.6.36)
-----> Installing Pip (1.3.1)
-----> Installing dependencies using Pip (1.3.1)
       Downloading/unpacking Flask==0.9 (from -r requirements.txt (line 1))
       ...
       Successfully installed Flask Werkzeug Jinja2 markupsafe
       Cleaning up...
-----> Building image
-----> Uploading image (25M)
       
To ssh://APP_NAME@app.exo.io/repository.git
 * [new branch]      master -> master

~~~

Last but not least deploy the latest version of the app with the exoapp deploy command:

~~~bash
$ exoapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Flask app running at `http[s]://APP_NAME.app.exo.io`.

[Flask]: http://flask.pocoo.org/
[exoscale]: http://www.exoscale.ch
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[example_app]: https://github.com/cloudControl/python-flask-example-app.git
