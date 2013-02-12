#Deploying Zend Framework 2 to cloudControl

If you're looking for a feature-rich, flexible and capable PHP Framework for your projects, you can't go past [Zend Framework](http://framework.zend.com/). Now at [version 2.0.6](http://framework.zend.com/downloads/latest#ZF2) it comes with a variety of features to speed up your application development, including:

 * Straightforward MVC approach
 * Large, thriving, community
 * Sub-library components for working with a range of services, including PayPal and Google
 * Easy-to-read documentation

In this tutorial, we're going to take you through the process of deploying a Zend Framework v2.0.6 app to [the cloudControl platform](http://www.cloudcontrol.com).

##Prerequisites

 * [cloudControl user account](https://www.cloudcontrol.com/for-developers)
 * [Git client](http://git-scm.com/)
 * MySQL client, such as [MySQL Workbench](http://dev.mysql.com/downloads/workbench/) or the command-line tools

##1. Install Zend Framework and create the project

To build an application, you'll start with the ZendSkeletonApplication available on [github](https://github.com/zendframework/ZendSkeletonApplication). Use Composer (http://getcomposer.org) to create a new project from scratch:

###1.1. Install Composer

Install composer if you don't have it already:
~~~bash
$ curl -s https://getcomposer.org/installer | php
~~~

###1.2. Create the project
~~~bash
$ php composer.phar create-project --repository-url="http://packages.zendframework.com" zendframework/skeleton-application <path/to/install>
$ cd <path/to/install>
~~~

Now you also have a compose in your project directory. To avoid error messages while pushing to cloudControl you should update the composer in project directory:
~~~bash
$ php composer.phar self-update
~~~

###1.3. Check the installation

Once you have the skeleton application installed, verify that it works. There are two ways:

#### Virtual host

Set up a virtual host to point to the `public/` directory of the project and you should be ready to go.

#### Your PHP version is 5.4 or higher - with the built-in server

Enter the `public/` directory, and start the web server. As an example, the following will start the server on port 8888; you would then go to [localhost:8888](http://localhost:8888/) to test the app:
~~~bash
$ cd public/
$ php -S localhost:8888
~~~

##2. Modifying the code

As we mentioned before, a few changes need to be made to the default application configuration and code to accommodate cloudControl deployment. These changes are as follows:

 * Store session in a database, not on the filesystem
 * Logging to syslog
 * Auto-magically determine the environment and set the configuration

###2.1 Sessions in the database

Zend by default stores it's session files on the file system. However, this approach isn't recommended on the cloudControl platform.
Additionally, storing the files in a multi-server environment can lead to problems that are hard to debug. That's why we're going to store sessions in the database.

Luckily, Zend Framework is written in a very straight-forward and configurable manner, and this isn't that hard to do. The community around the framework is very healthy and there are many options as well as the support available.

###2.2 Logging to syslog

Zend by default stores the logs to the file system. The cloudControl platform provides a syslog to write the logs to a database and is is recommended to configure Zend to log to syslog. You can read the log by:
~~~bash
$ cctrlapp APP_NAME/DEPLOYMENT_NAME log error
~~~

###2.3 Determine the environment and set the configuration

The environment handling in Zend Framework 2 is different then in Zend Framework 1. If you want to use different configurations for different environments you have to put in your repository only the file related to that environment.

The global configuration file for the _production_ environment would be `config/autoload/dist-production.global.php`. In your master branch, you have to add this configuration file for production and remove all the config files from other deployments. In a similar way you have to add the relevant file for the _development_ environment and remove all the other environment configuration files.

_Unfortunately you have to do this for every branch!_

The exact commands are listed later in the tutorial.

##3. Put the code under git

Now it's time to start making previously mentioned changes and afterwords to deploy the application. You'll begin by putting it under git version control system. Run the following commands:
~~~bash
$ git init .
$ git add -A
$ git commit -m "Initial commit"
~~~

##3.1 Create a CloudControl Application

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
        Installing dependencies
            - Installing zendframework/zendframework (2.0.6)
            Downloading: 100%

            Skipped installation of bin/classmap_generator.php for package zendframework/zendframework: name conflicts with an existing file
        zendframework/zendframework suggests installing doctrine/common (Doctrine\Common >=2.1 for annotation features)
        zendframework/zendframework suggests installing pecl-weakref (Implementation of weak references for Zend\Stdlib\CallbackHandler)
        zendframework/zendframework suggests installing zendframework/zendpdf (ZendPdf for creating PDF representations of barcodes)
        zendframework/zendframework suggests installing zendframework/zendservice-recaptcha (ZendService\ReCaptcha for rendering ReCaptchas in Zend\Captcha and/or Zend\Form)
        Writing lock file
        Generating autoload files
    -----> Zend 2.x Framework detected
    -----> Building image
    -----> Uploading image (2.5M)

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

In Zend Framework you have to configure your database adapter and the database connection parameters in a configuration file.
Create a file `config/autoload/database.global.php`:
~~~php
<?php
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
    $database_name = '<local_zf2_database_name>';
    $database_user = '<local_zf2_database_user>';
    $database_password = '<local_zf2_database_password>';
}

return array(
    'session_db' => array(
        'driver'         => 'Pdo',
        'dsn'            => sprintf('mysql:dbname=%s;host=%s', $database_name, $database_host),
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => $database_user,
        'password' => $database_password,
    ),
);
~~~

###5.2. Configure session handler

Now you need to configure the session handler to write the session data to the database. Edit the `module/Application/Module.php` file and add to the end of the `Module::onBootstrap` method:
~~~php
    public function onBootstrap(MvcEvent $e)
    {
        ...
        // Configure session using database
        $config = $e->getApplication()->getServiceManager()->get('config');
        $dbAdapter = new \Zend\Db\Adapter\Adapter($config['session_db']);
        $sessionOptions = new \Zend\Session\SaveHandler\DbTableGatewayOptions();
        $sessionTableGateway = new \Zend\Db\TableGateway\TableGateway('session', $dbAdapter);
        $saveHandler = new \Zend\Session\SaveHandler\DbTableGateway($sessionTableGateway, $sessionOptions);
        $sessionManager = new \Zend\Session\SessionManager(NULL, NULL, $saveHandler);
        $sessionManager->start();
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
    `id` CHAR(32) NOT NULL DEFAULT '',
    `name` VARCHAR(255) NOT NULL,
    `modified` INT(11) NULL DEFAULT NULL,
    `lifetime` INT(11) NULL DEFAULT NULL,
    `data` TEXT NULL,
    PRIMARY KEY (`id`)
) COLLATE='utf8_general_ci' DEFAULT CHARSET=utf8;
~~~

You should also do this for the `APP_NAME/default` deployment.

##6. Logging to syslog

In this example you are using only two environments: _development_ and _production_. Here we show how to differentiate between them.
For both environments you should use the syslog writer, but with different error levels.

For the _development_ environment create a file `config/autoload/dist-development.global.php` with:
~~~php
<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Log\Logger' => function(){
                $logger = new Zend\Log\Logger;
                $writer = new Zend\Log\Writer\Syslog();
                $logger->addWriter($writer);
                return $logger;
            },
        ),
    ),
);
~~~

