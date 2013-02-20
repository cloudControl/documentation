# AWS S3 Guide for PHP

[AWS Simple Storage Service (S3)](http://aws.amazon.com/s3/) is a web service to store and retrieve data. S3 or similiar services are a key building block for scalable Cloud applications and perfectly complement cloudControl's [non persistent filesystem](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#non-persistent-filesystem).

 The single, downloadable package includes the AWS PHP Library and documentation.

### Getting Started

Amazon provides a [PHP SDK](https://github.com/aws/aws-sdk-php) to simplify the Amazon Web Services (AWS) usage. The SDK helps to remove the complexity from the code with a powerful suite of PHP classes for many AWS services including Amazon S3. This guide shows how to store data from PHP applications on S3 using the PHP SDK.

For obvious reasons this guide requires you to have an S3 account. Go ahead and [register](https://portal.aws.amazon.com/gp/aws/developer/subscription/index.html?productCode=AmazonS3) for a free one if you haven't already.

### Example App Using Amazon S3

The example app lists buckets and the files names (ids) inside a bucket. Uploading or viewing the content of a file functionalities have been omitted because the app is not protected by any form of authentication. The exaplce code is based on the Silex framework and is ready to be deployed on the [cloudControl PaaS](https://www.cloudcontrol.com).

Let's clone the example code from Github.

~~~bash
$ git clone git://github.com/cloudControl/php-s3-example-app.git
$ cd php-s3-example-app
~~~

#### Tracking Dependencies

The example app specifies the required dependencies in the `composer.json` file.

~~~json
{
    "require": {
        "aws/aws-sdk-php": "2.*",
        "silex/silex": "1.0.*@dev",
        "symfony/form": "2.1.*",
        "symfony/twig-bridge": "2.1.*",
        "symfony/translation": "2.1.*"
    }
}
~~~

To install the dependencies locally install and run Composer. Please note how the `vendor` directory is ignored via the `.gitignore` file because the [PHP buildpack](https://github.com/cloudControl/buildpack-php) will pull in the dependencies via [Composer](http://getcomposer.org/) automatically during the push later.

~~~bash
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
~~~

#### Example Application Logic

The simple application logic to list buckets or files lives in `public/index.php`. The code is easy to read and mostly self explanatory.

~~~php
<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Aws\Common\Aws;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Exception\S3Exception;

$app = new Silex\Application();

$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

/**
 * List Buckets
*/
$app->get('/', function () use ($app) {
    $s3 = Aws::factory('../awsconfig.php')->get('s3');
    $buckets = array();
    foreach ($s3->getIterator('ListBuckets') as $bucket) {
        array_push($buckets, $bucket);
    }
    return $app['twig']->render('index.twig', array('buckets' => $buckets));
});

/**
 * List Single Bucket Objects
*/
$app->get('/list/{bucketname}', function ($bucketname) use ($app) {
    $s3 = Aws::factory('../awsconfig.php')->get('s3');
    $objects = array();
    foreach ($s3->getIterator('ListObjects', array('Bucket' => $bucketname)) as $object) {
        array_push($objects, $object);
    }
    return $app['twig']->render('list.twig', array(
        'bucketname' => $bucketname,
        'objects' => $objects));
});

$app->run();
~~~

#### Reading S3 Credentials from Environment

To access your S3 account you obviously need to have your AWS credentials available to the app.

On cloudControl it's highly recommended to not have credentials in the repository but instead have them as part of the environment. We'll set the credentials via the [Custom Config Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config) later.

For now, let's take a look at the file `awsconfig.php` that will read the `AWS_KEY`, `AWS_SECRET_KEY` and `AWS_REGION` credentials from the environment once the application has been deployed.

~~~php
<?php
$aws_key = '';
$aws_secret_key = '';
$aws_region = '';

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
    $aws_key = $creds['CONFIG']['CONFIG_VARS']['AWS_KEY'];
    $aws_secret_key = $creds['CONFIG']['CONFIG_VARS']['AWS_SECRET_KEY'];
    $aws_region = $creds['CONFIG']['CONFIG_VARS']['AWS_REGION'];
}

return array(
    'includes' => array('_aws'),
    'services' => array(
        'default_settings' => array(
            'params' => array(
                'key'    => $aws_key,
                'secret' => $aws_secret_key,
                'region' => $aws_region
            )
        )
    )
);
~~~

### Deploy the App

Let's create a new application, push it and deploy it. As always replace `APP_NAME` with a creative and unique name of your choice.

~~~bash
$ cctrlapp APP_NAME create php
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy --stack pinky
~~~

Now use the Custom Config Add-on to add the AWS variables to your environment. Replace `YOUR_AWS_KEY`, `YOUR_AWS_SECRET_KEY` with the respective values from your AWS account.

~~~bash
$ cctrlapp APP_NAME/default addon.add config.free --AWS_KEY=YOUR_AWS_KEY --AWS_SECRET_KEY=YOUR_AWS_SECRET_KEY --AWS_REGION="eu-west-1"
~~~

Finally checkout your app under `http://APP_NAME.cloudcontrolled.com`.

Tip: If you plan to use the S3 bucket for anything else at a later point in time it's probably a good idea to delete the app and not have all current and future bucket and file names publicly available.

~~~bash
$ cctrlapp APP_NAME/default undeploy
Do you really want to delete this deployment? This will delete everything including files and the database. Type "Yes" without the quotes to delete: Yes
$ cctrlapp APP_NAME delete
Do you really want to delete this application? Type "Yes" without the quotes to delete: Yes
~~~
