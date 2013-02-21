#Deploying Yii to cloudControl

If you're looking for a lightning fast, effective and light PHP Framework for your projects, one without a lot of the cruft and legacy overhead of some of the other popular PHP frameworks available today, you can't get past [Yii Framework](http://www.yiiframework.com/). Now at version 1.1.13 it comes with a variety of features to speed up your application development, e.g:

 * Baked in Security
 * Clear MVC approach
 * Large, thriving community
 * Loads of plugins and Add-ons
 * Easy to read documentation

In this tutorial, we're going to take you through the process of deploying the Yii Framework v1.1.11 on the [cloudControl](http://www.cloudcontrol.com) platform.

##1. Install Yii Framework

Download a copy of the latest stable release. 
~~~bash
$ cd <your projects directory>
$ wget https://github.com/yiisoft/yii/archive/1.1.13.tar.gz
$ tar -xzf 1.1.13.tar.gz
# and change into
$ cd yii-1.1.13
$ git init .
$ git add -A
~~~

Now your Yii installation is ready. If you installed it beneath the default documents root, go to `http://localhost/path/to/yii/requirements/index.php`.

##Create a Basic Application

Create a simple application in your local development environment. The generator supports creation of files needed by Git version control system. The following command would create necessary .gitignore (e.g. content of the assets and runtime shouldn't be tracked) and .gitkeep (forces tracking of initially empty but important directories) files:
~~~bash
$ php framework/yiic.php webapp myapp git
# confirm when asked
~~~

This will create a skeleton Yii application under the directory `myapp` (whereby `myapp` is your applications public document root). The application has a directory structure that is needed by most Yii applications.

Then, after you've done that, you're going to make a set of simple changes and you'll be ready to deploy your first application to cloudControl.

##2. Modifying the code

Ok, now that you have your test application up and running, we need to modify a few thing:

 * Store session in a database
 * Logging to syslog
 * Auto-magically determine the environment and set the configuration

###2.1 Sessions in the database

Yii by default stores it's session files on the file system. However, this approach isn't recommended on the cloudControl platform.
Additionally, storing the files in a multi-server environment can lead to problems that are hard to debug. That's why we're going to store sessions in the database.

Luckily, Yii is written in a very straight-forward and configurable manner, and this isn't that hard to do. The community around the framework is very healthy and there are many options as well as the support available.

###2.2 Logging to syslog

Yii by default stores the logs to the filesystem. The cloudControl platform provides a syslog to write the logs to a database and so you have to log all the applications and system messages to syslog. You can read the log by:
~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT_NAME log error
~~~

###2.3 Auto-magically determine the environment and set the configuration

By default, Yii comes with 3 bootstrap configuration files. The can be found in `<yourapp>/protected/config`; the files are called `console.php`, `main.php` and `test.php`. They are the _console_, _production_ and _testing_ environment configurations, respectively. The `test.php` includes the `main.php` file and ovewrites the _production_ environment settings.

It is likely that each environment will have different configuration settings. For that reason, we need to be able to differentiate between all the environments.

The app should know the environment it runs in to set up the proper configuration options. That way the same code will run in all the environments.

##3. Create a CloudControl Application

Let's get started making these changes and deploying the application.

~~~bash
$ git add myapp/
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

Next, create a testing branch as well. That way you have one deployment to test with and one stable, production deployment. Run the following commands:
~~~bash
$ git checkout -b testing
# push and deploy the testing branch
$ cctrlapp APP_NAME/testing push
$ cctrlapp APP_NAME/testing deploy --stack pinky
~~~

When you do the push, you'll see the output similar to the following:
~~~bash
$ cctrlapp APP_NAME/testing push
    Total 0 (delta 0), reused 0 (delta 0)

    -----> Receiving push
    -----> Yii Framework detected
    -----> Building image
    -----> Uploading image (6.7M)

    To ssh://APP_NAME@cloudcontrolled.com/repository.git
     * [new branch]      testing -> testing
~~~

##4. Initialising MySQLs Add-on

Now we need to configure MySQL database. The MySQL database is needed to store the session information.
We are going to use [the MySQLs Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs) to access the MySQL database.

To initialise the MySQLs Add-on, run the following commands while noting of the output:
~~~bash
# Initialise the mysqls.free addon for the default deployment
cctrlapp APP_NAME/default addon.add mysqls.free

# Retrieve the settings
cctrlapp APP_NAME/default addon mysqls.free

# Initialise the mysqls.free addon for the testing deployment
cctrlapp APP_NAME/testing addon.add mysqls.free

# Retrieve the settings
cctrlapp APP_NAME/testing addon mysqls.free
~~~

The output of the commands will be similar to:
~~~
    Addon                    : mysqls.free

     Settings
       MYSQLS_DATABASE          : <database_username>
       MYSQLS_PASSWORD          : <database_password>
       MYSQLS_PORT              : 3306
       MYSQLS_HOSTNAME          : <database_hostname>
       MYSQLS_USERNAME          : <database_name>
~~~

##5. Store session in a database

###5.1 Read database connection parameter from credentials file

In `myapp/protected/config/main.php`, insert before the `return array(` command the following code:
~~~php
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

    $database_host = $creds["MYSQLS"]["MYSQLS_HOSTNAME"];
    $database_name = $creds["MYSQLS"]["MYSQLS_DATABASE"];
    $database_user = $creds["MYSQLS"]["MYSQLS_USERNAME"];
    $database_password = $creds["MYSQLS"]["MYSQLS_PASSWORD"];
} else {
    $database_host = 'localhost';
    $database_name = '<local_yii_database_name>';
    $database_user = '<local_yii_database_user>';
    $database_password = '<local_yii_database_password>';
}
~~~

In the same file, search for the mysql database section, uncomment it and edit it like this:
~~~php
        // uncomment the following to use a MySQL database
        'db'=>array(
            'connectionString' => sprintf('mysql:host=%s;dbname=%s', $database_host, $database_name),
            'emulatePrepare' => true,
            'username' => $database_user,
            'password' => $database_password,
            'charset' => 'utf8',
        ),
~~~

This code sets the database connection globally.

Comment the default database configuration that uses SQLite3, the 3 preceding lines to the mysqls section.

###5.2 Configure `myapp/protected/config/main.php` to use a database for storing the session data

Add following session configuration to the components section (directly after the database configuration section):
~~~php
        'session' => array (
            'class' => 'system.web.CDbHttpSession',
            'connectionID' => 'db',
            'sessionTableName' => 'YiiSession',
            'sessionName' => 'SiteSession',
            'autoCreateSessionTable' => false,
            'autoStart' => 'false',
            'timeout' => 300
        ),
~~~

###5.3 Database schema

Next you should create the session table.
~~~bash
$ mysql -u <database_username> -p \
    -h <database_host> \
    --ssl-ca=mysql-ssl-ca-cert.pem <database_name>
~~~

In the command above, you can see a reference to a **.pem** file. This file can be downloaded [here](http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem). Login to your database's shell and run the following code:
~~~sql
CREATE TABLE `YiiSession` (
    id CHAR(32) PRIMARY KEY,
    expire INTEGER,
    data TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
~~~

You should also do this for the `APP_NAME/default` deployment.

##6. Logging to syslog

Create a file `myapp/protected/extensions/SysLogRoute.php` with the following content:
~~~php
<?php

class SysLogRoute extends CLogRoute {
    public $levels;
    public $categories;
    public $logFacility;
    public $logName;
    private $_prioMap = array(
        'trace' => LOG_DEBUG,
        'info' => LOG_INFO,
        'profile' => LOG_NOTICE,
        'warning' => LOG_WARNING,
        'error' => LOG_ERR
    );

    public function init() {
        parent::init();
        if (empty($this->logName)) {
            $this->logName = 'YiiApp';
        }
        if (empty($this->logFacility)) {
            $this->logFacility = LOG_USER;
        }
        if (true !== openlog($this->logName, LOG_ODELAY | LOG_PID, $this->logFacility)) {
            throw new CException('Failed to initiate the logging subsystem.');
        }
    }
    protected function processLogs($logs) {
        foreach($logs as $log) {
            $pri = $this->_prioMap[$log[1]];
            $parts = explode("\n", $log[0]);
            syslog($pri, $log[1] . ' - (' . $log[2] . ') - ' . array_shift($parts));
            foreach ($parts as $m){
                syslog($pri, "    " . $m);
            }
        }
        closelog();
    }
}
~~~

This class will be the Syslog handler and has to be registered in the configuration files.
Edit the `myapp/protected/config/main.php` configuration file for the _production_ environment and change the components' log section to:
~~~php
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class' => 'ext.SysLogRoute',
                    'levels' => 'profile, warning, error',
                    'categories' => 'application.*,system.*',
                ),
            ),
        ),