For the _production_ environment create a file `config/autoload/dist-production.global.php`:
~~~php
<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Log\Logger' => function(){
                $logger = new Zend\Log\Logger;
                $writer = new Zend\Log\Writer\Syslog();
                $writer->addFilter(Zend\Log\Logger::ERR);
                $logger->addWriter($writer);
                return $logger;
            },
        ),
    ),
);
~~~
In this environment you get only the messages of `error` or higher level.

Unfortunately, the properly configured log writer isn't used to log exceptions by default. So you have to edit `module/Application/Module.php`. Add to the `Module::onBootstrap` method (beneath the session relevant code):
~~~php
        ...
        // Configure log exceptions to syslog
        $serviceManager = $e->getApplication()->getServiceManager();
        $eventManager->getSharedManager()->attach('Zend\Mvc\Application', array(MvcEvent::EVENT_DISPATCH, MvcEvent::EVENT_DISPATCH_ERROR),
            function($e) use ($serviceManager) {
                if ($e->getParam('exception')){
                    $serviceManager->get('Zend\Log\Logger')->err($e->getParam('exception'));
                }
            }
        );
        register_shutdown_function(function () use ($serviceManager) {
            if ($e = error_get_last()) {
                $logger = $serviceManager->get('Zend\Log\Logger');
                $logger->err($e['message'] . " in " . $e['file'] . ' line ' . $e['line']);
                $logger->__destruct();
            }
        });
~~~

##8. Create a Check Application

Now you are going to create an application that will test the changes you made. Zend Framework 2 currently don't have a skeleton generating mechanism, so you have to create the necessary files manually.

First create the controller `module/Application/src/Application/Controller/CheckController.php`:
~~~php
<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Application\Form\LogentryForm;
use Application\Model\Logentry;


