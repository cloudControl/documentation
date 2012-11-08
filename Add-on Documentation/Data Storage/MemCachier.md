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
#* [Rails](#rails)
* [Python](#python)
#* [Django](#django)
* [PHP](#php)
* [Java](#java)

<p class="note">Your credentials may take up to three (3) minutes to be synced to our servers. You may see authentication errors if you start using the cache immediately.</p>

Ruby
------

Start by adding the [memcachier](https://github.com/memcachier/memcachier-gem) and [dalli](http://github.com/mperham/dalli) gems to your Gemfile.

    :::ruby
    gem 'memcachier'
    gem 'dalli'

Then bundle install:

    :::term
    $ bundle install

`Dalli` is a Ruby memcache client, and the `memcachier` gem modifies the environment (`ENV`) such that the environment variables set by MemCachier will work with Dalli. Once these gems are installed you can start writing code. The following is a basic example using Dalli.

~~~
    :::ruby
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

<p class="callout" markdown="1">
We’ve built a small Rails example here: [MemCachier Rails Sample App](https://github.com/memcachier/memcachier-gis).
</p>

Start by adding the [memcachier](https://github.com/memcachier/memcachier-gem) and [dalli](http://github.com/mperham/dalli) gems to your Gemfile.

    :::ruby
    gem 'memcachier'
    gem 'dalli'

Then bundle install:

    :::term
    $ bundle install

`Dalli` is a Ruby memcache client, and the `memcachier` gem modifies the environment (`ENV`) such that the environment variables set by MemCachier will work with Dalli. Once these gems are installed you’ll want to configure the Rails cache_store appropriately. Modify `config/environments/production.rb` with the following:

    :::ruby
    config.cache_store = :dalli_store

<p class="callout" markdown="1">In your development environment, Rails.cache defaults to a simple in-memory store and so it doesn’t require a running memcached.</p>

From here you can use the following code examples to use the cache in your Rails app:

    :::ruby
    Rails.cache.write("foo", "bar")
    puts Rails.cache.read("foo")

Without the `memcachier` gem, you’ll need to pass the proper credentials to Dalli in `config/environments/production.rb`:

    :::ruby
    config.cache_store = :dalli_store, ENV["MEMCACHIER_SERVERS"],
                        {:username => ENV["MEMCACHIER_USERNAME"],
                         :password => ENV["MEMCACHIER_PASSWORD"]}

Python
-----

You can use many memcached clients for python. In this example we are gonig to use `Python-Binary-Memcached` client with built in SASL support. Run the following commands on your local machine:

    :::term
    $ pip install python-binary-memcached
    $ pip freeze > requirements.txt

Make sure your `requirements.txt` file contains this requirement (note that your versions may differ than what’s below):

    python-binary-memcached==0.14

Then you can put this code in you server.py and start caching:

~~~
    :::python
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

~~~
:::php
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

More information on how to use php-memcached can be found on [php.net](http://php.net/manual/en/book.memcached.php).

Java
----

 This is an example of how to use the MemCachier Add-on with your Java WEB application.

 In order to use the addon with Java you need the [SpyMemcached](https://code.google.com/p/spymemcached/) client. We also recommend using the [Apache Maven](https://maven.apache.org/) build manager for working with Java applications. If you aren't using `maven` and are instead using [Apache Ant](https://ant.apache.org/) or your own build system, then simply add the `spymemcached` jar file as a dependency of your application.

For `maven` however, start by adding the proper `spymemcached` repository to your pom.xml:

    <repository>
      <id>spy</id>
      <name>Spy Repository</name>
      <layout>default</layout>
      <url>http://files.couchbase.com/maven2/</url>
      <snapshots>
        <enabled>false</enabled>
      </snapshots>
    </repository>

Then add the `spymemcached` library to your dependencies:

    <dependency>
      <groupId>spy</groupId>
      <artifactId>spymemcached</artifactId>
      <version>2.8.1</version>
      <scope>provided</scope>
    </dependency>

Once your build system is configured, you can start by adding caching to your Java app:

~~~ :::java
    package com.NAME.SPACE.PACKAGE;

    import java.io.IOException;
    import java.io.PrintWriter;
    import java.util.HashMap;

    import javax.servlet.ServletException;
    import javax.servlet.http.*;

    import org.eclipse.jetty.server.Server;
    import org.eclipse.jetty.servlet.*;

    import net.spy.memcached.AddrUtil;
    import net.spy.memcached.MemcachedClient;
    import net.spy.memcached.ConnectionFactoryBuilder;
    import net.spy.memcached.auth.PlainCallbackHandler;
    import net.spy.memcached.auth.AuthDescriptor;

    /*
    * Java WEB application with embedded Jetty server and MemCachier addon
    *
    */
    public class App extends HttpServlet
    {

        private static final long serialVersionUID = -96650638989718048L;

        @Override
        protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException
        {
          
            String ipAddress  = req.getHeader("X-FORWARDED-FOR");
            if(ipAddress == null)
            {
                ipAddress = req.getRemoteAddr();
            }

            System.out.println("Request received from: "+req.getLocalAddr());
            resp.setContentType("text/html");
            PrintWriter out = resp.getWriter();
            out.println("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">");
            out.println("<HTML>");
            out.println(" <HEAD><TITLE>Java Memcachier example</TITLE></HEAD>");
            out.println(" <BODY>");
            out.print("<center>");
            out.print("<h1>Hello " + ipAddress + "</h1>");
            out.print("This is visit number " + getVisit(ipAddress) + ".");
            out.print("</center>");
            out.println(" </BODY>");
            out.println("</HTML>");
            out.flush();
            out.close();
        }

        public static void main(String[] args) throws Exception
        {
          
            Server server = new Server(Integer.valueOf(System.getenv("PORT")));
            ServletContextHandler context = new ServletContextHandler(ServletContextHandler.SESSIONS);
            context.setContextPath("/");
            server.setHandler(context);
            context.addServlet(new ServletHolder(new App()),"/*");
            server.start();
            server.join();
            System.out.println("Application started");
        }

        private int getVisit(String ipAddr) throws IOException{
            Credentials cr = Credentials.getInstance();
            String addon = "MEMCACHIER"; // capital letters not required
            HashMap<String, Object> creds = new HashMap<String, Object>();
            creds.put("srv", cr.getCredential("servers", addon));
            creds.put("usr", cr.getCredential("username", addon));
            creds.put("pwd", cr.getCredential("password", addon));

            AuthDescriptor ad = new AuthDescriptor(new String[] { "PLAIN" },
                    new PlainCallbackHandler((String)creds.get("usr"),
                        (String)creds.get("pwd")));

            int count=0;

            try {
                MemcachedClient mc = new MemcachedClient(new ConnectionFactoryBuilder()
                        .setProtocol(ConnectionFactoryBuilder.Protocol.BINARY)
                        .setAuthDescriptor(ad).build(), AddrUtil.getAddresses(
                            (String)creds.get("srv") + ":11211"));

                if(mc.get(ipAddr)!=null){
                    count=(Integer)mc.get(ipAddr);
                }

                count++;
                mc.set(ipAddr,0,count);

            } catch (IOException ioe) {
                System.out.println("Couldn't create a connection to MemCachier: \nIOException "
                        + ioe.getMessage());
            }

            return count;
        }
    }
~~~

You also need to follow the [Getting Add-on credentials](https://github.com/cloudControl/documentation/blob/master/Guides/Java/GetCredentials.md) in order to authenticate your Add-on.

You may wish to look the `spymemcached` [JavaDocs](http://dustin.github.com/java-memcached-client/apidocs/) or some more [example code](https://code.google.com/p/spymemcached/wiki/Examples) to help in using MemCachier effectively.

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
  <a href="http://github.com/ronnywang/PHPMemcacheSASL">PHPMemcacheSASL</a>
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

<p class="callout" markdown="1">This will install memcached without SASL authentication support. This is generally what you want as client code can still try to use SASL auth and memcached will simply ignore the requests which is the same as allowing any credentials. So your client code can run without modification locally and on cloudControl.</p>

On Ubuntu:

    :::term
    $ sudo apt-get install memcached

Or on OS X (with Homebrew):

    :::term
    $ brew install memcached

Or for Windows please refer to [these instructions](http://www.codeforest.net/how-to-install-memcached-on-windows-machine).

For further information and resources (such as the memcached source
code) please refer to the [Memcache.org
homepage](http://memcached.org)

To run memcached simply execute the following command:

    :::term
    $ memcached -v

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

All Memcachier support and runtime issues should be submitted via on of the cloudControl Support channels](https://www.cloudcontrol.com/dev-center/support). Any non-support related issues or product feedback is welcome via email at: [support@memcachier.com](mailto:support@memcachier.com)

Any issues related to Memcachier service are reported at [Memcachier Status](http://status.memcachier.com/).
