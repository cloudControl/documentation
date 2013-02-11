#Deploying a Java/Jetty application

If you're looking for a fast and lightweight Java web server / Servlet container for your projects, you definitely have to try [Jetty](http://jetty.codehaus.org/jetty/).

In this tutorial we're going to show you how to deploy a Jetty application on [cloudControl](https://www.cloudcontrol.com/). You can find the [source code on Github](https://github.com/cloudControl/java-jetty-example-app). Check out the [Java buildpack](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)

##Cloning a Hello World application
First, clone the hello world app from our repository:

~~~bash
$ git clone https://github.com/cloudControl/java-jetty-example-app.git
$ cd java-jetty-example-app
~~~

Now you have a small but fully functional Java/Jetty application.

##Dependency declaration with Maven:
To create this application we had to provide Jetty server and Servlet library as Maven dependencies. [Maven dependency plugin](http://maven.apache.org/plugins/maven-dependency-plugin/) is also required to copy all dependencies to target directory since maven local reposiotory is not included in build image:

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

##Process type definitions
cloudControl uses a `Procfile` to know how to start your processes.

There must be a file called Procfile at the top level of your repository, with the following content:

~~~
web:    java -cp target/classes:target/dependency/* com.cloudcontrolled.sample.jetty.App
~~~

The `web` process type is required and specifies the command that will be executed when the app is deployed.

##Pushing and deploying your app
Choose a unique name (from now on called APP_NAME) for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository:

~~~bash
$ cctrlapp APP_NAME/default push

-----> Receiving push
-----> Installing OpenJDK 1.6...done
-----> Installing settings.xml... done
-----> executing /srv/tmp/buildpack-cache/.maven/bin/mvn -B -Duser.home=/srv/tmp/builddir -Dmaven.repo.local=/srv/tmp/buildpack-cache/.m2/repository -s /srv/tmp/buildpack-cache/.m2/settings.xml -DskipTests=true clean install
       [INFO] Scanning for projects...
       [INFO]
       [INFO] ------------------------------------------------------------------------
       [INFO] Building APP_NAME 1.0-SNAPSHOT
       [INFO] ------------------------------------------------------------------------
       ...
       [INFO] ------------------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] ------------------------------------------------------------------------
       [INFO] Total time: 5:57.950s
       [INFO] Finished at: Fri Jan 11 14:09:05 UTC 2013
       [INFO] Final Memory: 10M/56M
-----> Building image
-----> Uploading image (39M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
   54b0da2..d247825  master -> master
~~~

Deploy your app:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

**Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.**
