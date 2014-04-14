# Deploying a Clojure application

In this tutorial we're going to show you how to deploy a Clojure  application on [cloudControl]. You can find the [source code on Github](https://github.com/cloudControl/clojure-example-app) and check out the [Clojure buildpack] for supported features.

## The Clojure Application Explained
### Get the App
First, clone the Clojure application from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/clojure-example-app.git
$ cd clojure-example-app
~~~

Now you have a small, but fully functional Clojure application.

### Dependency Tracking
Clojure tracks your dependencies with the help of [Leiningen]. They are defined in the `project.clj` file which needs to be located in the root of your repository. The one you cloned as part of the example app looks like this: 
~~~clojure
(defproject clojure-sample "1.0.1"
  :description "Hello World Clojure Web App"
  :dependencies [[org.clojure/clojure "1.4.0"]
                 [compojure "1.1.1"]
                 [ring/ring-jetty-adapter "1.1.2"]]
  :main ^:skip-aot sample.app)
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web: lein run
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform: 

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push

[...]
-----> Receiving push
-----> Installing OpenJDK 1.7...
-----> Installing Leiningen
       Downloading: leiningen-1.7.1-standalone.jar
       Downloading: rlwrap-0.3.7
       Writing: lein script
-----> Building with Leiningen
       Running: lein deps
       [...]
       Copying 20 files to /srv/tmp/builddir/lib
-----> Building image
-----> Uploading image (54M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Clojure application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[cloudControl]: https://www.cloudcontrol.com/
[Clojure buildpack]: https://github.com/cloudControl/buildpack-clojure
[cloudControl-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[Leiningen]: http://leiningen.org/
