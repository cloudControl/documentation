# dotCloud Quickstart

It's easy to start with dotCloud. Follow this 5 minute quickstart to get your first app running on dotCloud PaaS.

**Note:** All examples starting with $ are supposed to be run in a terminal. For Windows we recommend using Git bash, which comes bundled with the Windows Git installer. Throughout this quickstart and the rest of the documentation placeholders are marked by being written all uppercase.

## Install the Required Software

### Requirements

* git version control system
* dotcloudng command line client

### Install git

Install Git from the [official site](http://git-scm.com/) or your package repository of choice. For Windows it's recommended to use the official installer and Git bash. Come back when you are done.

### Install dotcloudng

**Linux/Mac OS X:** We recommend installing dotcloudng via pip.

~~~bash
# if you don't have pip yet
$ sudo easy_install pip
$ sudo pip install dotcloudng
~~~

**Windows:** Please download the provided [installer](https://download.dotcloudapp.com/windows).

## Create a User Account (if you haven't already)

You can register on the website or directly from the command line. Provide the required values when prompted.

~~~bash
$ dcuser create
Username: USERNAME
Email   : EMAIL
Password: PASSWORD
Password (again): PASSWORD
User has been created. Please check you e-mail for your confirmation code.
~~~

Activate your user account with the activate command.

~~~bash
$ dcuser activate USERNAME ACTIVATION_CODE
~~~

Replace `USERNAME` and `ACTIVATION_CODE` with the values form the activation e-mail. If you didn't receive one, double check the spelling of your e-mail address or check your SPAM folder.

## Add a Public Key

~~~bash
$ dcuser key.add
Email   : EMAIL
Password: PASSWORD
~~~

The command line client will determine if you already have a public key and upload that or offer to create one.

## Create the First Application on dotCloud

Create a new application on the dotCloud platform by giving it an unique `APP_NAME` (the name is used as the `.cloudcontrolled.com` subdomain) and choosing the `TYPE`.

~~~bash
$ dcapp APP_NAME create [java, php, python, ruby, nodejs]
~~~

If the `APP_NAME` is already taken, please pick another one.

Change to the working directory where you want to store your source code.

~~~bash
$ cd PATH_TO/YOUR_WORKDIR
~~~

Clone one of the example apps in your preferred programming language and push it to the dotCloud platform.

~~~bash
# for Java
$ git clone https://github.com/cloudControl/java-jetty-example-app.git
$ cd java-jetty-example-app

# for PHP
$ git clone https://github.com/cloudControl/php-silex-example-app.git
$ cd php-silex-example-app

# for Python
$ git clone https://github.com/cloudControl/python-flask-example-app.git
$ cd python-flask-example-app

# for Ruby
$ git clone https://github.com/cloudControl/ruby-sinatra-example-app.git
$ cd ruby-sinatra-example-app

# for Node.js
$ git clone https://github.com/cloudControl/nodejs-express-example-app.git
$ cd nodejs-express-example-app

# now push
$ dcapp APP_NAME push
~~~

The push fires a hook that prepares your application for deployment like pulling in requirements and more. You can see the output of the build process in your terminal.

## Deploy Your Application on dotCloud

Deploy your app with

~~~bash
$ dcapp APP_NAME deploy
~~~

**Congratulations, your app is now up and running.**

~~~bash
http[s]://APP_NAME.cloudcontrolled.com
~~~

## Cheatsheet

Grab [our cheatsheet (PDF)](/dev-center/dotcloudng_cheatsheet.pdf) to have the most important command line client commands handy at all times.

## Documentation

To learn more about all the platform features and how to integrate it seamlessly into the development life cycle please refer to the extensive [platform documentation](https://next.dotcloud.com/dev-center/platform-documentation).
