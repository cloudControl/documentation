#Deploying a Java application with MySQLs addon

In this tutorial we're going to show you how to deploy a web application (run on embedded Jetty) integrated with MySQL via JDBC on [cloudControl](https://www.cloudcontrol.com/). MySQL database instance will be provided via [MySQLs Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs). You can find the [source code on Github](https://github.com/cloudControl/java-mysql-example-app). Check out the [buildpack-java](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account with billing](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)
 * [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * [Maven3](http://maven.apache.org/download.html)

##Creating an application:

Create application using maven:

~~~bash
mvn archetype:generate \
    -DarchetypeGroupId=org.apache.maven.archetypes \
    -DgroupId=com.cloudcontrolled.sample.mysql \
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
    │                   └── mysql
    │                       └── App.java
    └── test
        └── java
            └── com
                └── cloudcontrolled
                    └── sample
                        └── mysql
                            └── AppTest.java
~~~

If you want to develop given example in [Eclipse IDE](http://www.eclipse.org/downloads/) just execute:

~~~bash
cd PROJECTDIR ; mvn eclipse:eclipse
~~~

This will create Eclipse project files. Right now you can proceed using Eclipse.

###Extending pom.xml with missing dependencies and build plugins:

You have to specify maven dependencies to include Jetty server, Servlet library and JDBC MySQL driver. Add build plugins: [maven dependency plugin](http://maven.apache.org/plugins/maven-dependency-plugin/) and [maven compiler plugin](http://maven.apache.org/plugins/maven-compiler-plugin/).

~~~xml
<project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd">
  <modelVersion>4.0.0</modelVersion>
  <groupId>com.cloudcontrolled.sample.mysql</groupId>
  <artifactId>APP_NAME</artifactId>
  <version>1.0-SNAPSHOT</version>
  <packaging>jar</packaging>
    <dependencies>
        <dependency>
            <groupId>mysql</groupId>
            <artifactId>mysql-connector-java</artifactId>
            <version>5.1.22</version>
        </dependency>
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

The application will provide HTTP API to write and read message from database. MySQL database credentials are provided by MySQLs Add-on and are accessible via environment variables:

~~~java
package com.cloudcontrolled.sample.mysql;

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import javax.servlet.ServletException;
import javax.servlet.http.*;

import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.servlet.*;

public class App {

    private static class Write extends HttpServlet {
        @Override
        protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
            Connection connection;
            try {
                connection = getConnection();
                String insert = "INSERT INTO messages (message) VALUES (?)";
                PreparedStatement stmt = connection.prepareStatement(insert);
                stmt.setString(1, req.getParameter("message"));
                stmt.executeUpdate();
                connection.close();
            } catch (SQLException e) {
                throw new ServletException(e);
            }
        }
    }

    private static class Read extends HttpServlet {
        @Override
        protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
            Connection connection;
            PrintWriter out = resp.getWriter();
            try {
                connection = getConnection();
                Statement stmt = connection.createStatement();
                String select = "SELECT * FROM messages GROUP BY id DESC LIMIT 1";
                ResultSet rs = stmt.executeQuery(select);
                if (rs.next()) {
                    out.print(rs.getString("message"));
                } else {
                    out.print("Sorry, no message for you!!!");
                }
                out.flush();
                out.close();
                connection.close();
            } catch (SQLException e) {
                throw new ServletException(e);
            }
        }
    }

    public static void main(String[] args) throws Exception {
        createSchema();
        Server server = new Server(Integer.valueOf(System.getenv("PORT")));
        ServletContextHandler context = new ServletContextHandler(ServletContextHandler.SESSIONS);
        context.setContextPath("/");
        server.setHandler(context);
        context.addServlet(new ServletHolder(new Write()), "/write");
        context.addServlet(new ServletHolder(new Read()), "/read");
        server.start();
        server.join();
    }

    private static void createSchema() throws SQLException {
        Connection connection = getConnection();
        Statement stmt = connection.createStatement();
        stmt.executeUpdate("DROP TABLE IF EXISTS messages");
        stmt.executeUpdate("CREATE TABLE messages (id INT AUTO_INCREMENT, message VARCHAR(45), PRIMARY KEY (id))");
        connection.close();
    }

    protected static Connection getConnection() throws SQLException {
        String host = System.getenv("MYSQLS_HOSTNAME");
        String port = System.getenv("MYSQLS_PORT");
        String database = System.getenv("MYSQLS_DATABASE");
        String username = System.getenv("MYSQLS_USERNAME");
        String password = System.getenv("MYSQLS_PASSWORD");
        String dbUrl = "jdbc:mysql://" + host + ":" + port + "/" + database;
        return DriverManager.getConnection(dbUrl, username, password);
    }
}
~~~

###Defining the process type
CloudControl uses a `Procfile` to know how to start your process. Create a file called Procfile:

~~~
web:    java -cp target/classes:target/dependency/* com.cloudcontrolled.sample.mysql.App
~~~

###Initializing git repository
Initialize a new git repository in the project directory and commit the files you have just created.

~~~bash
$ git init
$ git add pom.xml Procfile src
$ git commit -am "Initial commit"
~~~

##Pushing, creating MySQL addon and deploying your app
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
       [INFO] Total time: 5.356s
       [INFO] Finished at: Wed Jan 16 14:50:00 UTC 2013
       [INFO] Final Memory: 15M/322M
       [INFO] ------------------------------------------------------------------------
-----> Building image
-----> Uploading image (40M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
   bd556b1..d7b04b5  master -> master
~~~

Create MySQLs addon:

~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.PLAN
~~~

Deploy your app:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

**Congratulations, you can now test your application:**

~~~bash
$ curl APP_NAME.cloudcontrolled.com/read
Sorry, no message for you!!!

$ curl -X POST -d "message=Hello World" http://APP_NAME.cloudcontrolled.com/write

$ curl APP_NAME.cloudcontrolled.com/read
Hello World

$ curl -X POST -d "message=Hallo Welt" http://APP_NAME.cloudcontrolled.com/write

$ curl APP_NAME.cloudcontrolled.com/read
Hallo Welt
~~~
