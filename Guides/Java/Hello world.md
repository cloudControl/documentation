#Deploying a Java/Jetty application

If you're looking for a fast and lightweight Java web server / Servlet container for your projects, you definitely have to try [Jetty](http://jetty.codehaus.org/jetty/). Now at [version 7.6.0](http://dist.codehaus.org/jetty/jetty-hightide-7.6.0/), it provides a variety of features to speed up and simplify your application development, including:

* Open source
* Commercially usable
* Embeddable
* Enterprise scalable
* Integrated with Eclipse

In this tutorial we're going to show you how to deploy a Jetty application on [cloudControl](https://www.cloudcontrol.com/). You can find the [source code on Github](https://github.com/cloudControl/java-jetty-example-app). Check out the [buildpack-java](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)
 * [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * [Maven3](http://maven.apache.org/download.html)

##Creating a Hello World application

Create application using maven:

~~~bash
mvn archetype:generate \
    -DarchetypeGroupId=org.apache.maven.archetypes \
    -DgroupId=com.cloudcontrolled.sample.jetty \
    -DartifactId=APP_NAME
~~~

Accept all default options proposed by maven. This should create given project structure:

~~~bash
PROJECTDIR
├── pom.xml
└── src
    ├── main
    │   └── java
    │       └── com
    │           └── cloudcontrolled
    │               └── sample
    │                   └── jetty
    │                       └── App.java
    └── test
        └── java
            └── com
                └── cloudcontrolled
                    └── sample
                        └── jetty
                            └── AppTest.java
~~~

###Extending pom.xml with missing dependencies and build plugins:

You have to specify maven dependencies to include Jetty server and Servlet library. Add build plugins: [maven dependency plugin](http://maven.apache.org/plugins/maven-dependency-plugin/) and [maven compiler plugin](http://maven.apache.org/plugins/maven-compiler-plugin/).

~~~xml
<project
  xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd">

  <modelVersion>4.0.0</modelVersion>
  <groupId>com.cloudcontrolled.sample.jetty</groupId>
  <artifactId>APP_NAME</artifactId>
  <version>1.0-SNAPSHOT</version>
  <dependencies>
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
      <dependency>
          <groupId>junit</groupId>
          <artifactId>junit</artifactId>
          <version>3.8.1</version>
          <scope>test</scope>
      </dependency>
  </dependencies>
  <build>
      <plugins>
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
          <plugin>
        <groupId>org.apache.maven.plugins</groupId>
        <artifactId>maven-compiler-plugin</artifactId>
        <version>2.3.2</version>
        <configuration>
          <source>1.6</source>
          <target>1.6</target>
        </configuration>
      </plugin>
    </plugins>
  </build>
</project>
~~~

###Write your web application:

~~~java
package com.cloudcontrolled.sample.jetty;

import java.io.IOException;
import java.io.PrintWriter;

import javax.servlet.ServletException;
import javax.servlet.http.*;

import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.servlet.*;


public class App extends HttpServlet
{

  private static final long serialVersionUID = -1;

  @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException
    {
        resp.setContentType("text/html");
        PrintWriter out = resp.getWriter();
        out.print("Hello world");
        out.flush();
        out.close();
    }

    public static void main(String[] args) throws Exception
    {
        Server server = new Server(Integer.valueOf(System.getenv("PORT")));
        ServletContextHandler context = new ServletContextHandler(ServletContextHandler.SESSIONS);
        context.setContextPath("/");
        server.setHandler(context);
        context.addServlet(new ServletHolder(new App()),"/*");
        server.start();
        server.join();
    }
}
~~~

###Defining the process type
CloudControl uses a `Procfile` to know how to start your process. Create a file called Procfile:

~~~
web:    java -cp target/classes:target/dependency/* com.cloudcontrolled.sample.jetty.App
~~~

###Initializing git repository
Initialize a new git repository in the project directory and commit the files you have just created.

~~~
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
