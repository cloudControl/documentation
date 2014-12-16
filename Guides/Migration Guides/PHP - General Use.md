# PHP & PHP Worker Migration Guide
This guide is intended to help migrate a PHP app deployed on [dotCloud], to the [Next dotCloud] PaaS.

The Next dotCloud PaaS can host any kind of PHP5 based website, e.g:

* Frameworks, e.g Symfony and CakePHP
* Content Management Systems, e.g. Drupal, Joomla, WordPress and PHPBB
* and of course custom applications.

## Introduction
In many cases you can just push your code and deploy it. But there are some differences between the platforms that need to be considered.

* On Next dotCloud PaaS your PHP application relies on [Apache and Fast-CGI](#apache) to forward PHP requests to PHP-FPM.
* [Pecl extensions](#pecl) are not built during the push process.
* [Pear extensions](#pear) have to be loaded with [Composer].
* [PHP Worker](#php-worker)
* [Set timezone](#set-timezone)

## Apache
[Apache] uses a default configuration that listens on the platform assigned port. The document root is set up as a `<Directory>` reachable without access limitations and `AllowOverride All` set to enable the use of `.htaccess` files. The `DirectoryIndex` directive is set to `index.php index.html index.htm`.

### Setting the DocumentRoot
The `DocumentRoot` is the directory from which `httpd` will serve files, which is not necessarily the application root. By default the `DocumentRoot` of the web application is `/app/code`, where the repository content is extracted. The `DocumentRoot` can be modified in custom Apache configuration files. Below is an example of the Apache configuration file (e.g. `.buildpack/apache/conf/custom_document_root.conf`) specifying a custom `DocumentRoot` (in this case the `public` folder in your application root):
~~~xml
DocumentRoot "/app/code/public"
<Directory "/app/code/public">
    AllowOverride All
    Options SymlinksIfOwnerMatch
    Order Deny,Allow
    Allow from All
    DirectoryIndex index.php index.html index.htm
</Directory>
~~~
For more information check out the [buildpack documentation].

### .htaccess
> **You should avoid using `.htaccess` files completely. Using .htaccess files slows down your Apache http server.**
> **Any directive that you can include in a .htaccess file is better set in a Directory block, as it will have the same effect with better performance.**

You can set the configuration similar to [Setting the DocumentRoot](#setting-the-documentroot). For more information check out the [buildpack documentation].

Many PHP frameworks and applications require a custom Apache configuration to enable “pretty URLs” (that’s the name used by WordPress to designate this feature). Many of these frameworks provide `.htaccess` files by default. You can put the `.htaccess` files content into `.buildpack/apache/conf/*.conf` files and still use the Apache webserver's useful features, e.g:

* [Authorization, authentication](#authentcation)
    An apache configuration is often used to specify security restrictions for a directory, hence the filename "access". The authorization is often accompanied by a `.htpasswd` file which stores valid usernames and their passwords.

* [Blocking](#blocking-directory)
    Use allow/deny to block users by IP address or domain. Also, use to block bad bots, rippers and referrers. Often used to restrict access by Search Engine spiders

* [Rewriting URLs](#redirect-all-requests-to-index.php)
    Servers often use apache configuration to rewrite long, overly verbose URLs to shorter and more memorable ones.

* [Cache Control](#cache-control)
    The server sets the `Expires` HTTP header and the `max-age` directive of the `Cache-Control` HTTP header in server responses, to control the browser cache.

* SSI
    Enable server-side includes.

* Directory listing
    Control how the server will react when no specific web page is specified.

* Customized error responses
    Changing the page that is shown when a server-side error occurs, for example HTTP 404 Not Found or, to indicate to a search engine that a page has moved, HTTP 301 Moved Permanently.

* MIME types
    Instruct the server how to treat different varying file types.

Following examples should helps you migrate the nginx.conf to a apache config file.

#### Authentcation
~~~xml
<Directory "/app/code/protected">
  AuthType Basic
  AuthName "Authentication Required"
  AuthUserFile "/app/code/protected/.htpasswd"
  Require valid-user

  Order allow,deny
  Allow from all
</Directory>
~~~
This config blocks all requests to the protected directory. Only authenticated users are allowed to pass.

#### Blocking directory
~~~xml
 # Block External Access
<Directory "/app/code/system/">
    deny from all
</Directory>
~~~
This config blocks all requests to the system folder. You can only access through the console by `dcapp APP_NAME/default run bash`. For more information to access the console see: [Secure Shell].

#### Redirect all requests to index.php
~~~xml
<Directory "/app/code">
  RewriteEngine On
  RewriteCond $1 !^(index\.php|public|css|js|robots\.txt)
  RewriteRule ^(.*)$ index.php/$1 [L,QSA]
</Directory>
~~~
In this example all requests that don't point to a `public`, `css` or `js` folder or don't point to the `index.php` or `robots.txt` file will be redirected to index.php with all arguments.

#### Cache Control
You can control the cache by setting the `Cache-Control` header directly. In this example you **deactivate** caching.
~~~xml
<Directory "/app/code/web">
    Header Set Cache-Control "max-age=0, no-store"
</Directory>
~~~
For more information see [W3 Cache Control] section.

Or you can set the `max-age` directive of the `Cache-Control` header by setting the `ExpireDefault` and `ExpiresByType` Apache configuration:
~~~xml
<Directory "/app/code/static">
  ExpiresActive On
  ExpiresDefault "access plus 300 seconds"
  ExpiresByType text/html "access plus 1 day"
  ExpiresByType text/css "access plus 1 day"
  ExpiresByType text/javascript "access plus 1 day"
  ExpiresByType image/gif "access plus 1 month"
  ExpiresByType image/jpg "access plus 1 month"
  ExpiresByType image/png "access plus 1 month"
  ExpiresByType application/x-shockwave-flash "access plus 1 day"
</Directory>
~~~
For more information see [Apache mod_expire] documentation.

## Pecl
Next dotCloud PaaS provides many PHP extensions out of the box. This includes popular extensions like php5-mysql, php5-curl, and php5-imagemagick, and many more. The MongoDB database driver and the OAuth pecl extensions are installed as well.
For PHP extensions that are not available by default, you can use self-compiled PHP libraries on [Next dotCloud]. This blogpost shows how you can do this: [Using self-compiled PHP libraries on cloudControl]

## Pear
Pear dependencies should be handled by using [Composer]. This example will install code from `pear2.php.net`:
~~~json
{
    "repositories": [
        {
            "type": "pear",
            "url": "http://pear2.php.net"
        }
    ],
    "require": {
        "pear-pear2/PEAR2_Text_Markdown": "*",
        "pear-pear2/PEAR2_HTTP_Request": "*"
    }
}
~~~
The first section "`repositories`" will be used to let [Composer] know it should “initialise” (or “discover” in PEAR terminology) the pear repo. Then the require section will prefix the package name like this:
> pear-channel/Package

The “pear” prefix is hardcoded to avoid any conflicts, as a pear channel could be the same as another packages vendor name for example, then the channel short name (or full URL) can be used to reference which channel the package is in.

When this code is installed it will be available in your vendor directory and automatically available through the Composer autoloader:
>vendor/pear-pear2.php.net/PEAR2_HTTP_Request/pear2/HTTP/Request.php

To use this PEAR package simply reference it like so:
~~~php
<?php
$request = new pear2\HTTP\Request();
?>
~~~
Learn more about using [PEAR with Composer]

## PHP Worker
Tasks that will take longer than 55s to execute or should be handled asyncronously to not keep the user waiting, are best handled by the [Worker add-on]. Workers are long-running processes started in containers. Just like the web processes but they are not listening on any port and therefore do not receive http requests. More information to workers are available in the [Worker Add-on documentation].

Once you add the [Worker add-on] to your deployment, you have to create a `Procfile` in which you define the workers name and the command to run the worker. For example:
~~~yaml
web: bash boot.sh
myworker: php /app/code/myworker.php
~~~
The repository contents are located beneath `/app/code/`. Best use the absolute path to your workers file.

The worker files are part of the same deployment which contains the web service source code. Therefore, you have to define the web process too, which is in PHP per default `web: bash boot.sh`.

## Set timezone
Many PHP applications require a correctly configured timezone. To get this done, create a `.buildpack/php/conf/timezone.ini` file in your application root with:
~~~ini
[Date]
date.timezone = America/Los_Angeles
~~~
Choose your PHP timezone from the [list of supported timezones].

[dotCloud]: https://www.dotcloud.com/
[Next dotCloud]: https://next.dotcloud.com/
[Composer]: https://getcomposer.org/
[Apache]: http://httpd.apache.org/
[buildpack documentation]: https://github.com/cloudControl/buildpack-php
[W3 Cache Control]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
[Apache mod_expire]: http://httpd.apache.org/docs/2.2/mod/mod_expires.html 
[Secure Shell]: https://next.dotcloud.com/dev-center/platform-documentation#secure-shell-(ssh)
[Using self-compiled PHP libraries on cloudControl]: https://www.cloudcontrol.com/blog/self-compiled-php-libraries-cloudControl
[PEAR with Composer]: https://getcomposer.org/doc/05-repositories.md#pear
[Worker add-on]: https://next.dotcloud.com/add-ons/worker
[Worker Add-on documentation]: https://next.dotcloud.com/dev-center/add-on-documentation/worker
[list of supported timezones]: http://php.net/manual/en/timezones.php
