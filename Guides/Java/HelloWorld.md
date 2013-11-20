# Deploying a Java/Jetty Application

If you're looking for a fast and lightweight Java web server / Servlet container for your projects, you definitely have to try [Jetty].

In this tutorial we're going to show you how to deploy a Jetty application on [cloudControl]. You can find the [source code on Github](https://github.com/cloudControl/java-jetty-example-app) and check out the [Java buildpack] for supported features.


## The Jetty Application Explained
### Get the App
First, clone the hello world app from our repository:

~~~bash
$ git clone https://github.com/cloudControl/java-jetty-example-app.git
$ cd java-jetty-example-app
~~~

Now you have a small but fully functional Java/Jetty application.


### Dependency Tracking
To create this application we had to provide Jetty server and Servlet library as Maven dependencies in the `pom.xml`.
~~~xml
<dependency>
    <groupId>org.eclipse.jetty</groupId>
    <artifactId>jetty-servlet</artifactId>
    <version>7.6.0.v20120127</version>
</dependency>
<dependency>
    <groupId>javax.servlet</groupId>
    <artifactId>servlet-api</artifactId>
    <version>2.5</version>
</dependency>
~~~

[Maven dependency plugin] is also required in the `pom.xml` to copy all dependencies to the target directory to make them available in the classpath. A local Maven repository is not included in the build image.

~~~xml
<plugin>
    <groupId>org.apache.maven.plugins</groupId>
    <artifactId>maven-dependency-plugin</artifactId>
    <version>2.4</version>
    <executions>
        <execution>
            <id>copy-dependencies</id>
            <phase>package</phase>
            <goals><goal>copy-dependencies</goal></goals>
        </execution>
    </executions>
</plugin>
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your repository. It looks like this:

~~~
web:  java -cp target/classes:target/dependency/*  com.cloudcontrolled.sample.jetty.App
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.
The java command starts the 'com.cloudcontrolled.sample.jetty.App' with the classpath set to the compiled Java classes and dependencies.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform: 

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:


~~~bash
$ cctrlapp APP_NAME/default push

-----> Receiving push
-----> Installing OpenJDK 1.7(openjdk7.b32.tar.gz)... done
-----> Installing Maven (maven_3_1_with_cache_1.tar.gz)... done
-----> Installing settings.xml... done
-----> executing /srv/tmp/buildpack-cache/.maven/bin/mvn -B -Duser.home=/srv/tmp/builddir -Dmaven.repo.local=/srv/tmp/buildpack-cache/.m2/repository -s /srv/tmp/buildpack-cache/.m2/settings.xml -DskipTests=true clean install
       [INFO] Scanning for projects...
       [INFO]
       [INFO] --------------------------------------------------------------
       [INFO] Building APP_NAME 1.0-SNAPSHOT
       [INFO] --------------------------------------------------------------
       ...
       [INFO] --------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] --------------------------------------------------------------
       [INFO] Total time: 5:57.950s
       [INFO] Finished at: Fri Jul 11 14:09:05 UTC 2013
       [INFO] Final Memory: 10M/56M
-----> Building image
-----> Uploading image (39M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
   54b0da2..d247825  master -> master
~~~

Last but not least deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Jetty Application running at `http[s]://APP_NAME.cloudcontrolled.com`.

[Jetty]: http://jetty.codehaus.org/jetty/
[cloudControl]: https://www.cloudcontrol.com/
[Java buildpack]: https://github.com/cloudControl/buildpack-java
[cloudControl-command-line-client]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Maven dependency plugin]: http://maven.apache.org/plugins/maven-dependency-plugin/
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
