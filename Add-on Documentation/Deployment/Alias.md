# Alias Add-on

Adding custom domains to a deployment is supported via the Alias add-on. The process requires basic knowledge about how the DNS system works. To add a custom domain follow the following simple steps **for each domain**.

## Adding an Alias

 1. Get the verification code.
 The verification code is unique to the owner of the app. To get it simply use the alias command.

 ~~~
 $ exoapp APP_NAME/default alias APP_NAME.app.exo.io
 ~~~

 The verification code is case sensitive and includes a space after the colon. Please ensure, it keeps the exact same syntax in the TXT records text or the alias will not get verified.

 1. Add it as a TXT record to your root domain.

 Please use the interface of your DNS provider to add a [TXT record](http://de.wikipedia.org/wiki/TXT_Resource_Record) to your root domain. Please note how the TXT record is set for `example.com` but used to verify `www.example.com`.

 ~~~
 example.com.	3600	IN	TXT	"cloudControl-verification: 
 68b676e063eadb350876ae291e9ae43748d6e51c85ecd3c4cc026c869acc9d2d"
 ~~~

 Since we are going to use a CNAME to point the custom domain to the provided `.app.exo.io` subdomain all additional record types will be ignored. It's therefor required to set the TXT record on the root domain. This has the added benefit, that if you can verifiy multiple domains like e.g. `www.example.com` and `secure.example.com` with just one TXT record set for `example.com`.

 1. Add a CNAME pointing to the provided `.app.exo.io` subdomain.

 In addition to the TXT record, go ahead and also add a CNAME pointing to your apps `.app.exo.io` subdomain. Use the command line client's alias command to show the one specific to your deployment.

 ~~~
 # for the default deployment
  $ exoapp APP_NAME/default alias
 Aliases
 name                                                         default  verified
 APP_NAME.app.exo.io                                                 1        1
 # for any additional deployment
 $ exoapp APP_NAME/DEP_NAME alias
 Aliases
 name                                                         default  verified
 DEP_NAME.APP_NAME.app.exo.io                                        1        1
 ~~~

 The resulting CNAME record should look something like this example.

 ~~~
 www.example.com.	1593	IN	CNAME	APP_NAME.app.exo.io.
 ~~~

 1. Add one alias per domain to your deployment.

 Next add the domain as an alias to your deployment using the alias.add command.

 ~~~
 $ exoapp APP_NAME/DEP_NAME alias.add www.example.com
 ~~~

 You should now see your domain in the deployment's list of aliases.

 ~~~
 $ exoapp APP_NAME/DEP_NAME alias
 Aliases
 name                                                         default  verified
 www.example.com                                                     0        0
 [...]
 ~~~

 The verification takes at least 30 minutes, but depends on the TTL of you DNS configuration.

 1. Wait for the DNS changes to propagate.

 As soon as the changes have propagated through the DNS the alias will be verified and the deployment will start answering requests to that domain automatically.

 ~~~
 $ exoapp APP_NAME/DEP_NAME alias
 Aliases
 name                                                         default  verified
 www.example.com                                                     0        1
 [...]
 ~~~

## Removing an Alias

To remove an alias, simply use the alias.remove command.

~~~
$ exoapp APP_NAME/DEP_NAME alias.remove www.example.com
~~~

## Special Case: Wildcard Domains

The alias add-on does support wildcard domains. A wildcard domain like `*.example.com` allows you to have your deployment answer to arbitrary subdomains like `something.example.com` and `somethingelse.example.com` without needing to add every one of them as an alias in advance.

To use this feature first upgrade your alias add-on from free to wildcard.

~~~
$ exoapp APP_NAME/DEP_NAME addon.upgrade alias.free alias.wildcard
~~~

Then add the wildcard domain itself as an alias.

~~~
$ exoapp APP_NAME/DEP_NAME alias.add *.example.com
~~~

The TXT record requirement also applies to wildcard domains, so please follow the steps above accordingly.

