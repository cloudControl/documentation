# QuotaGuard Static

QuotaGuard Static is an add-on that allows you to route outbound traffic through a static IP address on cloudControl. You can provide this IP address to an API partner for IP based whitelisting and open your own firewall to access internal resources.

QuotaGuard Static is accessible as an HTTP or SOCKS5 proxy so is language and platform agnostic. There is native support across Ruby, Python, Node.js, Scala, Java and every other mainstream language.

## Adding QuotaGuard Static

QuotaGuard Static can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add quotaguardstatic.OPTION
~~~

## Upgrade QuotaGuard Static

Upgrading to another version of QuotaGuard Static is easy and instant:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade quotaguardstatic.OPTION_OLD quotaguardstatic.OPTION_NEW 
~~~

## Downgrade QuotaGuard Static

Downgrading to another version of QuotaGuard Static is easy and instant and will not change your IP address:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade quotaguardstatic.OPTION_OLD quotaguardstatic.OPTION_NEW 
~~~

## Removing QuotaGuard Static

Removing QuotaGuard Static will instantly prevent you from accessing our proxy so use with caution. If you re-add the add-on then you may be assigned a different IP address:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove quotaguardstatic.OPTION
~~~

## Using QuotaGuard Static

QuotaGuard Static provisioning provides you with a unique login to our proxy service exposed via a QUOTAGUARDSTATIC_URL environment variable. How you use this depends on how you are accessing your external APIs but most HTTP libraries include a way of specifying a proxy server. For Ruby on Rails applications we recommend accessing the QuotaGuard credentials via the ENV variable. This allows you to easily set your proxy server in an initializer statement. For example with the Ruby RestClient gem:
~~~ruby
require "rest-client"

RestClient.proxy = ENV["QUOTAGUARDSTATIC_URL"]

res = RestClient.get("http://ip.jsontest.com")

puts "Your Static IP is: #{res.body}"
~~~

Alternatively you can read the QuotaGuard Static credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "QUOTAGUARDSTATIC_URL":{
      "QUOTAGUARDSTATIC_URL":"http://username:password@static.quotaguard.com:9293"
   }
}
~~~
## Monitoring QuotaGuard Static

All our plans include real-time analytics and log access via our dashboard. Access this by logging in to your cloudControl console and clicking on the QuotaGuard Static add-on from within one of your deployments. This will take you to your dashboard on QuotaGuard.com where you will be able to see your real-time and historic usage data.

## HTTP vs. SOCKS5 proxy

The first decision you must make is whether to target our HTTP or SOCKS proxy. SOCKS is more versatile as it handles TCP level traffic but the setup is more involved than the HTTP proxy. Our general rule of thumb is use HTTP if accessing a web service otherwise use SOCKS.