~~~

Edit the `myapp/protected/config/test.php` configuration file for the _testing_ environment and add the log section to the components' section:
~~~php
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class' => 'ext.SysLogRoute',
                        'levels' => 'trace, info, profile, warning, error',
                        'categories' => 'application.*,system.*',
                    ),
                ),
            ),
~~~

This section overwrites the log section from the main configuration and adds the `trace` and `info` log level.

##7. Auto-magically determine the environment and set the configuration

Change the `myapp/index.php` to:
~~~php
<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/test.php';

// Auto-Magically Determine the Environment
if (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['DEP_NAME'])) {
    $parts = explode('/', $_SERVER['DEP_NAME']);
    $environment = $parts[count($parts)-1];
    switch ($environment) {
        case 'default':
        case 'production':
            $config=dirname(__FILE__).'/protected/config/main.php';
            break;
        default:
            $config=dirname(__FILE__).'/protected/config/test.php';
            defined('YII_DEBUG') or define('YII_DEBUG',true);
            // specify how many levels of call stack should be shown in each log message
            defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
    }
}

require_once($yii);
Yii::createWebApplication($config)->run();
~~~

##8. Simple Check application

In this example you'll use the Yii demo files. Add a method to _SiteController_ class in `myapp/protected/controllers/SiteController.php` file:
~~~php
    public function actionCheck() {
        $model = new LoggingtestForm();
        if (isset($_POST['LoggingtestForm'])) {
            $model->attributes = $_POST['LoggingtestForm'];
            if ($model->validate()){
                $model->log();
                Yii::app()->user->setFlash('check', sprintf('Message was sent to syslog. Check this in your console by accessing "cctrlapp %s log error"', $_SERVER['DEP_NAME']));
            }
        }
        $visits = (int)Yii::app()->session['visits'];
        Yii::app()->session['visits'] = ++$visits;
        $this->render('check', array(
            'model' => $model,
            'visits' => $visits));
    }
