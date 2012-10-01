#Deploying Java WEB application with embedded Jetty server

If you're looking for a fast and light Java WEB server / Servlet container for your projects, you definitely have to try [Jetty](http://jetty.codehaus.org/jetty/). Now at [version 7.6.0](http://dist.codehaus.org/jetty/jetty-hightide-7.6.0/), it provides a variety of features to speed up and simplify your application development, including:

* Open source 
* Commercially usable 
* Embeddable 
* Enterprise scalable 
* Integrated with Eclipse

In this tutorial, we're going to take you through deploying Java WEB application with embedded Jetty server to [the cloudControl platform](http://www.cloudcontrol.com).

##Prerequisites

You're going to need only a few things to following along with this tutorial. These are:

 * A [J2SE JDK/JVM](http://www.oracle.com/technetwork/java/javase/downloads/index.html)
 * A [Maven3](http://maven.apache.org/download.html)
 * A cloudControl client - `easy_install cctrl` or `pip install cctrl`
 * A cloudControl user account. You can creat it via [cloudControl web page](https://www.cloudcontrol.com/sign-up) or cloudControl client:
 
~~~
$ cctrluser create [--name USERNAME] [--email EMAIL] [--password PWD]
~~~
	
~~~
$ cctrluser activate USERNAME ACTIVATION_CODE
~~~
 
##Create your application structure using maven:
 
Execute:

~~~ 
mvn archetype:generate \
    -DarchetypeGroupId=org.apache.maven.archetypes \
    -DgroupId=com.cloudcontrol.example \
    -DartifactId=APP_NAME
~~~
		
Accept all default options proposed by maven. This should create given project structure. Get rid of test directories since we will not use them: 

~~~
$ cd PROJECTDIR ; rm -rf src/test
~~~

![[image]](https://raw.github.com/mkorszun/documentation/master/Guides/Java/images/project.png)
		
##Extend pom.xml with missing dependencies and build directive:

You have to specify maven dependencies to include Jetty server and Servlet library. Add build directive specifying [maven dependency plugin](http://maven.apache.org/plugins/maven-dependency-plugin/) and [maven compiler plugin](http://maven.apache.org/plugins/maven-compiler-plugin/).

~~~
<project 
	xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/xsd/maven-4.0.0.xsd">
	
	<modelVersion>4.0.0</modelVersion>
	<groupId>com.cloudcontrol.example</groupId>
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

##Write your web application:

~~~
package com.cloudcontrol.example;

import java.io.IOException;
import java.io.PrintWriter;

import javax.servlet.ServletException;
import javax.servlet.http.*;

import org.eclipse.jetty.server.Server;
import org.eclipse.jetty.servlet.*;

/**
* Java WEB application with embedded Jetty server
*
*/
public class App extends HttpServlet
{

	private static final long serialVersionUID = -96650638989718048L;

	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException
	{
    	System.out.println("Request received from: "+req.getLocalAddr());
    	resp.setContentType("text/html");
    	PrintWriter out = resp.getWriter();
    	out.println("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">");
    	out.println("<HTML>");
    	out.println(" <HEAD><TITLE>Java WEB/Jetty example</TITLE></HEAD>");
    	out.println(" <BODY>");
    	out.print("<center>");
    	out.print(" This is Java WEB application with embedded Jetty server deployed in cloudControl platform");
    	out.print("</center>");
    	out.println(" </BODY>");
    	out.println("</HTML>");
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
    	System.out.println("Application started");
	}
}
~~~
	
##Deploy application locally

* **Build and package application with maven:**

~~~
$ cd PROJECTDIR ; mvn package
~~~
	
* **Deploy it**

~~~
$ cd PROJECTDIR
$ export PORT=8888
$ java -cp target/classes:target/dependency/* com.cloudcontrol.example.App
~~~
	
	You should see:

~~~
2012-09-27 15:37:16.165:INFO:oejs.Server:jetty-7.6.0.v20120127
2012-09-27 15:37:16.227:INFO:oejsh.ContextHandler:started o.e.j.s.ServletContextHandler{/,null}
2012-09-27 15:37:16.251:INFO:oejs.AbstractConnector:Started SelectChannelConnector@0.0.0.0:8888
~~~
		
* **Test it**

	![[image]](https://raw.github.com/mkorszun/documentation/master/Guides/Java/images/local_test.png)	
	
##Deploy application to cloudControl

* **Create application:**

~~~
$ cctrlapp APP_NAME create java
~~~

* **Create a file called 'Procfile' in the project dir with the following contents to specify the start command:**

~~~
web:    java -cp target/classes:target/dependency/* com.cloudcontrol.example.App
~~~

* **Init git repository:**

~~~
$ cd PROJECTDIR ; git init ; git add pom.xml Procfile src/ ; git commit -am "MSG"
~~~

* **Push code to cloudControl:**

~~~
$ cctrlapp APP_NAME/default push
~~~

* **Deploy application:**

~~~
$ cctrlapp APP_NAME/default deploy
~~~

* **Check deployment details (it can take a few seconds to change status to "deployed"):**

~~~
$ cctrlapp APP_NAME/default details
Deployment
	name: APP_NAME/default
	stack: pinky
	branch: ssh://APP_NAME@cloudcontrolled.com/repository.git
	private files: sftp://DEP_ID@cloudcontrolled.com/
	last modified: 2012-09-27 11:27:38
	current version: ddb81c1c510d9c845492d2322a6bdc1cfaba4bdc
	current state: deployed
	min boxes: 1
	max boxes: 1
~~~
  
* **Test it:**

	![[image]](https://raw.github.com/mkorszun/documentation/master/Guides/Java/images/test.png)


##Monitor your application

* **Deploy logs:**

~~~
$ cctrlapp APP_NAME/default log deploy
[Thu Sep 27 12:10:23 2012] lxc-dev-136 INFO Deploying ...
[Thu Sep 27 12:10:35 2012] lxc-dev-136 INFO Deployed version: f2b73a2d941a67ad5a2e2a400b9b88cc665caf6f
[Thu Sep 27 12:10:36 2012] ip-10-53-143-27 INFO Routing requests to new version
[Thu Sep 27 12:10:38 2012] ip-10-234-178-109 INFO Routing requests to new version
~~~
		
* **Access logs:**

~~~
$ cctrlapp APP_NAME/default log access
178.19.208.122 - - [27/Sep/2012:12:11:37 +0000] "GET http://APP_NAME.cloudcontrolled.com/ HTTP/1.1" 200 39 "" "Mozilla/5.0 		(Macintosh; Intel Mac OS X 10_8_0) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4"
178.19.208.122 - - [27/Sep/2012:12:11:37 +0000] "GET http://APP_NAME.cloudcontrolled.com/favicon.ico HTTP/1.1" 200 39 ""		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_0) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4"
~~~

* **Error / Application logs:**

~~~
$ cctrlapp APP_NAME/default log error
[Thu Sep 27 12:10:37 2012] info 2012-09-27 12:10:37.914:INFO:oejs.Server:jetty-7.6.0.v20120127
[Thu Sep 27 12:10:38 2012] info 2012-09-27 12:10:38.066:INFO:oejsh.ContextHandler:started o.e.j.s.ServletContextHandler{/,null}
[Thu Sep 27 12:10:38 2012] info 2012-09-27 12:10:38.127:INFO:oejs.AbstractConnector:Started SelectChannelConnector@0.0.0.0:29492
[Thu Sep 27 12:11:37 2012] info Request received from: 10.238.105.194
[Thu Sep 27 12:11:37 2012] info Request received from: 10.238.105.194	
~~~
 
