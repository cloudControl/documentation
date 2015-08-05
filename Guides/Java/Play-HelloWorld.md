# Deploying a Play! V1 application

In this tutorial we're going to show you how to deploy a Play! application on [dotCloud]. You can find the [source code on Github](https://github.com/cloudControl/play-example-app) and check out the [Play buildpack] for supported features.

If you want to deploy a Play! V2 application, see the [Play 2 Tutorial].

## The Play! Application Explained
### Get the App
First, clone the Play! application from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/play-example-app.git
$ cd play-example-app
~~~

Now you have a small, but fully functional Play! application.

### Dependency Tracking
Dependencies in Play! applications are resolved using [Ivy]. The dependency requirements are defined in the `conf/dependencies.yml` file which needs to be located in the root of your repository. The one you cloned as part of the example app looks like this:

~~~yaml
require:
    - play
~~~

### Process Type Definition
dotCloud uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web: play run --http.port=$PORT $PLAY_OPTS
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed. The environment variable `$PORT` defines the port the application-server should listen to.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the dotCloud platform:

~~~bash
$ dcapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ dcapp APP_NAME/default push
[...]
-----> Receiving push
-----> Installing OpenJDK 1.6...
-----> Installing Play! 1.2.4.....
-----> done
-----> Installing ivysettings.xml..... done
-----> Building Play! application...
       ~        _            _
       ~  _ __ | | __ _ _  _| |
       ~ | '_ \| |/ _' | || |_|
       ~ |  __/|_|\____|\__ (_)
       ~ |_|            |__/
       ~
       ~ play! 1.2.4, http://www.playframework.org
       ~
       1.2.4
       Building Play! application at directory ./
       Precompiling: .play/play precompile ./ --silent 2>&1
-----> Building image
-----> Uploading image (65M)

To ssh://APP_NAME@dotcloudapp.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the dcapp deploy command:

~~~bash
$ dcapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Play! application running at `http[s]://APP_NAME.dotcloudapp.com`.

[dotCloud]: https://next.dotcloud.com/
[Play buildpack]: https://github.com/cloudControl/buildpack-play
[Play 2 tutorial]: https://next.dotcloud.com/dev-center/guides/java/java-play-2
[dotCloud-command-line-client]: https://next.dotcloud.com/dev-center/platform-documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[Ivy]: http://ant.apache.org/ivy/
