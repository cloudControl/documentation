# SSL Add-on

SSL encryption is available for improved security when transmitting passwords and other sensitive data. As part of the provided `.cloudcontrolled.com` subdomain all deployments have access to piggyback SSL using a `*.cloudconrolled.com` wildcard certificate. To use this, simply point your browser to `https://APP_NAME.cloudcontrolled.com`. SSL support for custom domains is available through the SSL add-on.

## Custom Domain Certificates

To enable SSL support for custom domains like `www.example.com` or `secure.example.com` you need the SSL add-on. Root or naked domains like `example.com` are not supported.

Currently the SSL add-on is not fully automated but needs manual approval by one of our support engineers.

Please follow the following simple steps to add SSL support to your deployment.

 1. Acquire a signed certificate from your certificate authority of trust.
 1. Ensure the key is not protected by a passphrase.
 1. Upload the certificate-, key- and certificate-chain files.
 
 To securely upload the files we provide SFTP access to a private directory for every deployment. Use the deployment details command to get the SFTP URL.
 
 ~~~
 $ cctrlapp APP_NAME/DEP_NAME details
 [...]
 private files: sftp://DEP_ID@cloudcontrolled.com/
 [...]
 ~~~
 
 Use any SFTP compatible client to upload the files to the /private directory. It expects the same SSH key that is used for pushing for authentication. The private directory is only accessible via SFTP. Even the deployment itself can not access this directory.
 
 ~~~
 $ sftp DEP_ID@cloudcontrolled.com
 Connected to cloudcontrolled.com.
 sftp> cd /private
 sftp> put CERT_FILE
 sftp> put KEY_FILE
 sftp> put CHAIN_FILE
 ~~~
 
 1. Send an e-mail to [support@cloudcontrol.de] to request activation.
 
 Please provide the common APP_NAME/DEP_NAME string as part of your e-mail.

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

