# Deploying a Tornado Application

[Tornado] is an open source version of the scalable, non-blocking web server
and tools that power FriendFeed written in Python.

In this tutorial we're going to show you how to deploy a simple Tornado based
application on [exoscale].

## The Example App Explained

### Get the App
First, lets clone the example code from Github.
~~~bash
$ git clone https://github.com/cloudControl/python-tornado-example-app.git
$ cd python-tornado-example-app
~~~

The code from the example repository is ready to be deployed. Lets still go
through the different files and their purpose real quick.

### Dependency Tracking

The [Python buildpack] tracks dependencies via pip and the `requirements.txt`
file. It needs to be placed in the root directory of your repository. The
example app specifies only Tornado itself as a dependency. The one you cloned
as part of the example app looks like this:
~~~pip
tornado==2.4.1
~~~

### Process Type Definition
exoscale uses a [Procfile] to know how to start the app's processes.

The example code already includes a file called `Procfile` at the top level of
your repository. It looks like this:
~~~
web: python app.py --port=$PORT --logging=error
~~~

Left from the colon we specified the **required** process type called `web`
followed by the command that starts the app and listens on the port specified
by the environment variable `$PORT`.

### The Actual Application Code

The actual application code is really straight forward. It provides one handler
and sets up the app to handle incoming requests asynchronously and render the
defined template.

Finally, we define the command line parameter to specify the port as
seen in the `Procfile` above, instantiate the HTTP server, make it listen on
the specified port and start the server forking one process per CPU core.
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

Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the exoscale platform:
~~~bash
$ exoapp APP_NAME create python
~~~

Push your code to the application's repository, which triggers the deployment
image build process:
~~~bash
$ exoapp APP_NAME/default push
Counting objects: 7, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (4/4), done.
Writing objects: 100% (7/7), 1.01 KiB, done.
Total 7 (delta 0), reused 7 (delta 0)
       
-----> Receiving push
-----> No runtime.txt provided; assuming python-2.7.3.
-----> Using Python runtime (python-2.7.3)
-----> Installing dependencies using Pip (1.3.1)
       Downloading/unpacking tornado==2.4.1 (from -r requirements.txt (line 1))
       ...
       Successfully installed tornado
       Cleaning up...
-----> Building image
-----> Uploading image (25M)
       
To ssh://APP_NAME@app.exo.io/repository.git
 + [new branch] master -> master
~~~

Last but not least deploy the latest version of the app with the exoapp
deploy command.
~~~bash
$ exoapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Tornado app running at `http://APP_NAME.app.exo.io`.

[Tornado]: http://www.tornadoweb.org
[exoscale]: http://www.exoscale.ch
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
