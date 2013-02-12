# Getting the Add-on credentials

There are two ways to get the [Add-on credentials] in a Java app.

## Reading the credentials from environment variables

By default, each Add-on exposes its credentials in the environment. You can look up the individual environment
variable names either in the respective Add-on documentation or with the command
`cctrlapp APP_NAME/DEP_NAME addon.creds`.
To read them, simply use the `System.getenv()` method.
Some examples for database Add-ons can be seen in the last section.

Note that there are some other interesting [environment variables] available in your deployment containers.

## Reading the credentials from the credentials file

If your application possibly exposes it's environment variables in some way (e.g. automated bug reports,
debug email, showing it on error pages) you should [disable exporting credentials in the environment][cred-env-vars].
You can still access your credentials using the JSON formatted credentials file available to your deployment.
The path to the JSON file can be found in the `CRED_FILE` environment variable.

We provide a small [cloudControl credentials helper class](https://gist.github.com/b350762c61fcc069b427) to get the Add-on credentials from the file.
It requires [json-simple](http://code.google.com/p/json-simple/), a simple Java toolkit to encode or decode JSON text easily.
To use it in your project, add it as a maven dependency:
~~~xml
<dependencies>
    <dependency>
        <groupId>com.googlecode.json-simple</groupId>
        <artifactId>json-simple</artifactId>
        <version>1.1</version>
    </dependency>
</dependencies>
~~~
Now you can get the credentials like this:
~~~java
Credentials cr = Credentials.getInstance();
String database = (String)cr.getCredential("MYSQLS_DATABASE", "MYSQLS");
~~~

# Adding databases

cloudControl offers number of data storage solutions via [Add-on marketplace](https://www.cloudcontrol.com/add-ons/?c=1). Find below examples how to access Add-on credentials for MySQL and PostgreSQL - the most popular ones.

## MySQL

To add a MySQL database, use MySQL (Shared) addon. More information can be found [here](https://www.cloudcontrol.com/add-ons/mysqls).

Here's a Java snippet that reads the MySQL settings from the environment variables:

~~~java
String host = System.getenv("MYSQLS_HOSTNAME");
String port = System.getenv("MYSQLS_PORT");
String database = System.getenv("MYSQLS_DATABASE");
String username = System.getenv("MYSQLS_USERNAME");
String password = System.getenv("MYSQLS_PASSWORD");
~~~

## PostgreSQL

To add a PostgreSQL database, use the ElephantSQL Add-on. More information can be found [here](https://www.cloudcontrol.com/add-ons/elephantsql).

Here is a Java snippet that reads PostgreSQL settings from the environment variables:

~~~java
String url = System.getenv("ELEPHANTSQL_URL");
~~~

You can also find a working example of a [Java application with MySQL](https://github.com/cloudControl/java-mysql-example-app) on github.

[environment variables]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables
[Add-on credentials]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-on-credentials
[cred-env-vars]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#enabling-disabling-credentials-environment-variables
