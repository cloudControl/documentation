# Alias Add-on

Adding custom domains to a deployment is supported via the Alias add-on. The process requires basic knowledge about how the DNS system works. To add a custom domain follow the following simple steps.

 1. Get the verification code.
 ## Verification code

 The verification code is unique to the owner of the app. To get it simply use the alias command.

 ~~~
 $ cctrlapp APP_NAME/DEP_NAME alias APP_NAME.cloudcontrolled.com
 ~~~

 **The verification code is case sensitive and includes a space after the colon.**

 1. Add it as a TXT record to your domain.
 1. Add a CNAME pointing to the provided ´APP_NAME.cloudcontrolled.com´ subdomain.
 1. Add one alias per domain to your deployment.
 1. Wait for the DNS changes to propagate.



Sub-Domain
Sub-domains can be easily pointed to your deployment with a CNAME entry: 


Watch out that the text record does not include line breaks.

Alias.free and Alias.wildcard

Alias.free is free of costs and automatically added to each deployment. With this add-on you can add aliasses as described below.

In order to add wildcard entries for a hostname you need to upgrade to the alias.wildcard add-on:

$ cctrlapp APP_NAME/DEP_NAME addon.upgrade alias.free alias.wildcard 
The wildcard alias can easily be added by CNAME:


Watch out that the text record does not include line breaks.

Adding and removing aliases

Add Alias
Adds an alias to a deployment, e.g. www.example.com or test.www.example.com:

$ cctrlapp APP_NAME/DEP_NAME alias.add WWW.EXAMPLE.COM
An overview of all added aliases can be found in the deployment details:

$ cctrlapp APP_NAME/DEP_NAME alias
 Aliases
 name                                                 default  verified
 APP_NAME.cloudcontrolled.com                               1        1
 WWW.EXAMPLE.COM                                            0        1
Show Alias Details
Shows all details of an alias, e.g. whether an alias is verified and the verification code:

$ cctrlapp APP_NAME/DEP_NAME alias WWW.EXAMPLE.COM
Alias                    : WWW.EXAMPLE.COM
   is_default               : False
   is_verified              : True
   verification_errors      : 0
   verification_code        : cloudControl-verification: f6554424981ddsfbdsf1sdf4ssdf2afg34375456dd4dsfsd28a0sdfsafassfs
   date_created             : 2011-02-07 11:03:05
   date_modified            : 2011-02-08 20:12:24
Remove Alias
$ cctrlapp APP_NAME/DEP_NAME alias.remove ALIAS_NAME 
SEO: Avoiding duplicated content

By using an alias, the content of the page will been shown on two domains simultaneously. This is a disadvantage for search engines. To avoid this, it is best to create a 301 redirect (permanent) to the main alias in the .htaccess file:

RewriteEngine On
RewriteBase /
RewriteCond %{HTTP_HOST} !^alias\.tld$ [NC]
RewriteRule ^(.*)$ http://alias.tld/$1 [R=301,L]
