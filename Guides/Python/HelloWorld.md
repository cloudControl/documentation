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
$ git clone https://github.com/cloudControl/python-flask-example-app.git
$ cd python-flask-example-app
~~~

The code from the example repository is ready to be deployed. Lets still go
through the different files and their purpose real quick.

### Dependency Tracking
The Python buildpack tracks dependencies via pip and the `requirements.txt` file. It needs to be placed in the root directory of your repository. The example app specifies only Flask itself as a dependency and looks like this:

~~~pip
Flask==0.10.1
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes a file called `Procfile` at the top level of your repository. It looks like this:

~~~
web: python server.py
~~~

Left from the colon we specified the **required** process type called `web` followed by the command that starts the app and listens on the port specified by the environment variable `$PORT`.

###The Actual Application Code

The actual application code is really straight forward. We import the required
modules and create an instance of the class. Next we set the routes to trigger
our hello() function which will then render the jinja template.

~~~python
import os
from flask import Flask, render_template

app = Flask(__name__)


@app.route('/')
def hello():
    return render_template('hello.jinja', domain=os.environ['DOMAIN'])

app.debug = True
app.run(host='0.0.0.0', port=int(os.environ['PORT']))
~~~

## Pushing and Deploying the App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create python
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 3, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (2/2), done.
Writing objects: 100% (3/3), 283 bytes | 0 bytes/s, done.
Total 3 (delta 1), reused 0 (delta 0)

-----> Receiving push
-----> No runtime.txt provided; assuming python-2.7.8.
-----> Preparing Python runtime (python-2.7.8)
-----> Installing Distribute (0.6.36)
-----> Installing Pip (1.3.1)
-----> Installing dependencies using Pip (1.3.1)
       Downloading/unpacking Flask==0.10.1 (from -r requirements.txt (line 1))
        ...
       Successfully installed Flask Werkzeug Jinja2 itsdangerous MarkupSafe
       Cleaning up...
-----> Building image
-----> Uploading image (25.1 MB)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master

~~~

Last but not least deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Flask app running at `http[s]://APP_NAME.cloudcontrolled.com`.

[Flask]: http://flask.pocoo.org/
[cloudControl]: http://www.cloudcontrol.com
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.cloudcontrol.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[example_app]: https://github.com/cloudControl/python-flask-example-app.git
