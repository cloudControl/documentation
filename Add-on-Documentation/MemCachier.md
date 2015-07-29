# MemCachier Add-on


[MemCachier](http://www.memcachier.com) is an implementation of the
[Memcache](http://memcached.org) in-memory key/value store used for
caching data. It is a key technology in modern web applications for
scaling and reducing server loads. The MemCachier add-on manages and
scales clusters of memcache servers so you can focus on your app. Tell
us how much memory you need and get started for free instantly. Add
capacity later as you need it.

The information below will quickly get you up and running with the
MemCachier Add-on for cloudControl. For information on the benefits of
MemCachier and how it works, please refer to the more extensive [User
Guide](https://www.memcachier.com/documentation).

For status and any cache issues please refer to our [status
page](http://status.memcachier.com/).

Finally, follow our [blog](http://blog.memcachier.com) or twitter
([@memcachier](http://twitter.com/MemCachier)), for status and product
announcements.

## Getting started

Start by installing the add-on:

    $ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.dev

You can start with more memory if you know you'll need it:

    $ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.100mb
    $ cctrlapp APP_NAME/DEP_NAME addon.add memcachier.250mb
     ... etc ...

Once the add-on has been added you’ll notice three configuration
variables:

    $ cctrlapp APP_NAME/DEP_NAME addon.creds
    MEMCACHIER_SERVERS    => mcx.eu.ec2.memcachier.com
    MEMCACHIER_USERNAME   => bobslob
    MEMCACHIER_PASSWORD   => l0nGr4ndoMstr1Ngo5strang3CHaR4cteRS

Your credentials may take up to three (3) minutes to be synced to our
servers. You may see authentication errors if you start using the
cache immediately.

Next, setup your app to start using the cache. We have documentation
for the following languages and frameworks:

 * [Ruby](#ruby)
 * [Rails 3 & 4](#rails-3-and-4)
 * [Rails 2](#rails2)
 * [Rack::Cache](#rails-rack-cache)
 * [PHP](#php)
 * [CakePHP](#cakephp)
 * [Symfony2](#symfony2)
 * [Django](#django)
 * [Node.js](#node.js)
 * [Java](#java)

## Ruby

Start by adding the [dalli](http://github.com/mperham/dalli) gem to
your Gemfile. `Dalli` is a Ruby memcache client.

~~~ruby
gem 'dalli'
~~~

Then bundle install:

~~~
$ bundle install
~~~

You can now start writing some code. First, you'll need to create a
client object with the correct credentials and settings:

~~~ruby
require 'dalli'
cache = Dalli::Client.new((ENV["MEMCACHIER_SERVERS"] || "").split(","),
                    {:username => ENV["MEMCACHIER_USERNAME"],
                     :password => ENV["MEMCACHIER_PASSWORD"],
                     :failover => true,
                     :socket_timeout => 1.5,
                     :socket_failure_delay => 0.2
                    })
~~~

Once setup, you can start using Dalli to cache objects. The following
is a basic example using Sinatra:

~~~ruby
require 'sinatra'
require 'dalli'
require 'json'

def getVisits()

 cache = Dalli::Client.new((ENV["MEMCACHIER_SERVERS"] || "").split(","),
                    {:username => ENV["MEMCACHIER_USERNAME"],
                     :password => ENV["MEMCACHIER_PASSWORD"],
                     :failover => true,
                     :socket_timeout => 1.5,
                     :socket_failure_delay => 0.2
                    })

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

We’ve built a small Ruby Sinatra example, with the
<a class="github-source-code" href="http://github.com/memcachier/examples-sinatra">source code</a>
available.

## Rails 3 and 4


Here we explain how you setup and install MemCachier with Rails. Refer
to the [Rails caching guide](https://docs.djangoproject.com/en/dev/topics/cache/#the-per-site-cache)
for information on how you use MemCachier with Rails. Rails supports
automatic whole site caching, per-view caching and fragment caching.

We’ve built a small Rails example here:
[MemCachier Rails Sample App](https://github.com/memcachier/memcachier-gis).

Start by adding the [dalli](http://github.com/mperham/dalli) gem to
your Gemfile. `Dalli` is a Ruby memcache client.

~~~ruby
gem 'dalli'
~~~

Then bundle install:

~~~
$ bundle install
~~~

Once this gem is installed you’ll want to configure the Rails
`cache_store` appropriately. Modify your
`config/environments/production.rb` with the following:

~~~ruby
config.cache_store = :dalli_store,
                    (ENV["MEMCACHIER_SERVERS"] || "").split(","),
                    {:username => ENV["MEMCACHIER_USERNAME"],
                     :password => ENV["MEMCACHIER_PASSWORD"],
                     :failover => true,
                     :socket_timeout => 1.5,
                     :socket_failure_delay => 0.2
                    }
~~~

In your development environment, Rails.cache defaults to a simple
in-memory store and so it doesn't require a running Memcached.

From here you can use the following code examples to use the cache in
your Rails app:

~~~ruby
Rails.cache.write("foo", "bar")
puts Rails.cache.read("foo")
~~~

## Rails 2

Start by adding the [dalli](http://github.com/mperham/dalli) gem to
your Gemfile. You will need to use dalli **v1.0.5** as later versions
of Dalli don't support Rails 2. `Dalli` is a Ruby memcache client.

~~~ruby
gem 'dalli', '~>1.0.5'
~~~

Then run bundle install:

~~~
$ bundle install
~~~

Once this gem is installed you’ll want to configure the Rails
`cache_store` appropriately. Modify `config/environments/production.rb`
with the following:

~~~ruby
require 'active_support/cache/dalli_store23'
config.cache_store = :dalli_store,
                    (ENV["MEMCACHIER_SERVERS"] || "").split(","),
                    {:username => ENV["MEMCACHIER_USERNAME"],
                     :password => ENV["MEMCACHIER_PASSWORD"],
                     :failover => true,
                     :socket_timeout => 1.5,
                     :socket_failure_delay => 0.2
                    }
~~~

In your development environment, Rails.cache defaults to a simple
in-memory store and so it doesn’t require a running memcached.

In `config/environment.rb`:

~~~ruby
config.gem 'dalli'
~~~

From here you can use the following code examples to use the cache in
your Rails app:

~~~ruby
Rails.cache.write("foo", "bar")
puts Rails.cache.read("foo")
~~~

## Django

Here we explain how you setup and install MemCachier with Django. Please
see the [Django caching
guide](https://docs.djangoproject.com/en/dev/topics/cache/#the-per-site-cache)
for how you effectively use MemCachier. Django supports
whole site caching, per-view caching and fragement caching.

We’ve built a small Django example.You can grab the
<a class="github-source-code" href="http://github.com/memcachier/examples-django">source code</a>.

MemCachier has been tested with the `pylibmc` memcache client, but the
default client doesn’t support SASL authentication. Run the following
commands on your local machine to install the necessary pips:

~~~
$ sudo port install libmemcached
$ LIBMEMCACHED=/opt/local pip install pylibmc
$ pip install django-pylibmc
~~~

Be sure to update your `requirements.txt` file with these new
requirements (note that your versions may differ than what’s below):

~~~
pylibmc==1.3.0
django-pylibmc==0.5.0
~~~

Next, configure your settings.py file the following way:

~~~python
os.environ['MEMCACHE_SERVERS'] = os.environ.get('MEMCACHIER_SERVERS', '').replace(',', ';')
os.environ['MEMCACHE_USERNAME'] = os.environ.get('MEMCACHIER_USERNAME', '')
os.environ['MEMCACHE_PASSWORD'] = os.environ.get('MEMCACHIER_PASSWORD', '')

CACHES = {
    'default': {
        'BACKEND': 'django_pylibmc.memcached.PyLibMCCache',
        'BINARY': True,
        'OPTIONS': {
            'no_block': True,
            'tcp_nodelay': True,
            'tcp_keepalive': True,
            'remove_failed': 4,
            'retry_timeout': 2,
            'dead_timeout': 10,
            '_poll_timeout': 2000
        }
    }
}
~~~

From here you can start writing cache code in your Django app:

~~~python
from django.core.cache import cache
cache.set("foo", "bar")
print cache.get("foo")
~~~

*NOTE*: A confusing error message you may get from `pylibmc` is
**MemcachedError: error 37 from memcached_set: SYSTEM ERROR (Resource
temporarily unavailable)**. This indicates that you are trying to
store a value larger than 1MB. MemCachier has a hard limit of 1MB for
the size of key-value pairs. To work around this, either consider
sharding the data or using a different technology. The benefit of an
in-memory key-value store diminishes at 1MB and higher.

## Python

You can use many Memcached clients for python. In this example we are
gonig to use `Python-Binary-Memcached` client with built in SASL
support. Run the following commands on your local machine:

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

## PHP

We recommended you use the [PHP Memcached
client](http://www.php.net/manual/en/book.memcached.php) to connect
with MemCachier. It supports the full protocol and has great
performance.

We’ve built a small PHP example. You can grab the
<a class="github-source-code" href="https://github.com/memcachier/examples-php">source code</a>.

First, if using composer, you'll need to modify your `composer.json`
file to include the module:

~~~js
{
    "require": {
        "php": ">=5.3.2",
        "ext-memcached": "*"
    }
}
~~~

Then, you can connect to MemCachier using the client:

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

  // create a new persistent client
  $m = new Memcached("memcached_pool");
  $m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);

  // some nicer default options
  $m->setOption(Memcached::OPT_NO_BLOCK, TRUE);
  $m->setOption(Memcached::OPT_AUTO_EJECT_HOSTS, TRUE);
  $m->setOption(Memcached::OPT_CONNECT_TIMEOUT, 2000);
  $m->setOption(Memcached::OPT_POLL_TIMEOUT, 2000);
  $m->setOption(Memcached::OPT_RETRY_TIMEOUT, 2);

  // setup authentication
  $m->setSaslAuthData($config['USERNAME'], $config['PASSWORD']);

  // We use a consistent connection to memcached, so only add in the
  // servers first time through otherwise we end up duplicating our
  // connections to the server.
  if (!$m->getServerList()) {
      $m->addServers($config['SERVERS']);
  }

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

You should look at the PHP
[Memcached client documentation](http://www.php.net/manual/en/book.memcached.php)
for a list of API calls you can make against MemCachier.

### Session Support

You can configure PHP to store sessions in MemCachier as follows.

First, start by configuring an appropriate `.user.ini` in your
document root. It should contain the following:

~~~php
session.save_handler=memcached
memcached.sess_binary=1
session.save_path="PERSISTENT=myapp_session ${MEMCACHIER_SERVERS}"
memcached.sess_sasl_username=${MEMCACHIER_USERNAME}
memcached.sess_sasl_password=${MEMCACHIER_PASSWORD}
~~~

In your code you should then be able to run:

~~~php
// Enable MemCachier session support
session_start();
$_SESSION['test'] = 42;
~~~

### Alternative PHP Client

This is not our recommended client for using MemCachier from PHP. We
recommend the [php memcached](#php) client. However, it may work
better for you if you are running into any problems with the php
memcached client.

You should first install the
[PHPMemcacheSASL](https://github.com/memcachier/PHPMemcacheSASL)
client. You can either grab the code directly or use
[composer](https://getcomposer.org/) for package management. We
suggest composer.

First, if using composer, you'll need to modify your `composer.json`
file to include the module:

~~~js
{
    "require": {
        "php": ">=5.3.2",
        "memcachier/php-memcache-sasl": ">=1.0.1"
    }
}
~~~

Then, you can connect to MemCachier using the client:

~~~php
require 'vendor/autoload.php';
use MemCachier\MemcacheSASL;

// Create client
$m = new MemcacheSASL();
$servers = explode(",", getenv("MEMCACHIER_SERVERS"));
foreach ($servers as $s) {
    $parts = explode(":", $s);
    $m->addServer($parts[0], $parts[1]);
}

// Setup authentication
$m->setSaslAuthData( getenv("MEMCACHIER_USERNAME")
                   , getenv("MEMCACHIER_PASSWORD") );

// Test client
$m->add("foo", "bar");
echo $m->get("foo");
~~~

## CakePHP

The CakePHP framework has excellent support for caching and can be
easily used with MemCachier as the provider. To setup CakePHP with
MemCachier, you'll need to edit the file `app/Config/bootstrap.php`
and add the following lines:

~~~php
Cache::config('default', array(
    'engine' => 'Memcached',
    'prefix' => 'mc_',
    'duration' => '+7 days',
    'servers' => explode(',', getenv('MEMCACHIER_SERVERS')),
    'compress' => false,
    'persistent' => 'memcachier',
    'login' => getenv('MEMCACHIER_USERNAME'),
    'password' => getenv('MEMCACHIER_PASSWORD'),
    'serialize' => 'php'
));
~~~

After that, you should be able to use caching throughout your application like
so:

~~~php
class Post extends AppModel {

    public function newest() {
        $model = $this;
        return Cache::remember('newest_posts', function() use ($model){
            return $model->find('all', array(
                'order' => 'Post.updated DESC',
                'limit' => 10
            ));
        }, 'longterm');
    }
}
~~~

The above will fetch the value associated with the key `newest_posts` from the
cache if it exists. Otherwise, it will execute the function and SQL query,
storing the result in the cache using the `newest_posts` key.

You can find much more information on how to use caching with CakePHP
[here](http://book.cakephp.org/2.0/en/core-libraries/caching.html).

## Node.js

For Node.js we recommend the use of the
[memjs](http://github.com/alevy/memjs) client library. It is written
and supported by MemCachier itself! To install, use the [node package
manager (npm)](http://npmjs.org/):

We’ve built a small Node.js example. You can grab the
<a class="github-source-code" href="https://github.com/memcachier/examples-node">source code</a>.

~~~
$ npm install memjs
~~~

Using it is straight-forward as memjs understands the
`MEMCACHIER_SERVERS`, `MEMCACHIER_USERNAME` and `MEMCACHIER_PASSWORD`
environment variables that the MemCachier add-on setups. For example:

~~~javascript
var memjs = require('memjs')
var mc = memjs.Client.create()
mc.get('hello', function(val) {
    alert(val)
})
~~~

## Java

In this short example we will show you how to integrate your Java
application with Memcachier Add-on. We recommend using the
[SpyMemcached](http://code.google.com/p/spymemcached/) client.
To use it in your project, just specify additional dependency in your
`pom.xml` file:

~~~xml
...
<dependency>
    <groupId>com.google.code.simple-spring-memcached</groupId>
    <artifactId>spymemcached</artifactId>
    <version>2.8.9</version>
</dependency>
...
~~~

Once your build system is configured, you can start adding caching to
your Java app:

##### Create Memcached SASL connection:

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

##### Use Memcachier:

~~~java
String user = System.getenv("MEMCACHIER_USERNAME");
String pass = System.getenv("MEMCACHIER_PASSWORD");
String addr = System.getenv("MEMCACHIER_SERVERS");
MemcachierConnection mc = new MemcachierConnection(user, pass, addr);
~~~

You can also find a ready-to-deploy example on
[Github](https://github.com/cloudControl/java-spring-jsp-example-app/tree/memcached_guide).

You may wish to look the `spymemcached`
[JavaDocs](http://dustin.github.com/java-memcached-client/apidocs/) or
some more [example
code](http://code.google.com/p/spymemcached/wiki/Examples) to help in
using MemCachier effectively.

## Client library support

MemCachier will work with any memcached binding that supports [SASL
authentication](http://en.wikipedia.org/wiki/Simple_Authentication_and_Security_Layer)
and the [binary
protocol](http://code.google.com/p/memcached/wiki/MemcacheBinaryProtocol).
We have tested MemCachier with the following language bindings,
although the chances are good that other SASL binary protocol packages
will also work.

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
  <a href="http://github.com/jbalogh/django-pylibmc">django-pylibmc</a>
</td>
</tr>
<tr>
<td>PHP</td>
<td>
  <a href="http://www.php.net/manual/en/book.memcached.php">PHP Memcached</a>
</td>
</tr>
<tr>
<td>Node.js</td>
<td>
  <a href="http://github.com/alevy/memjs">memjs</a>
</td>
</tr>
<tr>
<td>Java</td>
<td>
  <a href="http://code.google.com/p/spymemcached/">spymemcached</a>
  (version <b>2.8.9</b> or earlier) <b>or</b>
  <a href="http://code.google.com/p/xmemcached/">xmemcached</a>
</td>
</tr>
<tr>
<td>Go</td>
<td><a href="http://github.com/bmizerany/mc">mc</a></td>
</tr>
<tr>
<td>Haskell</td>
<td><a href="http://hackage.haskell.org/package/memcache">memcache</a></td>
</tr>
</tbody>
</table>

## Local setup

To test against your cloudControl application locally, you will need
to run a local Memcached process. MemCachier can only run on
cloudControl but because MemCachier and Memcached speak the same
protocol, you shouldn't have any issues testing it locally.
Installation depends on your platform.

This will install Memcached without SASL authentication support. This
is generally what you want as client code can still try to use SASL
auth and Memcached will simply ignore the requests which is the same
as allowing any credentials. So your client code can run without
modification locally and on cloudControl.

On Ubuntu:

~~~
$ sudo apt-get install memcached
~~~

Or on OS X (with Homebrew):

~~~
$ brew install memcached
~~~

Or for Windows please refer to [these
instructions](http://www.codeforest.net/how-to-install-memcached-on-windows-machine).

For further information and resources (such as the memcached source
code) please refer to the [Memcache.org
homepage](http://memcached.org)

To run memcached simply execute the following command:

~~~
$ memcached -v
~~~

## Usage analytics


Our analytics dashboard is a simple tool that gives you more insight
into how you’re using memcache.  Here's a screenshot of the dashboard:

![Analytics Dashboard](https://www.memcachier.com/images/analytics.png)

To access, just open your application's dashboard on CloudControl's
[web interface](https://www.cloudcontrol.com/console).

The analytics displayed are:

* _Limit_ -- Your current cache size and memory limit. Once usage comes
  close to this amount you will start seeing evictions.
* _Live Connections_ -- Number of connections currently open to your
  cache.
* _Total connections_ -- Number of connections ever made to your cache.
  (So always larger than live connections).
* _Items_ -- Number of items currently stored in your cache.
* _Evictions_ -- Number of items ever evicted from your cache due to
  memory pressure. Items are evicted in an LRU order.
* _New Evictions_ -- Number of evictions that have occured since the
  last time we sampled your cache.
* _Hit Rate_ -- The ratio of `get` commands that return an item (hit)
  vs. the number that return nothing (miss). This ratio is for the
  period between now and when we last sampled your cache.
* _Set Cmds_ -- Number of times you have ever performed a set command.
* _Flush Cmds_ -- Number of times you have ever performned a flush
  command.

With the basic analytics dashboard we sample your cache once per hour.
With the advance dashboard we sample it once every 30 minutes.

### Advanced analytics

We offer higher paying customers an advance version of our analytics
dashboard. Currently, this offers two primary advantages:

* _Higher Sample Rate_ -- We sample the cache for collecting analytics
  once every thirty minutes, twice the rate of the basic analytics
  dashboard. We don't sample more often than that as a higher
  granularity hasn't proven to be useful, it leads to more noise and
  less signal.
* _More Graphs_ -- We offer two additional graphs for the advanced
  analytics dashboard.
  * _Eviction Graph_ -- Your new evictions tracked over time.
  * _Connection Graph_ -- Your new connecions tracked over time.

## New Relic integration

MemCachier supports integration with your New Relic dashboard if you
happen to be a customer of both MemCachier and New Relic. Currently
this feature is only available to caches of <strong>500MB</strong> or
larger. A blog post showing the integration can be found
[here](http://blog.memcachier.com/2014/03/05/memcachier-and-new-relic-together/).

To setup the integration you will need to find your New Relic license
key. This can be done by going to your "Account Settings" page when
logged in to New Relic by click on your New Relic username in the top
right corner. Then you will find your license key in the right side
information column. It should be exactly 40 characters long. Please
refer to the [blog
post](http://blog.memcachier.com/2014/03/05/memcachier-and-new-relic-together/)
for a visual walkthrough.

Once you have your New Relic licence key, it can be entered for your
cache on the analytics dashboard page. In the bottom right corner
there is a button to do this.

## Upgrading and downgrading

Changing your plan, either by upgrading or downgrading, can be done
easily at any time through CloudControl.

* No code changes are required.
* Your cache won't be lost or reset except when moving between a free
  and paid plan as they are isolated clusters.
* You are charged by the hour for plans, so try experimenting with
  different cache sizes with low cost.

## Key-Value size limit (1MB)

MemCachier has a maximum size that a key-value object can be of
__1MB__. This applies to both key-value pairs created through a `set`
command, or existing key-value pairs grown through the use of an
`append` or `prepend` command. In the later case, the size of the
key-value pair with the new data added to it, must still be less than
1MB.

The 1MB limit applies to the size of the key and the value together. A
key of size 512KB with a value of 712KB would be in violation of the
1MB limit.

The reason for this has partially to do with how memory is managed in
MemCachier. A limitation of the high performance design is a
restriction on how large key-value pairs can become. Another reason is
that storing values larger than 1MB doesn't normally make sense in a
high-performance key-value store. The network transfer time in these
situations becomes the limiting factor for performance. A disk cache
or even a database makes sense for this size value.

## Sample apps

We've built a number of working sample apps, too:

* [Sinatra Example](https://github.com/memcachier/examples-sinatra)
* [Rails Example](https://github.com/memcachier/examples-rails)
* [Django Example](https://github.com/memcachier/examples-django)
* [PHP Example](https://github.com/memcachier/examples-php)
* [Node.js Example](https://github.com/memcachier/examples-node)
* [Java Example](https://github.com/memcachier/examples-java)

## Support

All Memcachier support and runtime issues should be submitted via one
of the
[cloudControl Support channels](https://www.cloudcontrol.com/dev-center/support).
Any non-support related issues or product feedback is welcome via
email at: [support@memcachier.com](mailto:support@memcachier.com).
Please include your `MEMCACHIER_USERNAME` with support tickets.

Any non-support related issues or product feedback is welcome via
email at: [support@memcachier.com](mailto:support@memcachier.com).

Any issues related to MemCachier service are reported at [MemCachier
Status](http://status.memcachier.com/).

Please also follow us on twitter,
[@memcachier](http://twitter.com/MemCachier), for status and product
announcements.
