#Deploying a Google Web Toolkit on embedded Jetty

[Google Web Toolkit (GWT)](https://developers.google.com/web-toolkit/) is a development toolkit for building and optimizing complex browser-based applications. GWT is used by many products at Google, including Google AdWords and Orkut. It's open source, completely free, and used by thousands of developers around the world.

In this tutorial we're going to show you how to create example GWT application, deploy it on embedded Jetty server and run on [cloudControl](https://www.cloudcontrol.com/). You can find the [source code on Github](https://github.com/cloudControl/java-gwt-example-app). Check out the [buildpack-java](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)
 * [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * [GWT SDK](https://developers.google.com/web-toolkit/download)
 * [Maven3](http://maven.apache.org/download.html)

##Creating a sample application

Use `webAppCreator` provided with GWT SDK to create example application:

~~~bash
webAppCreator -maven -noant -out java-gwt-example-app com.cloudcontrolled.sample.gwt.GreetingEntry
~~~

~~~
PROJECTDIR
├── pom.xml
└── src
    ├── main
    │   ├── java
    │   │   └── com
    │   │       └── cloudcontrolled
    │   │           └── sample
    │   │               └── gwt
    │   │                   ├── GreetingEntry.gwt.xml
    │   │                   ├── client
    │   │                   │   ├── GreetingEntry.java
    │   │                   │   ├── GreetingService.java
    │   │                   │   └── GreetingServiceAsync.java
    │   │                   ├── server
    │   │                   │   └── GreetingServiceImpl.java
    │   │                   └── shared
    │   │                       └── FieldVerifier.java
    │   └── webapp
    │       ├── GreetingEntry.css
    │       ├── GreetingEntry.html
    │       ├── WEB-INF
    │       │   └── web.xml
    │       └── favicon.ico
    └── test
        └── java
            └── com
                └── cloudcontrolled
                    └── sample
                        └── gwt
                            ├── GreetingEntryJUnit.gwt.xml
                            └── client
                                └── GreetingEntryTest.java
~~~

###Extending pom.xml:

For a fast and easy way to run your app, without having to install and administer a Jetty server, use the [Jetty Runner](http://wiki.eclipse.org/Jetty/Howto/Using_Jetty_Runner) - add to build plugins:

~~~xml
...
<plugin>
    <groupId>org.apache.maven.plugins</groupId>
    <artifactId>maven-war-plugin</artifactId>
    <version>2.2</version>
</plugin>
<plugin>
    <groupId>org.apache.maven.plugins</groupId>
    <artifactId>maven-dependency-plugin</artifactId>
    <version>2.4</version>
    <executions>
        <execution>
            <phase>package</phase>
            <goals>
                <goal>copy</goal>
            </goals>
            <configuration>
                <artifactItems>
                    <artifactItem>
                        <groupId>org.mortbay.jetty</groupId>
                        <artifactId>jetty-runner</artifactId>
                        <version>8.1.8.v20121106</version>
                        <destFileName>jetty-runner.jar</destFileName>
                    </artifactItem>
                </artifactItems>
            </configuration>
        </execution>
    </executions>
</plugin>
...
~~~

###Defining the process type
CloudControl uses a `Procfile` to know how to start your process.

Create a file called `Procfile` with the following content:
~~~
web: java $JAVA_OPTS -jar target/dependency/jetty-runner.jar --port $PORT target/*.war
~~~

###Initializing git repository
Initialize a new git repository in the project directory and commit the files you have just created.

~~~bash
$ git init
$ git add pom.xml Procfile src
$ git commit -am "Initial commit"
~~~

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
       [INFO] Packaging webapp
       [INFO] Assembling webapp [example] in [/srv/tmp/builddir/target/example-1.0-SNAPSHOT]
       [INFO] Processing war project
       [INFO] Copying webapp resources [/srv/tmp/builddir/src/main/webapp]
       [INFO] Webapp assembled in [938 msecs]
       [INFO] Building war: /srv/tmp/builddir/target/example-1.0-SNAPSHOT.war
       [INFO] WEB-INF/web.xml already added, skipping
       [INFO]
       ...
       [INFO] ------------------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] ------------------------------------------------------------------------
       [INFO] Total time: 2:36.638s
       [INFO] Finished at: Mon Jan 14 15:24:05 UTC 2013
       [INFO] Final Memory: 13M/317M
       [INFO] ------------------------------------------------------------------------
-----> Building image
-----> Uploading image (66M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Deploy your app:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

**Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.**
