# Deploying a Java/Tomcat Application

If you're looking for a full-featured Java web server and servlet container,
you definitely have heard of [Tomcat]. Tomcat offers an implementation of the
Java Servlet and JavaServer Pages (JSP) technologies.

In this tutorial we're going to show you how to deploy a JSP application running on embedded Tomcat on
[dotCloud]. You can find the [source code on Github](https://github.com/cloudControl/java-tomcat-example-app)
and check out the [Java buildpack] for supported features.


## The Tomcat Application Explained
### Get the App
First, clone the Java/Tomcat app from our repository:

~~~bash
$ git clone https://github.com/cloudControl/java-tomcat-example-app.git
$ cd java-tomcat-example-app
~~~

Now you have a small but fully functional Java/Tomcat application.


### Dependency Tracking

To create this application we have to provide the Tomcat server and its
surrounding libraries as Maven dependencies. You can see below how we did that in the `pom.xml`:
~~~xml
<properties>
  <tomcat.version>8.0.12</tomcat.version>
</properties>
<dependencies>
  <dependency>
      <groupId>org.apache.tomcat.embed</groupId>
      <artifactId>tomcat-embed-core</artifactId>
      <version>${tomcat.version}</version>
  </dependency>
  <dependency>
      <groupId>org.apache.tomcat.embed</groupId>
      <artifactId>tomcat-embed-logging-juli</artifactId>
      <version>${tomcat.version}</version>
  </dependency>
  <dependency>
      <groupId>org.apache.tomcat.embed</groupId>
      <artifactId>tomcat-embed-jasper</artifactId>
      <version>${tomcat.version}</version>
  </dependency>
  <dependency>
      <groupId>org.apache.tomcat</groupId>
      <artifactId>tomcat-jasper</artifactId>
      <version>${tomcat.version}</version>
  </dependency>
  <dependency>
      <groupId>org.apache.tomcat</groupId>
      <artifactId>tomcat-jasper-el</artifactId>
      <version>${tomcat.version}</version>
  </dependency>
  <dependency>
      <groupId>org.apache.tomcat</groupId>
      <artifactId>tomcat-jsp-api</artifactId>
      <version>${tomcat.version}</version>
  </dependency>
</dependencies>
~~~

[Application Assembler Maven Plugin] is also required to generate the required
scripts for starting the application. You can see below how we added this in
the `pom.xml`:
~~~xml
<plugin>
    <groupId>org.codehaus.mojo</groupId>
    <artifactId>appassembler-maven-plugin</artifactId>
    <version>1.1.1</version>
    <configuration>
        <assembleDirectory>target</assembleDirectory>
        <programs>
            <program>
                <mainClass>launch.Main</mainClass>
                <name>webapp</name>
            </program>
        </programs>
    </configuration>
    <executions>
        <execution>
            <phase>package</phase>
            <goals>
                <goal>assemble</goal>
            </goals>
        </execution>
    </executions>
</plugin>
~~~

### Java Version
We are using the latest version of Apache Tomcat 8 which requires Java 7+ in
order to run. The default Java version on dotCloud is 7 but you can
explicitly define it in the `system.properties` file like this:
~~~
java.runtime.version=1.7
~~~

### Process Type Definition
dotCloud uses a [Procfile] to know how to start your processes.

The example code already includes the `Procfile` at the top level of your
repository. It looks like this:
~~~
web: sh target/bin/webapp
~~~

The `web` process type is required and specifies the command that will be
executed when the app is deployed.  The webapp script contains the necessary
commands to start your servlet using the built classes.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the dotCloud platform:
~~~bash
$ dcapp APP_NAME create java
~~~

Push your code to the application's repository, which triggers the deployment image build process:
~~~bash
$ dcapp APP_NAME/default push
Counting objects: 2, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (2/2), done.
Writing objects: 100% (2/2), 292 bytes | 0 bytes/s, done.
Total 2 (delta 1), reused 0 (delta 0)

-----> Receiving push
-----> Installing OpenJDK 1.7(openjdk7.jdk7u60-b03.tar.gz)... done
-----> Installing settings.xml... done
-----> executing /srv/tmp/buildpack-cache/.maven/bin/mvn -B -Duser.home=/srv/tmp/builddir -Dmaven.repo.local=/srv/tmp/buildpack-cache/.m2/repository -s /srv/tmp/buildpack-cache/.m2/settings.xml -DskipTests=true clean install
       [INFO] Scanning for projects...
       [INFO]
       [INFO] ------------------------------------------------------------------------
       [INFO] Building embeddedTomcatSample Maven Webapp 1.0-SNAPSHOT
       [INFO] ------------------------------------------------------------------------
       [INFO]
       [INFO] --- maven-clean-plugin:2.5:clean (default-clean) @ embeddedTomcatSample ---
       ...
       [INFO] Installing /srv/tmp/builddir/target/embeddedTomcatSample.jar to /srv/tmp/buildpack-cache/.m2/repository/com/cctrl/sample/embeddedTomcatSample/1.0-SNAPSHOT/embeddedTomcatSample-1.0-SNAPSHOT.jar
       [INFO] Installing /srv/tmp/builddir/pom.xml to /srv/tmp/buildpack-cache/.m2/repository/com/cctrl/sample/embeddedTomcatSample/1.0-SNAPSHOT/embeddedTomcatSample-1.0-SNAPSHOT.pom
       [INFO] ------------------------------------------------------------------------
       [INFO] BUILD SUCCESS
       [INFO] ------------------------------------------------------------------------
       [INFO] Total time: 2.138s
       [INFO] Finished at: Wed Sep 24 09:17:14 UTC 2014
       [INFO] Final Memory: 14M/202M
       [INFO] ------------------------------------------------------------------------
-----> Building image
-----> Uploading image (50.2 MB)

To ssh://APP_NAME@dotcloudapp.com/repository.git
   db605ac...6a884f1 master -> master
~~~

Last but not least, deploy the latest version of the app with the dcapp deploy command:

~~~bash
$ dcapp APP_NAME/default deploy
~~~

Congratulations, you can now see your JSP Application running on Tomcat at `http[s]://APP_NAME.dotcloudapp.com`.

[Tomcat]: https://tomcat.apache.org/
[dotCloud]: https://next.dotcloud.com/
[Java buildpack]: https://github.com/cloudControl/buildpack-java
[dotCloud-command-line-client]: https://next.dotcloud.com/dev-center/platform-documentation#command-line-client-web-console-and-api
[Git client]: http://git-scm.com/
[Application Assembler Maven Plugin]: http://mojo.codehaus.org/appassembler/appassembler-maven-plugin/
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
