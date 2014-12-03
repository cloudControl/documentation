# Deploying a Go Application
The [Go] language standard library comes with a full working web server that we used to build our *Hello world* example application 

In this tutorial we're going to show you how to deploy this simple Go app on [dotCloud].

## The Go App Explained

### Get the App
First, clone the hello world app from our repository:

~~~bash
$ git clone https://github.com/cloudControl/go_hello_world_app.git
$ cd go_hello_world_app
~~~

Now you have a small but fully functional Go application.

### Dependency Tracking

The Go buildpack tracks dependencies using [godep]. The dependency requirements are defined in a 
`Godeps.json` file which needs to be located in the `Godeps` folder of the repository.
Our app is using the standard library only without any dependencies but we've to specify a Go version for the buildpack.
The `Godeps.json` you cloned as part of the example app looks like this:

~~~ json
{
	"ImportPath": "github.com/cloudControl/go_hello_world_app",
	"GoVersion": "go1.3.1",
	"Deps": []
}
~~~

### Process Type Definition

dotCloud uses a [Procfile] to know how to start your processes.

The example code already includes a file called `Procfile` at the top level of your repository. It looks like this:
~~~
web: go_hello_world_app
~~~

Left from the colon we specified the **required** process type called `web` followed by the command that starts the app.

### The Actual Application Code

The example app has two request handlers registered in the `main` function. The `http.FileServer` returns the static content while the other serves the default page. To start the web server `http.ListenAndServe` is called with the server port passed. The app listens on the port specified in the `$PORT` environment variable as is required on dotCloud.


~~~go
package main

import (
	"fmt"
	"html/template"
	"log"
	"net/http"
	"os"
)

func defaultHandler(w http.ResponseWriter, r *http.Request) {
	t := template.Must(template.ParseFiles("templates/hello.html"))
	pageModel := make(map[string]interface{})
	pageModel["Domain"] = getEnv("DOMAIN", "cloudcontrolled.com")
	if err := t.Execute(w, pageModel); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
	}
}

func main() {
	http.Handle("/static/", http.FileServer(http.Dir(".")))
	http.HandleFunc("/", defaultHandler)

	log.Fatal(http.ListenAndServe(fmt.Sprintf(":%v", getEnv("PORT", "8080")), nil))
}

func getEnv(key, defaultValue string) string {
	value := os.Getenv(key)
	if value != "" {
		return value
	}
	return defaultValue
}

~~~

## Pushing and Deploying the App

Choose a unique name (from now on called APP_NAME) for your application and create it on the dotCloud platform:

~~~bash
$ dcapp APP_NAME create custom --buildpack https://github.com/cloudControl/buildpack-go
~~~

Push your code to the application's repository:

~~~bash
$ dcapp APP_NAME/default push

Counting objects: 17, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (12/12), done.
Writing objects: 100% (17/17), 258.87 KiB | 0 bytes/s, done.
Total 17 (delta 0), reused 0 (delta 0)

-----> Receiving push

-----> Using go1.3.1
-----> Running: godep go install ...
-----> Building image remote: 
-----> Custom buildpack provided
-----> Uploading image (2.2 MB)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the dcapp deploy command:

~~~bash
$ dcapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Go app running at `http[s]://APP_NAME.cloudcontrolled.com`.

[Go]: http://golang.org/
[dotCloud]: http://next.dotcloud.com
[godep]: https://github.com/tools/godep
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