class CheckController extends AbstractActionController
{
    public function indexAction()
    {
        $session = new SessionContainer('check_page');
        $visits = (int)$session->offsetGet('visits');
        $session->offsetSet('visits', ++$visits);

        $form = new LogentryForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $logentry = new Logentry();
            $form->setInputFilter($logentry->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $logentry->exchangeArray($form->getData());
                $logger = $this->getServiceLocator()->get('Zend\Log\Logger');
                $logger->info(sprintf("Logentry with INFO prio sent at %s, message: %s", $logentry->date->format("Y-m-d H:i:s"), $logentry->logentry));
                $logger->err(sprintf("Logentry with ERR prio sent at %s, message: %s", $logentry->date->format("Y-m-d H:i:s"), $logentry->logentry));
                return $this->redirect()->toRoute('check');
            }
        }
        return new ViewModel(array(
            'visits' => $visits,
            'form' => $form
        ));
    }
}
~~~

In this example we use a form and a model class. Create `module/Application/src/Application/Form/LogentryForm.php`:
~~~php
<?php

namespace Application\Form;

use Zend\Form\Form;

class LogentryForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('logentry');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'logentry',
            'attributes' => array('type' => 'text'),
            'options' => array('label' => 'Logentry')
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}
~~~

Create `module/Application/src/Application/Model/Logentry.php`:
~~~php
<?php

namespace Application\Model;

use \DateTime;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Logentry implements InputFilterAwareInterface {
    public $logentry;
    public $date;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->logentry = (isset($data['logentry'])) ? $data['logentry'] : null;
        $this->date = new DateTime('now');
    }
    public function setInputFilter(InputFilterInterface $inputFilter) {}
    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $inputFilter->add($factory->createInput(array(
                'name'     => 'logentry',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
~~~

You also need a view. Create a file `module/Application/view/application/check/index.phtml` with:
~~~html
<div class="hero-unit">
    <h1><?php echo sprintf($this->translate('Welcome to %sCheck Page%s'), '<span class="zf-green">', '</span>') ?></h1>
</div>

<div>
    <div class="container">
        <h2><?php echo $this->translate('Session Test') ?></h2>
        <p>Visitor count: <?php echo $this->visits;  ?></p>
    </div>
    <div class="container">
        <h2><?php echo $this->translate('Logging Test') ?></h2>
        <p>
        <?php
        $form = $this->form;
        $form->prepare();

        echo $this->form()->openTag($form);
        echo $this->formRow($form->get('logentry'));
        echo $this->formSubmit($form->get('submit'));
        echo $this->form()->closeTag();
        ?>
        </p>
   </div>
   <div class="container">
       <h2><?php echo $this->translate('Environment Test') ?></h2>
       <p></p>
   </div>
</div>
~~~

Next, you need to register controller and set the route to it. For this you have to edit the `module/Application/config/module.config.php`.
Add the controller to the controllers' section, so that it looks like:
~~~php
...
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Check' => 'Application\Controller\CheckController',
        ),
    ),
...
~~~

To set the route, edit the same file and add to the routes section the `check`-route, so that it looks like:
~~~php
...
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
            ...
            ),
            'check' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/check',
                    'defaults' => array(
                        'controller'    => 'Application\Controller\Check',
                        'action'        => 'index',
                    ),
                )
            ),
...
~~~

It is possible to use another routes configuration (e.g. the magic ones). For the purpose of this tutorial, we decided to go with simpler approach.

Now you can add all the files to the repository, commit, push and deploy your development deployment:
~~~bash
# add a favicon.ico (to avoid a mess of log messages)
$ touch public/favicon.ico
$ git add .

# handle the environment:
$ git rm --cached config/autoload/dist-*.global.php
$ git add config/autoload/dist-development.global.php

$ git commit -am "Store logs and sessions in db and auto-detect environment"
$ cctrlapp APP_NAME/development push
$ cctrlapp APP_NAME/development deploy
~~~

###8.1 Review _development_ deployment

Visit the new website at [development.APP_NAME.cloudcontrolled.com/check](http://development.APP_NAME.cloudcontrolled.com/check/). Check the list to test if all is working as it should.

The session counter should increase at every reload.

Send a log line and watch the cloudControl log. It should be similar to:
~~~bash
$ cctrlapp APP_NAME/development log error
[Tue Jan 29 18:17:30 2013] info Logentry with INFO prio sent at 2013-01-29 19:17:30, message: This is my custom logmessage
[Tue Jan 29 18:17:30 2013] warn Logentry with WARN prio sent at 2013-01-29 19:17:30, message: This is my custom logmessage
~~~

###8.2 Merge to _production_ deployment

If everything until now was ok, you can merge the development branch to the master branch, push and deploy to the _production_ deployment.
~~~bash
$ git checkout master
$ git merge development

# handle the environment:
$ git rm --cached config/autoload/dist-*.global.php
$ git add config/autoload/dist-production.global.php

$ git commit -am "Add production config file"
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~

On the production branch you can check the environment change by watching the cloudControl log and sending a log message with [APP_NAME.cloudcontrolled.com/check](http://APP_NAME.cloudcontrolled.com/check). The cloudControl log should contain only the error messages.

