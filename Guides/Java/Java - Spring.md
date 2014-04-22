#Deploying a Spring Application

In this tutorial we're going to show you how to deploy a Spring/MVC/Hibernate application on [exoscale]. The example app is a ready to deploy project based on the [Spring Roo petclinic] example.

## The Spring Application Explained

### Get the App


First, clone the Spring application from our repository:

~~~bash
$ git clone https://github.com/cloudControl/java-spring-hibernate-example-app
$ cd java-spring-hibernate-example-app
~~~


### Production Server

The [Jetty Runner] provides a fast and easy way to run your app in an application server. We've added a dependency to the build plugins section in the `pom.xml`:

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



### Production Database

In this tutorial we use the [Shared MySQL Add-on]. We have changed the `src/main/resources/META-INF/spring/applicationContext.xml` to read the [Database credentials] provided by MySQLs Add-on:

~~~xml
<property name="url" value="jdbc:mysql://${MYSQLS_HOSTNAME}:${MYSQLS_PORT}/${MYSQLS_DATABASE}"/>
<property name="username" value="${MYSQLS_USERNAME}"/>
<property name="password" value="${MYSQLS_PASSWORD}"/>
~~~

### Adjust Logger Configuration

Logging to a file is not recommended since the container's [file system] is not persistent.
The default logger configuration - `src/main/resources/log4j.properties` is modified to log to `stdout/stderr`.
Then exoscale can pick up all the messages and provide them to you via the [log command]. This is how the file looks now:
~~~xml
og4j.rootLogger=DEBUG, stdout
log4j.appender.stdout=org.apache.log4j.ConsoleAppender
log4j.appender.stdout.layout=org.apache.log4j.PatternLayout
log4j.appender.stdout.layout.ConversionPattern=%p [%t] (%c) - %m%n%
~~~

### Process Type Definition

exoscale uses the `Procfile` to start the application. The `Procfile` in the project root therefore specifies the command which executes the Jetty Runner:

~~~
web: java $JAVA_OPTS -jar target/dependency/jetty-runner.jar --port $PORT target/*.war
~~~


## Pushing and Deploying your App

Choose a unique name (from now on called APP_NAME) for your application and create it on the exoscale platform:

~~~bash
$ exoapp APP_NAME create java
~~~

Push your code to the application's repository:

~~~bash
$ exoapp APP_NAME/default push
Counting objects: 223, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (212/212), done.
Writing objects: 100% (223/223), 99.59 KiB, done.
Total 223 (delta 107), reused 0 (delta 0)

-----> Receiving push
-----> Installing OpenJDK 1.7(openjdk7.b32.tar.gz)... done
-----> Installing Maven (maven_3_1_with_cache_1.tar.gz)... done
-----> Installing settings.xml... done
-----> executing /srv/tmp/buildpack-cache/.maven/bin/mvn -B -Duser.home=/srv/tmp/builddir -Dmaven.repo.local=/srv/tmp/buildpack-cache/.m2/repository -s /srv/tmp/buildpack-cache/.m2/settings.xml -DskipTests=true clean install
       [INFO] Scanning for projects...
       [INFO]
       [INFO] ---------------------------------------------------------------
       [INFO] Building petclinic 0.1.0-SNAPSHOT
       [INFO] ---------------------------------------------------------------
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
       [INFO] ---------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] ---------------------------------------------------------------
       [INFO] Total time: 3:38.174s
       [INFO] Finished at: Thu Juli 20 11:23:02 UTC 2013
       [INFO] Final Memory: 20M/229M
       [INFO] ---------------------------------------------------------------
-----> Building image
-----> Uploading image (84M)

To ssh://APP_NAME@app.exo.io/repository.git
 * [new branch]      master -> master
~~~

Add MySQLs Add-on with free plan to your deployment and deploy it:

~~~bash
$ exoapp APP_NAME/default addon.add mysqls.free
$ exoapp APP_NAME/default deploy --memory=768MB
~~~

The `--memory=768MB` argument increases the container size to meet the high memory consumption of the Spring framework. Please note: increasing the size comes with additional costs.

Et voila, the app is now up and running at `http[s]://APP_NAME.app.exo.io` .


[Spring Roo petclinic]: http://static.springsource.org/spring-roo/reference/html/intro.html#intro-exploring-sample
[Database credentials]: Add-on%20credentials
[Jetty Runner]: http://wiki.eclipse.org/Jetty/Howto/Using_Jetty_Runner
[exoscale]: http://exoscale.ch
[file system]: ../../Platform%20Documentation#non-persistent-filesystem
[log command]: ../../Platform%20Documentation#logging
[Shared MySQL Add-on]: ../../Add-on%20Documentation/Data%20Storage/MySQLs
