# CakePHP Migration Guide
This guide is intended to help migrate a CakePHP app deployed on [dotCloud], to the [Next dotCloud] PaaS.

Please look at our [Quickstart] and [Introduction for dotCloud Developers] to make yourself familiar with the Next dotCloud platform as well as its differences with the dotCloud platform. Furthermore, have a look at the [PHP & PHP Worker Migration Guide] which explains some basic PHP migration steps.

A CakePHP application's migration should not be difficult. Mostly you can push and deploy your source code. Only a few points have to be changed:

* Database configuration
* Worker

## Database configuration
Add the [MySQLs Add-on] to your deployment and migrate your data - check our [mysql migration guide] for any help.
You have to change your `/app/Config/database.php` to get the new database credentials:
~~~php
<?php
class DATABASE_CONFIG {

    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => null,
        'login' => null,
        'password' => null,
        'database' => null,
        'prefix' => '',
        'encoding' => 'utf8',
    );

    public $test = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => null,
        'login' => null,
        'password' => null,
        'database' => null,
        'prefix' => '',
        'encoding' => 'utf8',
    );

    public function __construct(){
        $string = file_get_contents($_ENV['CRED_FILE'], false);
        $creds = json_decode($string, true);
        $this->default['host'] = $creds['MYSQLS']['MYSQLS_HOSTNAME'];
        $this->default['login'] = $creds['MYSQLS']['MYSQLS_USERNAME'];
        $this->default['password'] = $creds['MYSQLS']['MYSQLS_PASSWORD'];
        $this->default['database'] = $creds['MYSQLS']['MYSQLS_DATABASE'];
    }
}
?>
~~~

## Worker
Probably you have defined a php-worker service on dotcloud. You can run [CakePHP background processes] on Next dotCloud as well. To do this, you have to add the [Worker add-on] to your deployment first. For more details read the [Worker Add-on documentation].

Add a `Procfile` to your repository which should look like:
~~~yaml
web: bash boot.sh
mailworker: /app/code/lib/Cake/Console/cake CakeResque.CakeResque start --queue mail
~~~
In this example [CakeResque] is used to creating background jobs that can be processed offline later.

You can run the worker by using the Next dotCloud CLI, if you need to add parameter you can pass them enclosed by double quotes:
~~~bash
dcapp APP_NAME/DEP_NAME worker.add mailworker "--interval 15"
~~~

[dotCloud]: https://www.dotcloud.com/
[Next dotCloud]: https://next.dotcloud.com/
[Quickstart]: https://next.dotcloud.com/dev-center/quickstart
[Introduction for dotCloud Developers]: https://next.dotcloud.com/dev-center/guides/migration-guides/an-introduction
[PHP & PHP Worker Migration Guide]: https://next.dotcloud.com/dev-center/guides/migration-guides/php-basic-use
[MySQLs Add-on]: https://next.dotcloud.com/add-ons/mysqls
[mysql migration guide]: https://next.dotcloud.com/dev-center/guides/migration-guides/migrating-mysql-services.md
[Worker add-on]: https://next.dotcloud.com/add-ons/worker
[Worker Add-on documentation]: https://next.dotcloud.com/dev-center/add-on-documentation/worker
[CakeResque]: http://cakeresque.kamisama.me/
[CakePHP background processes]: http://book.cakephp.org/2.0/en/console-and-shells.html
