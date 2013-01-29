# PHP buildpack - supported features

[PHP buildpack](https://github.com/cloudControl/buildpack-php) provides dependency management, framework detection and custom configuration for your PHP application.

## Composer - dependency management
[Composer](https://getcomposer.org/) is used to manage the dependencies. There should be a 'composer.json' file in the top directory of the project.

For example the 'composer.json' file, for a project that uses the Zend framework, would look like this:

~~~json
{
    "name": "application-name",
    "description": "Application's description",
    "license": "the-licence",
    "keywords": [
        "keyword1",
        "keyword2"
    ],
    "homepage": "http://example.com/",
    "require": {
        "php": ">=5.3.3",
        "zendframework/zendframework": "2.*"
    }
}
~~~

It is also possible to include composer executable (composer.phar) in the top directory of the project. In this case provided composer executable will be used instead of platform default one.

## Frameworks
The following frameworks are currently supported:

* [Symfony 1](http://symfony.com/legacy)
* [Symfony 2](http://symfony.com/)
* [Zend 1](http://framework.zend.com/)
* [Zend 2](http://framework.zend.com/)
* [Yii](http://www.yiiframework.com/)
* [Kohana](http://kohanaframework.org/)
* [CakePhp](http://www.cakephp.de/)

Other frameworks might work if you just
[specify the DocumentRoot](#manually-setting-the-documentroot) manually.

## Configuration
### Buildpack

You can place buildpack configuration in the `.buildpack` directory of your repository. Some influential variables can be set in the file `.buildpack/envrc`.

Currently supported variables are:

`COMPOSER_INSTALL_ARGS` to set additional arguments you want to pass to the composer install command.

Example .buildpack/envrc:

~~~bash
export COMPOSER_INSTALL_ARGS="--prefer-source --optimize-autoloader"
~~~

### Apache
For normal deployments the buildpack's default settings should work out of the
box. If you want to pass additional options to Apache, place them in files under
.buildpack/apache/conf directory. All files in this directory ending in .conf get included
at the end of Apache's httpd.conf.

#### Manually setting the DocumentRoot
By default the document root of the web application is '/app/www'. This can be modified in custom Apache configuration files too. Below is the example of the Apache configuration file specifying a custom [DocumentRoot](http://httpd.apache.org/docs/current/mod/core.html#documentroot) and [Directory](http://httpd.apache.org/docs/current/mod/core.html#directory):

    # If the webroot is /page/public in your project, the DocumentRoot will be
    # /app/www/page/public
    DocumentRoot /app/www/page/public
    # allow access to this directory (required)
    <Directory /app/www/page/public>
        AllowOverride All
        Options SymlinksIfOwnerMatch
        Order Deny,Allow
        Allow from All
        DirectoryIndex index.php index.html index.htm
    </Directory>

#### Create alias
Whenever need to map between URLs and file system paths not being under DocumentRoot specify [alias](http://httpd.apache.org/docs/2.2/mod/mod_alias.html#alias) and pass it in custom configuration file, e.g `.buildpack/apache/conf/sf_alias.conf`:

    #Create alias for symfony resources
    Alias /sf /app/www/lib/vendor/symfony/data/web/sf
    <Directory /app/www/lib/vendor/symfony/data/web/sf>
        AllowOverride All
        Options SymlinksIfOwnerMatch
        Order Deny,Allow
        Allow from All
    </Directory>

### PHP
Similarly, the default PHP configuration can be overridden or extended by specifying custom configuration files in .buildpack/php/conf directory. They should follow the PHP config syntax and should have an '.ini' extension, e.g:

	[MySQL]
	mysql.allow_persistent = On
	mysql.max_persistent = -1
	mysql.max_links = -1
	mysql.connect_timeout = 60
	mysql.trace_mode = Off

	[MySQLi]
	mysqli.max_links = -1
	mysqli.default_port = 3306
	mysqli.default_host = 127.0.0.1
	mysqli.reconnect = Off

	[APC]
	apc.stat = 1
	apc.enabled = 0
	apc.shm_size = 27M
