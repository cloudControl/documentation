# Drupal Migration Guide
This guide is intended to help migrate a Drupal app deployed on [dotCloud], to the [Next dotCloud] PaaS.

Please look at our [Quickstart] and [Introduction for dotCloud Developers] to make yourself familiar with the Next dotCloud platform as well as its differences with the dotCloud platform. Furthermore, have a look at the [PHP & PHP Worker Migration Guide] which explains some basic PHP migration steps.

Migrating your Drupal application is pretty straightforward and involves two steps:

* Redirect to index.php
* Database configuration

## Redirect to index.php
Drupal provides by default a `.htaccess` file, that sets several webserver configurations (among others the redirection). To increase the application's performance you can put the `.htaccess` content into a buildpack apache configuration file. Create a file `.buildpack/apache/conf/drupal.conf` in the applications root directory and paste the `.htaccess` content into it. To get webserver compliance you need to make following changes:

* remove all comments (whitespaces before the comment "#" character are treated as error)
* enclose all content by:
~~~xml
<Directory "/app/code">
  ...
</Directory>
~~~

## Database configuration
Add the [MySQLs Add-on] to your deployment and migrate your data - check our [mysql migration guide] for any help.

Drupal's database configuration (among others) is set by using the installation wizard. The wizard creates a file `sites/default/settings.php` on the webserver. 
You need to get this file from the dotcloud webserver. Then you have to replace the database configuration with:
~~~php
$string = file_get_contents($_ENV['CRED_FILE'], false);
$creds = json_decode($string, true);
$databases = array (
  'default' => array (
    'default' => array (
      'database' => $creds["MYSQLS"]["MYSQLS_DATABASE"],
      'username' => $creds["MYSQLS"]["MYSQLS_USERNAME"],
      'password' => $creds["MYSQLS"]["MYSQLS_PASSWORD"],
      'host' => $creds["MYSQLS"]["MYSQLS_HOSTNAME"],
      'port' => '',
      'driver' => 'mysql',
      'prefix' => '',
    ),
  ),
);
~~~
Drupal list this file in the `.gitignore` file, since it may contain sensitive informations, like database passwords. You have to remove this line from the `.gitignore` file and add the database configuration to the repository.

[dotCloud]: https://www.dotcloud.com/
[Next dotCloud]: https://next.dotcloud.com/
[Quickstart]: https://next.dotcloud.com/dev-center/quickstart
[Introduction for dotCloud Developers]: https://next.dotcloud.com/dev-center/guides/migration-guides/an-introduction
[PHP & PHP Worker Migration Guide]: https://next.dotcloud.com/dev-center/guides/migration-guides/php-general-use
[MySQLs Add-on]: https://next.dotcloud.com/add-ons/mysqls
[mysql migration guide]: https://next.dotcloud.com/dev-center/guides/migration-guides/mysql-migration-guide
