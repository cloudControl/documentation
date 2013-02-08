#Creating a Facebook App with cloudControl

Have you wanted to develop a Facebook application but you've never been quite sure how to do it? Maybe you've watched or played some games, such as [FarmVille](https://apps.facebook.com/onthefarm/), [Diamond Dash](https://www.facebook.com/DiamondDashGame) and [CityVille](https://apps.facebook.com/cityville/). 

Maybe you've used apps such as [Graffiti](https://www.facebook.com/graffitiwall) and [Causes](https://apps.facebook.com/causes/). Maybe, after using them, you thought - HEY, I could write an app just as good. No! I could write one **much better**!

Sound like you? Perhaps you're just interested in writing one for a school, university or personal pet project. Well today I want to show you how to do it, quickly, simply, effectively. 

I'm going to show you how to write one, from start to finish, using the cloudControl platform. By the time we're finished, in just under 40 minutes, you'll know the following:

 1. What a Facebook application is
 2. How to create and configure it - on the Facebook side
 3. How to create an application locally which you can deploy to cloudControl
 4. How to link the two together, so the user can accept and use it
 
This application assumes no prior knowledge of creating Facebook applications and only a limited amount of knowledge of creating HTML websites and PHP-based applications.

##What Is a Facebook Application

![Facebook application composition](images/application-composition.png "Facebook application composition")

Looking at the image above you see a simplistic representation of how a Facebook application works. Said succinctly, a Facebook application is one hosted somewhere outside of Facebook's servers, in our case, with cloudControl, but that is then accessed through Facebook, via an iframe.

This has a series of advantages, the prime one being we can make use of both Facebook's resources and our own. Secondly we can develop the application, nearly completely independently of Facebook, locally; designing it, building it and testing it. 

Then, when we're ready, link the two together so more than a billion people are able to access it, like it, talk about it and encourage their friends to use it - *pretty impressive stuff*. 

##Configuring the Application on Facebook

Ok, the first thing you need to do is to create the application, inside your Facebook account. So if you're not already there, navigate over to [https://developers.facebook.com/apps](https://developers.facebook.com/apps) and login. 

After you've done this, you'll see a page similar to the one in the image below. Click the **Create New App** button in the top right-hand corner. 

![Facebook application composition](images/create-app.png "Facebook application composition")

This will display a modal popup as in the image below, allowing use to enter the application name and namespace. For the purposes of this example, I've configure the options as:

 - **App Name**: "Hello World"
 - **App Namespace**: cloudcontrolhw 
 - **Web Hosting**: leave it unchecked as we're deploying to cloudControl

![Facebook application composition](images/create-app-step-2.png "Facebook application composition")

Once you're happy with the information you've specified, click **Continue**. You'll then be asked to pass a security check. So, enter the two words in the prompt into the input box below. 

If you're not able to read one or both of the words, then click "**Try different words or an audio [CAPTCHA](http://www.captcha.net/)**". After you've entered the two words, click **Continue** again. 

![Facebook application composition](images/create-app-step-3.png "Facebook application composition")

###The Application Details

Once you've done this, you'll see a window like the following, where you'll be able to enter the details of the application. You'll see at the top of the window, in **Basic Info**, the details you've filled already. Leave the remainder of the settings as they are. Then set:

 - **App Domains**: blank
 - **Sandbox Mode**: Enabled

![Facebook application composition](images/app-details-2.png "Facebook application composition")

In the section under basic info, you'll see: **Select how your app integrates with Facebook**. There are a number of options to choose from. But for the purposes of this example, we're going to be using: **App on Facebook**. 

Click the row in the list and you'll see the section expand allowing you to configure it. There will be a number of options available, including:

 - Canvas URL
 - Secure Canvas URL
 - Canas Width
 - Canvas Height
 
As this is a simple, introductory example, we're going to leave **Secure Canvas URL** blank and **Canas Width** and **Canvas Height** to *Fixed* and *Fluid* respectively. 

The secure canvas URL is where users visiting via a HTTPS connection will retrieve the content from. The canvas width and height options allow the output of your app to respond to the users browsers dimensions, proportionately, or to stay at a fixed width and height. 

Feel free to set them as suits you best. But bear in mind the constraints this may place on your final application.

![Facebook application composition](images/app-details-4.png "Facebook application composition")

Ok, with all of the options specified, then click **Save Changes** at the bottom to save and create our wonderfully, simple, new application. 

##Developing the Application

This is where Facebook really makes it easy for us, as developers, to create applications for their platform. Effectively, our application can be whatever we want it to be, so long as we don't breach [the platform policies](https://developers.facebook.com/policy/).

So, for the purposes of this tutorial, we're going to create a simple application where the user can input their cloudControl username and password to retrieve the list of applications which they have deployed.

Now, is this as exciting as *FarmVille* or *Texas Hold 'em*, likely not. But it will show you how to write an application, using external resources, which can be used through Facebook. Once you've worked through this example, I'm sure you'll start getting a raft of potential ideas for applications you can build.

###Application Dependencies

Before we go any further however, we're going to need to satisfy the application dependencies, of which there are two:

 - [The PHP library for the cloudControl API (phpcclib)](https://github.com/cloudControl/phpcclib)
 - [The Facebook PHP SDK](https://github.com/facebook/facebook-php-sdk)
 
Both of these are available on Github. So clone both of them as follows:

####phpcclib

    git clone git://github.com/cloudControl/phpcclib.git
    
####facebook php sdk

    git clone git://github.com/facebook/facebook-php-sdk.git

Now you have those cloned, copy ``phpcclib.php`` and the ``lib`` directory from the Facebook php sdk to your application's project directory. To keep things simple, I also copied the ``phpcclib.php`` file to the ``lib`` directory along with the Facebook files. 

###Static Files / CSS

Create a new directory called ``static`` and in there, create a file called ``style.css``.  In it, add the following style definitions:

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

Now create a new file, called ``index.php``. This is going to contain the **PHP** code for the application which we'll go through shortly. 

At the top of index.php, include the two core php files as follows:

    require_once('lib/facebook.php');
    require_once('lib/phpcclib.php');

You could write a basic autoloader function, but ``require_once`` does the     job nicely. Next we need to set up the environment, ready to interact with Facebook's API.

[](id:facebook-settings)After you created and configured the application in Facebook, the application was given it's **app id** and **secret**. Make a note of them, because we'll be using storing them in [the application's environment settings](#environment-settings) later. 

###The Application Code

####index.php

How the application works is by using [the output buffering control functions](http://php.net/manual/en/book.outcontrol.php) in PHP to allow us to create and capture the output, then display it in a simple view afterwards. Let's step through it.

First we enable output buffering:
    
    ob_start();
    
We then call **[getFacebookConfig](#get-facebook-config)** which pulls together the configuration information which we'll use to initialise our Facebook object.
    
    try {
        $facebookConfig = getFacebookConfig();
    } catch (Exception $e){
        $errormsg = $e->getMessage();
        include 'views/error.php';
    }
    
If we have a Facebook App Id available, we then attempt to create a Facebook object with the configuration details and attempt to retrieve the logged in user (if available).
    
    if(isset($facebookConfig['appId'])){
        // Create An instance of our Facebook Application.
        $facebook = new Facebook($facebookConfig);
        // Get the app User ID
        $user = $facebook->getUser();
     
If the user's available, we then retrieve their user profile. If not, we include the **[error.php](#view-error)** view. 
        
        if ($user) {
            try {
                // If the user has been authenticated then proceed
                $user_profile = $facebook->api('/me');
                // debug($user_profile);
            } catch (FacebookApiException $e) {
                $errormsg = $e->getMessage();
                include 'views/error.php';
                $user = null;
            }
        }
  
If the user is available, then we retrieve the logout url and call the **[showCloudControlApps](#show-cloud-control-apps)** function which renders the core of our application. If the user's not available, we show the login url to the user, which allows the user to login and use the application.
  
        if ($user) {
            $logoutUrl = $facebook->getLogoutUrl();
            showCloudControlApps();
        } else {
            $loginUrl = $facebook->getLoginUrl(array(
                'redirect_uri' => $facebookConfig['appUrl']
            ));
            printf("<script type='text/javascript'>top.location.href = '%s';</script>", $loginUrl);
            exit();
        }
    }
    
We then catch everything that has been output, directly or via the included views and store it in the variable ``$content``.

    $content = ob_get_contents();
    ob_end_clean();
    
With all of the hard work done, we now show the constructed application with **[base.php](#view-base)**.

    include 'views/base.php';

[](id:get-facebook-config)
####Retrieving the Facebook Configuration

The details that we'll set in the application environment later, we retrieve via the getFacebookConfig function. It's this information that is later used when we initialse a Facebook object.

    function getFacebookConfig() 
    {
    
In the **App on Facebook** setting, you'll also remember there was a pre-configured string in the Canvas Page setting. Copy that value below to ``appUrl``, replacing what I have here.
    
        $facebookCredentials = array(
            'appUrl' => "http://apps.facebook.com/mwfacebookapp",
            'cookies' => 'true',        
        );
        
We perform a quick check to see if we're within the cloudControl environment with a check for the ``CRED_FILE`` environment variable. If it's found, we attempt to extract and decode the JSON information contained within. We throw an exception if no information's available or we not able to successfully decode it.
        
        if (!empty($_SERVER['HTTP_HOST']) && isset($_ENV['CRED_FILE'])) {
            $string = file_get_contents($_ENV['CRED_FILE'], false);
            if ($string == false) {
                throw new Exception('Could not read credentials file');
            }
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
                throw new Exception(sprintf(
                    'A json error occured while reading the credentials file: %s', 
                    $json_errors[$error]
                ));
            }
            if (!array_key_exists('CONFIG', $creds)){
                throw new Exception(
                    'No Config credentials found. 
                    Please make sure you have added the config addon.'
                );
            }
            
If we're successful - and we've setup the environment settings properly - then we extract them and store them in the ``$facebookCredentials`` array against the ``appId`` and ``secret`` keys. This information is then returned, so we can use it later. 
            
            $facebookCredentials['appId'] = $creds['CONFIG']['CONFIG_VARS']['APP_ID'];
            $facebookCredentials['secret'] = $creds['CONFIG']['CONFIG_VARS']['SECRET_KEY'];
        }
        return $facebookCredentials;
    }
    
###Platform Note:
    
If you're not that familiar with [the cloudControl platform](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#environment-variables), you're able to store settings that you can use in your application, environment specific (production, development, staging). 

This saves us adding them to configuration files and makes it very simple and flexible to change them, add to them and remove some on an as-needed basis. 
   
[](id:show-cloud-control-apps)
####Show The Deployed Applications
   
This is the core function of the application, allowing the user to either request the deployed applications or show the available list. 
   
    function showCloudControlApps() 
    {
    
Firstly we initialise an array called ``$args``. It is going to be passed to [filter_input_array](http://php.net/manual/en/function.filter-input-array.php) so we can extract **sanitised and filtered versions** of the form information submitted by the user. 

As the users will login using their email address and password, I've configured it to ensure both values are scalar and they're sanitised appropriately.
    
        $args = array(
            'email' => array(
                'filter' => FILTER_SANITIZE_STRING,
                'flags' => FILTER_REQUIRE_SCALAR
            ),
            'password' => array(
                'filter' => FILTER_SANITIZE_STRING,
                'flags' => FILTER_REQUIRE_SCALAR
            ),
        );
        
Here we retrieve the sanitised form information from the $_POSTS superglobal.
        
        $formInput = (object)filter_input_array(INPUT_POST, $args);
        
Then we perform a quick check to see we have all the information required before going any further. 
        
        if ($formInput && !empty($formInput->email) 
            && !empty($formInput->password)) {
            debug($formInput);
            
If so, we initialise a new ``Api`` object and perform an authentication request using the submitted email address and password.
            
            try {
                $api = new Api();
                $apiToken = $api->auth(
                    $formInput->email,
                    $formInput->password
                );
                
Assuming the authentication request was successful, we then attempt to retrieve the list of applications for the user, using the ``application_getList`` method. If anything goes wrong, we display the **[error.php](#view-error)** view.
                
                $applicationList = $api->application_getList();
            } catch (Exception $e) {
                // skipping over the myriad of exceptions which
                // could be thrown. More at: http://bit.ly/U80uVd
                $errormsg = $e->getMessage();
                include 'views/error.php';
                return;
            }
            
If a list of application's is returned from the list, then we iterate over them in **[applist.php](#view-applist)**, rendering the application name and type in an auto-generated table. If no application's are available, we render ``views/error.php`` again.
            
            if (!empty($applicationList)) {
                include 'views/applist.php';
            } else {
                $errormsg = "No applications available";
                include 'views/error.php';
            }
        } else {
        
If there was no POST request, then we display **[loginform.php](#view-loginform)** rendering the login form.
        
            include 'views/loginform.php';
        }
    }
 
####Debugging

You may have noticed calls to ``debug`` through the functions. This is a simple, custom, function that allows use to dump the error information to the [php stderr stream](http://php.net/manual/en/features.commandline.io-streams.php) which we can keep an eye on through tailing [the environment error log](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#logging). 

**Please Note:** this is only available on the **pinky stack**. So if you're not on that version, please don't use this function.

    // only available on pinky stack
    function debug($object){
        $parts = explode("\n", print_r($object, true));
        file_put_contents('php://stderr', array_shift($parts));
        foreach ($parts as $line){
            file_put_contents('php://stderr', $line);
        }
    }
 
To tail the error log, run the following in your terminal:

    $ cctrlapp APP_NAME/DEP_NAME log error
 
###The Views

Ok, we've worked through the application functions. Let's now setup the views by creating a folder, aptly called **views**, to store them.

[](id:view-error)
####error.php

This one is really simple, just displaying the error message to the user. Feel free to customise it as suits you best. 

    <div id="error">
        <?php echo $e->getMessage(); ?>
    </div>

[](id:view-loginform)    
####loginform.php

This allows the user to login. You can see it's a pretty standard configuration with the two input fields (email and password) and a submit button. 

    <form action="" method="post" enctype="application/x-www-form-urlencoded">
        <fieldset>                        
            <input type="email" name="email" 
                class="email" placeholder="your email address" />
            <input type="password" name="password" 
                class="password" placeholder="your password" />
            <input name="submit" class="submit" type="submit" />
        </fieldset>
    </form>
    
[](id:view-applist)
####applist.php

If we retrieved a list of the user's applications, then we iterate over them in the following view. It renders the **application name and type** and an application list total, in tabular format.

    <table>
        <thead>
            <tr>
                <th>Application Name</th>
                <th>Application Type</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($applicationList as $application) {
            printf("<tr><td>%s</td><td>%s</td></tr>", $application->name, $application->type->name);
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total Apps:</td>
                <td><?php print count($applicationList); ?></td>
            </tr>
        </tfoot>
    </table>
    
[](id:view-base)
####base.php

This is the _bootstrap_ template. You can see that it's a pretty simple HTML page. It outputs the  $content variable that we've initialised through the output control functions earlier. 

It also presents the logout url if it's been initialised. So if the user's logged in or out, submitted the form or not, they see the right aspect of the application. 

    <!DOCTYPE HTML>
    <html>
        <head>
        <meta charset="utf-8">
            <title>Design and Code an integrated Facebook App</title>
            <link rel="stylesheet" type="text/css" href="static/style.css"></link>
        </head>
        <body>
            <div id="content">
                <?php echo $content; ?>
            </div>
            <?php if(isset($logoutUrl)): ?>
            <div id="logout">
                <a class="button right" href="<?php echo $logoutUrl; ?>"><span class="buttonimage left"></span>Logout</a>
            </div>
            <?php endif; ?>
        </body>
    </html>
    
###Deploying to CloudControl

With that, we've not constructed all the elements of the application and we're ready to deploy it to cloudControl. So let's get started. We'll begin by putting it under Git control. To do it, run the following command:

    cd <your project directory>
    
    git init .
    
    git add *.*
    
    git commit -m "First addition of the source files"
    
Then we'll initialise and deploy it to cloudControl. If you've followed along with any of the other PHP tutorials in the developer library, this will be pretty similar. 
    
    // create the application setting its type as PHP
    cctrlapp cloudcontrolhw create php
    
    // push and deploy the default branch
    cctrlapp cloudcontrolhw/default push    
    cctrlapp cloudcontrolhw/default deploy
    
You should see output similar to the following:

    $ cctrlapp cloudcontrolhw/testing push
    Total 0 (delta 0), reused 0 (delta 0)
           
    >> Receiving push
    >> Compiling PHP
    >> Building image
    >> Uploading image 

[](id:environment-settings)
###Configuring the Environment Settings

Remember [the Facebook settings](#facebook-settings) we talked about keeping a note of earlier? Now's the time to use them. With your terminal window still open, set them into your application's environment as follows (_formatted for readability_):

    cctrlapp APP_NAME/default addon.add config.free \
        --APP_ID=YOUR_APP_ID --SECRET_KEY=YOUR_SECRET_KEY

With the application deployed to cloudControl, we can either visit it directly or look at it through Facebook. To keep this concise, we'll skip straight to Facebook. 

##Allowing Access on Facebook

Before the user can use our application the first time, they have to allow it to do so. If you've used other applications, or linked applications to Twitter, Google+ and the like, you'll have seen this before. 

![Allowing access to the Facebook application](images/allowing-application-access.png "Allowing access to the Facebook application")

In the screenshot above, you'll see the prompt which the user will see the first time they attempt to access our application. You can see the name, short and long description we entered earlier. 

When you see this, click **Go to App** and you'll be able to view the application.

##Using the Application

![Allowing access to the Facebook application](images/application-running.png "Allowing access to the Facebook application")

The first time you view it, it will look like the above. Not that pretty, *but it works*! If you're already a cloudControl user, enter your email address and password in the form and click **Submit Query**. After doing so, you'll get output similar to that in the screenshot below.

![Allowing access to the Facebook application](images/application-output-1.png "Allowing access to the Facebook application")

##We're Finished

There you have it. In only about 40 minutes, we've created, deployed and used our first Facebook application. Now, admittedly, this is a rather simple, introductory, example. 

But you can see that it's not so complex or detailed to do. Let your mind wander and have a look at a lot of the other Facebook applications for inspiration on what you can achieve. For a copy of the complete code, you can clone a copy from the cloudControl Github repo.

##Further Questions & Reading

If this has whet your appetite for building and deploying Facebook applications, you'll find below a selection of links that will help you to expand your knowledge. 

 - [Facebook Developer Platform](https://developers.facebook.com/)
 - [Design and Code an Integrated Facebook App (nettuts+)](http://net.tutsplus.com/tutorials/javascript-ajax/design-and-code-an-integrated-facebook-app/)
 - [How to create a Facebook application using PHP and graph api (Devlup)](http://devlup.com/programming/php/how-to-create-facebook-application-using-php-and-graph-api/1589/)

##Get Started Today!

This guide was written by Malt Blue, [a cloudControl Solution Provider](https://www.cloudcontrol.com/solution-providers/Maltblue). If you'd like assistance getting up and running with your next PHP web application on the cloudControl platform, [get in touch](mailto:matthew@maltblue.com?subject=I'd like to talk about a new web application) today. 