#Creating a Facebook App with cloudControl

Have you wanted to develop a Facebook application, but you've never been quite sure how to do it? Maybe you've watched or played some games, such as [FarmVille](https://apps.facebook.com/onthefarm/), [Diamond Dash](https://www.facebook.com/DiamondDashGame) and [CityVille](https://apps.facebook.com/cityville/). 

Maybe you've used apps such as [Graffiti](https://www.facebook.com/graffitiwall) and [Causes](https://apps.facebook.com/causes/). Maybe, after using them, you thought - HEY, I could write an app just as good. No! I could write one **much better**!

Sound like you? Perhaps you're just interested in writing one for a school, university or personal pet project. Well today I want to show you how to do it, quickly, simply, effectively. 

I'm going to show you how to write one, from start to finish, using the cloudControl platform. By the time we're finished, in just under 40 minutes, you'll know the following:

 1. What a Facebook application is
 2. How to create and configure it - on the Facebook side
 3. How to create an application locally which you can deploy to cloudControl
 4. How to link the two together, so the user can accept and use it
 
This application assumes no prior knowledge of creating Facebook applications and only a limited amount of knowledge of creating HTML websites and PHP-based applications.

##Prerequisites

 * [cloudControl user account](https://www.cloudcontrol.com/for-developers)
 * [Git client](http://git-scm.com/)
 * MySQL client, such as [MySQL Workbench](http://dev.mysql.com/downloads/workbench/) or the command-line tools

##What Is a Facebook Application

![Facebook application composition](images/facebook-application-composition.png "Facebook application composition")

Looking at the image above you see a simplistic representation of how a Facebook application works. Said succinctly, a Facebook application is one hosted somewhere outside of Facebook's servers, in our case, with cloudControl, but which is then accessed through Facebook, via an iframe.

This has a series of advantages, the prime one being we can make use of both Facebook's resources and our own. Secondly we can develop the application, nearly completely independently of Facebook, locally; designing it, building it and testing it. 

Then, when we're ready, link the two together so more than a billion people are able to access it, like it, talk about it and encourage their friends to use it - *pretty impressive stuff*. 

##1. Configure the Application on Facebook

Ok, the first thing you need to do is to create the application, inside your Facebook account. So if you're not already there, navigate over to [https://developers.facebook.com/apps](https://developers.facebook.com/apps) and login. 

After you've done this, you'll see a page similar to the one in the image below. Click the **Create New App** button in the top right-hand corner. 

![Facebook application composition](images/facebook-create-app.png "Facebook application composition")

This will display a modal popup as in the image below, allowing use to enter the application name and namespace. For the purposes of this example, I've configure the options as:

 - **App Name**: "Hello World"
 - **App Namespace**: cloudcontrolhw 
 - **Web Hosting**: leave it unchecked as we're deploying to cloudControl

![Facebook application composition](images/facebook-create-app-step-2.png "Facebook application composition")

Once you're happy with the information you've specified, click **Continue**. You'll then be asked to pass a security check. So, enter the two words in the prompt into the input box below. 

If you're not able to read one or both of the words, then click "**Try different words or an audio [CAPTCHA](http://www.captcha.net/)**". After you've entered the two words, click **Continue** again. 

![Facebook application composition](images/facebook-create-app-step-3.png "Facebook application composition")

###The Application Details

Once you've done this, you'll see a window like the following, where you'll be able to enter the details of the application. You'll see at the top of the window, in **Basic Info**, the details you've filled already. Leave the remainder of the settings as they are. Then set:

 - **App Domains**: blank
 - **Sandbox Mode**: Enabled

![Facebook application composition](images/facebook-app-details-2.png "Facebook application composition")

In the section under basic info, you'll see: **Select how your app integrates with Facebook**. There are a number of options to choose from. But for the purposes of this example, we're going to be using: **App on Facebook**. 

Click the row in the list and you'll see the section expand allowing you to configure it. There will be a number of options available, including:

 - Canvas URL
 - Secure Canvas URL
 - Canas Width
 - Canvas Height
 
As this is a simple, introductory example, we're going to leave **Secure Canvas URL** blank and **Canas Width** and **Canvas Height** to *Fixed* and *Fluid* respectively. 

The secure canvas URL is where users visiting via a HTTPS connection will retrieve the content from. The canvas width and height options allow the output of your app to respond to the users browsers dimensions, proportionately, or to stay at a fixed width and height. 

Feel free to set them as suits you best. But bear in mind the constraints this may place on your final application.

![Facebook application composition](images/facebook-app-details-4.png "Facebook application composition")

Ok, with all of the options specified, then click **Save Changes** at the bottom to save and create our wonderfully, simple, new application. 

##2. Developing the Application

This is where Facebook really makes it easy for us, as developers, to create applications for their platform. Effectively, our application can be whatever we want it to be, so long as we don't breach [the platform policies](https://developers.facebook.com/policy/).

For the purposes of this tutorial, we're going to create a simple application where the user can input their cloudControl username and password and retrieve the list of applications which they have deployed.

Now, is this as exciting as *FarmVille* or *Texas Hold 'em*? Likely not. But it will show you how to write an application, using external resources and dependencies, which can be *used through Facebook*. Once you've worked through this example, I'm sure you'll start seeing a load of ideas for applications you can build.

###Application Dependencies

As this project has a number of dependencies, we're going to [use Composer](http://getcomposer.org/) to manage them; that way we know they'll be there when we need them. If you've not used composer before, that's not a problem. 

Have a read of the [getting started page](http://getcomposer.org/doc/00-intro.md), which provides you with a series of options for installing it, whether you're on Linux, Mac or Windows. 

With Composer available, create a file called ``composer.json`` in the root of your project directory which looks like the following:

    {
        "require": {
            "doctrine/dbal": "2.2.*",
            "facebook/php-sdk": "dev-master",
            "silex/silex": "1.0.*@dev",
            "symfony/form": "2.1.*",
            "symfony/twig-bridge": "2.1.*",
            "symfony/translation": "2.1.*",
            "cloudcontrol/phpcclib": "dev-master"
        },
        "repositories": [
            {
                "type": "pear",
                "url": "http://pear.php.net"
            }
        ]
    }

What this does is two things:

 1. Specifies the required files for the project
 2. Makes the pear repository available to our project, in addition to the standard [packagist repositories](https://packagist.org/)
 
Going through the dependencies, we'll be using the following:

 - **Doctrine:** For simple database interaction
 - **The Facebook PHP SDK:** For Facebook interaction
 - **The cloudControl phpcclib:** For retrieving cloudControl account information
 - **Silex:** A micro framework based on the [Symfony 2](http://symfony.com/) and [Pimple](http://pimple.sensiolabs.org/) libraries
 - **Symfony:** One of the leading PHP frameworks, originally created by [Sensio Labs](http://sensiolabs.com/en)

The reason that we're using all of these dependencies is primarily to make our job as simple as possible and to avoid reinventing the wheel. These dependencies, collectively, will take care of most of the work of form generation and validation, application bootstrapping, and interacting with the services that we need.

###Static Files

####CSS

Inside the project root directory, create a new directory called ``public`` and inside that, create another directory called ``css``. In there, create a file called ``style.css`` and add the following style definitions:

    @CHARSET "UTF-8";
    
    body {
    	font-family: sans-serif;
    }
    #content {
    	margin-top: 20px;
    }
    #header {
    	width: 100%;
    	height: 51px;
    	position: relative;
    	text-align: center;
    	background-color: #efefef;
    	margin-bottom: 10px;
    	border-top: 1px solid #e4e4e4;
    	border-bottom: 1px solid #e4e4e4;
    }
    #logout {
        margin-top: 10px;
    }
    #error {
        border: 1px solid red;
        background-color: #FFFFEE;
        padding: 5px;
        margin: 10px 0px 10px 0px;
    }
    h1 {
    	font-size: 120%;
    	font-weight: bold;
    	margin: auto;
    	line-height: 51px;
    	vertical-align: middle;
    }
    table {
    	margin-left: auto;
    	margin-right: auto;
    	clear: both;
    }
    
    th,td {
    	border: 1px solid #e4e4e4;
    	margin: 2px;
    	padding: 5px;
    }
    th {
    	font-weight: bold;
    	text-align: center;
    }
    tfoot tr td {
    	font-weight: bold;
    }
    
    .logo {
    	width: 557px;
    	height: 227px;
    	margin-left: auto;
    	margin-right: auto;
    	margin-bottom: 26px;
    	position: relative;
    	-moz-box-shadow: 0 14px 10px -12px rgba(0, 0, 0, 0.7);
    	-webkit-box-shadow: 0 14px 10px -12px rgba(0, 0, 0, 0.7);
    	box-shadow: 0 14px 10px -12px rgba(0, 0, 0, 0.7);
    }

NB: if you're on a *NIX box, create the directory and file in one step with the following command:

	mkdir -p public/css && touch style.css;

####htaccess

To ensure all application requests are routed through a single bootstrap file, but all static files are served up normally, inside ``public`` create a new file called ``.htaccess`` and in it add the following directives:

	RewriteEngine On
	RewriteRule !\.(js|ico|txt|gif|jpg|png|css)$ index.php [NC,L]

###The Application Code

####index.php

Under public, create a new file, called ``index.php``. This is the bootstrap file for the application. It will contain the following code. 

Firstly, we include the core files for the application. ``/../vendor/autoload.php`` includes the autoloader generated by composer when we install all the dependencies, as listed in composer.json. We then include the CloudControl and Facebook controller classes, which we'll go through shortly.

	require_once __DIR__.'/../vendor/autoload.php';
	require_once __DIR__.'/../src/CloudControlController.php';
	require_once __DIR__.'/../src/FacebookController.php';
	
Next, we indicate the PHP namespaces which we'll be using in the application. If you're not familiar with namespaces in PHP, check out [the excellent documentation online](http://www.php.net/manual/en/language.namespaces.rationale.php), or the links in the further reading section at the end of the article.

	use Silex\Provider\FormServiceProvider;
	use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
	use Symfony\Component\HttpFoundation\Request;
	use Mwfacebookapp\CloudControlController;
	use Mwfacebookapp\FacebookController;
	
We then create a new Silex application object, which will handle most of the dispatch and routing for us in the application.

	$app = new Silex\Application();
	
Next, we register three new objects with the application. A form provider object, a translation object and a Twig object which allows us to use the, rather excellent, [Twig templating language](http://twig.sensiolabs.org/). When initialising Twig, we also specify the path to the Twig templates (which we'll see later on).

	$app->register(new FormServiceProvider());
	$app->register(new Silex\Provider\TranslationServiceProvider());
	$app->register(new Silex\Provider\TwigServiceProvider(), array(
	    'twig.path' => __DIR__.'/../views',
	));
	
Next, we retrieve the credentials via the CloudControl controller from the environment, which we'll be setting shortly, when we deploy the application.

	$creds = CloudControlController::getCredentials('MYSQLS');
	
Next, we register [a Doctrine object](http://www.doctrine-project.org/projects/dbal.html) with the application object so we can access the database, as needed, initialising it the database configuration settings which we just retrieved from the environment.

	$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	    'db.options' => array(
	        'driver'   => 'pdo_mysql',
	        'host'     => $creds["MYSQLS_HOSTNAME"],
	        'user'     => $creds["MYSQLS_USERNAME"],
	        'dbname'   => $creds["MYSQLS_DATABASE"],
	        'password' => $creds["MYSQLS_PASSWORD"],
	    ),
	));
	
Next up, we register [a session service provider object](http://silex.sensiolabs.org/doc/providers/session.html) with the application and configure it to use a database table as the storage handler. To do so, we refer to the database configuration we've just initialised.

	$app->register(new Silex\Provider\SessionServiceProvider());
	
	$app['session.db_options'] = array(
	    'db_table'      => 'session',
	    'db_id_col'     => 'session_id',
	    'db_data_col'   => 'session_value',
	    'db_time_col'   => 'session_time',
	);
	
	$app['session.storage.handler'] = $app->share(function () use ($app) {
	    return new PdoSessionHandler(
	        $app['db']->getWrappedConnection(),
	        $app['session.db_options'],
	        $app['session.storage.options']
	    );
	});
	
Here, we register [a Silex Application filter](http://silex.sensiolabs.org/api/Silex/Application.html#method_before) that ensures, before any route is served, we ensure that if the user is not logged in, they are redirected to the login page. We'll see the definition of these functions shortly.
	
	$app->before(function () {
	    $facebookController = new FacebookController();
	    if(!$facebookController->loggedIn()){
	        $response = $facebookController->login();
	        $response->send();
	        exit();
	    }
	});
	
Here we setup error handling for the application using a simple closure. We've set the output of any errors to be sent to ``php://stderr``. Once errors have been logged, the user is then shown the errors using the ``error.twig`` template.

	$app->error(function (\Exception $e, $code) use ($app) {
	    // write to cloudControl log
	    file_put_contents('php://stderr', $e->getMessage());
	    $parts = explode("\n", $e->getTraceAsString());
	    foreach ($parts as $line){
	        file_put_contents('php://stderr', $line);
	    }
	    return $app['twig']->render('error.twig', array(
	        'exception' => $e,
	        'code' => $code
	    ));
	});
	
When the application is live, we'll be able to see the error log output by running the following command from the terminal:

    cctrlapp APP_NAME/default log error

For further details about the cloudControl platform, have a look at [the extensive documentation online](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#logging).
	
Now we initialise a new cloudControl controller object, passing in the ``$app`` object which we've just setup. We'll cover the cloudControl controller shortly.
	
	$cloudControlController = new CloudControlController($app);
	
Finally we set up a basic routing table for the application. If the user is on the main page of the application, then we call ``appList`` to render a list of the users applications.

	$app->match('/', function () use ($cloudControlController) {
	    return $cloudControlController->appList();
	});

Here the user can see an application's details by using the url: ``'/app/{applicationName}'``.
	
	$app->get(
	    '/app/{applicationName}', 
	    function ($applicationName) use ($cloudControlController) {
	        return $cloudControlController->appDetails($applicationName);
	    }
	);

Here the user can see a deployment's details by using the url: ``'/deployment/{applicationName}/{deploymentName}'``.
	
	$app->get(
	    '/deployment/{applicationName}/{deploymentName}', 
	    function ($applicationName, $deploymentName) use ($cloudControlController) {
	        return $cloudControlController->deploymentDetails(
	            $applicationName, $deploymentName
	        );
	    }
	);
	
If the login url is requested, then the login method of the cloudControl Controller is called, rendering a login form for them.
	
	$app->match(
	    '/login', 
	    function (Request $request) use ($cloudControlController) {
	        return $cloudControlController->login($request);
	    }
	);
	
If the logout url is requested, then the logout method of the cloudControl Controller is called, ending the user's session.
	
	$app->get(
	    '/logout', 
	    function (Request $request) use ($cloudControlController) {
	        return $cloudControlController->logout();
    	}
    );
	
Finally, the application is bootstrapped.
	
	$app->run();

####CloudControlController.php

Under the ``src`` directory create a new file called ``CloudControlController.php`` an add the following code, which we'll work through together. 

Firstly we specify our application will use ``Mwfacebookapp`` as it's namespace and then bring in a number of key namespaces which we'll need.

    <?php
    namespace Mwfacebookapp;
    use CloudControl\API;
    use CloudControl\TokenRequiredError;
    use CloudControl\UnauthorizedError;
        
    class CloudControlController {
 
We then set two private member variables, ``app`` and ``creds``, which we've seen referenced earlier. 
        
        /**
         * @var \Api
         */
        private $api;   
        private static $creds = array();
        
In the constructor, we initialise the api variable and setup the api token.
        
        public function __construct($app) {
            $this->app = $app;
            $this->api = new API();
            $token = $this->app['session']->get('token');
            if ($token){
                $this->api->setToken($token);
            }
        }
        
The login function firstly initialised our login function with two fields, email and password. It checks if a POST request has been received. If so, binds the request information to the form and then runs the form validation process. If the form validates, the data supplied in the form is retrieved and we attempt to authenticate the user. 

If the user is authenticated, a session token is stored, aiding in better security, and the user is redirected to the root of the application. If no post request is received, then the login form is displayed.
        
        public function login($request){
            $form = $this->app['form.factory']->createBuilder('form')
                         ->add('email', 'text', array('label' => "Email"))
                         ->add(
                             'password', 
                             'password', 
                             array('label' => "Password")
                         )
                         ->getForm();
            if ('POST' == $request->getMethod()) {
                $form->bind($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $this->api->auth(
                        $data['email'],
                        $data['password']
                    );
                    $this->app['session']->set(
                        'token', 
                        $this->api->getToken()
                    );
                    return $this->app->redirect('/');
                }
            }
            return $this->app['twig']->render('login.twig', array(
                'form' => $form->createView()));
        }
        
The logout function is rather simple; The session token is reset, the api object is reinitialised to a blank state and the user is then redirected to the login route. 
        
        public function logout(){
            $this->app['session']->set('token', '');
            $this->api = new API();
            return $this->app->redirect('/login');
        }
        
Next comes the ``appList`` function. This function is responsible for retrieving the list of applications from cloudControl the user has and rendering them in the ``applist.twig`` template. You can see how first the list is retrieved, and then it is passed to the render method, which assigns it to the ``applicationList`` template variable. 

The method performs some sanity checks by catching both the ``Unauthorized`` and ``TokenRequired`` error exceptions. Just to clarify, the first is if an unauthorised user is attempting to access the list, the second if the session token is missing.
        
        public function appList(){
            try {
                $applicationList = $this->api->application_getList();
                return $this->app['twig']->render(
                    'applist.twig', array(
                        'applicationList' => $applicationList
                    )
                );
            } catch (UnauthorizedError $e) {
                return $this->app->redirect('/login');
            } catch (TokenRequiredError $e) {
                return $this->app->redirect('/login');
            }
        }
        
The next function retrieves the specific details of an application, based on its name. The name is passed to the ``application_getDetails`` method of the api object, which returns an application object. 

This object, along with the application name are then set as template variables, which will be used in the ``appDetails`` template. As with the appList method, the ``Unauthorized`` and ``TokenRequired`` error exceptions are handled.
        
        public function appDetails($applicationName){
            try {
                $application = $this->api->application_getDetails($applicationName);
                return $this->app['twig']->render(
                    'appDetails.twig', 
                    array(
                        'application' => $application, 
                        'applicationName' => $applicationName
                    )
                );
            } catch (UnauthorizedError $e) {
                return $this->app->redirect('/login');
            } catch (TokenRequiredError $e) {
                return $this->app->redirect('/login');
            }
        }
        
In the ``deploymentDetails`` function, the deployment details, i.e, live, testing, staging etc details are retrieved, so we can directly inspect the specifics of a deployment. The application and deployment names are passed to the ``deployment_getDetails`` method, which returns a deployment object with a set of information relating to that specific deployment.
        
        public function deploymentDetails($applicationName, $deploymentName){
            try {
                $deployment = $this->api->deployment_getDetails(
                    $applicationName, $deploymentName
                );
                return $this->app['twig']->render(
                    'deploymentDetails.twig', 
                    array(
                        'deployment' => $deployment
                    )
                );
            } catch (UnauthorizedError $e) {
                return $this->app->redirect('/login');
            } catch (TokenRequiredError $e) {
                return $this->app->redirect('/login');
            }
        }
        
The next and last method in the controller, ``getCredentials``, retrieves credentials information from the running environment. This method attempts to retrieve an environment variable ``CRED_FILE`` which is available to all cloudControl deployments. This is a JSON encoded set of configuration details relating to the deployment. 

If it's able to do so, it decodes it and assigns the information to the $creds static member variable. If it's not available, or could not be decoded, an exception is thrown stating the reason for the issue.
        
        public static function getCredentials($addonName){
            if (empty(self::$creds) && !empty($_SERVER['HTTP_HOST']) 
                && isset($_ENV['CRED_FILE'])) 
            {
                // read the credentials file
                $string = file_get_contents($_ENV['CRED_FILE'], false);
                if ($string == false) {
                    throw new \Exception('Could not read credentials file');
                }
                // the file contains a JSON string, decode it 
                // and return an associative array
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
                    throw new \Exception(sprintf(
                        'A json error occured while reading the credentials file: %s',
                        $json_errors[$error]
                    ));
                }
                self::$creds = $creds;
            }
            if (!array_key_exists($addonName, self::$creds)){
                throw new \Exception(sprintf(
                    'No credentials found for addon %s. Please make sure you have added the config addon.', 
                    $addonName
                ));
            }
            return self::$creds[$addonName];
        }
    }

####FacebookController.php
    
Ok, we're just about there, with just have one controller left to cover. Under the src directory, create a new file called ``FacebookController.php`` and append the following code as we step through it. 

As we did in the CloudControl Controller, we indicate the namespace we'll be using and ensure that we've required the Facebook PHP SDK and the CloudControl Controller.
    
    <?php
    namespace Mwfacebookapp;
    require_once __DIR__ . '/../vendor/facebook/php-sdk/src/facebook.php';
    require_once __DIR__.'/CloudControlController.php';
    
Then, we bring in the namespaces we'll be using in the controller.
    
    use Symfony\Component\HttpFoundation\Response;
    use Mwfacebookapp\CloudControlController;
    
    class FacebookController {
        /**
         * @var array
         */
        private $facebookConfig;
        
In the constructor, we call the CloudControlController ``getCredentials`` method, which we saw earlier, so we have access to both the ``APP_ID`` and ``SECRET_KEY`` variables; and we specify the url of our application. With this, we then create a new Facebook application object.
        
        public function __construct() {
            $creds = CloudControlController::getCredentials('CONFIG');
            $this->facebookConfig = array(
                'appUrl' => "http://apps.facebook.com/mwfacebookapp",
                'cookies' => 'true',
                'appId' => $creds['CONFIG_VARS']['APP_ID'],
                'secret' => $creds['CONFIG_VARS']['SECRET_KEY']
            );
            $this->facebook = new \Facebook($this->facebookConfig);
        }
        
Next we have the function ``loggedIn``. If no valid user object is returned from the ``getUser`` method of the Facebook object, then we return ``false``. If one is returned, then we attempt to retrieve the details of the users profile.
        
        public function loggedIn() {
            $user = $this->facebook->getUser();
            if ($user) {
                try {
                    $this->facebook->api('/me');
                    return true;
                } catch (\FacebookApiException $e) {
                    // 
                }
            }
            return false;
        }
        
Finally, we have the login method. This method retrieves the login url from the Facebook object, using the application url we specified earlier. We return it in a ``Response`` object, passing a status code of 200 indicating success. This will be used later in the templates, so the user has a link to click to go to the login form. 
        
        public function login() {
            $loginUrl = $this->facebook->getLoginUrl(array(
                'redirect_uri' => $this->facebookConfig['appUrl']
            ));
            $content = sprintf(
                "<script type='text/javascript'>top.location.href = '%s';</script>",
                $loginUrl
            );
            return new Response(
                $content, 200, array('content-type' => 'text/html')
            );
        }
    }
    
###Platform Note
    
If you're not too familiar with [the cloudControl platform](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables), you're able to store settings which you can use in your application, environment specific (production, development, staging). 

This saves us adding them to configuration files and makes it very simple and flexible to change them, add to them and remove some on an as-needed basis. 
    
###The Views

Ok, we've looked at all the code which's required for the application. Now let's cover each of the views. There's only 6, so this won't take long. Firstly, create a directory called **views** in the project root and then we'll work through each of the views.

####base.twig

In the views directory, create a file called ``base.twig`` and add the code below. This provides the core template which our application will use. The output from the other templates will be rendered inside this one. 

If you're familiar with the two-step view process in Zend Framework or similar approaches in other PHP frameworks, this will be very familiar to you. If not, in the template below, there is the following line:

    <div id="content">{% block content %}{% endblock %}</div>
    
The output generated by each of the following templates will replace ``{% block content %}{% endblock %}``.

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
          <link rel="stylesheet" href="/css/style.css" />
      </head>
      <body>
        <h1>Welcome to cloudControl</h1> 
        <div id="content">{% block content %}{% endblock %}</div>
      </body>
    </html>

####appDetails.twig

In the views directory, create a file called ``appDetails.twig`` and add the code below. This template is responsible for displaying the details of the application, once the user is logged in. 

You can see it has template variables for the *application name*, *date created*, *date modified* and *repository*. The slightly more complex aspect to it are the to for loops which loop through the applications users, rendering their username and email address and the application deployments, rendering the a link to them via their name.

    {% extends "base.twig" %}
    {% block content %}
        <h2>Your cloudControl App "{{ applicationName }}"</h2>
        <table>
            <tr>
                <td>date created</td>
                <td>{{ application.date_created }}</td>
            </tr>
            <tr>
                <td>date modified</td>
                <td>{{ application.date_modified }}</td>
            </tr>
            <tr>
                <td>users</td>
                <td>
                    {% for user in application.users %}
                        {{ user.username|e }}, {{ user.email|e }}<br/>
                    {% endfor %}
                </td>
            </tr>
            <tr>
                <td>repository</td>
                <td>{{ application.repository }}</td>
            </tr>
            <tr>
                <td>deployments</td>
                <td>
                    {% for deployment in application.deployments %}
                        <a href="/deployment/{{ deployment.name|e }}"
                            >{{ deployment.name|e }}</a>
                        <br/>
                    {% endfor %}
                </td>
            </tr>
        </table>
        <hr/>
        <a href="/logout">Logout</a>
    {% endblock %}

####applist.twig

In the views directory, create a file called ``applist.twig`` and add the code below. As before, the content in this template will replace ``{% block content %}{% endblock %}`` in ``base.twig.``. With this template, we display the applications the user has deployed, output in a simple table format. 

You can see we're using a simple for loop, as in ``appDetails.twig`` to list the applications and in the table footer provide a count of the applications available.

    {% extends "base.twig" %}
    {% block content %}
        <h1>Your cloudControl Apps</h1>
        <table>
            <thead>
                <tr>
                    <th>Application Name</th>
                    <th>Application Type</th>
                </tr>
            </thead>
            <tbody>
            {% for app in applicationList %}
            <tr>
                <td>
                    <a href="/app/{{ app.name }}">{{ app.name }}</a>
                </td>
                <td>{{ app.type.name }}</td>
            </tr>
            {% endfor %}
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Apps:</td>
                    <td>{{ applicationList|length }}</td>
                </tr>
            </tfoot>
        </table>
        <hr/>
        <a href="/logout">Logout</a>
    {% endblock %}

####deploymentDetails.twig

In the views directory, create a file called ``deploymentDetails.twig`` and add the code below. In this template, we're showing more specific details of a deployment. You can see the *date created* and *modified*, the *state* and *stack name*, *deployment id*, *version*, *min and max boxes* and *default subdomain*. 

    {% extends "base.twig" %}
    {% block content %}
        <h2>Your cloudControl Deployment "{{ deployment.name }}"</h2>
        <table>
            <tr>
                <td>date created</td>
                <td>{{ deployment.date_created }}</td>
            </tr>
            <tr>
                <td>date modified</td>
                <td>{{ deployment.date_modified }}</td>
            </tr>
            <tr>
                <td>state</td>
                <td>{{ deployment.state }}</td>
            </tr>
            <tr>
                <td>stack</td>
                <td>{{ deployment.stack.name }}</td>
            </tr>
            <tr>
                <td>dep_id</td>
                <td>{{ deployment.dep_id }}</td>
            </tr>
            <tr>
                <td>version</td>
                <td>{{ deployment.version }}</td>
            </tr>
            <tr>
                <td>min_boxes</td>
                <td>{{ deployment.min_boxes }}</td>
            </tr>
            <tr>
                <td>max_boxes</td>
                <td>{{ deployment.min_boxes }}</td>
            </tr>
            <tr>
                <td>default_subdomain</td>
                <td>
                    <a href="http://{{ deployment.default_subdomain }}" 
                        target="_blank">
                            {{ deployment.default_subdomain }}
                    </a>
                </td>
            </tr>
        </table>
        <hr/>
        <a href="/logout">Logout</a>
    {% endblock %}

####error.twig

In the views directory, create a file called ``error.twig`` and add the code below. This template is used when the application encounters any errors. It's a rather simple one showing the exception message and the stack trace so the user can trace it back to its source.

    {% extends "base.twig" %}
    {% block content %}
        <h2>An error occured!</h2>
        <h3>{{ exception.getMessage }}</h3>
        <pre>
        {{ exception.getTrace() }}
        </pre>
        <hr/>
        <a href="/logout">Logout</a>
    {% endblock %}

####login.twig

In the views directory, create a file called ``login.twig`` and add the code below. In this, the last template, we provide the user with a login form. If you're not familiar with Symfony forms, this may seem a little strange to you. 

But we use the tags to output the form attributes and fields as well as the value of the submit button. For more information, check out the [Symfony forms online documentation](http://symfony.com/doc/2.0/reference/forms/types.html).

    {% extends "base.twig" %}
    {% block content %}
        <h2>Login to cloudControl</h2> 
        <form action="/login" method="post" {{ form_enctype(form) }}>
            {{ form_widget(form) }}
            <input type="submit" value="{{ 'Send'|trans }}"/>
        </form>
    {% endblock %}
    
###3. Deploy to CloudControl

With that, we've constructed all the elements of the application and we're ready to deploy it to cloudControl. So, let's work through these final, required, steps

####Manage With Git

Next, we need to put the code under Git control. To do so, run the following command:
    
    git init .
    
    git add *.*
    
    git commit -m "First addition of the source files"
    
Then we'll initialise and deploy it to cloudControl. If you've followed along with any of the other [Developer Library PHP tutorials](https://www.cloudcontrol.com/dev-center/Guides/PHP/CakePHP%202.2.1), this will be pretty similar. 
    
    // create the application setting its type as PHP
    cctrlapp cloudcontrolfb create php
    
    // push and deploy the default branch
    cctrlapp cloudcontrolfb/default push    
    
You should see output similar to the following (truncated for readability):

Counting objects: 27, done.
Delta compression using up to 2 threads.
Compressing objects: 100% (22/22), done.
Writing objects: 100% (27/27), 144.79 KiB, done.
Total 27 (delta 0), reused 27 (delta 0)
       
-----> Receiving push
       Loading composer repositories with package information
       Initializing PEAR repository http://pear.php.net
       Installing dependencies
         - Installing facebook/php-sdk (dev-master v3.2.2)
           Cloning v3.2.2
       
         - Installing psr/log (1.0.0)
           Downloading: 100%
       
         - Installing twig/twig (v1.12.2)
           Downloading: 100%
       
         - Installing doctrine/common (2.2.3)
           Downloading: 100%
       
         - Installing symfony/routing (v2.2.0)
           Downloading: 100%
       
         - Installing symfony/http-foundation (v2.2.0)
           Downloading: 100%
       
         - Installing symfony/options-resolver (v2.1.8)
           Downloading: 100%
       
         - Installing symfony/locale (v2.1.8)
           Downloading: 100%
       
         - Installing symfony/form (v2.1.8)
           Downloading: 100%
       
         - Installing symfony/twig-bridge (v2.1.8)
           Downloading: 100%
       
         - Installing symfony/translation (v2.1.8)
           Downloading: 100%
       
         - Installing doctrine/dbal (2.2.2)
           Downloading: 100%
       
       Generating autoload files
-----> Building image
-----> Uploading image (4.6M)

In the console output, notice the Composer-based dependencies being automatically included. A great feature of the latest build is that you don't need to specifically run ``composer update`` yourself. The push process automatically does this for you. 

For further information, check out the [Buildpacks and the Procfile](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile) section of the platform documentation.

Then deploy the application:

    cctrlapp cloudcontrolfb/default deploy

[](id:environment-settings)
####Configuring the Environment Settings

Remember [the Facebook settings](#facebook-settings) we talked about keeping a note of earlier? Now's the time to use them. With your terminal window still open, set them into your application's environment as follows (_formatted for readability_):

    cctrlapp cloudcontrolfb/default addon.add config.free \
        --APP_ID=YOUR_APP_ID --SECRET_KEY=YOUR_SECRET_KEY

With the application deployed to cloudControl, we can either visit it directly or look at it through Facebook. To keep this concise, we'll skip straight to Facebook. 

####Initialise the Required Add-ons

Now we need to configure two add-ons, [config](https://www.cloudcontrol.com/documentation/add-ons/config) and [mysqls](https://www.cloudcontrol.com/documentation/add-ons/mysql-shared). The config add-on's required for determining the active environment and mysqls for storing our session and logging information. 

####Check the Add-on Configuration

Now let's be sure everything is in order by having a look at the add-on configuration output, in this case for testing. To do so, run the command below:

    // Initialise the mysqls.free addon for the default deployment
    cctrlapp cloudcontrolfb/default addon.add mysqls.free
    
    // Retrieve the settings
    cctrlapp cloudcontrolfb/default addon mysqls.free

The output of the commands will be similar to the following:

    Addon                    : mysqls.free
       
     Settings
       MYSQLS_DATABASE          : <database_name>
       MYSQLS_PASSWORD          : <database_password>
       MYSQLS_PORT              : 3306
       MYSQLS_HOSTNAME          : mysqlsdb.co8hm2var4k9.eu-west-1.rds.amazonaws.com
       MYSQLS_USERNAME          : <database_username>

####Initialising config

Now we need to configure the config add-on and store the respective environment setting in it. So run the following commands to do this:

    // Set the default environment setting
    cctrlapp cloudcontrolfb/default addon.add config.free --APPLICATION_ENV=main

Now we're ready to make some changes to our code to make use of the new configuration. 

####Database Setup

As the session data will be stored in the database, you'll need to apply the following database schema definition, from the command line, to your configured cloudControl MySQL database. So save this script as ``fb_session_init.sql``.

    CREATE TABLE `session` (
        `session_id` varchar(255) NOT NULL,
        `session_value` text NOT NULL, 
        `session_time` int(11) NOT NULL,
        PRIMARY KEY (`session_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

To do so, run the following command, changing the respective options with your configuration settings:

    mysql -u <database_username> -p \
        -h mysqlsdb.co8hm2var4k9.eu-west-1.rds.amazonaws.com \
        --ssl-ca=mysql-ssl-ca-cert.pem <database_name> < fb_session_init.sql

In the command above, you can see a reference to a **.pem** file. This can be downloaded from: [http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem](http://s3.amazonaws.com/rds-downloads/mysql-ssl-ca-cert.pem). All being well, the command will finish silently, loading the data. You can check all's gone well with following commands:

    mysql -u <database_username> -p \
        -h mysqlsdb.co8hm2var4k9.eu-west-1.rds.amazonaws.com \
        --ssl-ca=mysql-ssl-ca-cert.pem <database_name>
    
    show tables;
    
This will verify the table has been successfully created.

That's it. Our application is now ready to be used!

##Access on Facebook

Before the user can use our application the first time, they have to allow it to do so. If you've used other applications, or linked applications to Twitter, Google+ and the like, you'll have seen this before. 

![Allowing access to the Facebook application](images/facebook-allowing-application-access.png "Allowing access to the Facebook application")

In the screenshot above, you'll see the prompt which the user will see the first time they attempt to access our application. You can see the name, short and long description we entered earlier. 

When you see this, click **Go to App** and you'll be able to view the application.

##The Running Application

![The login screen](images/facebook-login-screen.png "The login screen")

The first time you view it, it will look like the above. Not that pretty, *but it works*! If you're already a cloudControl user, enter your email address and password in the form and click **send**. After doing so, you'll get output similar to the screenshot below.

![Deployments list](images/facebook-cloudcontrol-applications-list.png "Deployments list")

Clicking on one of the applications in the list shows us the details of that application, as in the screenshot below

![Application details](images/facebook-application-details.png "Application details")

Clicking on a deployment on that page, shows us details of the deployment, as in the screenshot below

![Deployments details](images/facebook-deployment-details.png "Deployments details")

##We're Finished

There you have it. In about 40 minutes we've created, deployed and used our first Facebook application. Admittedly this is a rather simple, introductory, example. But you can see it's not so complex or detailed to do. 

Let your mind wander and have a look at a lot of the other Facebook applications for inspiration on what you can achieve. [Clone a copy of the code](https://github.com/cloudControl/php-facebook-example-app) from the cloudControl Github repo and **go wild**!

##Further Questions & Reading

If this has whet your appetite for building and deploying Facebook applications, you'll find below a selection of links which will help you to expand your knowledge. 

 - [Facebook Developer Platform](https://developers.facebook.com/)
 - [Design and Code an Integrated Facebook App (nettuts+)](http://net.tutsplus.com/tutorials/javascript-ajax/design-and-code-an-integrated-facebook-app/)
 - [How to create a Facebook application using PHP and graph api (Devlup)](http://devlup.com/programming/php/how-to-create-facebook-application-using-php-and-graph-api/1589/)
 - [Symfony Forms](http://symfony.com/doc/2.0/reference/forms/types.html)
 - [Silex, The PHP micro-framework
based on the Symfony2 Components](http://silex.sensiolabs.org/)
 - [Twig, The flexible, fast, and secure
template engine for PHP](http://twig.sensiolabs.org/)
 - [Composer Dependency Manager for PHP](http://getcomposer.org/)
 - [PHP Namespaces](http://php.net/manual/en/language.namespaces.php)

##Get Started Today!

This guide was written by Malt Blue, [a cloudControl Solution Provider](https://www.cloudcontrol.com/solution-providers/Maltblue). If you'd like assistance getting up and running with your next PHP web application on the cloudControl platform, [get in touch](http://www.maltblue.com/contact) today. 