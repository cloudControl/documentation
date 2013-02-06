<?php
require_once('src/facebook.php');
require_once('src/phpcclib.php');

$appId = '151069181712790';
$secret = '3cb909e01468e057e9646485cf172f31';

$fbconfig['appUrl'] = "http://apps.facebook.com/cloudcontrolhw";

// Create An instance of our Facebook Application.
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $secret,
  'cookies' => 'true',
));

// Get the app User ID
$user = $facebook->getUser();

if ($user) {
    try {
        // If the user has been authenticated then proceed
        $user_profile = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        print $e->getMessage();
        $user = null;
    }
}

// If the user is authenticated then generate the variable for the logout URL
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
?>

<!DOCTYPE HTML>
<html>
    <head>
    <meta charset="utf-8">
        <title>Design and Code an integrated Facebook App</title>
        <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css">
        <style type="text/css">
            h1 {
                font-size: 120%;
                font-weight: bold;
                margin:auto;
                line-height:51px;
                vertical-align:middle;
            }
            
            table {
                margin-left:auto;
                margin-right:auto;
                clear: both;
            }
            
            #header {
                width: 100%;
                height:51px;
                position:relative;
                text-align:center;
                background-color: #efefef;
                margin-bottom: 10px;
                border-top: 1px solid #e4e4e4;
                border-bottom: 1px solid #e4e4e4;
            }
            
            th, td {
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
                width:557px;
                height:227px;
                margin-left:auto;
                margin-right:auto;
                margin-bottom:26px;
                position: relative;
                -moz-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
                -webkit-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
                box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
            } 
            
            #logout {
                margin-top: 10px;
            }        
        </style>
    </head>
    <body>
        <div class="wrapper">

            <div class="logo">
                <img src="images/logo.png" width="557" height="227" alt="cloudcontrol logo">
            </div>
                
            <div id="header">
                <h1>My Applications</h1>
            </div>

            <div class="sidebar">

                <?php
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
                    $formInput = (object)filter_input_array(INPUT_POST, $args);
                    if ($formInput && !empty($formInput->email) && !empty($formInput->password)) {
                        $api = new Api();
                        $apiToken = $api->auth($formInput->email, $formInput->password);
                        
                        try {
                            $applicationList = $api->application_getList();
                        } catch (Exception $e) {
                            // skipping over the myriad of exceptions that could be thrown.
                            // find out more at: https://github.com/cloudControl/phpcclib/blob/master/phpcclib.php
                            print $e->getMessage();
                        }
                        
                        if (!empty($applicationList)) {
                            ?>
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
                                    printf(
                                        "<tr><td>%s</td><td>%s</td></tr>", 
                                        $application->name,
                                        $application->type->name
                                    );
                                }
                            ?>
                            <tfoot>
                                <tr>
                                    <td>Total Apps:</td>
                                    <td><?php print count($applicationList); ?></td>
                                </tr>
                            </tfoot>
                            </table>
                            <?php
                        } else {
                            print "No applications available";
                        }
                        
                    } else {
                ?>
                
                <form action="" method="post" enctype="application/x-www-form-urlencoded">
                    <fieldset>                        
                        <input type="email" name="email" 
                            class="email" placeholder="your email address" />
                        <input type="password" name="password" 
                            class="password" placeholder="your password" />
                        <input name="submit" class="submit" type="submit" />
                    </fieldset>
                </form>
                
                <?php
                    }
                ?>

                <div id="logout">
                    <a class="button right" href="#"><span class="buttonimage left"></span>Logout</a>
                </div>
            </div><!--End Sidebar-->
            
    <!-- Insert Logged in HTML here -->
    <?php
    
    } else {
      $loginUrl = $facebook->getLoginUrl(array('redirect_uri' => $fbconfig['appUrl']));
      print "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
    }

    ?>
    </body>
</html>