Example use cases
1. Accessing an HTTP or HTTPS API (e.g. https://api.github.com/users/octocat/orgs) ==> **HTTP Proxy**
2. Accessing a MySQL database ==> **SOCKS Proxy**

## QuotaGuard Static Code Examples

###Ruby/Rails
Ruby has an excellent REST client that easily allows you to specify an HTTP proxy. You can run the below example in an irb session and verify that the final IP returned is one of your two static IPs.

~~~ruby
require "rest-client"

RestClient.proxy = ENV["QUOTAGUARDSTATIC_URL"]

res = RestClient.get("http://ip.jsontest.com")

puts "Your Static IP is: #{res.body}"
~~~

### Python/Django
#### Using with the Requests library
[Requests](http://docs.python-requests.org/en/latest/) is a great HTTP library for Python. It allows you to specify an authenticated proxy on a per request basis so you can pick and choose when to route through your static IP.

~~~python
import requests
import os

proxies = {
"http": os.environ['QUOTAGUARDSTATIC_URL']
}

res = requests.get("http://ip.jsontest.com/", proxies=proxies)
print res.text
~~~
#### Using with the urllib2
urllib2 is a more basic library used for HTTP communication in Python and uses environment variables to set a proxy service.

In your application initialization you should set the `http_proxy` variable to match the `QUOTAGUARDSTATIC_URL`.

~~~python
# Assign QuotaGuard to your environment's http_proxy variable
os.environ['http_proxy'] = os.environ['QUOTAGUARDSTATIC_URL']
~~~

To test in the Python interpreter

~~~python
import urllib2, os
os.environ['http_proxy'] = os.environ['QUOTAGUARDSTATIC_URL']
url = 'http://ip.jsontest.com/'
proxy = urllib2.ProxyHandler()
opener = urllib2.build_opener(proxy)
in_ = opener.open(url)
res = in_.read()
print res
~~~

###Node.js
####Accessing an HTTP API with Node.js
To access an HTTP API you can use the standard HTTP library in Node.js but must ensure you correctly set the “Host” header to your target hostname, not the proxy hostname.

~~~javascript
var http, options, proxy, url;

http = require("http");

url = require("url");

proxy = url.parse(process.env.QUOTAGUARDSTATIC_URL);
target  = url.parse("http://ip.jsontest.com/");

options = {
  hostname: proxy.hostname,
  port: proxy.port || 80,
  path: target.href,
  headers: {
    "Proxy-Authorization": "Basic " + (new Buffer(proxy.auth).toString("base64")),
    "Host" : target.hostname
  }
};

http.get(options, function(res) {
  res.pipe(process.stdout);
  return console.log("status code", res.statusCode);
});
~~~
####Accessing an HTTPS API with Node.js
The standard Node.js HTTPS module does not handle making requests through a proxy very well. If you need to access an HTTPS API we recommend using the Request module (npm install request).

~~~javascript
var request = require('request');

var options = {
    proxy: process.env.QUOTAGUARDSTATIC_URL,
    url: 'https://api.github.com/repos/joyent/node',
    headers: {
        'User-Agent': 'node.js'
    }
};

function callback(error, response, body) {
    if (!error && response.statusCode == 200) {
        console.log(body);
    }
}

request(options, callback);
~~~
####Accessing a MySQL Database using SOCKS Proxy in Node.js
To use a SOCKS proxy for any reason in Node.js we recommend socksjs as this is one of the only Node.js SOCKS modules to support authentication.
~~~bash
npm install socksjs
~~~
This sample creates a connection to a SOCKS connection to our proxy and uses that for all MySQL requests.
~~~javascript
var mysql = require('mysql2');
var url = require("url");
var SocksConnection = require('socksjs');
var remote_options = {
host:'mysql.db.hostname',
port: 3306
};
var proxy = url.parse(process.env.QUOTAGUARDSTATIC_URL);
var auth = proxy.auth;
var username = auth.split(":")[0]
var pass = auth.split(":")[1]

var sock_options = {
host: proxy.hostname,
port: 1080,
user: username,
pass: pass
}
var sockConn = new SocksConnection(remote_options, sock_options)
var dbConnection = mysql.createConnection({
user: 'test',
database: 'test',
password: 'testpw',
stream: sockConn
});
dbConnection.query('SELECT 1+1 as test1;', function(err, rows, fields) {
if (err) throw err;

console.log('Result: ', rows);
sockConn.dispose();
});
dbConnection.end();
~~~
###Using with PHP
PHP cURL is the easiest way to make HTTP requests via QuotaGuard Static. This example assumes that you have set the QUOTAGUARDSTATIC_URL environment variable which is automatically set for you when you provision the add-on.

The IP address printed on screen will be one of your two static IP addresses, run it a couple of times and you’ll probably see the other one too

~~~php
<?php

function lookup(){
  $quotaguard_env = getenv("QUOTAGUARDSTATIC_URL");
  $quotaguard = parse_url($quotaguard_env);

  $proxyUrl       = $quotaguard['host'].":".$quotaguard['port'];
  $proxyAuth       = $quotaguard['user'].":".$quotaguard['pass'];

  $url = "http://ip.jsontest.com/";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
  curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyAuth);
  $response = curl_exec($ch);
  return $response;
}

$res = lookup();
print_r($res);

?>
~~~
###SOCKS proxy setup
QuotaGuard Static provides a wrapper script that transparently forwards all outbound TCP traffic through your Static IP. This is language independent but there are known issues with certain Node.js connections hanging so please contact us if you have any issues.

####Installing the QuotaGuard Static socksify wrapper
Download and extract the wrapper in your app directory:

~~~bash
$ curl https://s3.amazonaws.com/quotaguard/quotaguard-socksify-latest.tar.gz | tar xz
~~~

Now modify your app Procfile to prepend the wrapper to your standard commands:

~~~bash
 web: bin/qgsocksify bundle exec unicorn -p $PORT -c ./config/unicorn.rb
~~~

You should add all extracted files to your git repo except QUOTAGUARD-LICENSE.txt which can be gitignored.

~~~bash
$ echo "QUOTAGUARD-LICENSE.txt" >> .gitignore
$ git add bin/qgsocksify vendor/dante
$ git commit -m "Add QuotaGuard Static socksify"
~~~

####Controlling what traffic goes through proxy
You can provide a standard subnet mask to only route traffic to certain IP subnets via the QUOTAGUARDSTATIC_MASK environment variable.

~~~bash
$ cctrlapp app_name/dep_name config.add QUOTAGUARDSTATIC_MASK="100.30.68.0/24"
~~~

All outbound traffic to 100.30.68.* would be routed via your Static IPs. Multiple masks can be provided by comma separating the mask values:

~~~bash
$ cctrlapp app_name/dep_name config.add QUOTAGUARDSTATIC_MASK="100.30.68.0/24,99.29.68.0/24"
~~~

###FAQs

####Is the HTTP proxy secure when accessing HTTPS services?
Yes. You can access HTTPS services via the HTTP proxy whilst still getting full SSL/TLS security. When you make a request via the proxy to an HTTPS endpoint your client should transparently issue a CONNECT request rather than a basic GET request.

On receipt of this CONNECT request the proxy will open a tunnel between your client and the endpoint, allowing your client to negotiate a standard SSL session with the endpoint. Once negotiated all traffic sent between your client and the endpoint will be encrypted as if you had connected directly with them.

####What happens when I reach my usage limit?
To make sure we grow in harmony with your application QuotaGuard Static operates initially with a soft limit. When you reach your plan’s monthly usage limit your requests will continue going through but we will reach out to you via e-mail to ask that you upgrade your plan.

If you repeatedly exceed your limits without upgrading then hard limits may be placed on your account but this is a very last resort.

####I’ve forgotten what my Static IPs are!
Both IPs are shown on your QuotaGuard Static Dashboard which you can access by logging in to your cloudControl console and clicking on the QuotaGuard Static add-on from within one of your deployments.

####Why have you given me two Static IP addresses?
We believe all apps should be built for scalability and high availability. Our commitment to this means we only provide load balanced, high availability services. Load balancing our nodes allows one node to fail or be brought down for maintenance with no impact to your application. Each IP you are given represents one proxy node that is running behind a load balancer.

####Can I access MySQL or Postgres through this?
Yes we have many users doing this. The easiest way for most languages is to use our SOCKS proxy wrapper(installation details higher up the page). If you are using Node.js you can also configure the SOCKS proxy in your Javascript code without using the wrapper (details also on this page).

###QuotaGuard vs. QuotaGuard Static
We offer two products on cloudControl, QuotaGuard and QuotaGuard Static.

QuotaGuard routes your traffic through a dynamic set of IP addresses that may change at any time and is intended for accessing APIs like Google Maps that restrict usage based on your IP address. It should be used if you want to access these APIs without your limit being shared with other cloudControl apps.

QuotaGuard Static routes your traffic through a pair of static IP addresses that never change. It should be used if you need your traffic to pass through a known IP address for the purpose of firewall ingress rules or application whitelisting with a third party.

Please [send us a mail](mailto:support@teachmatic.com) if you’d like more guidance on what service fits your needs best.

## Support
Please email [support@teachmatic.com](mailto:support@teachmatic.com) if you have any problems.

## Additional Resources
More information may be available on our [documentation page](https://www.quotaguard.com/docs).