# Blackfire

[Blackfire](https://blackfire.io) automatically instruments your code to gather data about consumed server resources like memory, CPU time, and I/O.

## Adding Blackfire

The Blackfire add-on can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add blackfire.PLAN
~~~

**Note:** Currently there is only one plan available: free

When added, Blackfire automatically creates a user for the application and updates the addon configuration with its credentials.
You can access Blackfire for any deployment in the web console via Single Sign-On (SSO).
Navigate to the specific deployment, choose "Add-Ons" tab, click on "Settings" link and "Login to dashboard".

On Blackfire, first log-in with Github, Google+ or SensioLabsConnect. Then, the Single Sign-One link from cloudControl acts as an authorization layer.

**Note:** Blackfire requires a PHP extension to work. You can install it by adding 'ext-blackire' to the 'require' section of your 'composer.json' file:

~~~
$ composer require 'ext-blackfire:*'
~~~

After adding the add-on everything is set up for you. The next step is to set up the [browser companion](https://blackfire.io/doc/web-page) and learn how to [analyse your first profile](https://blackfire.io/doc/first-profile).

## Upgrade Blackfire

Upgrading to another plan of Blackfire is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade blackfire.PLAN_OLD blackfire.PLAN_NEW 
~~~

## Downgrade Blackfire

Downgrading to another plan of Blackfire is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade blackfire.PLAN_OLD blackfire.PLAN_NEW 
~~~

## Removing Blackfire

Removing Blackfire for any deployment is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove blackfire.PLAN
~~~

