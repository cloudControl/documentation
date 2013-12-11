# Deploying a Scala application

In this tutorial we're going to show you how to deploy a Scala application on [cloudControl]. You can find the [source code on Github](https://github.com/cloudControl/scalatra-example) and check out the [Scala buildpack] for supported features.

## The Scala Application Explained
### Get the App
First, clone the Scala application from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/scalatra-example.git
$ cd scalatra-example
~~~

Now you have a small, but fully functional Scala application.

### Dependency Tracking
Scala applications are built by [sbt], so you define your dependencies in a `build.sbt`.
~~~scala
import com.typesafe.startscript.StartScriptPlugin
seq(StartScriptPlugin.startScriptForClassesSettings: _*)
organization := "org.soichiro"
name := "scalatra"
version := "0.1.0-SNAPSHOT"
scalaVersion := "2.9.1"
seq(webSettings :_*)
libraryDependencies ++= Seq(
  "org.scalatra" %% "scalatra" % "2.0.4",
  "org.scalatra" %% "scalatra-scalate" % "2.0.4",
  "org.scalatra" %% "scalatra-specs2" % "2.0.4" % "test",
  "ch.qos.logback" % "logback-classic" % "1.0.0" % "runtime",
  "org.eclipse.jetty" % "jetty-webapp" % "7.6.0.v20120127" % "container",
  "org.eclipse.jetty" % "jetty-webapp" % "7.6.0.v20120127" % "compile",
  "javax.servlet" % "servlet-api" % "2.5" % "provided"
)
resolvers += "Sonatype OSS Snapshots" at "http://oss.sonatype.org/content/repositories/snapshots/"
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web: target/start
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform: 

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME push
[...]
-----> Receiving push
-----> Installing OpenJDK 1.6...
-----> Building app with sbt
-----> Running: sbt clean compile stage
        [...]
-----> Building image
-----> Uploading image (80M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Scala application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[cloudControl]: https://www.cloudcontrol.com/
[Scala buildpack]: https://github.com/cloudControl/buildpack-scala
[sbt]: http://www.scala-sbt.org/
[cloudControl-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile