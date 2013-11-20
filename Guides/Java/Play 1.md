# Deploying a Play! V1 application

In this tutorial we're going to show you how to deploy a Play! application on [cloudControl]. You can find the [source code on Github](https://github.com/cloudControl/devcenter-play) and check out the [Play buildpack] for supported features.

If you want to deploy a Play! V2 application, see the [Scala Tutorial]. 

## The Play! Application Explained
### Get the App
First, clone the hello world app from our repository:

~~~bash
$ git clone https://github.com/cloudControl/devcenter-play.git
$ cd devcenter-play
~~~

Now you have a small but fully functional Play! application.

### Dependency Tracking
Dependency tracking for Play! applications happens in `conf/dependencies.yml`. 

~~~yaml
require:
    - play
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web: play run --http.port=$PORT $PLAY_OPTS
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

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Play! Application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[cloudControl]: https://www.cloudcontrol.com/
[Play buildpack]: https://github.com/cloudControl/buildpack-play
[Scala tutorial]: Scala
[cloudControl-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile