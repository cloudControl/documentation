# Deploying a Tornado Application
[Tornado] is an open source version of the scalable, non-blocking web server and tools that power FriendFeed written in Python.

In this tutorial we're going to show you how to deploy a simple Tornado based application on [cloudControl].

## The Example App Explained
First, lets clone the example code from Github.

~~~bash
$ git clone git://github.com/cloudControl/python-tornado-example-app.git
$ cd python-tornado-example-app
~~~

The code from the example repository is ready to be deployed. Let's still go through the different files and their purpose real quick.

### Dependency Tracking
The [Python buildpack] tracks dependencies via pip and the `requirements.txt` file. It needs to be placed in the root directory of your repository. The example app specifies only Tornado itself as a dependency. The one you cloned as part of the example app looks like this:

~~~pip
tornado==2.4.1
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start the app's processes.

The example code already includes a file called `Procfile` at the top level of your repository. It looks like this:

~~~
web: python app.py --port=$PORT --logging=error
~~~

Left from the colon we specified the **required** process type called `web` followed by the command that starts the app and listens on the port specified by the environment variable `$PORT`.

### The Actual Application Code

The actual application code is really straight forward. It provides one handler and sets up the app to handle incoming requests asynchronously and return `Hello, world!`.

Last but not least, we define the command line parameter to specify the port as seen in the `Procfile` above, instantiate the HTTP server, make it listen on the specified port and start the server forking one process per CPU core.

~~~python
from tornado.options import define, options
import tornado.ioloop
import tornado.web
import tornado.httpserver


class MainHandler(tornado.web.RequestHandler):
    @tornado.web.asynchronous
    def get(self):
        self._async_callback()

    def _async_callback(self):
        self.write("Hello, world!")
        self.finish()

app = tornado.web.Application([
    (r"/", MainHandler),
])

define("port", default="5555", help="Port to listen on")

if __name__ == "__main__":
    tornado.options.parse_command_line()
    server = tornado.httpserver.HTTPServer(app)
    server.bind(options.port)
    # autodetect cpu cores and fork one process per core
    try:
        server.start(0)
        tornado.ioloop.IOLoop.instance().start()
    except KeyboardInterrupt:
        tornado.ioloop.IOLoop.instance().stop()
~~~

## Pushing and Deploying the App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create python
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 10, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (6/6), done.
Writing objects: 100% (7/7), 1.08 KiB, done.
Total 7 (delta 4), reused 0 (delta 0)
       
-----> Receiving push
-----> Preparing Python interpreter (2.7.2)
-----> Creating Virtualenv version 1.7.2
       New python executable in .heroku/venv/bin/python2.7
       Not overwriting existing python script .heroku/venv/bin/python (you must use .heroku/venv/bin/python2.7)
       Installing distribute..................................................................................................................................................................................................done.
       Installing pip................done.
       Running virtualenv with interpreter /usr/bin/python2.7
-----> Activating virtualenv
-----> Installing dependencies using pip version 1.2.1
       Requirement already satisfied (use --upgrade to upgrade): tornado==2.4.1 in ./.heroku/venv/lib/python2.7/site-packages (from -r requirements.txt (line 1))
       Cleaning up...
-----> Building image
-----> Uploading image (2.4M)
       
To ssh://APP_NAME@cloudcontrolled.com/repository.git
   a624133..1e0630f  master -> master
~~~

Last but not least deploy the latest version of the app with the cctrlapp deploy command.

~~~bash
$ cctrlapp APP_NAME/default deploy 
~~~

Congratulations, you can now see your Tornado app running at `http://APP_NAME.cloudcontrolled.com`.

[Tornado]: http://www.tornadoweb.org
[cloudControl]: http://www.cloudcontrol.com
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
