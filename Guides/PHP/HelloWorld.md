#Deploying a Silex application on exoscale

[Silex] is a PHP microframework for PHP 5.3. It is inspired by sinatra and
built on the shoulders of Symfony2 and Pimple.

In this tutorial we're going to show you how to deploy a Silex application on
[exoscale]. You can find the [source code on Github][example-app] and check
out the [php buildpack] for supported features.


##The Silex App Explained

###Get the App
First, let’s clone the Silex App from our repository on Github:
~~~bash
$ git clone https://github.com/cloudControl/php-silex-example-app.git
$ cd php-silex-example-app
~~~

Now you have a small but fully functional Silex application.


### Dependency Tracking
The PHP buildpack tracks dependencies via [Composer]. Requirements are read
from `composer.json` in the project's root directory. For this simple app the
requirements are:

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
you should run the `composer.phar update` command to update the
`composer.lock`.  This file must be in your repository and ensures that all the
developers always use the same versions of all the libraries. It also makes the
changes visible in git. Also note that your `.gitignore` should contain
`vendor` as proposed in the 
[Composer documentation](http://getcomposer.org/doc/01-basic-usage.md#installing-dependencies),
since you don't need all that code in your repository.


## Document Root Definition

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

## Pushing and Deploying the App
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the exoscale platform:
~~~bash
$ exoapp APP_NAME create php
~~~

Push your code to the application's repository, which triggers the deployment image build process:
~~~
$ exoapp APP_NAME/default push
Counting objects: 29, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (21/21), done.
Writing objects: 100% (29/29), 459.72 KiB | 720 KiB/s, done.
Total 29 (delta 5), reused 18 (delta 0)

-----> Receiving push
       Loading composer repositories with package information
       Installing dependencies (including require-dev) from lock file
         - Installing symfony/process (dev-master 998d489)
           Cloning 998d489806011e1d790db5fc0284e6083cc8ea8b

         - Installing kriswallsmith/assetic (dev-master f9f754d)
           Cloning f9f754dc7524acd6daf0bf510d22c055b4967e08

         - Installing symfony/finder (dev-master 57b6772)
           Cloning 57b67729f863be8b950441a739b82678b91accde

           ...

       Generating autoload files
-----> Building image
-----> Uploading image (14M)

To ssh://APP_NAME@app.exo.io/repository.git
* [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the exoapp deploy command:
~~~bash
$ exoapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Silex app running at `http[s]://APP_NAME.app.exo.io`.


[silex]: http://silex.sensiolabs.org/
[exoscale]: http://www.exoscale.ch
[exoscale-doc-user]: https://www.exoscale.ch/dev-center/Platform%20Documentation#user-accounts
[exoscale-doc-cmdline]: https://www.exoscale.ch/dev-center/Platform%20Documentation#command-line-client-web-console-and-api "documentation of the exoscale-command-line-client"
[php buildpack]: https://github.com/cloudControl/buildpack-php
[procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[git]: https://help.github.com/articles/set-up-git
[composer]: http://getcomposer.org/
[example-app]: https://github.com/cloudControl/php-silex-example-app
