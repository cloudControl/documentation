# MemCachier Add-on

MemCachier manages and scales clusters of memcached servers so you can focus on your app. Tell us how much memory you need and get started for free instantly. Add capacity later as you need it.

## Add the MemCachier Add-on

The MemCachier Add-on comes in different [sizes and prices](https://www.cloudcontrol.com/add-ons/memcachier). It can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.OPTION
~~~
".option" represents the package size, e.g. memcachier.25mb

## Example using the MemCachier Add-on

## Authentification

The authentification has to be done with:

~~~
<?php
    $m->configureSasl("MEMCACHIER_USERNAME", "MEMCACHIER_PASSWORD");
?>
~~~

or alternatively with:

~~~
<?php
    $m->setOption(Memcached::OPT_BINARY_PROTOCOL, 1);
    $m->setSaslData("MEMCACHIER_USERNAME", "MEMCACHIER_PASSWORD");
?>
~~~

configureSasl() automatically sets the binary protocol, while it has to be set manually with the alternative method.

The two paramaters MEMCACHIER_USERNAME and MEMCACHIER_PASSWORD can be retrieved with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon memcachier.OPTION
Addon                    : memcachier.25mb
Settings
MEMCACHIER_SERVERS        : mc1.eu.ec2.memcachier.com
MEMCACHIER_USERNAME        : Yh87AGW3EkvhGkJ3L25q9QX
MEMCACHIER_PASSWORD        : Yh87AGW3EkvhGkJ3L25q9QX
~~~

More information on how to use php-memcached can be found on [php.net](http://php.net/manual/en/book.memcached.php).

## MemCachier example

Memcached provided by MemCachier can be used like this:

~~~
<?php
     $string = file_get_contents($_ENV['CRED_FILE'], false);
    if ($string == false) {
        die('FATAL: Could not read credentials file');
    }

    $creds = json_decode($string, true);

    # ['MEMCACHIER_SERVERS', 'MEMCACHIER_USERNAME', 'MEMCACHIER_PASSWORD']
    $config = array(
        'SERVERS' => $creds['MEMCACHIER']['MEMCACHIER_SERVERS'],
        'USER' => $creds['MEMCACHIER']['MEMCACHIER_USERNAME'],  
        'PSWD' => $creds['MEMCACHIER']['MEMCACHIER_PASSWORD'],
    );

    $m = new Memcached();
    $m->setOption(Memcached::OPT_BINARY_PROTOCOL, 1);
    $m->setSaslData($config['USER'], $config['PSWD']);
    $m->addServer($config['SERVERS'], 11211);
    $current_count = (int) $m->get($_SERVER['HTTP_X_FORWARDED_FOR']);
    $current_count += 1;
    $m->set($_SERVER['HTTP_X_FORWARDED_FOR'], $current_count);
?>
<html>
<head>
<title>Memcachier Example</title>
</head>
<body>
<h1>Hello <?php print $_SERVER['HTTP_X_FORWARDED_FOR'] ?>!</h1>
<p>This is visit number <?php print $current_count ?>.</p>
</body>
</html>
~~~

