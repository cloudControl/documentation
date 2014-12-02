# Deploying a Clojure application

In this tutorial we're going to show you how to deploy a Clojure  application on [dotCloud]. You can find the [source code on Github](https://github.com/cloudControl/clojure-example-app) and check out the [Clojure buildpack] for supported features.

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
(defproject clojure-sample "1.1.0"
  :description "Hello World Clojure Web App"
  :min-lein-version "2.0.0"
  :dependencies [[org.clojure/clojure "1.6.0"]
                 [compojure "1.1.8"]
                 [ring/ring-jetty-adapter "1.3.0"]]
  :main ^:skip-aot sample.app)
~~~

### Process Type Definition
dotCloud uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web: lein run
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the
dotCloud platform:

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
[...]
-----> Receiving push
-----> Installing OpenJDK 1.7(openjdk7.b32.tar.gz)... done
done
-----> Installing Leiningen
       Downloading: leiningen-2.4.2-standalone.jar
       Writing: lein script
-----> Building with Leiningen
       Running: lein with-profile production compile :all
       (Retrieving org/clojure/clojure/1.6.0/clojure-1.6.0.pom from central)
       [...]
       Compiling app
-----> Building image
-----> Uploading image (59M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master

~~~

Last but not least, deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Clojure application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[dotCloud]: https://www.cloudcontrol.com/
[Clojure buildpack]: https://github.com/cloudControl/buildpack-clojure
[dotCloud-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[Leiningen]: http://leiningen.org/
