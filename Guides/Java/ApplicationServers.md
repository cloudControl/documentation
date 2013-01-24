#Deploying Spring/JSP application on embedded Jetty/Tomcat/Glassfish application server

In this tutorial we're going to show you how to deploy Spring/JSP hello world application on embedded Jetty/Tomcat/Glassfish/JBoss application server and run it on [cloudControl](https://www.cloudcontrol.com/). Check out the [buildpack-java](https://github.com/cloudControl/buildpack-java) for supported features.

##Prerequisites
 * [cloudControl user account](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#user-accounts)
 * [cloudControl command line client](https://github.com/cloudControl/documentation/blob/master/Platform%20Documentation.md#command-line-client-web-console-and-api)
 * [git](https://help.github.com/articles/set-up-git)
 * [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * [Maven3](http://maven.apache.org/download.html)

##Creating a Hello World application

Create application structure:

~~~bash
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
            └── WEB-INF
                ├── applicationContext.xml
                ├── jsp
                │   └── index.jsp
                ├── log4j.xml
                ├── springSample-servlet.xml
                └── web.xml
~~~

###Define pom.xml specifying Spring framework dependencies as well a build plugins:

~~~xml
<project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd">
	<modelVersion>4.0.0</modelVersion>
	<groupId>com.cloudcontrolled.sample.spring</groupId>
	<artifactId>java-spring-jsp-example-app</artifactId>
	<version>0.0.1-SNAPSHOT</version>
	<packaging>war</packaging>
	<properties>
		<org.springframework.version>3.2.0.RELEASE</org.springframework.version>
	</properties>
	<build>
		<plugins>
			<plugin>
				<groupId>org.apache.maven.plugins</groupId>
				<artifactId>maven-compiler-plugin</artifactId>
				<version>2.3.2</version>
				<configuration>
					<source>1.6</source>
					<target>1.6</target>
				</configuration>
			</plugin>
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
						<id>copy-dependencies</id>
						<phase>package</phase>
						<goals>
							<goal>copy-dependencies</goal>
						</goals>
					</execution>
				</executions>
			</plugin>
		</plugins>
	</build>
	<repositories>
		<repository>
			<id>com.springsource.repository.bundles.release</id>
			<name>SpringSource Enterprise Bundle Repository - SpringSource Bundle Releases</name>
			<url>http://repository.springsource.com/maven/bundles/release</url>
		</repository>
		<repository>
			<id>repository.codehaus.org</id>
			<name>http://repository.codehaus.org/</name>
			<url>http://repository.codehaus.org/</url>
		</repository>
		<repository>
			<id>maven.atlassian.com</id>
			<name>Atlassin</name>
			<url>http://maven.atlassian.com/repository/public/</url>
		</repository>
		<repository>
			<id>newrelic</id>
			<name>newrelic</name>
			<url>http://download.newrelic.com/</url>
		</repository>
	</repositories>
	<dependencies>
		<dependency>
			<groupId>javax.servlet</groupId>
			<artifactId>servlet-api</artifactId>
			<version>2.5</version>
		</dependency>
		<dependency>
			<groupId>org.springframework</groupId>
			<artifactId>spring-core</artifactId>
			<version>${org.springframework.version}</version>
		</dependency>
		<dependency>
			<groupId>org.springframework</groupId>
			<artifactId>spring-webmvc</artifactId>
			<version>${org.springframework.version}</version>
		</dependency>
		<dependency>
			<groupId>org.springframework</groupId>
			<artifactId>spring-context</artifactId>
			<version>${org.springframework.version}</version>
		</dependency>
		<dependency>
			<groupId>org.springframework</groupId>
			<artifactId>spring-aop</artifactId>
			<version>${org.springframework.version}</version>
		</dependency>
		<dependency>
			<groupId>org.springframework</groupId>
			<artifactId>spring-beans</artifactId>
			<version>${org.springframework.version}</version>
		</dependency>
		<dependency>
			<groupId>org.springframework</groupId>
			<artifactId>spring-beans</artifactId>
			<version>${org.springframework.version}</version>
		</dependency>
		<dependency>
			<groupId>log4j</groupId>
			<artifactId>log4j</artifactId>
			<version>1.2.17</version>
		</dependency>
		<dependency>
			<groupId>org.slf4j</groupId>
			<artifactId>slf4j-log4j13</artifactId>
			<version>1.0.1</version>
		</dependency>
	</dependencies>
</project>
~~~

###Write your web application:

Resource controller - `IndexController.java`:

~~~java
package com.cloudcontrolled.sample.spring.web;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;

@Controller
public class IndexController {

	@RequestMapping("/")
	public String index() {
		return "index";
	}
}
~~~

JSP page - `index.jsp`:

~~~jsp
<%@ page session="false"%>
<html>
<head><title>Spring Java Example</title></head>
<body>
Hello World!
</body>
</html>
~~~

Application context - `applicationContext.xml`:

~~~xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:context="http://www.springframework.org/schema/context"
	xmlns:tx="http://www.springframework.org/schema/tx" xmlns:mvc="http://www.springframework.org/schema/mvc"
	xsi:schemaLocation="
		http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans-3.0.xsd
		http://www.springframework.org/schema/context http://www.springframework.org/schema/context/spring-context-3.0.xsd
		http://www.springframework.org/schema/tx http://www.springframework.org/schema/tx/spring-tx-3.0.xsd
		http://www.springframework.org/schema/mvc http://www.springframework.org/schema/mvc/spring-mvc-3.0.xsd">

	<context:component-scan base-package="com.cloudcontrolled.sample.spring.*">
		<context:exclude-filter type="annotation"
			expression="org.springframework.stereotype.Controller" />
	</context:component-scan>

</beans>
~~~

Log4j configuration - `log4j.xml`:

~~~xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE log4j:configuration SYSTEM "log4j.dtd" >
<log4j:configuration xmlns:log4j="http://jakarta.apache.org/log4j/">

	<appender name="console" class="org.apache.log4j.ConsoleAppender">
		<param name="Target" value="System.out" />
		<layout class="org.apache.log4j.PatternLayout">
			<param name="ConversionPattern" value="%d{yyyy-MM-dd HH:mm:ss} [%-5p] %C{1}#%M():%L %m%n" />
		</layout>
	</appender>

	<root>
		<priority value="INFO" />
		<appender-ref ref="console" />
	</root>

</log4j:configuration>
~~~

JSP servlet configuration - `springSample-servlet.xml`:

~~~xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:context="http://www.springframework.org/schema/context"
	xmlns:tx="http://www.springframework.org/schema/tx" xmlns:mvc="http://www.springframework.org/schema/mvc"
	xsi:schemaLocation="
		http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans-3.0.xsd
		http://www.springframework.org/schema/context http://www.springframework.org/schema/context/spring-context-3.0.xsd
		http://www.springframework.org/schema/tx http://www.springframework.org/schema/tx/spring-tx-3.0.xsd
		http://www.springframework.org/schema/mvc http://www.springframework.org/schema/mvc/spring-mvc-3.0.xsd">

	<context:component-scan base-package="com.cloudcontrolled.sample.spring.*">
		<context:include-filter type="annotation"
			expression="org.springframework.stereotype.Controller" />
	</context:component-scan>
	<context:annotation-config />

	<mvc:annotation-driven />

	<bean id="jspViewResolver"
		class="org.springframework.web.servlet.view.InternalResourceViewResolver">
		<property name="order" value="1" />
		<property name="prefix" value="/WEB-INF/jsp/" />
		<property name="suffix" value=".jsp" />
		<property name="exposeContextBeansAsAttributes" value="true" />
		<property name="alwaysInclude" value="true" />
	</bean>
</beans>
~~~

Web container configuration - `web.xml`:

~~~xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app xmlns="http://java.sun.com/xml/ns/j2ee" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://java.sun.com/xml/ns/j2ee http://java.sun.com/xml/ns/j2ee/web-app_2_4.xsd"
	version="2.4">

	<display-name>CloudControl Spring Sample</display-name>

	<context-param>
		<param-name>log4jConfigLocation</param-name>
		<param-value>WEB-INF/log4j.xml</param-value>
	</context-param>
	<listener>
		<listener-class>org.springframework.web.util.Log4jConfigListener</listener-class>
	</listener>

	<context-param>
		<param-name>contextConfigLocation</param-name>
		<param-value>WEB-INF/applicationContext.xml</param-value>
	</context-param>
	<listener>
		<listener-class>org.springframework.web.context.ContextLoaderListener</listener-class>
	</listener>

	<servlet>
		<servlet-name>springSample</servlet-name>
		<servlet-class>org.springframework.web.servlet.DispatcherServlet</servlet-class>
		<load-on-startup>1</load-on-startup>
	</servlet>
	<servlet-mapping>
		<servlet-name>springSample</servlet-name>
		<url-pattern>/</url-pattern>
	</servlet-mapping>
</web-app>
~~~

#### Jetty specific

Extend build plugins - provide Jetty runner:

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

Extend build plugins - provide Tomcat runner:

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

Extend dependencies - provide embedded Glassfish artifact:

~~~xml
<dependency>
    <groupId>org.glassfish.extras</groupId>
    <artifactId>glassfish-embedded-web</artifactId>
    <version>3.0</version>
    <scope>provided</scope>
</dependency>
~~~

Implement embedded Glassfish runner:

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
    private static final String SERVER_ID = "embedded_jetty";
    private static final String SERVER_INSTALL_DIR = "target/glassfish/installroot";
    private static final String SERVER_INSTANCE_DIR = "target/glassfish/instanceroot";

    /**
     * Embedded Glassfish runner
     *
     * @param args
     *            - path to *.war archive to be deployed
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

####Jetty:

~~~
web: java $JAVA_OPTS -jar target/dependency/jetty-runner.jar --port $PORT target/java-spring-jsp-example-app-0.0.1-SNAPSHOT.war
~~~

####Tomcat:

~~~
web: java $JAVA_OPTS -jar target/dependency/webapp-runner.jar --port $PORT target/java-spring-jsp-example-app-0.0.1-SNAPSHOT
~~~

####Glassfish:

~~~
web: java $JAVA_OPTS -cp target/classes:target/dependency/* com.cloudcontrolled.sample.spring.GlassfishRunner target/java-spring-jsp-example-app-0.0.1-SNAPSHOT.war
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
$ cctrlapp APP_NAME/default deploy --max=4
~~~

**Congratulations, you should now be able to reach your application at http://APP_NAME.cloudcontrolled.com.**

You can find the source code on Github: [Jetty](https://github.com/cloudControl/java-spring-jsp-example-app), [Tomcat](https://github.com/cloudControl/java-spring-jsp-example-app/tree/tomcat) and [Glassfish](https://github.com/cloudControl/java-spring-jsp-example-app/tree/glassfish).
