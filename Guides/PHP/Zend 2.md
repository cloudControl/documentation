# Deploying Zend Framework 2 to cloudControl

In this tutorial, we're going to walk you through the process of deploying a Zend Framework 2 based app to the [cloudControl PaaS](https://www.cloudcontrol.com).

The example app is a fork of the official ZendSkeletonApplication available on [github](https://github.com/zendframework/ZendSkeletonApplication) ready to be deployed on cloudControl.

## The Example App

Let's clone the example code from Github and walk through the cloudControl platform relevant changes.

~~~bash
$ git clone https://github.com/cloudControl/php-zend2-example-app.git
$ cd php-zend2-example-app
~~~

### Optional: Start the App Locally Using the PHP 5.4 Built-in Webserver

The app can be run locally with the PHP 5.4 built-in web server. Simply provide the local db credentials, install the dependencies using Composer, initialize the session table and then start the PHP 5.4 built-in web server.

Create the file `config/autoload/local.php` with the following code. Make sure to replace the `DATABASE`, `USERNAME` and `PASSWORD` placeholders.

~~~php
<?php

return array(
	'db' => array(
		'driver'         => 'Pdo',
		'dsn'            => 'mysql:dbname=DATABASE;host=localhost',
		'driver_options' => array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		),
		'username' => USERNAME,
		'password' => PASSWORD,
	)
);
~~~

~~~bash
$ php composer.phar install
$ php public/index.php init-session-table
[SUCCESS] Session table created.
$ cd public/
$ php -S localhost:8888
~~~

Open [localhost:8888](http://localhost:8888/) in your browser to visit the local app.

### Read Credentials from the Environment and Write the Log to Syslog

The code in `config/autoload/global.php` is pretty straightforward. If the environment variable `CRED_FILE` is set, the `get_credentials()` method is used to read the JSON file and return the db credentials as part of the Zend 2 config.

We also configure the logger to log to syslog.

~~~php
<?php
	
function get_credentials() {
	// read the credentials file
	$string = file_get_contents($_ENV['CRED_FILE'], false);
	if ($string == false) {
		throw new Exception('Could not read credentials file');
	}
	// the file contains a JSON string, decode it and return an associative array
	$creds = json_decode($string, true);

	if (!array_key_exists('MYSQLS', $creds)){
		throw new Exception('No MySQL credentials found. Please make sure you have added the mysqls addon.');
	}

	$database_host = $creds["MYSQLS"]["MYSQLS_HOSTNAME"];
	$database_name = $creds["MYSQLS"]["MYSQLS_DATABASE"];
	$database_user = $creds["MYSQLS"]["MYSQLS_USERNAME"];
	$database_password = $creds["MYSQLS"]["MYSQLS_PASSWORD"];

	return array(
		'driver'         => 'Pdo',
		'dsn'            => sprintf('mysql:dbname=%s;host=%s', $database_name, $database_host),
		'driver_options' => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		),
		'username' => $database_user,
		'password' => $database_password,
	);
}

$config = array();

// If the app is running on the cloudControl PaaS read the credentials
// from the environment. Local db credentials should be put in local.php
if (isset($_ENV['CRED_FILE'])) {
	$config['db'] = get_credentials();
}

$config['service_manager'] = array(
	'factories' => array(
		'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
		'Zend\Log\Logger' => function(){
			$logger = new Zend\Log\Logger;
			$writer = new Zend\Log\Writer\Syslog();
			if (!endsWith($_ENV['DEP_NAME'], '/default')) {
				$writer->addFilter(Zend\Log\Logger::ERR);
			}
			$logger->addWriter($writer);
			return $logger;
		}
	),
	'aliases' => array(
		'db' => 'Zend\Db\Adapter\Adapter'
	)
);

return $config;

~~~

### Store Sessions in the Database

Storing sessions on the local filesystem does not work well on a horizontally scaling platform like cloudControl. Additionally the filesystem on cloudControl is not persitent across deploys so all sessions are lost after each deploy.

To avoid this, the app is preconfigured to store sessions using the previously configured connection in the database.

The respective code lives in `module/Application/Module.php`. It uses the global database credentials and sets the built-in Zend 2 database session save handler as the default.

~~~php
[...]

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
    	// configure session to use database
    	$config = $e->getApplication()->getServiceManager()->get('config');
    	$dbAdapter = new \Zend\Db\Adapter\Adapter($config['db']);
    	$sessionOptions = new \Zend\Session\SaveHandler\DbTableGatewayOptions();
    	$sessionTableGateway = new \Zend\Db\TableGateway\TableGateway('session', $dbAdapter);
    	$saveHandler = new \Zend\Session\SaveHandler\DbTableGateway($sessionTableGateway, $sessionOptions);
    	$sessionManager = new \Zend\Session\SessionManager(NULL, NULL, $saveHandler);
    	Container::setDefaultManager($sessionManager);

[...]
~~~

## Deploy the Zend 2 Example App to cloudControl

After the short walkthrough of the code, lets go ahead and deploy the app to cloudControl. Make sure to pick a unique and exciting `APP_NAME`.

~~~bash
# create the application
$ cctrlapp APP_NAME create php
Email   : EMAIL
Password: PASSWORD
Git configuration found! Using "Git" as repository type.

# push the code
$ cctrlapp APP_NAME/default push
Counting objects: 2208, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (771/771), done.
Writing objects: 100% (2208/2208), 869.14 KiB | 180.00 KiB/s, done.
Total 2208 (delta 1087), reused 2208 (delta 1087)
       
-----> Receiving push
       Submodule 'vendor/ZF2' (https://github.com/zendframework/zf2.git) registered for path 'vendor/ZF2'
       Initialized empty Git repository in /data/applications/APP_NAME/git-push-92157c6dc50dfab545adbda2761e4ef5f2138dd9-sDyGf40f/builddir/vendor/ZF2/.git/
       Submodule path 'vendor/ZF2': checked out '6022f490695b1c835070d9e5a81b45dc20b4a51c'
       Loading composer repositories with package information
       Installing dependencies (including require-dev)
         - Installing zendframework/zendframework (2.2.1)
           Downloading: 100%
       
       [...]

       Writing lock file
       Generating autoload files
-----> Zend 2.x Framework detected
-----> Building image
-----> Uploading image (3.1M)
       
To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master

# deploy the app
$ cctrlapp APP_NAME/default deploy --stack pinky
~~~

## Add the Required MySQL Database Add-on and Initialize the Session Table

To store the sessions we need to add a database Add-on and initialize the table.

We are going to use [the MySQLs Add-on's free plan](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs). It provides a free shared database for testing and development.

Creating the session table is easy by executing the included init-session-table command in a run-container.

~~~bash
# add the Add-on
$ cctrlapp APP_NAME/default addon.add mysqls.free
# initialize the session table
$ cctrlapp APP_NAME/default run "php code/public/index.php init-session-table"
Connecting...
[SUCCESS] Session table created.
Connection to ssh.cloudcontrolled.net closed.
~~~

Et voila, the app is now up and running at `http[s]://APP_NAME.cloudcontrolapp.com`.
