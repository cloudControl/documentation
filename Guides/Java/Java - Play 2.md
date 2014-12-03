# Deploying a Play 2 application

In this tutorial we're going to show you how to deploy a [Play 2.3.x] application on
[dotCloud]. You can find the [source code on Github](https://github.com/cloudControl/java-play2-example-app)
and check out the [Scala buildpack] for supported features. The application
comes from the official Play framework templates that can be found at
https://github.com/playframework/playframework/tree/2.3.x/templates/play-java-intro,
and implements a simple CRUD functionality.

## The Play 2 Application Explained
### Get the App
First, clone the Play application from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/java-play2-example-app.git
$ cd java-play2-example-app
~~~

Now you have a small, but fully functional CRUD application in Play 2.

### Dependency Tracking
Dependencies in Play 2 applications are resolved using [sbt]. The dependency
requirements are defined in the `plugins.sbt` file which needs to be located in
`projects` folder under the root of your repository. The one you cloned as part of the example app
looks like this:
~~~scala
resolvers += "Typesafe repository" at "http://repo.typesafe.com/typesafe/releases/"

// The Play plugin
addSbtPlugin("com.typesafe.play" % "sbt-plugin" % "2.3.4")

// web plugins

addSbtPlugin("com.typesafe.sbt" % "sbt-coffeescript" % "1.0.0")

addSbtPlugin("com.typesafe.sbt" % "sbt-less" % "1.0.0")

addSbtPlugin("com.typesafe.sbt" % "sbt-jshint" % "1.0.1")

addSbtPlugin("com.typesafe.sbt" % "sbt-rjs" % "1.0.1")

addSbtPlugin("com.typesafe.sbt" % "sbt-digest" % "1.0.0")

addSbtPlugin("com.typesafe.sbt" % "sbt-mocha" % "1.0.0")
~~~


### Process Type Definition
dotCloud uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your
repository. It looks like this:
~~~
web: target/universal/stage/bin/play-java-intro -Dhttp.port=${PORT} -DapplyEvolutions.default=true
~~~

The `web` process type is required and specifies the command that will be
executed when the app is deployed. The environment variable `$PORT` defines the
port the application-server should listen to, and the applyEvolutions flag allows
the required database magritions to happen.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the dotCloud platform:

~~~bash
$ dcapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ dcapp APP_NAME/default push
Counting objects: 5, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (3/3), done.
Writing objects: 100% (3/3), 352 bytes | 0 bytes/s, done.
Total 3 (delta 1), reused 0 (delta 0)

-----> Receiving push
-----> Installing OpenJDK 1.7...-----> Installing OpenJDK 1.7(openjdk7.jdk7u60-b03.tar.gz)... done
done
-----> Running: sbt compile stage
       Getting org.scala-sbt sbt 0.13.5 ...
       :: retrieving :: org.scala-sbt#boot-app
       ...
       [success] Total time: 3 s, completed Sep 24, 2014 1:00:28 PM
-----> Dropping ivy cache from the slug
-----> Dropping compilation artifacts from the slug
-----> Building image
-----> Uploading image (0.1 GB)

To ssh://APP_NAME@dotcloudapp.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the dcapp deploy command:

~~~bash
$ dcapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Play 2 application running at `http[s]://APP_NAME.dotcloudapp.com`.

Note: This app uses an in-memory database, which is not persistent. After a
redeploy all changes will be lost. If you want a production database you should
use one of our available [Data Storage Add-ons].

[Play 2.3.x]: https://www.playframework.com/documentation/2.3.x/Home
[dotCloud]: https://next.dotcloud.com/
[Scala buildpack]: https://github.com/cloudControl/buildpack-scala
[dotCloud-command-line-client]: https://next.dotcloud.com/dev-center/platform-documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[sbt]: http://www.scala-sbt.org/
[Data Storage Add-ons]: https://next.dotcloud.com/add-ons?c=1
