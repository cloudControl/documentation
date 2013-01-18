# Getting Add-on credentials

Every deployment gets different credentials for each Add-on. Providers can change these credentials at any time, so they shouldn't be hard-coded in the source code. If the credentials are not in the source code, they also won't appear in the version control and if they did that would be potential security issue.

There are two ways to get the Add-on credentials in Java app.

## Reading the credentials from creds file

All the Add-on credentials can be found in provided JSON file.

The path to the JSON file can be found in the CRED_FILE environment variable. To see the format and contents of the creds.json file locally use the addon.creds command, cctrlapp APP_NAME/DEP_NAME addon.creds.

### Prepare your app for reading JSON files

In this case we'll use [json-simple](http://code.google.com/p/json-simple/), a simple Java toolkit to encode or decode JSON text easily. To use it in your project just add maven dependency:

~~~xml
<dependencies>
    <dependency>
        <groupId>com.googlecode.json-simple</groupId>
        <artifactId>json-simple</artifactId>
        <version>1.1</version>
    </dependency>
</dependencies>
~~~

Use the [cloudControl Credentials helper class](https://gist.github.com/b350762c61fcc069b427) to get Add-on credentials in easy way:

~~~java
Credentials cr = Credentials.getInstance();
String database = (String)cr.getCredential("MYSQLS_DATABASE", "MYSQLS");
~~~

## Reading the credentials from environment variables

An alternative (and simpler) way of reading the Add-on credentials is reading them from the environment variables. Each Add-on exposes some environment variables, the same ones that are nested under specific sections in cred file. To read them, simply use `System.getenv()` method. An example for database Add-on can be seen in the next section.

Note that there are some other interesting variables available in deployment's environment.

# Adding databases

There are generally two ways to access credentials and other database-related information:

* reading them from the relevant entry in "creds" JSON file
* reading them from the environment variables specific to the chosen database/Add-on

## MySQL

To add a MySQL database, use MySQL (Shared) addon. More information can be found [here](https://www.cloudcontrol.com/add-ons/mysqls).

Here's a Java snippet that reads database settings from the environment variables:

~~~java
String host = System.getenv("MYSQLS_HOSTNAME");
String port = System.getenv("MYSQLS_PORT");
String database = System.getenv("MYSQLS_DATABASE");
String username = System.getenv("MYSQLS_USERNAME");
String password = System.getenv("MYSQLS_PASSWORD");
~~~

## PostgreSQL

To add a PostgreSQL database, use the ElephantSQL Add-on. More information can be found [here](https://www.cloudcontrol.com/add-ons/elephantsql).

Here is a Java snippet that reads database settings from the environment variables:

~~~java
String url = System.getenv("ELEPHANTSQL_URL");
~~~

You can also find working example of [Java application with MySQL](https://github.com/cloudControl/java-mysql-example-app) on github.