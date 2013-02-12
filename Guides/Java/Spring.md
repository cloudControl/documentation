#Deploying a Spring application

[Spring](http://www.springsource.org/) is the most popular application development framework for enterprise Java™. Millions of developers use it to create high performing, easily testable, reusable code without any lock-in.

In this tutorial we're going to show you how to create an example Spring/MVC/Hibernate application using [Spring Roo](http://www.springsource.org/spring-roo), integrate it with the [MySQLs Add-on](https://www.cloudcontrol.com/add-ons/mysqls), deploy it on an [embedded Jetty server](http://jetty.codehaus.org/jetty/) and run it on [cloudControl](https://www.cloudcontrol.com/). You can find the [source code on Github](https://github.com/cloudControl/java-spring-hibernate-example-app). Check out the [buildpack-java](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)
 * [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * [Spring Roo](http://www.springsource.org/spring-roo)
 * [Maven3](http://maven.apache.org/download.html)

##Creating a sample application using Spring Roo

Download Spring Roo, extract it and use the `bin/roo.sh` script to create the "Petclinic" example application:

~~~bash
$ export PATH=SPRING_ROO_PATH/bin:$PATH
$ mkdir PROJECTDIR; cd PROJECTDIR;
$ roo.sh script --file clinic.roo
~~~

Generate data source configuration for [Hibernate](http://www.hibernate.org/) / MySQL

~~~bash
$ roo.sh persistence setup --provider HIBERNATE --database MYSQL
~~~

###Prepare to run on Jetty

For a fast and easy way to run your app, without having to install and administer a Jetty server, use the [Jetty Runner](http://wiki.eclipse.org/Jetty/Howto/Using_Jetty_Runner) - add it to build plugins in `pom.xml`:

~~~xml
...
        <plugin>
            <groupId>org.apache.maven.plugins</groupId>
            <artifactId>maven-dependency-plugin</artifactId>
            <version>2.3</version>
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
                                <version>7.4.5.v20110725</version>
                                <destFileName>jetty-runner.jar</destFileName>
                            </artifactItem>
                        </artifactItems>
                    </configuration>
                </execution>
            </executions>
        </plugin>
    </plugins>
</build>
~~~

###Adjust data source configuration to MySQLs Add-on

Go to the application context configuration file `src/main/resources/META-INF/spring/applicationContext.xml` and modify the datasource properties `username`, `password` and `url` to use the credentials provided by MySQLs Add-on:

~~~xml
<property name="url" value="jdbc:mysql://${MYSQLS_HOSTNAME}:${MYSQLS_PORT}/${MYSQLS_DATABASE}"/>
<property name="username" value="${MYSQLS_USERNAME}"/>
<property name="password" value="${MYSQLS_PASSWORD}"/>
~~~

###Adjust logger configuration

Logging to a file is not recommended since the container's [file system](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#non-persistent-filesystem) is not persistent. That is why default logger configuration - `src/main/resources/log4j.properties` should be modified to log either to stdout/stderr or to syslog:

~~~xml
log4j.rootLogger=DEBUG, stdout
log4j.appender.stdout=org.apache.log4j.ConsoleAppender
log4j.appender.stdout.layout=org.apache.log4j.PatternLayout
log4j.appender.stdout.layout.ConversionPattern=%p [%t] (%c) - %m%n
~~~

###Defining the process type

cloudControl uses a `Procfile` to know how to start your process. Create a file called Procfile in the project root:

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
       [INFO] Assembling webapp [petclinic] in [/srv/tmp/builddir/target/petclinic-0.1.0.BUILD-SNAPSHOT]
       [INFO] Processing war project
       [INFO] Copying webapp resources [/srv/tmp/builddir/src/main/webapp]
       [INFO] Webapp assembled in [365 msecs]
       [INFO] Building war: /srv/tmp/builddir/target/petclinic-0.1.0.BUILD-SNAPSHOT.war
       [INFO] WEB-INF/web.xml already added, skipping
       [INFO]
       [INFO] --- maven-dependency-plugin:2.3:copy (default) @ petclinic ---
       ...
       [INFO] ------------------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] ------------------------------------------------------------------------
       [INFO] Total time: 3:38.174s
       [INFO] Finished at: Thu Jan 24 10:16:16 UTC 2013
       [INFO] Final Memory: 20M/229M
       [INFO] ------------------------------------------------------------------------
-----> Building image
-----> Uploading image (84M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

You need the [MySQLs Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs) to use MySQL for your deployment. Check out the marketplace for [availalbe plans](https://www.cloudcontrol.com/add-ons/mysqls) and then add the Add-on:

~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.PLAN
~~~

Deploy your app:

~~~bash
$ cctrlapp APP_NAME/default deploy --max=6
~~~

Increase container size to meet high memory consumption by Spring framework.

**Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.**
