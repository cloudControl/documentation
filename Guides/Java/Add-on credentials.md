# Getting the Add-on Credentials

Each deployment gets different credentials for each Add-on. Providers can
change these credentials at any time, so they shouldn't be hard-coded in the
source code. If the credentials are not in the source code, they also won't
appear in the version control and cause potential security issues.

There are two ways to get the [Add-on credentials] in a Java app.

## Reading the Credentials from Environment Variables

By default, each Add-on exposes its credentials in the environment. You can
look up the individual environment variable names in the respective Add-on
documentation. To read them, simply use the `System.getenv()` method in your code.
Some examples for database Add-ons can be seen in the last section.

In case you don't want to expose these credentials in the environment, you can
disable them by executing:
~~~bash
$ cctrlapp APP_NAME/DEP_NAME config.add SET_ENV_VARS=false
~~~

The Add-on credentials can still be read from the credentials file, as explained in the next section.

Note that there are some other interesting [environment variables]
available in your deployment containers.

## Reading the Credentials from the Credentials File

All the [Add-on credentials] can be found in a provided JSON file as well, which path
is exposed in the `CRED_FILE` environment variable. You can see the format of that file locally:

~~~bash
$ cctrlapp APP_NAME/DEP_NAME addon.creds
~~~

We provide a small [cloudControl credentials helper class] to get the Add-on credentials from the file.
It requires [json-simple], a simple Java toolkit to encode or decode JSON text easily.
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
// e.g. for MySQLs
Credentials cr = Credentials.getInstance();
String database = (String)cr.getCredential("MYSQLS_DATABASE", "MYSQLS");
~~~

# Examples

cloudControl offers a number of data storage solutions via the [Add-on Marketplace].
Below you can find examples on how to access the Add-on
credentials for MySQL and PostgreSQL.

## MySQL
To add a MySQL database, use the [MySQL Dedicated Add-on] or [MySQL Shared Add-on].

Here's a Java snippet that reads the database settings from the environment variables:
~~~java
String database = System.getenv("MYSQLS_DATABASE");
String host 	= System.getenv("MYSQLS_HOSTNAME");
int port 		= Integer.valueOf(System.getenv("MYSQLS_PORT"));
String username = System.getenv("MYSQLS_USERNAME");
String password = System.getenv("MYSQLS_PASSWORD");
~~~
The example used the MySQLd Add-on. Variable names for MySQLs differ. Remember, you can always refer to the addon.creds command to see the actual variable names and values.

You can also find a working example of a [Java application with MySQL] on Github.

## PostgreSQL

To add a PostgreSQL database, use the [ElephantSQL Add-on].

With this Java snippet you can read the PostgreSQL settings from the environment variables:
~~~java
String elephantsqlUrl = System.getenv("ELEPHANTSQL_URL");
URI databaseUri = URI.create(elephantsqlUrl);

String database = databaseUri.getPath().substring(1);
String host 	= databaseUri.getHost();
int port 		= databaseUri.getPort();
String userInfo = databaseUri.getUserInfo();

String[] credentials = userInfo.split(":");
String username 	 = credentials[0];
String password 	 = credentials[1];
~~~

[Java application with MySQL]: https://github.com/cloudControl/java-mysql-example-app
[Add-on Marketplace]: https://www.cloudcontrol.com/add-ons/?c=1
[environment variables]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables
[Add-on credentials]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-on-credentials
[cred-env-vars]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#enabling-disabling-credentials-environment-variables
[json-simple]: http://code.google.com/p/json-simple/
[cloudControl credentials helper class]: https://gist.github.com/b350762c61fcc069b427
[MySQL Dedicated Add-on]: https://www.cloudcontrol.com/add-ons/mysqld
[MySQL Shared Add-on]: https://www.cloudcontrol.com/add-ons/mysqls
[ElephantSQL Add-on]: https://www.cloudcontrol.com/add-ons/elephantsql
