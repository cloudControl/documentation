# Deploying a Spring-Boot Application

With [Spring Boot] you can create stand-alone, Spring based applications without most of the boilerplate configuration that was needed before.

In this guide we are going to show you how to deploy a Spring/Hibernate/MySQL/Jetty application on [cloudControl]. The example app is a ready to deploy project based on the Spring-Boot [examples].

## The Spring-Boot Application Explained

### Get the App


First, clone the Spring-Boot application from our repository:

~~~bash
$ git clone https://github.com/cloudControl/spring-boot-example-app
$ cd spring-boot-example-app
~~~

### Production Server

Spring-Boot can be easily configured to start with an embedded [Jetty server]. We have done this by adding the `spring-boot-starter-jetty` dependency to the `pom.xml`.

~~~xml
...
<dependency>
  <groupId>org.springframework.boot</groupId>
  <artifactId>spring-boot-starter-jetty</artifactId>
  <version>${spring.boot.version}</version>
</dependency>
...
~~~

The server port is provided by the cloudControl platform via environment variable and is configured in `src/main/resources/application.properties`:

~~~
server.port = ${PORT}
~~~

The project files and dependencies are packed into an executable `jar` by the Spring-Boot Maven plugin. This makes it very convenient to start the server from the command line. You can see it in the `pom.xml`:

~~~xml
...
<plugins>
  <plugin>
    <groupId>org.springframework.boot</groupId>
    <artifactId>spring-boot-maven-plugin</artifactId>
    <version>${spring.boot.version}</version>
    <executions>
      <execution>
        <goals>
          <goal>repackage</goal>
        </goals>
      </execution>
    </executions>
  </plugin>
...
~~~


### Production Database

The original Spring-Boot JPA example ships with [hsqlDB] which is an in memory database. In this guide we use the [MySQLs Add-on] that provides us with a persistent MySQL instance.
In `src/main/resources/application.properties` you can find the database settings that read the [Database credentials] provided by the MySQLs Add-on via environment:

~~~
spring.datasource.url=jdbc:mysql://${MYSQLS_HOSTNAME}:${MYSQLS_PORT}/${MYSQLS_DATABASE}
spring.datasource.username=${MYSQLS_USERNAME}
spring.datasource.password=${MYSQLS_PASSWORD}
spring.datasource.driverClassName=com.mysql.jdbc.Driver
~~~

### Process Type Definition

cloudControl uses the `Procfile` to start the application. The `Procfile` in the project root therefore specifies the command which executes the Spring-Boot app:

~~~
web: java -jar target/spring-boot-example-app-*.jar
~~~

## Pushing and Deploying your App

Choose a unique name (from now on called APP_NAME) for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create java
~~~

Push your code to the application's repository:

~~~bash
$ cctrlapp APP_NAME/default push

Counting objects: 47, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (40/40), done.
Writing objects: 100% (47/47), 17.67 KiB | 0 bytes/s, done.
Total 47 (delta 15), reused 0 (delta 0)

-----> Receiving push
-----> Installing OpenJDK 1.7(openjdk7.jdk7u60-b03.tar.gz)... done
-----> Installing Maven (maven_3_1_with_cache_1.tar.gz)... done
-----> Installing settings.xml... done
-----> executing /srv/tmp/buildpack-cache/.maven/bin/mvn ...
       [INFO] Scanning for projects...
       [INFO]
       [INFO] -----------------------------------------------------------------
       [INFO] -----------------------------------------------------------------
       [INFO] Building Spring Boot example app 0.0.1.BUILD-SNAPSHOT
       [INFO] -----------------------------------------------------------------
       ...
       [INFO] -----------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] -----------------------------------------------------------------
       [INFO] Total time: 31.711s
       [INFO] Finished at: Tue Aug 19 12:50:35 UTC 2014
       [INFO] Final Memory: 25M/60M
       [INFO] -----------------------------------------------------------------
-----> Building image
-----> Uploading image (70.0 MB)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Add MySQLs Add-on with free plan to your deployment and deploy it:

~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.free
$ cctrlapp APP_NAME/default deploy --memory=768MB
~~~

The `--memory=768MB` argument increases the container size to meet the high memory consumption of the Spring framework. Please note: increasing the size comes with additional costs.

Et voila, the app is now up and running at `http[s]://APP_NAME.cloudcontrolled.com` .



[Jetty server]: http://www.eclipse.org/jetty/
[Spring Boot]: http://projects.spring.io/spring-boot/
[examples]: https://github.com/spring-projects/spring-boot/tree/master/spring-boot-samples
[Database credentials]: https://www.cloudcontrol.com/dev-center/guides/java/add-on-credentials-3
[cloudControl]: /
[MySQLs Add-on]: https://www.cloudcontrol.com/dev-center/add-on-documentation/mysqls
[hsqlDB]: http://hsqldb.org/
