# cloudControl Quickstart

It's easy to start with cloudControl. Follow this 5 minute quickstart to get your first app running on cloudControl PaaS.

**Note:** All examples starting with $ are supposed to be run in a terminal. For Windows we recommend using Git bash, which comes bundled with the Windows Git installer. Throughout this quickstart and the rest of the documentation placeholders are marked by being written all uppercase.

## Install the Required Software

### Requirements

* git version control system
* cctrl command line client

### Install git

Install Git from the [official site](http://git-scm.com/) or your package repository of choice. For Windows it's recommended to use the official installer and Git bash. Come back when you are done.

### Install cctrl

**Linux/Mac OS X:** We recommend installing cctrl via pip.

~~~bash
# if you don't have pip yet
$ sudo easy_install pip
$ sudo pip install cctrl
~~~

**Windows:** Please download the provided [installer](http://cctrl.s3-website-eu-west-1.amazonaws.com/#windows/). The file is named cctrl-x.x-setup.exe.

## Create a User Account (if you haven't already)

You can register on the website or directly from the command line. Provide the required values when prompted.

~~~bash
$ cctrluser create
Username: USERNAME
Email   : EMAIL
Password: PASSWORD
Password (again): PASSWORD
User has been created. Please check you e-mail for your confirmation code.
~~~

Activate your user account with the activate command.

~~~bash
$ cctrluser activate USERNAME ACTIVATION_CODE
~~~

Replace `USERNAME` and `ACTIVATION_CODE` with the values form the activation e-mail. If you didn't receive one, double check the spelling of your e-mail address or check your SPAM folder.

## Add a Public Key

~~~bash
$ cctrluser key.add
Email   : EMAIL
Password: PASSWORD
~~~

The command line client will determine if you already have a public key and upload that or offer to create one.

## Create the First Application on cloudControl

Create a new application on the cloudControl platform by giving it an unique `APP_NAME` (the name is used as the `.cloudcontrolled.com` subdomain) and choosing the `TYPE`.

~~~bash
$ cctrlapp APP_NAME create [java, php, python, ruby]
~~~

If the `APP_NAME` is already taken, please pick another one.

Change to the working directory where you want to store your source code.

~~~bash
$ cd PATH_TO/YOUR_WORKDIR
~~~

Clone one of the example apps in your preferred programming language and push it to the cloudControl platform.

~~~bash
# for Java
$ git clone git://github.com/cloudControl/java_hello_world_app.git
$ cd java_hello_world_app

# for PHP
$ git clone git://github.com/cloudControl/php_hello_world_app.git
$ cd php_hello_world_app

# for Python
$ git clone git://github.com/cloudControl/python_hello_world_app.git
$ cd python_hello_world_app

# for Ruby
$ git clone git://github.com/cloudControl/ruby_hello_world_app.git
$ cd ruby_hello_world_app

# now push
$ cctrlapp APP_NAME push
~~~

The push fires a hook that prepares your application for deployment like pulling in requirements and more. You can see the output of the build process in your terminal.

## Deploy Your Application on cloudControl

Deploy your app with

~~~bash
$ cctrlapp APP_NAME deploy
~~~

**Congratulations, your app is now up and running.**

~~~bash
http[s]://APP_NAME.cloudcontrolled.com
~~~

## Cheatsheet

Grab [our cheatsheet (PDF)](https://www.cloudcontrol.com/dev-center/cctrl_cheatsheet.pdf) to have the most important command line client commands handy at all times.

## Documentation

To learn more about all the platform features and how to integrate it seamlessly into the development life cycle please refer to the extensive [platform documentation](https://www.cloudcontrol.com/dev-center/Platform%20Documentation).
