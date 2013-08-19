#Deploying Spring/JSP application on embedded Jetty/Tomcat/Glassfish application server

In this tutorial we're going to show you how to deploy Spring/JSP hello world application on embedded Jetty/Tomcat/Glassfish application server and run it on [cloudControl](https://www.cloudcontrol.com/). Check out the [buildpack-java](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)
 * [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * [Maven3](http://maven.apache.org/download.html)

##Creating a Hello World application

Clone initial [Spring/JSP application](https://github.com/cloudControl/java-spring-jsp-example-app):

~~~bash
git clone https://github.com/cloudControl/java-spring-jsp-example-app.git
~~~

You should have below project structure:

~~~bash
├── README.md
├── pom.xml
└── src
    └── main
        ├── java
        │   └── com
        │       └── cloudcontrolled
        │           └── sample
        │               └── spring
        │                   └── web
        │                       └── IndexController.java
        └── webapp
            ├── WEB-INF
            │   ├── applicationContext.xml
            │   ├── jsp
            │   │   └── index.jsp
            │   ├── log4j.xml
            │   ├── springSample-servlet.xml
            │   └── web.xml
            └── static
                ├── bg_noise.jpg
                ├── helloworld.css
                └── rocket.png
~~~

### Prepare for deployment

Extend project for each Application server separately.

#### Jetty specific

Extend build plugins in `pom.xml` - provide Jetty runner:

~~~xml
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
~~~

#### Tomcat specific:

Extend build plugins in `pom.xml` - provide Tomcat runner:

~~~xml
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
                        <groupId>com.github.jsimone</groupId>
                        <artifactId>webapp-runner</artifactId>
                        <version>7.0.30.1</version>
                        <destFileName>webapp-runner.jar</destFileName>
                    </artifactItem>
                </artifactItems>
            </configuration>
        </execution>
    </executions>
</plugin>
~~~

#### Glassfish specific:

Extend dependencies in `pom.xml` - provide embedded Glassfish artifact:

~~~xml
<dependency>
    <groupId>org.glassfish.extras</groupId>
    <artifactId>glassfish-embedded-web</artifactId>
    <version>3.0</version>
    <scope>provided</scope>
</dependency>
~~~

Implement embedded Glassfish runner - `src/main/java/com/cloudcontrolled/sample/spring/GlassfishRunner.java`:

~~~java
package com.cloudcontrolled.sample.spring;

import java.io.File;
import java.io.IOException;

import org.glassfish.api.deployment.DeployCommandParameters;
import org.glassfish.api.embedded.ContainerBuilder;
import org.glassfish.api.embedded.EmbeddedContainer;
import org.glassfish.api.embedded.EmbeddedDeployer;
import org.glassfish.api.embedded.EmbeddedFileSystem;
import org.glassfish.api.embedded.LifecycleException;
import org.glassfish.api.embedded.Server;

public class GlassfishRunner {

    private static final String CONTEXT_ROOT = "/";
    private static final String SERVER_ID = "embedded_glassfish";
    private static final String SERVER_INSTALL_DIR = "target/glassfish/installroot";
    private static final String SERVER_INSTANCE_DIR = "target/glassfish/instanceroot";

    /**
     * Embedded Glassfish runner
     *
     * @param args
     *            - args[0] path to *.war archive to be deployed
     *            - args[1] optional context root
     * @throws LifecycleException
     * @throws IOException
     */
    public static void main(String[] args) throws LifecycleException, IOException {
        Server.Builder builder = new Server.Builder(SERVER_ID);
        builder.embeddedFileSystem(createEmbeddedFileSystem());

        final Server server = builder.build();
        server.createPort(Integer.valueOf(System.getenv("PORT")));

        ContainerBuilder<EmbeddedContainer> container = server
                .createConfig(ContainerBuilder.Type.web);
        server.addContainer(container);
        server.start();

        EmbeddedDeployer deployer = server.getDeployer();
        DeployCommandParameters params = new DeployCommandParameters();
        params.contextroot = args.length == 2 ? args[1] : CONTEXT_ROOT;
        deployer.deploy(new File(args[0]), params);
    }

    private static EmbeddedFileSystem createEmbeddedFileSystem() {
        EmbeddedFileSystem.Builder efsb = new EmbeddedFileSystem.Builder();
        efsb.installRoot(new File(SERVER_INSTALL_DIR));
        efsb.instanceRoot(new File(SERVER_INSTANCE_DIR));
        efsb.autoDelete(true);
        return efsb.build();
    }
}
~~~

###Defining the process type

Create a Procfile in project root directory and add the following line:

####Jetty:

~~~
web: java $JAVA_OPTS -jar target/dependency/jetty-runner.jar --port $PORT target/sample-spring-0.0.1-SNAPSHOT.war
~~~

####Tomcat:

~~~
web: java $JAVA_OPTS -jar target/dependency/webapp-runner.jar --port $PORT target/sample-spring-0.0.1-SNAPSHOT
~~~

####Glassfish:

~~~
web: java $JAVA_OPTS -cp target/classes:target/dependency/* com.cloudcontrolled.sample.spring.GlassfishRunner target/sample-spring-0.0.1-SNAPSHOT.war
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

Push your code to the application's repository and deploy it (increase web container size to meet hight memory consumption by spring framework and application servers):

~~~bash
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy --memory 512MB
~~~

**Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.**

You can find the source code on Github: [Jetty](https://github.com/cloudControl/java-spring-jsp-example-app/tree/jetty_guide), [Tomcat](https://github.com/cloudControl/java-spring-jsp-example-app/tree/tomcat_guide) and [Glassfish](https://github.com/cloudControl/java-spring-jsp-example-app/tree/glassfish_guide).
