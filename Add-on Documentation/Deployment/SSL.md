# SSL Add-on

SSL encryption is available for improved security when transmitting passwords and other sensitive data. As part of the provided `.cloudcontrolled.com` subdomain all deployments have access to piggyback SSL using a `*.cloudconrolled.com` wildcard certificate. To use this, simply point your browser to `https://APP_NAME.cloudcontrolled.com` for the default deployment or to `https://DEP_NAME-APP_NAME.cloudcontrolled.com` for non-default deployments (please note the dash between DEP_NAME and APP_NAME). SSL support for custom domains is available through the SSL add-on.

## Custom Domain Certificates

To enable SSL support for custom domains like `www.example.com` or `secure.example.com` you need the SSL add-on. Root or naked domains like `example.com` are not supported.

Currently the SSL add-on is not fully automated but needs manual approval by one of our support engineers.

Please follow the following simple steps to add SSL support to your deployment.

 1. Acquire a signed certificate from your certificate authority of trust.
 2. Ensure the key is not protected by a passphrase.
 3. Add the SSL addon providing the certificate, the private key and certificate-chain files.

### Adding the SSL addon

To add the SSL addon simply provide the aforementioned files using the respective parameters of the addon.add command.
 ~~~
 $ cctrlapp APP_NAME/DEP_NAME addon.add ssl.host --cert CERT_FILE --key KEY_FILE --chain CHAIN_FILE
 ~~~

In order to check the status of the addon you can do the following.
 ~~~
 $ cctrlapp APP_NAME/DEP_NAME addon ssl.host
 ~~~


## HTTPS Redirects


HTTPS termination is done at the routing tier. Requests are then routed via HTTP to one of your app's clones. To determine if a request was made via HTTPS originally the routing tier sets the `X-FORWARDED-PROTO` header to `https`. The header is only set for requests that arrived via HTTPS at the routing tier. This allows you to redirect accordingly.

### PHP Example

For PHP you can either redirect via Apache's mod_rewrite using a `.htaccess` file or directly in your PHP code.

#### .htaccess
~~~
<IfModule mod_rewrite.c> 
    RewriteEngine On
    
    RewriteCond %{HTTP:X-FORWARDED-PROTO} !=https [NC]
    RewriteRule ^.*$ https://%{HTTP_HOST}
</IfModule>
~~~

#### PHP
~~~php
<?php

    if (!isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
        $_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https') {
        
        header(
            'Location: https://' . 
            $_SERVER['HTTP_HOST'] . 
            $_SERVER['REQUEST_URI']
        );
    
    }

?>
~~~
