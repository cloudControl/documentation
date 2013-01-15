#Deploying Symfony 2.1.6 to cloudControl

If you're looking for a feature-rich, open source PHP Framework that also has a strong community, a large variety of plugins and Add-ons and a strong history of active development, you can't go past [Symfony 2](http://symfony.com/). It comes with a variety of features to speed up your application development, some of those are:

 * Database engine independent
 * Highly configurable
 * Enterprise ready
 * Easy to extend
 * Built-in internationalisation
 * Factories, plug-ins, and mixins
 * Built-in unit and functional testing framework

In this tutorial, we're going to take you through the process of deploying a Symfony v2.1.6 app to the [cloudControl platform](http://www.cloudcontrol.com).

##Prerequisites

 * [cloudControl user account](https://www.cloudcontrol.com/for-developers)
 * [Git client](http://git-scm.com/)
 * MySQL command line client

## Install Symfony

Symfony uses [Composer](https://getcomposer.org/) to manage dependencies.
Using composer is also the recommended way to create new projects.
If you don't have it yet, [download Composer](http://getcomposer.org/) or just run the following command:
~~~bash
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar create-project symfony/framework-standard-edition <project/path> 2.1.6
~~~

Composer will install Symfony framework and all the dependencies under the `<project/path>` directory. It is recommended to choose a path beneath your webserver's documents root.

### Checking your System Configuration

Before you start coding, make sure that the local system is properly configured for Symfony.
Go to the install directory and execute the check.php script from the command line:
~~~bash
$ cd <project/path>
$ php app/check.php
~~~

Access the config.php script from a browser:
http://localhost/<path/to/symfony>/web/config.php

If you get any warnings or recommendations, fix them before moving on.

## Modifying the code

As was mentioned before, a few changes need to be made to the default Symfony configuration:

### Storing sessions in a database

Symfony by default stores its session files on the file system. However, this approach isn't recommended on the cloudControl platform.
Additionally, storing the files in a multi-server environment can lead to problems that are hard to debug. That's why we're going to store sessions in the database.

### Logging to syslog

Symfony by default stores the logs to the file system. The cloudControl platform provides syslog to write the logs to a database and is recommended to configure Symfony to log to syslog.

### Auto-magically determine the environment and set the configuration

It is likely that each environment will have different configuration settings. For that reason, we need to be able to differentiate between all the environments.
We'll have one file (`web/app.php`) where the environment will be detected and the rest of the configuration will be imported accordingly to detected environment.

## Put the code under git

Now it's time to start making these changes and then deploy the application.
You'll begin by putting it under git version control. Run the following commands:
~~~bash
$ git init .
$ git add -A
$ git commit -m "Initial commit"
~~~

Now that the code is under version control, you're going to create a testing branch as well, so that we have testing separated from production. Run the following command:
~~~bash
$ git checkout -b testing
~~~

We use two different deployments on the platform.
The advantage of this approach is that you can have a working production deployment with the tested and stable code while also having one unstable deployment for the development process.

To run Symfony 2.x on the platform, it's highly recommended to deploy your app on [cloudControl's pinky stack](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#stacks). The pinky stack supports PHP 5.4 and offers many improvements over the default (luigi) stack.

Choose a unique name (from now on called APP_NAME) for your application and create it on the cloudControl platform:
~~~bash
# create the application
$ cctrlapp APP_NAME create php

# deploy the default branch
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy --stack=pinky

# deploy the testing branch
$ cctrlapp APP_NAME/testing push
$ cctrlapp APP_NAME/testing deploy --stack=pinky
~~~

From now until everything is working as intended, you'll be working on the testing branch. Only then should the testing branch be merged to master.

### Symfony auto-detected

When you do the push, you'll see the output similar to the following:
~~~bash
$ cctrlapp APP_NAME/testing push
    Counting objects: 85, done.
    Delta compression using up to 8 threads.
    Compressing objects: 100% (73/73), done.
    Writing objects: 100% (85/85), 61.00 KiB, done.
    Total 85 (delta 3), reused 0 (delta 0)

    -----> Receiving push
    All settings correct for using Composer
    Downloading...

    Composer successfully installed to: /srv/tmp/builddir/www/composer.phar
    Use it: php /srv/tmp/builddir/www/composer.phar
           Loading composer repositories with package information
           Installing dependencies from lock file
             - Installing doctrine/common (2.3.0)
               Downloading: 100%
    ...
           Installing assets for Sensio\Bundle\DistributionBundle into web/bundles/sensiodistribution
    -----> Symfony 2.x detected
    -----> Building image
    -----> Uploading image (6.0M)

    To ssh://APP_NAME@cloudcontrolled.com/repository.git
     * [new branch]      master -> master
~~~

Note the following line:
~~~
    -----> Symfony 2.x detected
~~~

cloudControl detects the Symfony framework automatically, installs all the dependencies specified in `composer.json` file and sets the document root to `web`.

## Initialising MySQLs Add-on

Now  we need to configure a MySQL database. The database is needed to store the session information.
We are going to use the [MySQLs Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs) for this tutorial.

To initialise the MySQLs Add-on, run the following commands:
~~~bash
# Add the mysqls.free addon for both deployments
$ cctrlapp APP_NAME/default addon.add mysqls.free
$ cctrlapp APP_NAME/testing addon.add mysqls.free
~~~

## Environment configuration

### Read database connection parameter from credentials file

Create a file `app/config/credentials.php` with the following content:
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
    $database_port = $creds["MYSQLS"]["MYSQLS_PORT"];
    $database_name = $creds["MYSQLS"]["MYSQLS_DATABASE"];
    $database_user = $creds["MYSQLS"]["MYSQLS_USERNAME"];
    $database_password = $creds["MYSQLS"]["MYSQLS_PASSWORD"];
} else {
    $database_host = 'localhost';
    $database_port = '<local_symfony_database_port>';
    $database_name = '<local_symfony_database_name>';
    $database_user = '<local_symfony_database_user>';
    $database_password = '<local_symfony_database_password>';
}
$container->setParameter('database_driver', 'pdo_mysql');
$container->setParameter('database_host', $database_host);
$container->setParameter('database_port', $database_port);
$container->setParameter('database_name', $database_name);
$container->setParameter('database_user', $database_user);
$container->setParameter('database_password', $database_password);
~~~

This file reads the credentials file and sets the database connection parameter used in the `app/config/config.yml` file.
So you have to import the `credentials.php` into `config.yml`. Add the line "`- { resource: credentials.php }`" to the imports section, so that is looks like this:
~~~
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: credentials.php }
~~~

The database connection parameters will be used in the `doctrine.dbal` section.

### Store sessions in the database

Open `apps/config/config.yml`. In that file, we need to change the session configuration. Add the following configuration:
~~~
framework:
    ...
    session:
        handler_id:     session.handler.pdo

parameters:
    pdo.options: []
    pdo.db_options:
        db_table:    session
        db_id_col:   session_id
        db_data_col: session_value
        db_time_col: session_time

services:
    session.handler.pdo:
        class:    Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler
        arguments:
            - @doctrine.dbal.default.wrapped_connection
            - %pdo.db_options%

    doctrine.dbal.default.wrapped_connection:
        factory_service: doctrine.dbal.default_connection
        factory_method: getWrappedConnection
        class: PDO
~~~

The session data will be stored by the `PdoSessionHandler` with a preconfigured doctrine.dbal connection.

Next you should create the session table.
~~~bash
# Retrieve the MySQL credentials
$ cctrlapp APP_NAME/default addon mysqls.free
$ cctrlapp APP_NAME/testing addon mysqls.free
~~~

The output of each of the commands will look like this:
~~~
    Addon                    : mysqls.free

     Settings
       MYSQLS_DATABASE          : <database_username>
       MYSQLS_PASSWORD          : <database_password>
       MYSQLS_PORT              : <database_port>
       MYSQLS_HOSTNAME          : <database_hostname>
       MYSQLS_USERNAME          : <database_name>
~~~
Using these credentials, connect to both databases and create the `session` table in both of them:
~~~bash
$ mysql --user=<database_username> --port=<database_port> --password=<database_password> --host=<database_host> <database_name>
~~~
~~~sql
CREATE TABLE `session` (
    `session_id` varchar(255) NOT NULL,
    `session_value` text NOT NULL,
    `session_time` int(11) NOT NULL,
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
~~~

### Auto-magically determine the environment and set the configuration

At the startup of the application, the environment has to be chosen automatically. This can be done by editing your `web/app.php` file and replacing the line:
~~~php
$kernel = new AppKernel('prod', false);
~~~
with the following content:
~~~php
$environment = 'dev';
$debug = true;
// Auto-Magically Determine the Environment
if (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['DEP_NAME'])) {
    $parts = explode('/', $_SERVER['DEP_NAME']);
    $environment = $parts[count($parts)-1];
    switch ($environment) {
        case 'test':
            break;
        case 'default':
        case 'production':
            $environment = 'prod';
            $debug = false;
            break;
        default:
            $environment = 'dev';
            $debug = true;
    }
}
$kernel = new AppKernel($environment, $debug);
~~~

In the previously listed code there is a mapping of the deployment's name to the predefined environments. If you have an environment named '_test_' you can leave as it is; deployments named '_default_' or '_production_' are mapped to the production environment and all the other deployments are mapped to the development environment.

### Logging to syslog

In Symfony 2.x, by default the logging is done via the `monolog` logging library. Open the `app/config/config_<environment>.yml` according to your environment ('_prod_', '_dev_' and '_test_') and add replace monolog handler configuration with the following.

For the `config_dev.yml` we recommend the following configuration:
~~~
monolog:
    handlers:
        main:
            type: syslog
            level: debug
        firephp:
            type:  firephp
            level: info
~~~

For the `config_prod.yml` we recommend the following configuration:
~~~
monolog:
    handlers:
        main:
            type: syslog
            level: error
~~~


In the production environment it is recommended to increase the logging level to '_error_'.

Hint: don't use the type `finger_crossed` in combination with the syslog logging. All buffered logging mechanism will not work as expected.

## Build a Check Issues Application

In this section you will create a Symfony bundle to test that the sessions and logging are functioning properly while fetching the correct environment.
You should first remove the Acme example.
Delete the following entries from `app/config/routing_dev.yml`:
~~~
_welcome:
    pattern:  /
    defaults: { _controller: AcmeDemoBundle:Welcome:index }

_demo_secured:
    resource: "@AcmeDemoBundle/Controller/SecuredController.php"
    type:     annotation

_demo:
    resource: "@AcmeDemoBundle/Controller/DemoController.php"
    type:     annotation
    prefix:   /demo
~~~

Remove the following line from the `app/AppKernel.php` file:
~~~php
$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
~~~

Then remove Acme in `web/bundles` and `src`.
~~~bash
$ rm -r web/bundles/ src/Acme/
~~~
Finally run: `php app/console cache:clear`.

### Generating a new bundle

Run in console following commands:
~~~bash
$ php app/console generate:bundle --namespace=CloudControl/Demo/CheckBundle
~~~

Symfony generates all necessary files and entries in an interactive mode. Choose the default values for everything by pressing enter.

The new bundle is already registerd in `app/AppKernel.php` with the following line:

### Implementing the Check

For the logging check you need to create a file `src/CloudControl/Demo/CheckBundle/LogEntry.php` with the following content:
~~~php
<?php
namespace CloudControl\Demo\CheckBundle;

class LogEntry {
    protected $logentry;
        protected $date;

        public function getLogentry() {
            return $this->logentry;
        }
        public function setLogentry($logentry) {
            $this->logentry = $logentry;
        }
        public function getDate() {
            return $this->date;
        }
        public function setDate(\DateTime $date = null) {
            $this->date = $date;
        }
}
~~~

For the check you have to modify the `src/CloudControl/Demo/CheckBundle/Controller/DefaultController.php`:
~~~php
<?php
namespace CloudControl\Demo\CheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use CloudControl\Demo\CheckBundle\LogEntry;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
    /**
        * @Route("/")
        * @Template()
        */
    public function indexAction(Request $request) {
            $session = new Session();
            $session->start();
            $visits = (int)$session->get('visits');
            $session->set('visits', ++$visits);

            // create a LogEntry and give it some dummy data for this example
            $logEntry = new LogEntry();
            $logEntry->setLogEntry('Write a log entry');
            $logEntry->setDate(new \DateTime('now'));

            $form = $this->createFormBuilder($logEntry)
            ->add('logentry', 'text')
            ->getForm();

            $logSuccessMessage = '';
            if ($request->isMethod('POST')) {
                $form->bind($request);
                if ($form->isValid()) {
                    $logger = $this->get('logger');
                    $logger->info(sprintf("date: %s, message: %s", $logEntry->getDate()->format('Y-m-d H:i:s'), $logEntry->getLogentry()));
                    $logSuccessMessage = sprintf('Log message was sent, check your log by entering in console: "cctrlapp %s log error"', $_SERVER['DEP_NAME']);
                }
            }

            return array(
                'visits' => $visits,
                'environment' => $this->container->get('kernel')->getEnvironment(),
                'form' => $form->createView(),
                'logSuccessMessage' => $logSuccessMessage);
    }
}
~~~

Finally you need to replace the template `src/CloudControl/Demo/CheckBundle/Resources/views/Default/index.html.twig`:
~~~twig
{% extends '::base.html.twig' %}
{% block body %}
    <h2>Session Check</h2>
    This is the visit count: {{ visits }} <br/>
    <h2>Environment Check</h2>
    And we are in the "{{ environment }}" environment.
    <h2>Logmessage Check</h2>
    <form action="{{ path('cloudcontrol_demo_check_default_index') }}" method="post" {{ form_enctype(form) }}>
    {{ form_widget(form) }}
    <input type="submit" />
    </form>
    {% if logSuccessMessage %}
        <h3>{{ logSuccessMessage }}</h3>
    {% endif %}
{% endblock %}
~~~

## Commit, push and deploy

Stage all the files in Git and commit them with a suitable commit message.
~~~bash
$ git add -A
$ git commit -m "Migrate to cloudControl"
~~~
Next, push and deploy the new version of the testing deployment.
~~~bash
$ cctrlapp APP_NAME/testing push
$ cctrlapp APP_NAME/testing deploy
~~~

## Review the Deployment

Now visit [testing.APP_NAME.cloudcontrolled.com](http://testing.APP_NAME.cloudcontrolled.com).
By reloading, the simple session based visitor counter should increase.
The environment should be displayed as 'dev'.
When writing a log message and submitting the form, the log entry should appear in the error log:
~~~
$ cctrlapp APP_NAME/default log error
    ...
    [Mon Jan 21 10:02:42 2013] info app.INFO: date: 2013-01-21 11:02:42, message: This is my log info message [] []\n
~~~

When all the checks are completed successfully, merge the changes to the master branch and deploy it:
~~~bash
$ git checkout master
$ git merge testing
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy
~~~

You can check this deployment at [APP_NAME.cloudcontrolled.com](http://APP_NAME.cloudcontrolled.com). The session test shouldn't differ from the testing deployment, but the environment name should be 'prod' and the loglines will not appear in cctrlapp log error (if you defined '_error_' as log level in `/app/config/config_prod.yml`).
