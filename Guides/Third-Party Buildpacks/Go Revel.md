# Deploying a Revel Application

[Revel] is an open source web framework for Go.

In this tutorial we're going to show you how to deploy a simple Revel based
application on [cloudControl].

## The Example App Explained

### Get the App
First, lets clone the example code from Github.

~~~bash
$ git clone https://github.com/cloudControl/go-revel-example-app.git
$ cd go-revel-example-app
~~~

The code from the example repository is ready to be deployed. Lets still go
through the different files and their purpose real quick.

### Dependency Tracking

The [Revel buildpack] handles the Revel dependencies automatically.

### Process Type Definition
cloudControl uses a [Procfile] to know how to start the app's processes. The example app provides a Procfile. It runs the revel app in production mode and listens on the port specified by the `$PORT` environment variable.

~~~
web: revel run github.com/cloudControl/go-revel-example-app prod $PORT
~~~

### The Actual Application Code

The example app uses the routes and controllers as bootstrapped using `revel new`. Using the routes requests to `/` are routed to the App controller, which simply renders and returns the Index template. The files look like this:

`app/controllers/app.go`

~~~go
package controllers

import "github.com/revel/revel"

type App struct {
	*revel.Controller
}

func (c App) Index() revel.Result {
	return c.Render()
}
~~~

`conf/routes`

~~~
# Routes
# This file defines all application routes (Higher priority routes first)
# ~~~~

module:testrunner

GET     /                                       App.Index

# Ignore favicon requests
GET     /favicon.ico                            404

# Map static resources from the /app/public folder to the /public path
GET     /public/*filepath                       Static.Serve("public")

# Catch all
*       /:controller/:action                    :controller.:action
~~~

## Pushing and Deploying the App

Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the cloudControl platform using the custom Go buildpack:

~~~bash
$ cctrlapp APP_NAME create custom --buildpack https://github.com/revel/heroku-buildpack-go-revel
~~~

Push your code to the application's repository, which triggers the deployment
image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 463, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (334/334), done.
Writing objects: 100% (463/463), 719.18 KiB | 279.00 KiB/s, done.
Total 463 (delta 47), reused 463 (delta 47)
       
-----> Receiving push
-----> Installing go1.3.1... done
       Installing Virtualenv... done
       Installing Mercurial... done
       Installing Bazaar... done
-----> Copying workspace
-----> Running: godep go install -tags heroku ./...
cat: /srv/tmp/builddir/.godir: No such file or directory
-----> Building image
remote: -----> Custom buildpack provided
-----> Uploading image (57.0 MB)
       
To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the cctrlapp
deploy command.

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Revel app running at `http://APP_NAME.cloudcontrolled.com`. The command line client provides the `open` command to quickly open the app in your default browser.

~~~bash
$ cctrlapp APP_NAME/default open
~~~

[Revel]: http://revel.github.io
[cloudControl]: https://www.cloudcontrol.com
[Revel buildpack]: https://github.com/revel/heroku-buildpack-go-revel
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
