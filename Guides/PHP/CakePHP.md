#Deploying CakePHP 2.2.5

If you're looking for a fast, light and effective PHP Framework for your projects, you can't go past [CakePHP](http://cakephp.org/). Now at [version 2.3](https://github.com/cakephp/cakephp/tags) it comes with a variety of features to speed up your application development, including:

 * Baked in Security
 * Clear MVC approach
 * Large, thriving community
 * Lots of plugins and Add-ons
 * Easy-to-read documentation

In this tutorial, we're going to take you through deploying CakePHP v2.3 to [the cloudControl platform](http://www.cloudcontrol.com).

##Prerequisites

 * [cloudControl user account](https://www.cloudcontrol.com/for-developers)
 * [Git client](http://git-scm.com/)
 * MySQL client, such as [MySQL Workbench](http://dev.mysql.com/downloads/workbench/) or the command-line tools

##1. Install CakePHP

Download a copy of the latest stable release, [2.3 at the time of publishing](https://github.com/cakephp/cakephp/archive/2.3.0.tar.gz) and extract it to your local file system.

##2. Modifying the code

As was mentioned before, a few changes need to be made to the default CakePHP configuration:

 * Store session in a database
 * Logging to syslog
 * Auto-magically determine the environment and set the configuration

###2.1 Sessions in the database

CakePHP by default stores it's session files on the file system. However, this approach isn't recommended on the cloudControl platform.
Additionally, storing the files in a multi-server environment can lead to problems that are hard to debug. That's why we're going to store sessions in the database.

Luckily, CakePHP is written in a very straight-forward and configurable manner, and this isn't that hard to do. The community around the framework is very healthy and there are many options as well as the support available.

###2.2 Logging to syslog

CakePHP by default stores the logs to the file system. The cloudControl platform provides a syslog to write the logs to a database and it is recommended to configure CakePHP to log to syslog. You can read the log by:
~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT_NAME log error
~~~

###2.3 Auto-magically determine the environment and set the configuration

CakePHP has no built-in support for different environments, but having different environments is often really convenient, e.g to account for differences between development, staging and production.
It is likely that each environment will have different configuration settings. For that reason, we need to be able to differentiate between all the environments.
The app should know the environment it runs in to set up the proper configuration options. That way the same code will run in all the environments.

##3. Put the code under git

In your CakePHP directory you will find a `.gitignore` file. The file needs to be modified as it currently isn't suitable to be used with the cloudControl platform. **Remove the `app/Config` entry** to enable tracking and pushing of the configuration files.

You'll start by putting the code under git version control system. Run the following commands:
~~~bash
$ cd <your CakePHP directory>
$ git init .
$ git add -A
$ git commit -m "Initial commit"
~~~

In this example we are using the application name's placeholder __APP_NAME__. You will of course have to use some other name instead.
Now you need to create a deployment on the cloudControl platform. First create the application, then push and deploy the `APP_NAME/default` deployment, also called _production_ deployment. Run the following commands:
~~~bash
# create the application
$ cctrlapp APP_NAME create php

# push and deploy the default branch
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy --stack pinky
~~~

Next, create a development branch as well. That way you'll have one deployment to test with and one stable, production deployment. Run the following commands:
~~~bash
$ git checkout -b development
# push and deploy the development branch
$ cctrlapp APP_NAME/development push
$ cctrlapp APP_NAME/development deploy --stack pinky
~~~

If you visit newly deployed website `http://development-APP_NAME.cloudcontrolled.com`, you'll get an error. The `app/tmp` folder with the cache and log files is ignored by git by default. This will be solved when the APC cache is used.

##4. Improvements

###4.1 Change Security Settings

To change the default `Security.salt` and `Security.cipherSeed` open `app/Config/core.php` and change the following values:
~~~php
...
Configure::write('Security.salt', '<40-characters-alphanumeric>');
...
Configure::write('Security.cipherSeed', '<32-numbers-only>');
...
~~~

There are several online generators that can be helpful, e.g. [Seth Cardoza](http://www.sethcardoza.com/tools/random-password-generator/).

###4.2. Use APC (Alternative PHP Cache)

Replace the following line in `app/Config/bootstrap.php`:
~~~php
Cache::config('default', array('engine' => 'File'));
~~~
with:
~~~php
Cache::config('default', array(
     'engine' => 'Apc', //[required]
     'duration'=> 3600, //[optional]
     'probability'=> 100, //[optional]
     'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 ));
~~~

In `app/Config/core.php`, change the line:
~~~php
$engine = 'File';
~~~
to:
~~~php
$engine = 'Apc';
~~~

##5. Auto-magically determine the environment and set the configuration

As mentioned before, CakePHP doesn't differentiate between different environments out of the box, so we'll do that by using different bootstrap files, such as **index.php**, **index-test.php** etc.

The app should know the environment it runs in to set proper configuration options. That way the code will run in every environment.

First, you're going to extend the bootstrap process to be able to determine which environment is being used. Create a new file under **`app/Config/environment.php`**, and add in the code below. Have a look at it and we'll go through it together.

###5.1 `app/Config/environment.php`

Create a new file `app/Config/environment.php` within a new class, called **ENVIRONMENT_CONFIG**, that will be later used by the database config. Note that we have 5 environments: **local, default, development** and **testing** (the _default_ environment corresponds to the _production_ deployment).
~~~php
<?php

class ENVIRONMENT_CONFIG {

    private static $environments = array('local', 'default', 'development', 'testing');
    private static $creds = array();

    public static function getCredentials() {
        if (self::$creds || !file_exists($_ENV['CRED_FILE'])) {
            return self::$creds;
        }
        // Parse the json file with ADDONS credentials
        $string = file_get_contents($_ENV['CRED_FILE'], false);
        if ($string == false) {
            throw new Exception('Could not read credentials file');
        }
        self::$creds = json_decode($string, true);

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

        return self::$creds;
    }
    public static function getEnvironmentName() {
        // set default to local
        Configure::write('Environment.name', 'local');

        if (!empty($_SERVER['HTTP_HOST']) && isset($_ENV['DEP_NAME'])) {
            // Set environment from the deployment's name
            $parts = explode('/', $_ENV['DEP_NAME']);
            $environment = $parts[count($parts) - 1];
            if (!in_array($environment, self::$environments)) {
                throw new Exception(sprintf("Wrong environment '%s'", $environment), 1);
            }
            Configure::write('Environment.name', $environment);
        }
        return (String) Configure::read('Environment.name');
    }
}
~~~

In the `getCredentials` function, we retrieve [the credentials file](https://github.com/cloudControl/add_on_cred_file/blob/master/_config.php) from the environment, which is a part of the standard cloudControl deployment. From the credentials we retrieve all the Add-on settings.

In the `getEnvironmentName function` we first check that we're on the cloudControl platform (indicated by the presence of the **DEP_NAME** environment variable). Then we proceed to retrieve the deployment's name from the environment variable that is present in cloudControl environment. From the deployment's name (in our case: 'APP_NAME/testing') we extract the current branch's name as the active environment, store it in an application environment setting and return the value.

Now that we know the environment we're operating in, we setup the database configuration properly. If we're in development, then we'll use the development configuration in `app/Config/database.php`. If we're not, then we retrieve the options from the `getCredentials` function.

##6. Store session in a database

###6.1. Initialising MySQLs Add-on

Now we need to configure MySQL database. The MySQL database will be used to store the session information.
We are going to use [the MySQLs Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs) to access the MySQL database.

Add the MySQLs Add-on to your deployments:
~~~bash
# Initialise the mysqls.free addon for the default deployment
$ cctrlapp APP_NAME/default addon.add mysqls.free

# Initialise the mysqls.free addon for the development deployment
$ cctrlapp APP_NAME/development addon.add mysqls.free

# Retrieve the settings
$ cctrlapp APP_NAME/development addon mysqls.free
~~~

The output of the commands will be similar to:
~~~
Addon : mysqls.free
Settings
MYSQLS_DATABASE : <database_name>
MYSQLS_PASSWORD : <database_password>
MYSQLS_PORT : 3306
MYSQLS_HOSTNAME : <database_host>
MYSQLS_USERNAME : <database_username>
~~~

Now you have to rename `app/Config/database.php.default` to `app/Config/database.php`. Do this with git:
~~~bash
$ git mv app/Config/database.php.default app/Config/database.php
~~~

###6.2 `app/Config/database.php`

When we configured the Add-on earlier, the settings were automatically added to the deployment environment. When we're not in the local development environment, we can retrieve these settings and use them to configure the database connection.

Let's now configure the development environment database settings. Open `app/Config/database.php` and replace the code with following to use `getCredentials` from our newly created ENVIRONMENT_CONFIG class.
~~~php
<?php
require_once APP . 'Config' . DS . 'environment.php';

class DATABASE_CONFIG {

    public function __construct() {
        $environment = ENVIRONMENT_CONFIG::getEnvironmentName();
        if ($environment && $environment !== 'local') {
            $creds = ENVIRONMENT_CONFIG::getCredentials();

            if (!array_key_exists('MYSQLS', $creds)){
                throw new Exception('No MySQL credentials found. Please make sure you have added the mysqls addon.');
            }

            $this->default = array(
                'datasource' => 'Database/Mysql',
                'persistent' => false,
                'host' => $creds["MYSQLS"]["MYSQLS_HOSTNAME"],
                'login' => $creds["MYSQLS"]["MYSQLS_USERNAME"],
                'password' => $creds["MYSQLS"]["MYSQLS_PASSWORD"],
                'database' => $creds["MYSQLS"]["MYSQLS_DATABASE"],
                'prefix' => '',
                'encoding' => 'utf8',
            );
        }
    }
    // default config, the last fallback
    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'localuser',
        'password' => 'localpassword',
        'database' => 'localdatabase',
        'prefix' => '',
        'encoding' => 'utf8',
    );
    // local unittests
    public $test = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'localuser',
        'password' => 'localpassword',
        'database' => 'localdatabase',
        'prefix' => '',
        'encoding' => 'utf8',
    );
}
~~~

###6.3 Database schema

Next you should create the session table.
~~~bash
$ mysql -u <database_username> -p \
    -h <database_host> \
    --ssl-ca=mysql-ssl-ca-cert.pem <database_name>
~~~

In the command above, you can see a reference to a **.pem** file. This file can be downloaded [here](http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem). Login to your database's shell and run the following code:
~~~sql
CREATE TABLE `cake_sessions` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `data` text,
  `expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
~~~
You should also do this for the `APP_NAME/default` deployment.

###6.4 Configure Sessionhandler

To configure CakePHP to store session data in the database, open up `app/Config/core.php` and add in the following code:
~~~php
Configure::write('Session', array(
    'defaults' => 'database',
    'handler' => array(
        'model' => 'cake_sessions'
    )
));
~~~

###6.5. Logging to Syslog

Create a file `app/Lib/Log/Engine/SysLog.php`:
~~~php
<?php
App::uses('BaseLog', 'Log/Engine');

class SysLog  extends BaseLog {

    protected $_logName = '';
    protected $_facility = LOG_USER;

    public function __construct($config = array()) {
        parent::__construct($config);
        if(isset($config['facility'])){
            $this->_facility = $config['facility'];
        }
        if(isset($config['logName'])){
            $this->_logName = $config['logName'];
        }
    }

    public function write($type, $message) {
        $priorities = array(
             'emergency' => LOG_EMERG,
             'alert' => LOG_ALERT,
             'critical' => LOG_CRIT,
             'error' => LOG_ERR,
             'warning' => LOG_WARNING,
             'notice' => LOG_NOTICE,
             'info' => LOG_INFO,
             'debug' => LOG_DEBUG,
        );
        $prio = (array_key_exists($type, $priorities)) ? $priorities[$type] : $priorities['info'];
        openlog($this->_logName, LOG_PID, $this->_facility);
        $parts = explode("\n", $message);
        syslog($prio, array_shift($parts));
        foreach ($parts as $line){
            syslog($prio, "    " . $line);
        }
        closelog();
    }
}
~~~

Next, configure the loghandler in `app/Config/bootstrap.php`. Replace:
~~~php
/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));
~~~
with:
~~~php
/**
 * Configures default logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('default', array(
    'engine' => 'SysLog',
    'facility' => LOG_USER,
    'logName' => 'MyAppName',
));

require_once APP . 'Config' . DS . 'environment.php';
$environment = ENVIRONMENT_CONFIG::getEnvironmentName();

try {
    Configure::load(sprintf('bootstrap-%s', strtolower($environment)));
} catch(ConfigureException $e){
    // ignore not existing bootstrap-* config files
}
~~~

Here you can load environment-specific bootstrap configuration. Create a file `app/Config/bootstrap-development.php` for the _development_ deployment:
~~~php
<?php
Configure::write('debug', 2);

App::uses('CakeLog', 'Log');
CakeLog::config('default', array(
    'engine' => 'SysLog',
    'facility' => LOG_USER,
    'logName' => 'MyAppName'
));
~~~

and create a file `app/Config/bootstrap-default.php` for the _production_ deployment:
~~~php
<?php
Configure::write('debug', 0);

App::uses('CakeLog', 'Log');
CakeLog::config('default', array(
    'engine' => 'SysLog',
    'facility' => LOG_USER,
    'logName' => 'MyAppName',
    'types' => array('alert', 'emergency')
));
~~~
For the _production_ deployment it is recommended to filter the log messages to send only important logs.


##7. Create a Check Page

Create a page to check if the changes are working. Create the `app/Controller/CheckController.php` controller:
~~~php
<?php

class CheckController extends AppController {
    public $helpers = array('Html', 'Form');

    public function index() {
        $visits = (int)$this->Session->read('visits');
        $this->Session->write('visits', ++$visits);

        if ($this->request->is('post')) {
            if ($this->request->data) {
                CakeLog::alert($this->request->data['logentry']);
                CakeLog::info($this->request->data['logentry']);
                $this->Session->setFlash(sprintf('Your log line was sent. Check it in "cctrlapp %s log error"', $_SERVER['DEP_NAME']));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your post.');
            }
        }
        $this->set('visits', $visits);
    }
}
~~~

Create the `app/View/Check/index.ctp` template:
~~~html
<h1>Check Page</h1>
<div>
    <h2>Session Test</h2>
    Visits: <?php echo $visits; ?>
</div>
<div>
    <h2>Logging Test</h2>
    <form method="post" action="">
        Logentry: <input type="text" name="logentry" label="logentry"/><br/>
        <input type="submit" value="Go"/>
    </form>
</div>
~~~


##8. Review the Deployment

Let's have a look at all the changes. Get the git status; your status should be practically the same:
~~~bash
$ git status
# On branch development
# Changes to be committed:
#   (use "git reset HEAD <file>..." to unstage)
#
#	renamed:    app/Config/database.php.default -> app/Config/database.php
#
# Changes not staged for commit:
#   (use "git add <file>..." to update what will be committed)
#   (use "git checkout -- <file>..." to discard changes in working directory)
#
#	modified:   app/Config/bootstrap.php
#	modified:   app/Config/core.php
#	modified:   app/Config/database.php
#
# Untracked files:
#   (use "git add <file>..." to include in what will be committed)
#
#	app/Config/bootstrap-default.php
#	app/Config/bootstrap-development.php
#	app/Config/environment.php
#	app/Controller/CheckController.php
#	app/Lib/Log/
#	app/View/Check/
~~~

Commit the changes you made earlier, push and deploy the _development_ deployment. Run the following commands:
~~~bash
# add the new files to your repository
$ git add .

# commit the changes
$ git commit -m "changed to store session in mysql, auto-determine environment and log to syslog"

#  push and deploy the changes
$ cctrlapp APP_NAME/development push
$ cctrlapp APP_NAME/development deploy
~~~

Check the development deployment: `http://development-APP_NAME.cloudcontrolled.com/Check`.

* Reload the page; the visits counter should increase.
* Send a log line; in the _development_ deployment's log should appear both alert and info messages:
~~~bash
$ cctrlapp APP_NAME/development log error
~~~
* Further, the debug level in the _development_ deployment is set to 2. That's why on the page bottom, there should appear the SQL log.

If everything until now was ok, you can merge the development branch to the master branch, push and deploy to the '_production_' deployment.
~~~bash
$ git checkout master
$ git merge development

# deploy the default branch
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~

Take a look at your _production_ deployment to ensure that it is working: `http://APP_NAME.cloudcontrolled.com/Check`.

* Reload the page; the visits counter should increase.
* Send a log line; in the _production_ deployment, only the alert log message should appear:
~~~bash
$ cctrlapp APP_NAME/default log error
~~~
* The debug level in the _production_ deployment is set to 0. That's why on the page bottom the SQL log shouldn't appear.

If you go to `http://APP_NAME.cloudcontrolled.com`, you should seen an error in the browser but not in the cloudControl log.
