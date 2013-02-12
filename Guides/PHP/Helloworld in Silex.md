# Deploying a Silex application
[Silex] is the PHP micro-framework based on the Symfony2 components.

In this tutorial we're going to show you how to deploy a Silex application on
[cloudControl]. You can find the [source code on Github][example-app].
Check out the [php buildpack] for supported features.


## Prerequisites
*   [cloudControl user account][cloudControl-doc-user]
*   [cloudControl command line client][cloudControl-doc-cmdline]
*   [git]


## Cloning a Hello World application
First, clone the hello world app from our repository:
~~~bash
$ git clone git://github.com/cloudControl/php-silex-example-app.git
$ cd php-silex-example-app
~~~

Now you have a small but fully functional Silex application.


## Dependency declaration with Composer
[Composer] requirements are read from `composer.json` in the project's root directory.

For this simple app the requirements are:
~~~json
{
    "minimum-stability": "dev",
    "require": {
        "silex/silex": "*",
        "twig/twig": "*",
        "mheap/Silex-Assetic": "*",
        "natxet/CssMin": "*"
    }
}
~~~

Note that there is also the `composer.lock`. When you change the dependencies,
you should run the `composer.phar update` command to update the `composer.lock`.
This file must be in your repository and ensures that all the developers always
use the same versions of all the libraries. It also makes the changes visible in git.

## Document root definition

The PHP buildpack allows the user to override the default document root. It is done via
configuration file in the `.buildpack/apache/conf/` directory. The file should use
`.conf` extension. In this tutorial the file is named `documentroot.conf` and has
the following content:
~~~conf
DocumentRoot /app/www/web
<Directory /app/www/web>
    AllowOverride All
    Options SymlinksIfOwnerMatch
    Order Deny,Allow
    Allow from All
    DirectoryIndex index.php index.html index.htm
</Directory>
~~~

For more information check out [the buildpack documentation][php buildpack].

## Pushing and deploying your app
Choose a unique name (from now on called APP_NAME) for your application and
create it on the cloudControl platform:
~~~bash
$ cctrlapp APP_NAME create php
~~~

Push your code to the application's repository. This will create a deployment image:
~~~
$ cctrlapp APP_NAME/default push
    Counting objects: 23, done.
    Delta compression using up to 8 threads.
    Compressing objects: 100% (15/15), done.
    Writing objects: 100% (23/23), 400.15 KiB | 244 KiB/s, done.
    Total 23 (delta 0), reused 0 (delta 0)
        
    -----> Receiving push
        Loading composer repositories with package information
        Installing dependencies from lock file
            - Installing symfony/finder (2.1.x-dev v2.1.7)
            Cloning v2.1.7
        
            ...
        
        symfony/routing suggests installing symfony/config (2.2.*)
        symfony/routing suggests installing symfony/yaml (2.2.*)
        symfony/routing suggests installing doctrine/common (~2.2)

        ...

    -----> Building image
    -----> Uploading image (8.4M)
        
    To ssh://APP_NAME@cloudcontrolled.com/repository.git
    * [new branch]      master -> master
~~~

Deploy the app on the pinky stack:
~~~bash
$ cctrlapp APP_NAME/default deploy --stack pinky
~~~

Congratulations, you should now be able to reach your application at `http://APP_NAME.cloudcontrolled.com`.


[silex]: http://silex.sensiolabs.org/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-user]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#user-accounts
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api "documentation of the cloudControl-command-line-client"
[php buildpack]: https://github.com/cloudControl/buildpack-php
[procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[git]: https://help.github.com/articles/set-up-git
[composer]: http://getcomposer.org/
[example-app]: https://github.com/cloudControl/php-silex-example-app
