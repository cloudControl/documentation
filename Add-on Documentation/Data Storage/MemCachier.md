# MemCachier Add-on

[MemCachier](http://www.memcachier.com) is an implementation of the [Memcached](http://memcached.org) in-memory key/value store used for caching data. It is a key technology in modern web applications for scaling and reducing server loads. The MemCachier Add-on manages and scales clusters of Memcached servers so you can focus on your app. Tell us how much memory you need and get started for free instantly. Add capacity later as you need it.

The information below will quickly get you up and running with the MemCachier Add-on for cloudControl. For information on the benefits of MemCachier and how it works, please refer to the more extensive [User Guide](http://www.memcachier.com/documentation/memcache-user-guide/).

Getting started
-----

Start by installing the Add-on:

    $ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.dev

You can start with more memory if you know you'll need it:

    $ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.100mb
    $ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.250mb
     ... etc ...

Next, setup your app to start using the cache. We have documentation for the following languages and frameworks:

 * [Ruby](#ruby)
 * [Rails](#rails)
 * [Python](#python)
 * [PHP](#php)
 * [Java](#java)

Your credentials may take up to three (3) minutes to be synced to our servers. You may see authentication errors if you start using the cache immediately.

Ruby
------

Start by adding the [memcachier](https://github.com/memcachier/memcachier-gem) and [dalli](http://github.com/mperham/dalli) gems to your Gemfile.

~~~ruby
gem 'memcachier'
gem 'dalli'
~~~

Then bundle install:

~~~
$ bundle install
~~~

`Dalli` is a Ruby Memcached client, and the `memcachier` gem modifies the environment (`ENV`) such that the environment variables set by MemCachier will work with Dalli. Once these gems are installed you can start writing code. The following is a basic example using Dalli.

~~~ruby
require 'sinatra'
require 'dalli'
require 'json'
require 'memcachier'

def getVisits()

 config = {
   :srv => ENV["MEMCACHIER_SERVERS"],
   :usr => ENV["MEMCACHIER_USERNAME"],
   :pwd => ENV["MEMCACHIER_PASSWORD"]
 }

 cache=Dalli::Client.new(config[:srv],{:username => config[:usr],:password => config[:pwd]})

 count=cache.get(request.ip)
 if count.nil?
     count=0
 end

 count+=1
 cache.set(request.ip,count)

 return count
end

get '/' do
 count=getVisits
 "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">"+
 "<HTML>"+
 "<HEAD><TITLE>Ruby Memcachier example</TITLE></HEAD>"+
 "<BODY>"+
 "<h1>Hello #{request.ip} </h1>"+
 "This is visit #{count}"+
 "</BODY>"+
 "</HTML>"
end
~~~

Rails
-----

Start by adding the [memcachier](https://github.com/memcachier/memcachier-gem) and [dalli](http://github.com/mperham/dalli) gems to your Gemfile. We’ve built a small Rails example here: [MemCachier Rails Sample App](https://github.com/memcachier/memcachier-gis).

~~~ruby
gem 'memcachier'
gem 'dalli'
~~~

Then bundle install:

~~~term
$ bundle install
~~~

`Dalli` is a Ruby Memcached client, and the `memcachier` gem modifies the environment (`ENV`) such that the environment variables set by MemCachier will work with Dalli. Once these gems are installed you'll want to configure the Rails cache_store appropriately. Modify `config/environments/production.rb` with the following:

~~~ruby
config.cache_store = :dalli_store
~~~

In your development environment, Rails.cache defaults to a simple in-memory store and so it doesn't require a running Memcached.

From here you can use the following code examples to use the cache in your Rails app:

~~~ruby
Rails.cache.write("foo", "bar")
puts Rails.cache.read("foo")
~~~

Without the `memcachier` gem, you’ll need to pass the proper credentials to Dalli in `config/environments/production.rb`:

~~~ruby
config.cache_store = :dalli_store, ENV["MEMCACHIER_SERVERS"],
                    {:username => ENV["MEMCACHIER_USERNAME"],
                     :password => ENV["MEMCACHIER_PASSWORD"]}
~~~

Python
-----

You can use many Memcached clients for python. In this example we are gonig to use `Python-Binary-Memcached` client with built in SASL support. Run the following commands on your local machine:

~~~
$ pip install python-binary-memcached
$ pip freeze > requirements.txt
~~~

Make sure your `requirements.txt` file contains this requirement (note that your versions may differ than what’s below):

~~~
python-binary-memcached==0.14
~~~

Then you can put this code in you server.py and start caching:

~~~python
import os
import cgi
import json
import bmemcached
from flask import Flask
from flask import request
from random import randint

app = Flask(__name__)

@app.route('/')
def hello():
 count=1
 try:
     cred_file = open(os.environ["CRED_FILE"])
     data = json.load(cred_file)
     creds = data['MEMCACHIER']
     config = {
             'srv': str(creds['MEMCACHIER_SERVERS']).split(','),
             'usr': str(creds['MEMCACHIER_USERNAME']),
             'pwd': str(creds['MEMCACHIER_PASSWORD'])
             }
 except IOError:
     print 'Could not open file'

 client=bmemcached.Client(config['srv'], config['usr'], config['pwd'])
 ipaddr=str(request.headers['X-Forwarded-For'])
 if client.get(ipaddr) is not None:
     count=int(client.get(ipaddr))+1
 client.set(ipaddr, str(count))

 return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\
         <HTML>\n\
         <HEAD><TITLE>Python Memcachier example</TITLE></HEAD>\n\
         <BODY>\n\
         <h1>Hello " + ipaddr + "</h1>\n\
         This is visit " + str(count) +"\n\
         </BODY>\n\
         </HTML>"

if __name__ == '__main__':
 port = int(os.environ.get('PORT', 5000))
 app.run(host='0.0.0.0', port=port)
~~~

PHP
------

Memcached provided by MemCachier can be used like this:

~~~php
<?php
  $creds_content = file_get_contents($_ENV['CRED_FILE'], false);
  if ($creds_content == false) {
      die('FATAL: Could not read credentials file');
  }

  $creds = json_decode($creds_content, true);

  $config = array(
      'SERVERS' => array_map(function($x) {return explode(":", $x);}, explode(",", $creds['MEMCACHIER']['MEMCACHIER_SERVERS'])),
      'USERNAME' => $creds['MEMCACHIER']['MEMCACHIER_USERNAME'],
      'PASSWORD' => $creds['MEMCACHIER']['MEMCACHIER_PASSWORD'],
  );
  $m = new Memcached();
  $m->setOption(Memcached::OPT_BINARY_PROTOCOL, 1);
  $m->setSaslAuthData($config['USERNAME'], $config['PASSWORD']);
  $m->addServers($config['SERVERS']);

  $current_count = (int)$m->get('count') + 1;
  $m->set('count', $current_count);
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

More information on how to use php-memcached can be found on [php.net](http://php.net/manual/en/book.memcached.php). The php-memcached extension is part of the cloudControl stacks.

Java
----

In this short example we will show you how to integrate your Java application with Memcachier Add-on. We will use `spymemcached` library with SASL authentication support. To use it in your project, just specify additional dependency in your `pom.xml` file:

~~~xml
...
<dependency>
    <groupId>com.google.code.simple-spring-memcached</groupId>
    <artifactId>spymemcached</artifactId>
    <version>2.8.4</version>
</dependency>
...
~~~

#####Create Memcached SASL connection:

~~~java
package com.cloudcontrolled.sample.spring.memcachier;

import java.io.IOException;
import java.net.InetSocketAddress;
import java.util.ArrayList;
import java.util.List;

import javax.security.auth.callback.CallbackHandler;

import net.spy.memcached.ConnectionFactory;
import net.spy.memcached.ConnectionFactoryBuilder;
import net.spy.memcached.MemcachedClient;
import net.spy.memcached.auth.AuthDescriptor;
import net.spy.memcached.auth.PlainCallbackHandler;

public class MemcachierConnection extends MemcachedClient {

   public MemcachierConnection(String username, String password, String servers) throws IOException {
       this(new SASLConnectionFactoryBuilder().build(username, password), getAddresses(servers));
   }

   public MemcachierConnection(ConnectionFactory cf, List<InetSocketAddress> addrs) throws IOException {
       super(cf, addrs);
   }

   private static List<InetSocketAddress> getAddresses(String servers) {
       List<InetSocketAddress> addrList = new ArrayList<InetSocketAddress>();
       for (String server : servers.split(",")) {
           String addr = server.split(":")[0];
           int port = Integer.parseInt(server.split(":")[1]);
           addrList.add(new InetSocketAddress(addr, port));
       }
       return addrList;
   }
}

class SASLConnectionFactoryBuilder extends ConnectionFactoryBuilder {
   public ConnectionFactory build(String username, String password){
       CallbackHandler ch = new PlainCallbackHandler(username, password);
       AuthDescriptor ad = new AuthDescriptor(new String[]{"PLAIN"}, ch);
       this.setProtocol(Protocol.BINARY);
       this.setAuthDescriptor(ad);
       return this.build();
   }
}
~~~

#####Use Memcachier:

~~~java
String user = System.getenv("MEMCACHIER_USERNAME");
String pass = System.getenv("MEMCACHIER_PASSWORD");
String addr = System.getenv("MEMCACHIER_SERVERS");
MemcachierConnection mc = new MemcachierConnection(user, pass, addr);
~~~

You can also find a ready-to-deploy example on [Github](https://github.com/cloudControl/java-spring-jsp-example-app/tree/memcached_guide).

Library support
-----

MemCachier will work with any Memcached binding that supports [SASL authentication](https://en.wikipedia.org/wiki/Simple_Authentication_and_Security_Layer) and the [binary protocol](https://code.google.com/p/memcached/wiki/MemcacheBinaryProtocol). We have tested MemCachier with the following language bindings, although the chances are good that other SASL binary protocol packages will also work.

<table>
<tbody>
<tr>
<th>Language</th>
<th>Bindings</th>
</tr>
<tr>
<td>Ruby</td>
<td><a href="http://github.com/mperham/dalli">dalli</a></td>
</tr>
<tr>
<td>Python</td>
<td>
  <a href="http://sendapatch.se/projects/pylibmc/">pylibmc</a>
</td>
</tr>
<tr>
<td>Django</td>
<td>
  <a href="https://github.com/jbalogh/django-pylibmc">django-pylibmc</a>
</td>
</tr>
<tr>
<td>PHP</td>
<td>
  <a href="http://php.net/manual/en/book.memcached.php">PHP-Memcached</a>
</td>
</tr>
<tr>
<td>Java</td>
<td>
  <a href="http://code.google.com/p/spymemcached/">spymemcached</a>
</td>
</tr>
</tbody>
</table>

Local setup
-----

To test against your cloudControl application locally, you will need to run a local Memcached process. MemCachier can only run on cloudControl but because MemCachier and Memcached speak the same protocol, you shouldn't have any issues testing it locally.  Installation depends on your platform.

This will install Memcached without SASL authentication support. This is generally what you want as client code can still try to use SASL auth and Memcached will simply ignore the requests which is the same as allowing any credentials. So your client code can run without modification locally and on cloudControl.

On Ubuntu:

~~~
$ sudo apt-get install memcached
~~~

Or on OS X (with Homebrew):

~~~
$ brew install memcached
~~~

Or for Windows please refer to [these instructions](http://www.codeforest.net/how-to-install-memcached-on-windows-machine).

For further information and resources (such as the memcached sourcecode) please refer to the [Memcache.org homepage](http://memcached.org)

To run Memcached simply execute the following command:

~~~
$ memcached -v
~~~

Usage analytics
------

Our analytics dashboard is a simple tool that gives you more insight into how you’re using memcache. Just open your application's dashboard on our [web interface](https://www.cloudcontrol.com/console).

Sample apps
-----

We've built a number of working sample apps, too:

* [Sinatra Memcached Example](http://github.com/memcachier/memcachier-social)
* [Rails Memcached Example](http://github.com/memcachier/memcachier-gis)
* [PHP Memcached Example](http://github.com/memcachier/memcachier-primes)
* [Java Jetty Memcached Example](https://github.com/memcachier/memcachier-fibonacci)

Upgrading and downgrading
------

Changing your plan, either by upgrading or downgrading, requires no code changes.  Your cache won't be lost, either.  Upgrading and downgrading Just Works™.

Support
-------

All Memcachier support and runtime issues should be submitted via one of the cloudControl Support channels](https://www.cloudcontrol.com/dev-center/support). Any non-support related issues or product feedback is welcome via email at: [support@memcachier.com](mailto:support@memcachier.com)

Any issues related to Memcachier service are reported at [Memcachier Status](http://status.memcachier.com/).

