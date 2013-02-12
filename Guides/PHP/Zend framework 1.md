#Deploying Zend Framework 1 to cloudControl

If you're looking for a feature-rich, flexible and capable PHP Framework for your projects, you can't go past [Zend Framework](http://framework.zend.com/). Now at [version 1.12.1](http://framework.zend.com/downloads/latest#ZF1) it comes with a variety of features to speed up your application development, including:

 * Straightforward MVC approach
 * Large, thriving community
 * Sub-library components for working with a range of services, including PayPal and Google
 * Easy-to-read documentation

In this tutorial, we're going to take you through the process of deploying a Zend Framework v1.12.1 app to [the cloudControl platform](http://www.cloudcontrol.com).

##Prerequisites

 * [cloudControl user account](https://www.cloudcontrol.com/for-developers)
 * [Git client](http://git-scm.com/)
 * MySQL client, such as [MySQL Workbench](http://dev.mysql.com/downloads/workbench/) or the command-line tools

##1. Install Zend Framework

Download a copy of the latest stable release.
~~~bash
$ cd <your projects directory>
$ wget https://packages.zendframework.com/releases/ZendFramework-1.12.1/ZendFramework-1.12.1.tar.gz
$ tar -xzf ZendFramework-1.12.1.tar.gz
$ cd ZendFramework-1.12.1
~~~

##1.1. Create the project

In order to get an application you have to create a project:
~~~bash
$ ./bin/zf.sh create project cloudControl
$ cd cloudControl

# you need the Zend library. The recommended way is to copy:
$ cp -r ../library/Zend library/
~~~

Now you have a basic Zend Framework 1 installation. To deploy it on the cloudControl platform, you'll need a special PHP-APC setting:
~~~bash
$ mkdir -p .buildpack/php/conf
$ cat << 'EOF' > .buildpack/php/conf/api.ini
[APC]
apc.stat = 1
EOF
~~~
By default the apc stat functionality is disabled because it is very resource intensive.

##2. Modifying the code

As was mentioned before, a few changes need to be made to the default Zend configuration:

 * Store session in a database
 * Logging to syslog
 * Auto-magically determine the environment and set the configuration

###2.1 Sessions in the database

Zend by default stores it's session files on the file system. However, this approach isn't recommended on the cloudControl platform.
Additionally, storing the files in a multi-server environment can lead to problems that are hard to debug. That's why we're going to store sessions in the database.

Luckily, Zend Framework is written in a very straight-forward and configurable manner, and this isn't that hard to do. The community around the framework is very healthy and there are many options as well as the support available.

###2.2 Logging to syslog

Zend by default stores the logs to the file system. The cloudControl platform provides a syslog to write the logs to a database and is recommended to configure Zend to log to syslog. You can read the log by:
~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT_NAME log error
~~~

###2.3 Auto-magically determine the environment and set the configuration

Zend has 4 default environments: _production_, _staging_, _testing_ and _development_. The environments are defined and configured in `application/configs/application.ini` file. By default, all environments settings extend the _production_ settings.

It is likely that each environment will have different configuration settings. For that reason, we need to be able to differentiate between all the environments.
The app should know the environment it runs in to set up the proper configuration options. That way the same code will run in all the environments.

##3. Put the code under git

Now it's time to start making previously mentioned changes and afterwords to deploy the application. You'll begin by putting it under git version control system. Run the following commands:
~~~bash
$ git init .
$ git add -A
$ git commit -m "Initial commit"
~~~

##3.1 Create a cloudControl application

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

When you do the push, you'll see the output similar to the following:
~~~bash
$ cctrlapp APP_NAME/development push
Total 0 (delta 0), reused 0 (delta 0)

-----> Receiving push
-----> Zend 1.x Framework detected
-----> Building image
-----> Uploading image (6.2M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      development -> development
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
cctrlapp APP_NAME/development addon.add mysqls.free

# Retrieve the settings
cctrlapp APP_NAME/development addon mysqls.free
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

Normally, the database adapter is configured in `application/configs/application.ini`, but on the platform, you have to read the connection parameters from the credentials file. To configure the database connection parameters just before loading other resources like session, edit the `public/index.php` file, so that it looks like.
:
~~~php
...
/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

$config = new Zend_Config_Ini(
	APPLICATION_PATH . '/configs/application.ini',
	APPLICATION_ENV,
	array('allowModifications' => true)
);

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
    $config->resources->db->params->host = $creds["MYSQLS"]["MYSQLS_HOSTNAME"];
    $config->resources->db->params->username = $creds["MYSQLS"]["MYSQLS_USERNAME"];
    $config->resources->db->params->password = $creds["MYSQLS"]["MYSQLS_PASSWORD"];
    $config->resources->db->params->dbname = $creds["MYSQLS"]["MYSQLS_DATABASE"];
}

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    $config
);
$application->bootstrap()
            ->run();
...
~~~

###5.2. Configure session handler

Now you need to configure the session handler to write the session data to the database. Edit the `application/configs/application.ini` file and add to the `production` section:
~~~ini
resources.session.use_only_cookies = true
resources.session.gc_maxlifetime = 864000
resources.session.remember_me_seconds = 864000
resources.session.saveHandler.class = "Zend_Session_SaveHandler_DbTable"
resources.session.saveHandler.options.name = "session"
resources.session.saveHandler.options.primary = "id"
resources.session.saveHandler.options.modifiedColumn = "modified"
resources.session.saveHandler.options.dataColumn = "data"
resources.session.saveHandler.options.lifetimeColumn = "lifetime"
~~~

###5.3. Database schema

Next you should create the session table.
~~~bash
$ mysql -u <database_username> -p \
    -h <database_host> \
    --ssl-ca=mysql-ssl-ca-cert.pem <database_name>
~~~

In the command above, you can see a reference to a **.pem** file. This file can be downloaded [here](http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem). Login to your database's shell and run the following code:
~~~sql
CREATE TABLE `session` (
  `id` char(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
~~~

You should also do this for the `APP_NAME/default` deployment.

##6. Logging to syslog

To configure the syslog logger you have to edit the `application/configs/application.ini` file and add to the `production` section:
~~~ini
resources.log.syslog.writerName = "Syslog"
resources.log.syslog.writerParams.application = "MyApp"
resources.log.syslog.writerParams.facility = 8
resources.log.syslog.filterName = 'Priority'
resources.log.syslog.filterParams.priority = 3
~~~
The priority filter is set to `ZEND_LOG::ERR`, that means only error messages and higher (critical, alert, emergency) will be logged.

To change the priority for the _development_ environment to `ZEND_LOG::DEBUG` add the line in the `development : production` section:
~~~ini
resources.log.syslog.filterParams.priority = 7
~~~

Additionaly, it is also recommended to differentiate the exception handling behaviour of the _development_ and _production_ environment. You can do this by editing the `application/configs/application.ini`. In the `production` section add the following lines:
~~~ini
resources.frontController.throwExceptions = 0
resources.frontController.params.useDefaultControllerAlways = 1
~~~
In the `development : production` section add the line:
~~~ini
resources.frontController.throwExceptions = 1
~~~

Finally, to use the logger in a controller you should register the logger. To do this, edit the `application/Bootstrap.php` file and add the following method to the `Bootstrap` class:
~~~php
protected function _initLog()
{
	if($this->hasPluginResource('log')){
		$logResource = $this->getPluginResource('log');
		$logger = $logResource->getLog();
        Zend_Registry::set('logger', $logger);			
	}
}
~~~

##7. Auto-magically determine the environment and set the configuration

At the startup of the application the environment has to be chosen automatically. This can be done by editing your `public/index.php` file to look like:
~~~php
<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

$environment = 'development';
// Auto-Magically Determine the Environment
if (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['DEP_NAME'])) {
    $parts = explode('/', $_SERVER['DEP_NAME']);
    $environment = $parts[count($parts)-1];
    switch ($environment) {
        case 'testing':
            break;
        case 'staging':
            break;
        case 'default':
        case 'production':
            $environment = 'production';
            break;
        default:
            $environment = 'development';
    }
}

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', $environment);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';
...
~~~

In the previously listed code there is a mapping of the deployment's name to the predefined environments. If you have deployments named _testing_ or _staging_ you can leave as it is; deployments named _default_ or _production_ are mapped to the _production_ environment and all the other deployments are mapped to the _development_ environment.

##8. Create a Check Application

Now you are going to create an application that will test the changes you made.

You should now create a layout:

~~~bash
$ ../bin/zf.sh enable layout
~~~
This command creates the necessary files and settings.

Now edit the `application/Bootstrap.php` file and add the following method to the `Bootstrap` class:
~~~php
    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
~~~

To initialize the view resource, add the following line to your `application/configs/application.ini` file, to the `production` section:
~~~ini
resources.view[] =
~~~

Change the `application/layouts/scripts/layout.phtml` file to get the layout working:
~~~html
<!-- application/layouts/scripts/layout.phtml -->
<?php echo $this->doctype() ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
  <title>Zend Framework 1 CloudControl Check Application</title>
</head>
<body>
<div id="header" style="background-color: #EEEEEE; height: 30px;">
    <div id="header-logo" style="float: left">
        <b>ZF CloudControl Check Application</b>
    </div>
    <div id="header-navigation" style="float: right">
        <a href="<?php echo $this->url(array('controller'=>'check'), 'default', true) ?>">Checkpage</a>
    </div>
</div>
<?php echo $this->layout()->content ?>
</body>
</html>
~~~

Next, create a controller:
~~~bash
$ ../bin/zf.sh create controller Check
~~~
This command creates needed files.

Change the controller class in `application/controllers/CheckController.php` file:
~~~php
<?php

class CheckController extends Zend_Controller_Action {

    public function indexAction(){
        Zend_Session::start();
        $ns = new Zend_Session_Namespace('CloudControl');
        $visits = (int)$ns->visits;
        $ns->visits++;
        $this->view->visits = $visits;

        $request = $this->getRequest();
        $form = new Application_Form_Logentry();
        $this->view->flashMessenger = $this->_helper->flashMessenger;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $logger = Zend_Registry::get('logger');
                $logger->log($form->getValue('logentry'), Zend_Log::INFO);
                $logger->log($form->getValue('logentry'), Zend_Log::ERR);
                $this->view->flashMessenger->addMessage(sprintf("Your logentry was sent. Check in your cloudControl console by 'cctrlapp %s log error'", $_SERVER['DEP_NAME']));
                return $this->_helper->redirector('');
            }
        }
        $this->view->form = $form;
    }
}
~~~

Next, change the `application/views/scripts/check/index.phtml` file:
~~~html
<br /><br />
<div id="view-content">
    <p>View script for controller <b>Check</b> and script/action name <b>index</b></p>
    <div>
        <h2>Session Test</h2>
        Visits: <?php echo $this->visits; ?>
    </div>
    <div>
        <h2>Logging Test</h2>
        Send a logentry to your cloudControl log
        <?php
        $this->form->setAction($this->url());
        echo $this->form;

        if($this->flashMessenger->hasMessages()): ?>
            <div>
                <?php foreach($this->flashMessenger->getMessages() as $message): ?>
                <?php echo $message; ?><br/>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
~~~

As can be seen in the controller's code, you need a form class.
You can generate this by:
~~~bash
$ ../bin/zf.sh create form Logentry
~~~

Edit the generated `application/forms/Logentry.php` file and replace the code with:
~~~php
<?php

class Application_Form_Logentry extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        $this->addElement('text', 'logentry', array(
            'label'      => 'Logentry:',
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Push your logentry to your cloudControl log',
        ));
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}
~~~

Finally, to get your site working on cloudControl you should edit the default `public/.htaccess` file and replace:
~~~ini
RewriteRule ^.*$ index.php [NC,L]
~~~
with
~~~ini
RewriteRule !.(js|ico|txt|gif|jpg|png|css|htc|swf|htm)$ index.php [NC,L]
~~~
Now the stylesheets, images and javascript files can also be found.

Now you can add all the files to the repository, commit, push and deploy your development deployment:
~~~bash
# add a favicon.ico (to avoid a mess of log messages)
$ touch public/favicon.ico
$ git add .
$ git commit -am "Store logs and sessions in db and auto-detect environment"
$ cctrlapp APP_NAME/development push
$ cctrlapp APP_NAME/development deploy
~~~

###8.1 Review _development_ deployment

Visit the new website _http://development.APP_NAME.cloudcontrolled.com/check/_. Check if:

* At every reload the session counter should increase.
* Sent log line is present in cloudControl log. It should look similar to:

~~~bash
$ cctrlapp APP_NAME/development log error
[Tue Feb 12 16:13:17 2013] info This is my custom log message
[Tue Feb 12 16:13:17 2013] error This is my custom log message
~~~
On the _production_ deployment you should only see the info log entry.

If you address a non-existing check action _http://development.APP_NAME.cloudcontrolled.com/check/aaaa_ you should get a fatal error on the website and in the cloudControl log.

###8.2 Merge to _production_ deployment

If everything until now was ok, you can merge the development branch to the master branch, push and deploy to the _production_ deployment.
~~~bash
$ git checkout master
$ git merge development
# git commits the merge automatically, so you can push right now
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~
On the _production_ branch you can check the environment change by watching the cloudControl log and addressing a wrong controller action  _http://APP_NAME.cloudcontrolled.com/check/aaaa_. The fatal error should not appear on the website nor in the cloudControl log (there will be a notice on the website though).