~~~

As you can see you need a _LoggingtestForm_. Create a file `myapp/protected/models/LoggingtestForm.php` with following code:
~~~php
<?php

class LoggingtestForm extends CFormModel {
    public $logmessage;
    public $datetime;

    public function __construct() {
        $this->datetime = new DateTime('now');
        parent::__construct();
    }
    public function rules() {
        return array(
            array('logmessage', 'required'),
        );
    }
    public function log() {
        $message = sprintf('date: %s, message: %s', $this->datetime->format('Y-m-d H:i:s'), $this->logmessage);
        Yii::log($message, 'profile', 'application');
    }
}
~~~

Now you need a view; create a file `myapp/protected/views/site/check.php`:
~~~html
<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>
<h1>Check Page</h1>

<div>
    <h2>Session Test</h2>
    Visitor count: <?php echo $visits; ?>
</div>

<div style="margin-top:20px;">
    <h2>Logging Test</h2>
    <div class="form">
    <?php echo CHtml::beginForm(); ?>
        <?php echo CHtml::errorSummary($model); ?>
        <div class="row">
            <?php echo CHtml::activeLabel($model,'logmessage'); ?>
            <?php echo CHtml::activeTextField($model,'logmessage') ?>
        </div>
            <div class="row submit">
            <?php echo CHtml::submitButton('Send'); ?>
        </div>
    <?php echo CHtml::endForm(); ?>
    </div><!-- form -->
    <?php if(Yii::app()->user->hasFlash('check')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('check'); ?>
    </div>
    <?php endif; ?>
</div>
~~~

Finally you need a link to the new page. Edit `myapp/protected/views/layouts/main.php` find the _mainmenu_ (div id="mainmenu") and add the _Check_ page's reference:
~~~php
                ...
                array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>'Check', 'url'=>array('/site/check')),
            ),
            ...
~~~
Note the comma at the end of 'Logout' line.

Now commit, push and deploy the changes to the _testing_ deployment:
~~~bash
# avoid annoying log messages for favicon
$ touch myapp/favicon.ico
$ git add -A
$ git commit -am "Store session to database, log to syslog, determine the environment and implement check page"
$ cctrlapp APP_NAME/testing push
$ cctrlapp APP_NAME/testing deploy
~~~

All the changes are deployed now. Go to `http://testing-APP_NAME.cloudcontrolled.com/` and navigate to your new _Check_ page. When reloading, the visitor count should increase. Send a log message and watch the log:
~~~bash
$ cctrlapp APP_NAME/testing log error
~~~

In the _testing_ environment you will get a bunch of log lines (due to the trace log level), they look like this:
~~~
[Wed Jan 23 10:07:42 2013] notice profile - (application) - date: 2013-01-23 11:07:42, message: this is a log message test
[Wed Jan 23 10:07:42 2013] debug pool www[38]: trace - (system.CModule) - Loading "log" application component
[Wed Jan 23 10:07:42 2013] debug pool www[38]:     in /srv/www/www/myapp/index.php (22)
[Wed Jan 23 10:07:42 2013] debug pool www[38]: trace - (system.CModule) - Loading "request" application component
[Wed Jan 23 10:07:42 2013] debug pool www[38]:     in /srv/www/www/myapp/index.php (22)
[Wed Jan 23 10:07:42 2013] debug pool www[38]: trace - (system.CModule) - Loading "urlManager" application component
[Wed Jan 23 10:07:42 2013] debug pool www[38]:     in /srv/www/www/myapp/index.php (22)
...
~~~

The log level in the _production_ environment allows only for `profile`(`notice`), `warning` and `error` messages. This way you won't have unnecessary logs in your _production_'s log.

If the _Check_ pages output is correct, you can merge the changes to the master branch, push and deploy your _production_ deployment.
~~~bash
$ git checkout master
$ git merge testing
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~

Check the _production_ deployment at `http://APP_NAME.cloudcontrolled.com/index.php?r=site/check`.
Take a look at your cloudControl log, it should contain fewer messages now. That's the proof that the environment detection is working.
