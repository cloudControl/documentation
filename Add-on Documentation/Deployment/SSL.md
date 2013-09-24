# SSL Add-on

Overview:

 * This Add-on provides SSL support for custom domains.
 * You need to have an RSA Private Key, an SSL Certificate and a Certificate Chain.
 * Add the Add-on to your deployment via our CLI with the [addon.add command](#adding-the-ssl-add-on).

Secure Socket Layer (SSL) encryption is available for improved security when
transmitting passwords and other sensitive data.

As part of the provided `.cloudcontrolled.com` subdomain, all deployments have
access to piggyback SSL using a `*.cloudcontrolled.com` wildcard certificate.
To use this, simply point your browser to:
* `https://APP_NAME.cloudcontrolled.com` for the default deployment
* `https://DEP_NAME-APP_NAME.cloudcontrolled.com` for non-default deployments

    Please note the **dash** between DEP_NAME and APP_NAME.

SSL support for custom domains is available through the SSL Add-on.

## Custom Domain Certificates

To enable SSL support for custom domains like `www.example.com` or
`secure.example.com`, you need the SSL Add-on.

Please go through the following steps, which are described in the upcoming
sections, to add SSL support to your deployment:

 * Acquire a signed certificate from your certificate authority of trust.
 * Add the SSL Add-on providing the certificate, the private key and the
   certificate-chain files.
 * Set your DNS entry to point to your SSL DNS Domain.

Note: Please allow up to one hour for DNS changes to propagate before they take
effect. Root or naked domains like `example.com` without a subdomain are not
supported.

### Acquiring an SSL Certificate

There is wide variety of Certificate Authorities (CA) which differ in cost and
process of acquiring an SSL certificate.
[SSLShopper](http://www.sslshopper.com/certificate-authority-reviews.html)
offers an easy way to compare CAs. Some even offer a free trial period. In
most cases, you need to carry out the following steps.

Note: For testing purposes you can always use a self-signed certificate which
is free of charge and does not require going through the registration process
of individual providers.

#### Generate a private key

As mentioned earlier, you need a private key, your certificate and the
certificate chain to enable SSL support. For that process you will need the
`openssl` toolkit which can be installed with one of the following methods
depending on your platform:

|Platform|Install method|
|:-------|:-------------|
|Mac OS X ([Homebrew](http://brew.sh/))| `brew install openssl`|
|Windows|[Windows package installer](http://gnuwin32.sourceforge.net/packages/openssl.htm)|
|Ubuntu GNU/Linux|`apt-get install openssl`|


After you are done with the installation, use the `openssl` command line tool to
proceed with generating your private RSA key:
 ~~~
 $ openssl genrsa -des3 -out server.key.org 2048
 # Enter and confirm a passphrase
 ~~~

#### Removing the passphrase

The generated key is protected by a passphrase which needs to be removed so
that it can be loaded by the web server.
 ~~~
 $ openssl rsa -in server.key.org -out server.key
 ~~~

Your private key used for the process is now saved in the file `server.key`

#### Generate a CSR (Certificate Signing Request)

For acquiring an SSL Certificate, you need to provide your CA with a CSR
(Certificate Signing Request). This can also be used for creating self-signed
certificates. The CSR contains all the information regarding your company or
organization, thus prompting you to enter those:
 ~~~
 $ openssl req -new -key server.key -out server.csr
 Country Name (2 letter code) [AU]:DE
 State or Province Name (full name) [Some-State]:
 Locality Name (eg, city) []:
 Organization Name (eg, company) [Internet Widgits Pty Ltd]:
 Organizational Unit Name (eg, section) []:Information Technology
 Common Name (eg, your name or your server's hostname) []:www.example.com
 Email Address []:
 Please enter the following 'extra' attributes
 to be sent with your certificate request
 A challenge password []:
 An optional company name []:
 ~~~

The file created after this process is named `server.csr`.

Note: Please pay attention to the fields Country Name and Common Name. The Country
Name should contain the 2 letter code of your country according to the
[ISO 3166-1](http://www.iso.org/iso/country_codes/iso_3166_code_lists/country_names_and_code_elements.htm)
format. Second and most important is the Common Name. This should reflect the
domain for which you want to issue the certificate. As mentioned earlier, this
cannot be a root domain but needs to have a format like `www.example.com`.

#### Issuing the Certificate

After choosing your CA, you have to go through their process of issuing the
certificate. For this you will need the CSR file, which was created in the
previous step. Quite often you will also need define the web server you are
going to use. In this case you should select the Nginx web server, and if this
is not an option then Apache 2.x should also be OK.

In the end, your CA will provide you with some files including the SSL
certificate and the certificate chain. Your certificate file should have either
a `.crt` or `.pem` extension. Our service requires the certificates to be in
PEM format, so if it isn't, you can transform it with the following command:
 ~~~
 $ openssl x509 -inform PEM -in www_example_com.crt -out www_example_com.pem
 ~~~

The content of the SSL certificate file should look like this:
 ~~~
 -----BEGIN CERTIFICATE-----
 ...
 -----END CERTIFICATE-----
 ~~~

The certificate chain is a chain of trust which proves that your certificate is
issued by a trustworthy provider authorized by a Root CA. Root CA certificates
are stored in all modern browsers and this is how your browser is able to
verify that a website is secure. In any other case, you will receive a warning
similar to this:

![Firefox warning](https://s3-eu-west-1.amazonaws.com/cctrl-www-production/custom_assets/attachments/000/000/038/original/ffssl.png)

You should also have a file which is a bundle of certificates which succeed each other:
 ~~~
 -----BEGIN CERTIFICATE-----
 ...
 -----END CERTIFICATE-----
 -----BEGIN CERTIFICATE-----
 ...
 -----END CERTIFICATE-----
 ~~~

Note: If you do not have a certificate bundle but a series of `.crt` files, you
have to place them in the right order starting from the intermediate
certificate and ending to the root certificate. Please make sure that they are
in PEM format.

### Adding the SSL Add-on

To add the SSL Add-on, simply provide the paths to the files provided by the
certificate authority using the respective parameters of the addon.add command.
 ~~~
 $ cctrlapp APP_NAME/DEP_NAME addon.add ssl.host --cert path/to/CERT_FILE --key path/to/KEY_FILE --chain path/to/CHAIN_FILE
 ~~~

In order to check the status of the Add-on, you can do the following.
 ~~~
 $ cctrlapp APP_NAME/DEP_NAME addon ssl.host
 Addon                    : ssl.host

 Settings
   SSLDEV_CERT_EXPIRES      : 2016-01-01 10:00:00
   SSLDEV_DNS_DOMAIN        : addonssl-depxxxxxxxx-1234567890.eu-west-1.elb.amazonaws.com
   SSLDEV_CERT_INCEPTS      : 2013-01-01 10:00:00
 ~~~

When the SSL certificate is expired, you can update it by removing the Add-on
and re-adding it, providing the updated certificate. The SSL service is provided
for 23 minutes after removing the Add-on so that it can be updated in the
meantime without interrupting the service. To achieve that you have to run the
following commands:
 ~~~
 $ cctrlapp APP_NAME/DEP_NAME addon.remove ssl.host
 $ cctrlapp APP_NAME/DEP_NAME addon.add ssl.host --cert path/to/NEW_CERT_FILE --key path/to/KEY_FILE --chain path/to/CHAIN_FILE
 ~~~

Note: You need to provide the original key and chain again when updating the
Add-on even if those are not changed.

## HTTPS Redirects

HTTPS termination is done at the routing tier. Requests are then routed via
HTTP to one of your app's clones. To determine if a request was made via HTTPS
originally, the routing tier sets the `X-FORWARDED-PROTO` header to `https`.
The header is only set for requests that arrived via HTTPS at the routing tier.
This allows you to redirect accordingly.

### PHP Example

For PHP you can either redirect via Apache's mod_rewrite using a `.htaccess`
file or directly in your PHP code.

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
