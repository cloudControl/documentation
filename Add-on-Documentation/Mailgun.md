# Mailgun

Mailgun is a complete email platform: optimized outbound message delivery, inbound email push into your app, real time email analytics and more.

## Adding Mailgun

Mailgun can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add mailgun.OPTION
~~~

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "MAILGUN":{
      "MAILGUN_SMTP_LOGIN":"postmaster@cloud.mailgun.org",
      "MAILGUN_SMTP_SERVER":"smtp.mailgun.org",
      "MAILGUN_SMTP_PORT":"587",
      "MAILGUN_SMTP_PASSWORD":"1337asdf1337ASDF",
      "MAILGUN_API_KEY":"key-12341337ASDF345567-qwert13373"
   }
}
~~~

## Upgrade Mailgun

Upgrading to another version of Mailgun is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade mailgun.OPTION_OLD mailgun.OPTION_NEW 
~~~

## Downgrade Mailgun

Downgrading to another version of Mailgun is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade mailgun.OPTION_OLD mailgun.OPTION_NEW 
~~~

## Removing Mailgun

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove mailgun.OPTION
~~~

## Mailgun Code Example

Here is an example on how to use Mailgun in a Zend setup.

## Step 1: Installing Zend

Let's use the [Zend framework](http://framework.zend.com/download/latest) to send our first mail. This example assumes that you've downloaded and extracted it to the vendor folder inside the main directory - so you should get similar output if you use *nix:

~~~
$ cd ~/cctrl_tutorial_app/
$ ~/cctrl_tutorial_app: ls vendor/Zend/
Acl Cache Crypt Dojo Form Layout Log.php Mime.php Pdf Search Test Validate
~~~

To make Zend usable, we need to add the vendor folder to the PHP search path of our application:

~~~
<?php
// somewhere at the start of index.php:
// update include path to include our vendor folder
$path = dirname(__DIR__) . '/vendor/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
?>
~~~

## Step 2: Getting access to Mailgun credentials

When you install the addon, Mailgun populates your app environment with the access credentials. Here's how you access them from the source code:

~~~
<
// Parse the json file with ADDONS credentials
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

$creds = json_decode($string, true);
>
~~~

## Step 3: Configure Zend to use Mailgun by default

~~~
<?php
$config = array('ssl' => 'tls', 
     'port' => $creds['MAILGUN']['MAILGUN_SMTP_PORT'], 
     'auth' => 'login', 
     'username' => $creds['MAILGUN']['MAILGUN_SMTP_LOGIN'],
     'password' => $creds['MAILGUN']['MAILGUN_SMTP_PASSWORD']);

$transport = new Zend_Mail_Transport_Smtp($creds['MAILGUN']['MAILGUN_SMTP_SERVER'], $config);
Zend_Mail::setDefaultTransport($transport);
?>
~~~

## Step 4: Send!

~~~
<?php
// Here we go, now you can send
$mail = new Zend_Mail();
$mail->setBodyText('This is the text of the mail.');
$mail->setFrom('somebody@yourdomain.com', 'Some Sender');
$mail->addTo('klizhentas@gmail.com', 'Some Recipient');
$mail->setSubject('TestSubject');
$mail->send();
?>
~~~

## Putting it all together

~~~
<?php

// update include path to include our vendor folder
$path = dirname(__DIR__) . '/vendor/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

// Now we can safely include Zend
require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';

// Parse the json file with ADDONS credentials
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

$creds = json_decode($string, true);

// Configure Zend to use mailgun
$config = array('ssl' => 'tls', 
    'port' => $creds['MAILGUN']['MAILGUN_SMTP_PORT'], 
    'auth' => 'login', 
    'username' => $creds['MAILGUN']['MAILGUN_SMTP_LOGIN'],
    'password' => $creds['MAILGUN']['MAILGUN_SMTP_PASSWORD']);

$transport = new Zend_Mail_Transport_Smtp($creds['MAILGUN']['MAILGUN_SMTP_SERVER'], $config);
Zend_Mail::setDefaultTransport($transport);

// Here we go, now you can send
$mail = new Zend_Mail();
$mail->setBodyText('This is the text of the mail.');
$mail->setFrom('somebody@yourdomain.com', 'Some Sender');
$mail->addTo('klizhentas@gmail.com', 'Some Recipient');
$mail->setSubject('TestSubject');
$mail->send();
?>
~~~

Read more on sending in Mailguns [documentation](http://documentation.mailgun.net/user_manual.html#sending-messages).

## Receiving Emails
There are two ways to handle incoming messages using Mailgun:

* Forward incoming messages using Routes to a URL or to another email address. 
* Store incoming messages in Mailboxes. 

In other words, Mailgun can POST each incoming message to your application as if someone submitted a form with email contents. This is particularly appealing because message content comes in as UTF-8, signature and quoted content are extracted, attachments arrive as file uploads.

Here's the simple script in PHP that shows how to process an incoming message posted by Mailgun: 

~~~
<?php

// get sender, recipient fields
$from = $_REQUEST['from'];
$recipient = $_REQUEST['recipient'];

// grab text and html parts
$text = $_REQUEST['body-plain'];
$html = $_REQUEST['body-html'];

// get file attachments (if any)
foreach($_FILES as $file){
    $name = $file['name'];
    $type = $file['type'];
}

?>
~~~

Read more on receiving messages [here](http://documentation.mailgun.net/user_manual.html#receiving-messages).

