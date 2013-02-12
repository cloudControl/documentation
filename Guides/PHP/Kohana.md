#Deploying a Kohana 3.3.0 application

If you're looking for a fast, light and highly configurable PHP Framework, look no further than [Kohana](http://kohanaframework.org/). Now at [version 3.3.0](http://kohanaframework.org/download) it comes with a variety of features to speed up your application development, including:

 * Excellent debugging and profiling tools
 * Flexible distribution license
 * Active community
 * Set of core libraries
 * Ability to easily override and extend the core libraries
 * Ability to add in 3rd party libraries (e.g. Zend Framework)
 * Rich [HMVC](http://en.wikipedia.org/wiki/Hierarchical_model%E2%80%93view%E2%80%93controller) support

In this tutorial, we're going to take you through deploying Kohana 3.3.0 to [the cloudControl platform](http://www.cloudcontrol.com). If you need further information about Kohana, check out [the online user guide](http://kohanaframework.org/documentation) or join to [the IRC channel](irc://irc.freenode.net/kohana).

##Prerequisites

 * [cloudControl user account](https://www.cloudcontrol.com/for-developers)
 * [Git client](http://git-scm.com/)
 * MySQL client, such as [MySQL Workbench](http://dev.mysql.com/downloads/workbench/) or the command-line tools

##1. Install Kohana Framework

Download a copy of the latest stable release [kohana-3.3.0.zip](https://github.com/downloads/kohana/kohana/kohana-3.3.0.zip) and extract it to your local filesystem.

##2. Modifying the code

As was mentioned before, a few changes need to be made to the default Kohana configuration:

 * Auto-magically determine the environment and set the configuration
 * Store session in a database
 * Logging to syslog

###2.1 Auto-magically determine the environment and set the configuration

It is likely that each environment will have different configuration settings. For that reason, we need to be able to differentiate between all the environments.
The app should know the environment it runs in to set up the proper configuration options. That way the same code will run in all the environments.

Kohana supports this out of the box via different bootstrap files.


###2.2 Sessions in the database

Kohana by default stores it's session files on the file system. However, this approach isn't recommended on the cloudControl platform.
Additionally, storing the files in a multi-server environment can lead to problems that are hard to debug. That's why we're going to store sessions in the database.

Luckily, Kohana is written in a very straight-forward and configurable manner, and this isn't that hard to do. The community around the framework is very healthy and there are many options as well as the support available.

###2.3 Logging to syslog

Kohana by default stores the logs to the file system. The cloudControl platform provides a syslog to write the logs to a database and is recommended to configure Kohana to log to syslog. You can read the log by:
~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT_NAME log error
~~~

##3. Put the code under git

Before you proceed further, you need to set a proper cookie salt. Generate a random string (hint: use a online generator e.g. [Seth Cardoza](http://www.sethcardoza.com/tools/random-password-generator)) and add this line to `application/bootstrap.php`:
~~~php
Cookie::$salt = '<40_characters_salt>';
~~~

Now it's time to start making previously mentioned changes and afterwords to deploy the application. You'll begin by putting it under git version control system. Run the following commands:
~~~bash
cd <your Kohana directory>
# update composer
$ php composer.phar self-update
$ git init .
$ git add -A
$ git commit -m "First addition of the source files"
~~~

###3.1 Create a CloudControl Application

In this example we are using the application name's placeholder __APP_NAME__. You will of course have to use some other name instead.
Now you need to create a deployment on the cloudControl platform. First create the application, then push and deploy the `APP_NAME/default` deployment, also called _production_ deployment. Run the following commands:

~~~bash
# create the application
$ cctrlapp APP_NAME create php

# push and deploy the default branch
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy --stack pinky
~~~

Next, create a development branch as well. That way you have one deployment to test with and one stable, production deployment. Run the following commands:
~~~bash
$ git checkout -b development
# push and deploy the development branch
$ cctrlapp APP_NAME/development push
$ cctrlapp APP_NAME/development deploy --stack pinky
~~~

When you do the push, you'll see the output similar to the following:
~~~bash
$ cctrlapp APP_NAME/development push
Total 0 (delta 0), reused 0 (delta 0)

-----> Receiving push
       Loading composer repositories with package information
       Installing dependencies from lock file
       Nothing to install or update
       Generating autoload files
-----> Kohana Framework detected
-----> Building image
-----> Uploading image (1.7M)

To ssh://APP_NAME2@cloudcontrolled.com/repository.git
 * [new branch]      development -> development
~~~

Both deployments have been created. You can take a look at:

 * _APP_NAME.cloudcontrolled.com/_  - for the production deployment
 * _development.APP_NAME.cloudcontrolled.com/_  - for the development deployment

You should see the installation page and the Environment Tests should pass. Once your install page reports that your environment is set up correctly you need to either rename or delete install.php in the root directory.
~~~bash
$ git rm install.php
$ git commit -am "remove install page"
$ cctrlapp APP_NAME/development push

# cloudControl remembered your last stack decision, so it's not necessary to repeat it
$ cctrlapp APP_NAME/development deploy
~~~

Kohana is now installed and you should see the output of the welcome controller:
~~~
hello, world!
~~~

The following code changes will be applied only to the development branch. When all the improvements have been implemented, the code changes would be merged to the master branch (_production_ deployment).


##4. Basic configuration

First you'll do some basic configuration. It's hardly recommended to enable the database module in the bootstrap file. You should also enable the `.htaccess` file to redirect all the requests to the `index.php` file.

###4.1 Enable modules

By default, in `application/bootstrap.php`, Kohana has the following configuration:
~~~php
Kohana::modules(array(
	// 'auth'       => MODPATH.'auth',       // Basic authentication
	// 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	// 'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
	// 'minion'     => MODPATH.'minion',     // CLI Tasks
	// 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));
~~~
This means that none of the modules above are ready to be used and this needs to be changed. In your `application/bootstrap.php` file, uncomment `database` so that it looks like:
~~~php
Kohana::modules(array(
    ...
	   'database'   => MODPATH.'database',   // Database access
	...
	));
~~~
Now you'll have database module available. Our example application is simple, so this is all you need to enable. Leave everything else in the file as it is.

###4.2 Routing

Kohana provides a very powerful routing system. To use it, every requests should be directed to `index.php`. This will be done by the apache web-server with the `.htaccess` configuration file. You already have a `example.htaccess` in the working folder. Rename it:
~~~bash
$ git mv example.htaccess .htaccess
~~~

##5. Auto-magically determine the environment and set the configuration

In `application/bootstrap.php`, search for the following lines:
~~~php
if (isset($_SERVER['KOHANA_ENV']))
{
    Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}
~~~
After you've found it, replace them with the following:
~~~php
// set a default value, for local development
$env = Kohana::DEVELOPMENT;

if (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['DEP_NAME'])) {
    $parts = explode('/', $_SERVER['DEP_NAME']);
    $environment = $parts[count($parts)-1];
    switch($environment) {
        case 'dev':
        case 'develop':
        case 'development':
            $env = Kohana::DEVELOPMENT;
            break;
        case 'test':
        case 'testing':
            $env = Kohana::TESTING;
            break;
        case 'stage':
        case 'staging':
            $env = Kohana::STAGING;
            break;
        case 'default':
        case 'production':
        default:
            $env = Kohana::PRODUCTION;
    }
}
Kohana::$environment = $env;
~~~

The code for environment detection based on cloudControl environment setting is now in place. The environment variable `DEP_NAME` stores the application and deployment name.

Note that the we're using Kohana's environment constants; they can be found in `system/classes/Kohana/Core.php`. This way the code stays consistent and there is no unnecessary additional complexity.

When you are in the local environment, determined by the absence of `DEP_NAME` environment variable,  you'll default to the development setting. Otherwise, you'll retrieve the `DEP_NAME` value and attempt to match it against the Kohana's environment configurations.

To get an environment based configuration create a file `application/<env>_bootrstrap.php`, where _env_ is the integer value of your environment (see `system/classes/Kohana/Core.php`). In case of _production_ environment, the file's name would be `application/10_bootstrap.php`.
In this file you can define environment-specific settings, e.g. the logging level (see below).

To get the custom bootstrap file loaded add at __the end of `application/bootstrap.php`__:
~~~php
/**
 * include an environment based bootstrap configuration
 */
$env_bootstrap = sprintf('%s%s_bootstrap.php', APPPATH, $env);
if(is_file($env_bootstrap)){
    require $env_bootstrap;
}
~~~

##6. Store session in a database

As you've already enabled the database module, to store the session data you only need database access and the some configuration.

###6.1. Initialising MySQLs Add-on

Now we need to configure MySQL database. The MySQL database is needed to store the session information.
We are going to use [the MySQLs Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs) to access the MySQL database.

To initialise the MySQLs Add-on, run the following commands while noting of the output:
~~~bash
# Initialise the mysqls.free addon for the default deployment
cctrlapp APP_NAME/default addon.add mysqls.free

# Initialise the mysqls.free addon for the development deployment
cctrlapp APP_NAME/development addon.add mysqls.free
~~~

Check the Add-on configuration:
~~~bash
# Retrieve the settings
$ cctrlapp APP_NAME/development addon mysqls.free
~~~

The output of the commands will be similar to:
~~~bash
Addon                    : mysqls.free

 Settings
   MYSQLS_DATABASE          : <database_name>
   MYSQLS_PASSWORD          : <database_password>
   MYSQLS_PORT              : 3306
   MYSQLS_HOSTNAME          : <database_host>
   MYSQLS_USERNAME          : <database_username>
~~~

###6.2. Database connection

Create a new file `application/config/database.php` with the following content:
~~~php
<?php

// override the core settings if you're not in a local development environment
if (!empty($_SERVER['HTTP_HOST']) && isset($_ENV['CRED_FILE'])) {
    // read the credentials file
    $string = file_get_contents($_ENV['CRED_FILE'], false);
    if ($string == false) {
        throw new Exception('Could not read credentials file');
    }
    // the file contains a JSON string, decode it and return an associative array
    $creds = json_decode($string, true);

    $error = json_last_error();
    if ($error != JSON_ERROR_NONE){
        $json_errors = array(
            JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX => 'Syntax error',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        throw new Exception(sprintf('A json error occured while reading the credentials file: %s', $json_errors[$error]));
    }

    if (!array_key_exists('MYSQLS', $creds)){
        throw new Exception('No MySQL credentials found. Please make sure you have added the mysqls addon.');
    }

    return array(
        'default' => array(
            'type'       => 'MySQL',
            'connection' => array(
                'hostname'   => $creds["MYSQLS"]["MYSQLS_HOSTNAME"],
                'username'   => $creds["MYSQLS"]["MYSQLS_USERNAME"],
                'password'   => $creds["MYSQLS"]["MYSQLS_PASSWORD"],
                'persistent' => FALSE,
                'database'   => $creds["MYSQLS"]["MYSQLS_DATABASE"],
            ),
            'table_prefix' => '',
            'charset'      => 'utf8',
            'profiling'    => TRUE,
        ),
    );
} else {
    return array(
        'default' => array(
            'type'       => 'MySQL',
            'connection' => array(
                'hostname'   => 'localhost',
                'username'   => 'local_username',
                'password'   => 'local_username',
                'persistent' => FALSE,
                'database'   => 'local_database',
            ),
            'table_prefix' => '',
            'charset'      => 'utf8',
            'profiling'    => TRUE,
        ),
    );
}
~~~

When you configured the MySQLs Add-on, the settings were automatically persisted in the deployment's environment. When in a proper environment, you can retrieve the settings and configure the database connection.

###6.3. Session configuration

In `application/bootstrap.php`, just below the cookie salt, set the session's default handler:
~~~php
...
Session::$default = 'database';
...
~~~

Create a new file `application/config/session.php` with the following content:
~~~php
<?php
// configure the system to store sessions in the database

return array(
    'database' => array(
        'name' => 'cookie_name',
        'encrypted' => false,
        'lifetime' => 43200,
        'group' => 'default',
        'table' => 'sessions',
        'columns' => array(
            'session_id'  => 'session_id',
            'last_active' => 'last_active',
            'contents'    => 'contents'
        ),
        'gc' => 500,
    ),
);
~~~
Now the session information will be stored in the database, in a table called 'sessions' and have a lifetime of __43200 seconds__. You can read about the other settings in [the session documentation online](http://kohanaframework.org/3.3/guide/kohana/sessions). Unset the `encrypted` flag. In our tests with enabled data encryption, the session data was not persisted. When `encrypted` is disabled, the data is stored in base64 format.


###6.4. Database schema

Next you should create the session table.

~~~bash
$ mysql -u <database_username> -p \
    -h <database_host> \
    --ssl-ca=mysql-ssl-ca-cert.pem <database_name>
~~~

In the command above, you can see a reference to a **.pem** file. This file can be downloaded [here](http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem). Login to your database's shell and run the following code:
~~~sql
CREATE TABLE `sessions` (
  `session_id` varchar(24) NOT NULL,
  `last_active` int(10) unsigned NOT NULL,
  `contents` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_active` (`last_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
~~~

You should also do this for the `APP_NAME/default` deployment.

##6. Logging to syslog

In `application/bootstrap.php`, find the place where the default file logger is defined:
~~~php
/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));
~~~
and replace the code with the follwoing:
~~~php
/**
 * Attach the syslog write to logging. Multiple writers are supported.
 */
$syslogwriter = new Log_Syslog();
Kohana::$log->attach($syslogwriter);
~~~

The default log writer will log the messages of all log levels.

The `$syslogwriter` variable is used if you want a customized logging configuration based on your environment. If you want a specific syslog configuration, you have to detach this default log writer first. You also need an environment specific logging configuration. Create/edit a file `application/<env>_bootrstrap.php` where _env_ is the environments integer value. In case of the _production_ environment, the file's name would be `application/10_bootstrap.php`.
~~~php
<?php
// detach the default log writer
Kohana::$log->detach($syslogwriter);
// re-attach the new log writer with limited log level
Kohana::$log->attach(new Log_Syslog(), $levels=array(
	Kohana_Log::EMERGENCY,
	Kohana_Log::ALERT,
	Kohana_Log::CRITICAL,
	Kohana_Log::ERROR
));
~~~
In the production environment it is recommended to log only the errors, so whitelist the log levels in the setting.

##7. A Simple Application

Let's build a very simple application to test your new configuration and deployment. It will have only one controller with a simple view. In the controller you're going to:

 * Create, store and manipulate a simple variable in the session
 * Log a message with the levels INFO and ERROR

Create the file `application/classes/Controller/Check.php` with the following content:
~~~php
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Check extends Controller {

    public function action_index()
    {
        $view = new View('check');
        $view->title = 'Check Page';

        $map = array(
            Kohana::PRODUCTION => 'PRODUCTION',
            Kohana::STAGING => 'STAGING',
            Kohana::TESTING => 'TESTING',
            Kohana::DEVELOPMENT => 'DEVELOPMENT'
        );
        $view->environment = array($map[Kohana::$environment], Kohana::$environment);

        $session = Session::instance();
        $visits = (int)$session->get('visits');
        $session->set('visits', ++$visits);
        $view->visits = $visits;

        if($this->request->method() == 'POST') {
            $post = Validation::factory($this->request->post());
            $post->rule('logentry_message', 'not_empty');
            if($post->check()) {
                Log::instance()->add(Log::INFO, sprintf('Info Logentry was sent: %s', $post['logentry_message']));
                Log::instance()->add(Log::ERROR, sprintf('Error logentry was sent: %s', $post['logentry_message']));
            }
        }
        echo $view->render();
    }
}
~~~

Implement minimal view in `application/views/check.php` with the following content:
~~~html
<?php if (!defined('SYSPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $title; ?></title>
</head>
<body>
    <div>
        <h2>Session Test</h2>
        Visits: <?php echo $visits; ?>
    </div>
    <div>
        <h2>Environment Test</h2>
        Current Environment: <?php echo $environment[0]; ?> (<?php echo $environment[1]; ?>)
    </div>
    <div>
        <h2>Logentry Test</h2>
        <?php echo Form::open(''); ?>
        <?php echo Form::label('logentry_message', 'Logentry');?>
        <?php echo Form::input('logentry_message', 'Add a logentry'); ?>
        <?php echo Form::submit('logentry_submit', 'Send it!'); ?>
        <?php echo Form::close(); ?>
    </div>
</body>
</html>
~~~

##8. Deploying the app

Now you need to commit the changes made on the development branch check that tests are passing. After that, merge the changes to the master branch you created earlier.

Run the commands:
~~~bash
$ git add -A
$ git commit -am "Updated to enable syslog, database & session and auto-determine the environment"

# push the code to the development branch
$ cctrlapp APP_NAME/development push
$ cctrlapp APP_NAME/development deploy
~~~

After deploying you can review the changes at _development.APP_NAME.cloudcontrolled.com/Check_. Make sure that everything is working.

* Reload the page, the visits counter should increase.
* The Environment should be "`DEVELOPMENT (40)`"
* Send a log line; in the _development_ deployment log both log messages (error and info) should appear:

~~~bash
$ cctrlapp APP_NAME/development log error
~~~

Now you can update the master branch:
~~~bash
$ git checkout master
$ git merge development

# push the code to the default (production) branch
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~
Check the results at _APP_NAME.cloudcontrolled.com/Check_.

* Reload the page, the visits counter should increase.
* The Environment should be "`PRODUCTION (10)`"
* Send a log line; in the _production_ deployment log only the error message should appear:

~~~bash
$ cctrlapp APP_NAME/default log error
~~~

With the last command, the tutorial is finished. You should now be ready to create your next, amazing PHP web application with Kohana on cloudControl. If you have any issues, feel free to email [support@cloudcontrol.com](mailto:support@cloudcontrol.com).
