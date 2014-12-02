# Deploying a Gradle application

In this tutorial we're going to show you how to deploy a Gradle Application on [dotCloud]. You can find the [source code on Github](https://github.com/cloudControl/gradle-example-app) and check out the [Gradle buildpack] for supported features.

## The Gradle Application Explained
### Get the App
First, clone the Gradle application from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/gradle-example-app.git
$ cd gradle-example-app
~~~

Now you have a small, but fully functional Gradle application.

### Dependency Tracking
The dependency requirements are defined in the `build.gradle` file which needs to be located in the root of your repository. The one you cloned as part of the example app looks like this:

~~~
apply plugin:'java'
apply plugin:'application'

mainClassName = "HelloWorld"
applicationName = "app"

repositories {
    mavenCentral()
}

dependencies {
    compile 'org.eclipse.jetty:jetty-servlet:7.5.3.v20111011'
    compile 'javax.servlet:servlet-api:2.5'
}

task stage(dependsOn: ['clean', 'installApp'])
~~~

### Process Type Definition
dotCloud uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the root of your repository. It looks like this:

~~~
web: ./build/install/app/bin/app
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and Deploying your Gradle App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the dotCloud platform:

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
[...]
-----> Receiving push
-----> Installing OpenJDK 1.6...
-----> Installing OpenJDK 1.6(openjdk6.b27.tar.gz)... done
-----> Installing gradle-1.0-milestone-5..... done
-----> Building Gradle app...
       WARNING: The Gradle buildpack is currently in Beta.
-----> executing gradle stage
       :clean
       :compileJava
       [...]
       :processResources UP-TO-DATE
       :classes
       :jar
       :startScripts
       :installApp
       :stage

       BUILD SUCCESSFUL
-----> Building image
-----> Uploading image (39M)

 To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Gradle application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[dotCloud]: https://next.dotcloud.com/
[Gradle buildpack]: https://github.com/cloudControl/buildpack-gradle
[dotCloud-command-line-client]: https://next.dotcloud.com/dev-center/platform-documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
