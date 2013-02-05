# MemCachier Add-on

[MemCachier](http://www.memcachier.com) is an implementation of the [Memcache](http://memcached.org) in-memory key/value store used for caching data. It is a key technology in modern web applications for scaling and reducing server loads. The MemCachier Add-on manages and scales clusters of memcache servers so you can focus on your app. Tell us how much memory you need and get started for free instantly. Add capacity later as you need it.

The information below will quickly get you up and running with the MemCachier Add-on for cloudControl. For information on the benefits of MemCachier and how it works, please refer to the more extensive [User Guide](http://www.memcachier.com/documentation/memcache-user-guide/).

Getting started
-----

Start by installing the Add-on:

    $ cctrlapp App_Name/Dep_Name addon.add memcachier.dev

You can start with more memory if you know you’ll need it:

    $ cctrlapp App_Name/Dep_Name addon.add memcachier.100mb
    $ cctrlapp App_Name/Dep_Name addon.add memcachier.250mb
     ... etc ...

Next, setup your app to start using the cache. We have documentation for the following languages and frameworks:

 * [Ruby](#ruby)
 * [Rails](#rails)
 * [Python](#python)
 * [Django](#django)
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

`Dalli` is a Ruby memcache client, and the `memcachier` gem modifies the environment (`ENV`) such that the environment variables set by MemCachier will work with Dalli. Once these gems are installed you can start writing code. The following is a basic example using Dalli.

~~~ruby
     require 'sinatra'
     require 'memcachier'
     require 'dalli'
     require 'json'
     
     def getVisits()
     
         begin
             cred_file = File.open(ENV["CRED_FILE"]).read
             creds = JSON.parse(cred_file)["MEMCACHIER"]
             config = {
                 :srv => creds["MEMCACHIER_SERVERS"],
                 :usr => creds["MEMCACHIER_USERNAME"],
                 :pwd => creds["MEMCACHIER_PASSWORD"]
             }
         rescue
             puts "Could not open file"
         end
     
         cache=Dalli::Client.new(config[:srv],{:username => config[:usr], :password => config[:pwd]})
     
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

`Dalli` is a Ruby memcache client, and the `memcachier` gem modifies the environment (`ENV`) such that the environment variables set by MemCachier will work with Dalli. Once these gems are installed you’ll want to configure the Rails cache_store appropriately. Modify `config/environments/production.rb` with the following:

~~~ruby
config.cache_store = :dalli_store
~~~

In your development environment, Rails.cache defaults to a simple in-memory store and so it doesn’t require a running memcached.

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

You can use many memcached clients for python. In this example we are gonig to use `Python-Binary-Memcached` client with built in SASL support. Run the following commands on your local machine:

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
 try:
     cred_file = open(os.environ["CRED_FILE"])
     data = json.load(cred_file)
     creds = data['MEMCACHIER']
     config = {
             'srv': creds['MEMCACHIER_SERVERS'],
             'usr': creds['MEMCACHIER_USERNAME'],
             'pwd': creds['MEMCACHIER_PASSWORD']
             }
 except IOError:
     print 'Could not open file'

 client = bmemcached.Client('{0}:11211'.format(config['srv']),str(config['usr']),str(config['pwd']))

 ipaddr=request.headers['X-Forwarded-For']
 count=1
 if client.get(ipaddr) is not None:
     count=int(client.get(ipaddr))+1

 client.set(ipaddr,str(count))
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

More information on how to use php-memcached can be found on [php.net](http://php.net/manual/en/book.memcached.php). The php-memcached extension is part of the cloudControl stacks.

Java
----

#####Memcached Java libraries:

There is a number of Memcached client libraries for Java:

* [spymemcached](http://code.google.com/p/spymemcached/wiki/Examples)
* [javamemcachedclient](http://code.google.com/p/javamemcachedclient/)
* [memcache-client-forjava](http://code.google.com/p/memcache-client-forjava/)
* [xmemcached](http://code.google.com/p/xmemcached/)
* [simple-spring-memcached](http://code.google.com/p/simple-spring-memcached/)
* [memcached-session-manager](http://code.google.com/p/memcached-session-manager/)

In this tutorial we will use `spymemcached`. To use it in your project, just specify additional dependency in your `pom.xml` file:

~~~xml
...
<dependency>
    <groupId>com.google.code.simple-spring-memcached</groupId>
    <artifactId>spymemcached</artifactId>
    <version>2.8.4</version>
</dependency>
...
~~~

#####Example application:

We will modify existing [Spring/JSP hello world application](https://github.com/cloudControl/java-spring-jsp-example-app) to store visits counter in the `Memcached`.

Extend your [pom.xml](https://github.com/cloudControl/java-spring-jsp-example-app/blob/memcached_guide/pom.xml) with required `spymemcached` dependency and embedded Jetty runner. Define [Procfile](https://github.com/cloudControl/java-spring-jsp-example-app/blob/memcached_guide/Procfile).

######Create memcached SASL connection:

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

    private static final int PORT = 11211;

    public MemcachierConnection(String username, String password, String servers) throws IOException {
        this(new SASLConnectionFactoryBuilder().build(username, password), getAddresses(servers));
    }

    public MemcachierConnection(ConnectionFactory cf, List<InetSocketAddress> addrs) throws IOException {
        super(cf, addrs);
    }

    private static List<InetSocketAddress> getAddresses(String addresses) {
        List<InetSocketAddress> addrList = new ArrayList<InetSocketAddress>();
        for (String addr : addresses.split(" ")) {
            addrList.add(new InetSocketAddress(addr, PORT));
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

Take care to use correct socket addresses (`getAddresses()` method) as list of servers in the Add-on credentials contain only hosts, without the port. The port is always the default one - `11211`.

######Use Memcached to track visits counter:

~~~java
package com.cloudcontrolled.sample.spring.visitcounter;

import java.io.IOException;
import com.cloudcontrolled.sample.spring.memcachier.MemcachierConnection;

public class VisitCounter {

    private static final String KEY = "count";
    private MemcachierConnection mc;

    public VisitCounter() throws IOException {
        String user = System.getenv("MEMCACHIER_USERNAME");
        String pass = System.getenv("MEMCACHIER_PASSWORD");
        String addr = System.getenv("MEMCACHIER_SERVERS");
        mc = new MemcachierConnection(user, pass, addr);
    }

    public int getVisitCount() {
        if (mc.get(KEY) == null) {
            return 0;
        } else {
            return (Integer) mc.get(KEY);
        }
    }

    public void updateVisitCount() {
        int count = getVisitCount();
        mc.set(KEY, 0, count + 1);
    }
}
~~~

`Memcachier` credentials are provided via environment variables: `MEMCACHIER_USERNAME`, `MEMCACHIER_PASSWORD` and `MEMCACHIER_SERVERS`. Check [the documentation](https://cloudcontrol.com/dev-center/Guides/Java/Read%20Configuration.md) for alternative ways of accessing the Add-on credentials.

######Use Memcachier in [example application](https://github.com/cloudControl/java-spring-jsp-example-app/blob/memcached_guide/src/main/java/com/cloudcontrolled/sample/spring/web/IndexController.java):

~~~java
VisitCounter vc = new VisitCounter();
vc.getVisitCount();
vc.updateVisitCount();
~~~

######Push, add Memcachier Add-on and deply:
~~~bash
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default addon.add memcachier.PLAN
$ cctrlapp APP_NAME/default deploy --max=4
~~~

You can also find ready-to-deply example on [Github](https://github.com/cloudControl/java-spring-jsp-example-app/tree/memcached_guide).

Library support
-----

MemCachier will work with any memcached binding that supports [SASL authentication](https://en.wikipedia.org/wiki/Simple_Authentication_and_Security_Layer) and the [binary protocol](https://code.google.com/p/memcached/wiki/MemcacheBinaryProtocol). We have tested MemCachier with the following language bindings, although the chances are good that other SASL binary protocol packages will also work.

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

To test against your cloudControl application locally, you will need to run a local memcached process. MemCachier can only run in cloudControl But because MemCachier and memcached speak the same protocol, you shouldn’t have any issues testing locally.  Installation depends on your platform.

This will install memcached without SASL authentication support. This is generally what you want as client code can still try to use SASL auth and memcached will simply ignore the requests which is the same as allowing any credentials. So your client code can run without modification locally and on cloudControl.

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

To run memcached simply execute the following command:

~~~
$ memcached -v
~~~

Usage analytics
------

Our analytics dashboard is a simple tool that gives you more insight into how you’re using memcache. Just open your application's dashboard on our [web interface](https://console.cloudcontrolled.com/).

Sample apps
-----

We've built a number of working sample apps, too:

* [Sinatra Memcache Example](http://github.com/memcachier/memcachier-social)
* [Rails Memcache Example](http://github.com/memcachier/memcachier-gis)
* [Django Memcache Example](http://github.com/memcachier/memcachier_algebra)
* [PHP Memcache Example](http://github.com/memcachier/memcachier-primes)
* [Java Jetty Memcache Example](https://github.com/memcachier/memcachier-fibonacci)

Upgrading and downgrading
------

Changing your plan, either by upgrading or downgrading, requires no code changes.  Your cache won't be lost, either.  Upgrading and downgrading Just Works™.

Support
-------

All Memcachier support and runtime issues should be submitted via one of the cloudControl Support channels](https://www.cloudcontrol.com/dev-center/support). Any non-support related issues or product feedback is welcome via email at: [support@memcachier.com](mailto:support@memcachier.com)

Any issues related to Memcachier service are reported at [Memcachier Status](http://status.memcachier.com/).

