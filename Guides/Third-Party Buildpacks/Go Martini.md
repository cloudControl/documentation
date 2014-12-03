# Deploying a Martini Application

[Martini] is an open source web framework for Go.

In this tutorial we're going to show you how to deploy a simple Martini based
application on [dotCloud].

## The Example App Explained

### Get the App
First, lets clone the example code from Github.

~~~bash
$ git clone https://github.com/cloudControl/go-martini-example-app.git
$ cd go-martini-example-app
~~~

The code from the example repository is ready to be deployed. Lets still go
through the different files and their purpose real quick.

### Dependency Tracking

The [Go buildpack] tracks dependencies via Godep and the `Godeps.json`
file. It specifies Martini, it's dependencies and Render, which is used to render HTML templates as dependencies. The one you cloned
as part of the example app looks like this:

~~~json
{
	"ImportPath": "github.com/cloudControl/go-martini-example-app",
	"GoVersion": "go1.3.1",
	"Deps": [
		{
			"ImportPath": "github.com/codegangsta/inject",
			"Comment": "v1.0-rc1-4-g4b81725",
			"Rev": "4b8172520a03fa190f427bbd284db01b459bfce7"
		},
		{
			"ImportPath": "github.com/codegangsta/martini",
			"Comment": "v1.0-39-g42b0b68",
			"Rev": "42b0b68fb383aac4f72331d0bd0948001b9be080"
		},
		{
			"ImportPath": "github.com/codegangsta/martini-contrib/render",
			"Comment": "v0.1-159-g8ce6181",
			"Rev": "8ce6181c2609699e4c7cd30994b76a850a9cdadc"
		},
		{
			"ImportPath": "github.com/go-martini/martini",
			"Comment": "v1.0-39-g42b0b68",
			"Rev": "42b0b68fb383aac4f72331d0bd0948001b9be080"
		}
	]
}
~~~

### Process Type Definition
dotCloud uses a [Procfile] to know how to start the app's processes.

The example code already includes a file called `Procfile` at the top level of
your repository. It looks like this:

~~~
web: MARTINI_ENV=production go-martini-example-app
~~~

Left from the colon we specify the **required** process type called `web` and then set the `$MARTINI_ENV` environment variable to `production` and run the application binary. All web processes on the dotCloud platform are required to listen on the port specified by the `$PORT` environment variable. Martini does this by default.

### The Actual Application Code

The actual application code is really straight forward. It simply uses the Martini framework to render the HTML output and return it to the client. The `m.Run()` automatically binds to the port specified in the `$PORT` environment variable as is required on [dotCloud].

~~~go
package main

import (
	"github.com/codegangsta/martini-contrib/render"
	"github.com/go-martini/martini"
)

func main() {
	m := martini.Classic()

	m.Use(render.Renderer())
	m.Use(martini.Static("assets"))

	m.Get("/", func(ren render.Render) {
		ren.HTML(200, "index", nil)
	})

	m.Run()
}
~~~

## Pushing and Deploying the App

Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the dotCloud platform using the custom Go buildpack:

~~~bash
$ dcapp APP_NAME create custom --buildpack https://github.com/cloudControl/buildpack-go
~~~

Push your code to the application's repository, which triggers the deployment
image build process:

~~~bash
$ dcapp APP_NAME/default push
Counting objects: 73, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (52/52), done.
Writing objects: 100% (73/73), 312.86 KiB | 0 bytes/s, done.
Total 73 (delta 3), reused 0 (delta 0)
       
-----> Receiving push
-----> Installing go1.3.1... done
-----> Running: godep go install -tags heroku ./...
-----> Building image
remote: -----> Custom buildpack provided
-----> Uploading image (2.5 MB)
       
To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the dcapp
deploy command.

~~~bash
$ dcapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Martini app running at `http://APP_NAME.cloudcontrolled.com`. The command line client provides the `open` command to quickly open the app in your default browser.

~~~bash
$ dcapp APP_NAME/default open
~~~

[Martini]: http://martini.codegangsta.io/
[dotCloud]: https://next.dotcloud.com
[Go buildpack]: https://github.com/cloudControl/buildpack-go
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
