# cloudControl Quickstart

It's easy to start with cloudControl. You will have an running application on cloudControl within 5 minutes when you follow this guide.

## Install the Required Software 
* Install git ([quick Git tutorial](http://rogerdudler.github.com/git-guide/))
* Linux/Mac OS X: Open a Terminal window and enter the following command to install the command line client
~~~
$ sudo easy_install cctrl
~~~

* Windows: Please use our [installer](https://github.com/cloudControl/cctrl/downloads) to install the command line client

## Create an User Account

* Open a terminal (Mac OS X/Linux) or a command line window (Windows)
* Create an user account with the command 
~~~
$ cctrluser create
~~~


* An activation code will be sent to the eMail account you've entered.
* Activate your user account with 
~~~
$ cctrluser activate USERNAME ACTIVATION_CODE
~~~ 
Replace *USERNAME* and *ACTIVATION_CODE* accordingly with your information.
* Upload your public SSH key with 
~~~
$ cctrluser key.add
~~~


## Create a new app on cloudControl

* Select the name of your app. Only letters and numbers are allowed. Remember the name and replace all following occurrences of *APP_NAME* with that name of your choice.
* Create a new PHP application on the cloudControl platform with 
~~~ 
$ cctrlapp APP_NAME create php
~~~
If the APP_NAME is already taken, please pick another one.

* Create a folder `myfirstproject` somewhere on your computer.
* Create a plain text file `index.php` in that folder with the following content
~~~
<?php 
     echo "Hello world!";
     phpinfo();
?>
~~~

* Open a terminal (Mac OS X/Linux) or Git Bash (Windows)
* Change in to your project's directory: 
~~~
$ cd PATHTO/myfistproject
~~~

* Init git repository, add files, commit and push the changes
~~~
$ git init
$ git add .
$ git commit -m “This is my first app.“
$ cctrlapp APP_NAME push
~~~

## Deploy and access your app on cloudControl
* Deploy your app with 
~~~
$ cctrlapp APP_NAME deploy
~~~

* You can access now your application using the adress 
~~~ 
http://APP_NAME.cloudcontrolled.com
~~~

## Cheatsheet
Grab [our cheatsheet (PDF)](http://example.org) to have an overview over all commands of the command line client.

  