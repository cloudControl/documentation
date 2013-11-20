# Deploying a Grails application

In this tutorial we're going to show you how to deploy a Grails  application on [cloudControl]. You can find the [source code on Github](https://github.com/cloudControl/grails_example_app) and check out the [Grails buildpack] for supported features.

## The Grails Application Explained
### Get the App
First, clone the hello world app from our repository:

~~~bash
$ git clone https://github.com/cloudControl/grails_example_app.git
$ cd grails_example_app
~~~

Now you have a small but fully functional Grails application.

### Dependency Tracking
As it is the default for grails applications, you define your dependencies and versions in `grails-app/conf/BuildConfig.groovy`.
~~~groovy
grails.servlet.version = "2.5" 
grails.project.target.level = 1.6
grails.project.source.level = 1.6

grails.project.dependency.resolution = {
    inherits("global") {
    }
    log "error" 
    checksums true
    legacyResolve false
    repositories {
        inherits true 
        grailsPlugins()
        grailsHome()
        grailsCentral()

        mavenLocal()
        mavenCentral()
    }
    dependencies {
        // runtime 'mysql:mysql-connector-java:5.1.20'
        runtime 'postgresql:postgresql:8.4-702.jdbc3'
    }
    plugins {
        runtime ":hibernate:$grailsVersion"
        runtime ":jquery:1.8.3"
        runtime ":resources:1.1.6"
        build ":tomcat:$grailsVersion"
        runtime ":database-migration:1.2.1"
        compile ':cache:1.0.1'
    }
}
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web: java $JAVA_OPTS -jar server/webapp-runner.jar --port $PORT target/*.war
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
-----> Grails 2.2.0 app detected
       WARNING: The Grails buildpack is currently in Beta.
-----> Installing OpenJDK 1.6...
-----> Installing Grails 2.2.0.....
-----> Done
-----> Executing grails -Divy.default.ivy.user.dir=/srv/tmp/buildpack-cache compile --non-interactive
        [...]
-----> Executing grails -plain-output -Divy.default.ivy.user.dir=/srv/tmp/buildpack-cache war --non-interactive
        [...]
-----> No server directory found. Adding webapp-runner 7.0.40.0 automatically.
-----> Building image
-----> Uploading image (73M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Grails Application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[cloudControl]: https://www.cloudcontrol.com/
[Grails buildpack]: https://github.com/cloudControl/buildpack-grails
[cloudControl-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
