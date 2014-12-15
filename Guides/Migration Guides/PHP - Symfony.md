# Symfony Migration Guide
This guide is intended to help migrate a Symfony app deployed on [dotCloud], to the [Next dotCloud] PaaS.

Please look at our [Quickstart] and [Introduction for dotCloud Developers] to make yourself familiar with the Next dotCloud platform as well as its differences with the dotCloud platform. Furthermore, have a look at the [PHP & PHP Worker Migration Guide] which explains some basic PHP migration steps.

Migrating your Symfony application is pretty straightforward and involves three steps:

* [Set the approot to the `web` directory](#set-the-approot-to-the-web-directory)
* [Database configuration](#database-configuration)
* [Install the Vendors](#install-the-vendors)

## Set the approot to the `web` directory
Symfony needs to set the approot to the `web`. You can get this done by creating a file `.buildpack/apache/conf/symfony.conf` in the applications root directory with the content:
~~~xml
DocumentRoot "/app/code/web"

<Directory "/app/code/web">
    AllowOverride All
    Options SymlinksIfOwnerMatch
    Order Deny,Allow
    Allow from All
</Directory>
~~~
Symfony provides three `.htaccess` files in `/web`, `/app` and `/src`. To increase the performance you should merge the content of those files into the `.buildpack/apache/conf/symfony.conf` file. Paste the content from `/web/.htaccess` beneath the `<Directory "/app/code/web">` directive. To get webserver compliance you need to remove all comments (whitespaces before the comment "#" character are treated as error)

Additionaly, create a new `Directory` directives: `<Directory "/app/code/app">` and `<Directory "/app/code/src">` with:
~~~xml
<Directory "/app/code/app">
    Deny from All
</Directory>

<Directory "/app/code/src">
    Deny from All
</Directory>
~~~

## Database configuration
Add the [MySQLs Add-on] to your deployment and migrate your data - check our [mysql migration guide] for any help.

Symfony's database configuration is set in `/app/config/config.yml`. To get the database configuration from the credentials file, create a file /app/config/credentials.php with:
~~~php
<?php
if (isset($_ENV['CRED_FILE'])) {
    $string = file_get_contents($_ENV['CRED_FILE'], false);
    $creds = json_decode($string, true);
    $database_host = $creds["MYSQLS"]["MYSQLS_HOSTNAME"];
    $database_name = $creds["MYSQLS"]["MYSQLS_DATABASE"];
    $database_user = $creds["MYSQLS"]["MYSQLS_USERNAME"];
    $database_password = $creds["MYSQLS"]["MYSQLS_PASSWORD"];
} else {
    $database_host = 'localhost';
    $database_name = '<local_symfony_database_name>';
    $database_user = '<local_symfony_database_user>';
    $database_password = '<local_symfony_database_password>';
}
$container->setParameter('database_driver', 'pdo_mysql');
$container->setParameter('database_port', 3306);
$container->setParameter('database_host', $database_host);
$container->setParameter('database_name', $database_name);
$container->setParameter('database_user', $database_user);
$container->setParameter('database_password', $database_password);
?>
~~~

Then you need to import this file in the `/app/config/config.yml` as resource:
~~~yaml
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: credentials.php }
...
~~~

## Install the Vendors
Symfony comes along with a `composer.json` file in the application root directory. The PHP buildpack tracks dependencies via [Composer] and install the requirements into the `vendor` directory.
Note that there is also the `composer.lock`. When you change the dependencies, you should run the composer.phar update command to update the `composer.lock`. This file must be in your repository and ensures that all the developers always use the same versions of all the libraries. It also makes the changes visible in git. Also note that your `.gitignore` should contain "vendor" as proposed in the Composer documentation, since you don't need all that code in your repository.

[dotCloud]: https://www.dotcloud.com/
[Next dotCloud]: https://next.dotcloud.com/
[Quickstart]: https://next.dotcloud.com/dev-center/quickstart
[Introduction for dotCloud Developers]: https://next.dotcloud.com/dev-center/guides/migration-guides/an-introduction
[PHP & PHP Worker Migration Guide]: https://next.dotcloud.com/dev-center/guides/migration-guides/php-basic-use
[MySQLs Add-on]: https://next.dotcloud.com/add-ons/mysqls
[mysql migration guide]: https://next.dotcloud.com/dev-center/guides/migration-guides/migrating-mysql-services.md
[Composer]: http://getcomposer.org
