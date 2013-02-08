<?php
require_once 'lib/facebook.php';
require_once 'lib/phpcclib.php';

// only available on pinky stack
function debug($object){
    $parts = explode("\n", print_r($object, true));
    file_put_contents('php://stderr', array_shift($parts));
    foreach ($parts as $line){
        file_put_contents('php://stderr', $line);
    }
}

function getFacebookConfig(){
    $facebookCredentials = array(
        'appUrl' => "http://apps.facebook.com/mwfacebookapp",
        'cookies' => 'true',        
    );
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
            throw new Exception(sprintf('A json error occured while reading the credentials file: %s', $json_errors[$error]));
        }
        if (!array_key_exists('CONFIG', $creds)){
            throw new Exception('No Config credentials found. Please make sure you have added the config addon.');
        }
        $facebookCredentials['appId'] = $creds['CONFIG']['CONFIG_VARS']['APP_ID'];
        $facebookCredentials['secret'] = $creds['CONFIG']['CONFIG_VARS']['SECRET_KEY'];
    }
    return $facebookCredentials;
}

function showCloudControlApps(){
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
        debug($formInput);
        try {
            $api = new Api();
            $apiToken = $api->auth(
                $formInput->email,
                $formInput->password
            );
            $applicationList = $api->application_getList();
        } catch (Exception $e) {
            // skipping over the myriad of exceptions which
            // could be thrown. More at: http://bit.ly/U80uVd
            $errormsg = $e->getMessage();
            include 'views/error.php';
            return;
        }
        
        if (!empty($applicationList)) {
            include 'views/applist.php';
        } else {
            $errormsg = "No applications available";
            include 'views/error.php';
        }
    } else {
        include 'views/loginform.php';
    }
}

// we catch the output to $content variable
ob_start();
try {
    $facebookConfig = getFacebookConfig();
} catch (Exception $e){
    $errormsg = $e->getMessage();
    include 'views/error.php';
}

if(isset($facebookConfig['appId'])){
    // Create An instance of our Facebook Application.
    $facebook = new Facebook($facebookConfig);
    // Get the app User ID
    $user = $facebook->getUser();
    
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
// catch everything that has been printed (or included views)
$content = ob_get_contents();
ob_end_clean();
// finally, we show altogether
include 'views/base.php';
