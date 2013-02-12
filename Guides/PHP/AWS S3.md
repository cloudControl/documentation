# PHP AWS S3 - data storage solution

If your application is hosted on a horizontally scaled network, you might need to store the data in one centralized place.
E.g. if you want to store user-created images, it makes no sense to store them on the file system, as the images created on one node are not available on another node.

A handy solution for this problem is using a storage service provider:

_"A Storage Service Provider (SSP) is any company that provides computer storage space and related management services. SSPs also offer periodic backup and archiving.
Advantages of managed storage are that more space can be ordered as required. Depending upon your SSP, backups may also be managed. Faster data access can be ordered as required. Also, maintenance costs may be reduced, particularly for larger organizations who store a large or increasing volumes of data. Another advantage is that best practices are likely to be followed. Disadvantages are that the cost may be prohibitive, for small organizations or individuals who deal with smaller amounts or static volumes of data and that there's less control of data systems."_ -- _[Storage service provider on wikipedia](http://en.wikipedia.org/wiki/Storage_service_provider)_

We recommend using [Amazon Simple Storage Service (Amazon S3)](http://aws.amazon.com/en/s3/).

_"Amazon S3 provides a simple web services interface that can be used to store and retrieve any amount of data, at any time, from anywhere on the web. It gives any developer access to the same highly scalable, reliable, secure, fast, inexpensive infrastructure that Amazon uses to run its own global network of web sites. The service aims to maximize benefits of scale and to pass those benefits on to developers."_ -- [Amazon S3](http://aws.amazon.com/en/s3/)

## PHP Amazon S3 Integration

Amazon provides SDK to simplify the Amazon Web Services (AWS) usage. The SDK helps to remove the complexity from the code with a powerful suite of PHP classes for many AWS services including Amazon S3. The single, downloadable package includes the AWS PHP Library and documentation.

### Getting Started

For installation and basic usage read the [Amazon SDK for PHP 2 documentation](https://github.com/aws/aws-sdk-php). This guide shows how to start building PHP applications on the Amazon Web Services platform with the AWS SDK for PHP 2.

### Example App Using Amazon S3

In this example app you can list all your buckets, list the keys of a bucket and upload a file to a bucket. We use the Silex framework to simplify some steps. When the application is fully implemented, you can deploy it on the cloudControl platform to see how it's working.

In the root folder create `composer.json` file with the following content:
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

If you don't have the composer already, you need to download it:
~~~bash
$ curl -s https://getcomposer.org/installer | php
~~~
and install the requirements:
~~~bash
$ php composer.phar install
~~~

Next, create a public folder and the file called `public/index.php`:
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

/**
 * Upload File To S3
*/
$app->match('/upload/{bucketname}', function (Request $request, $bucketname) use ($app) {
    $form = $app['form.factory']->createBuilder('form')
    ->add('fileUpload', 'file', array('label' => " "))
    ->getForm();

    if ('POST' == $request->getMethod()) {
        $form->bind($request);
        if ($form->isValid()) {
            $data = $form->getData();
            // do something with the data
            $file = $data['fileUpload'];
            if ($file->isValid()){
                $s3 = Aws::factory('../awsconfig.php')->get('s3');
                $s3->putObject(array(
                    'Bucket' => $bucketname,
                    'Key'    => $file->getClientOriginalName(),
                    'Body'   => fopen($file->getPathname(), 'r'),
                    'ACL'    => CannedAcl::PUBLIC_READ
                ));
            }
            return $app->redirect('/');
        }
    }
    return $app['twig']->render('upload.twig', array(
        'bucketname' => $bucketname,
        'form' => $form->createView()));
});

$app->run();
~~~

Now you need the views. Create a folder `views` that contains `views/index.twig`:
~~~html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head></head>
  <body>
    <h1>Your Current Bucket List</h1>
    {% for bucket in buckets %}
      <a href="/upload/{{ bucket['Name'] }}">[U]</a> - <a href="/list/{{ bucket['Name'] }}">{{ bucket['Name'] }}</a><br/>
    {% endfor %}
    </br>
    <span style="font-size:90%">Click on [U] to upload a file to the bucket.
  </body>
</html>
~~~

`views/list.twig`:
~~~html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head></head>
  <body>
    <h1>Your Buckets "{{ bucketname }}" Object List</h1>
    {% for object in objects %}
      {{ object['Key'] }}<br/>
    {% endfor %}
  </body>
</html>
~~~

and `views/upload.twig`:
~~~html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head></head>
  <body>
    <h1>Upload a File to "{{ bucketname }}"</h1>
    <form action="/upload/{{ bucketname }}" method="post" {{ form_enctype(form) }}>
        {{ form_widget(form) }}
        <input type="submit" value="{{ 'Send'|trans }}">
    </form>
  </body>
</html>
~~~


#### Amazon credentials
To connect to your AWS account you need to have your AWS credentials available in the app.

You shouldn't hard-code the credentials as they will appear in your code repository.
The proper way to solve this is to have them as variables available in the environment.
This way your credentials are tied to the deployment and not to the code.

For now, let's create a file `awsconfig.php` with the following content:
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
In the code listed above, we read the values for AWS keys and region from credentials file.
The credentials file itself will be populated later, when we create the app on the platform, and expose the following variables: 'AWS_KEY', 'AWS_SECRET_KEY' and 'AWS_REGION'.

Next, to route all requests to `public/index.php` create a file `public/.htaccess` with:
~~~bash
RewriteEngine On
RewriteRule ^.*$ index.php [NC,L]
~~~

You have to set the document root to the `public`. Create the directory structure and the file `.buildpack/apache/conf/custom_document_root.conf` with the following content:
~~~
DocumentRoot /app/www/public
<Directory /app/www/public>
    AllowOverride All
    Options SymlinksIfOwnerMatch
    Order Deny,Allow
    Allow from All
    DirectoryIndex index.php index.html index.htm
</Directory>
~~~

### Deploy the App

CloudControl will download the required libraries, so you need to git-ignore the `vendor` folder:
~~~bash
$ echo "vendor/*" >> .gitignore
~~~

Use git:
~~~bash
$ git init
$ git add -A
$ git commit -am "Initial commit"
~~~

In this example we are using the application name's placeholder APP_NAME. You will of course have to use some other name instead.
Create a new cloudControl application with your cloudControl account.
~~~bash
$ cctrlapp APP_NAME create php
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default deploy --stack pinky
~~~

Don't forget to use the [Custom Config Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config)
to add the AWS variables to your environment:
~~~bash
$ cctrlapp APP_NAME/default addon.add config.free --AWS_KEY=YOUR_AWS_KEY --AWS_SECRET_KEY=YOUR_AWS_SECRET_KEY --AWS_REGION=YOUR_AWS_REGION
~~~

That's all, now you can list your buckets at APP_NAME.cloudcontrolled.com. You can also upload files or list the bucket objects via links.

Have fun :-)
