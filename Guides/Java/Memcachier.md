#Memcachier

In this short tutorial we will show you how to easily integrate Java application with
[Memcachier Add-on](https://www.cloudcontrol.com/add-ons/memcachier), which manages and scales clusters of [Memcached](http://memcached.org/) servers.

##Memcached Java libraries:

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

##Example application:

We will modify existing [Spring/JSP hello world application](https://github.com/cloudControl/java-spring-jsp-example-app) to store visits counter in the `Memcached` provied by `Memcachier Add-on` and deploy it on [cloudControl](https://www.cloudcontrol.com/) platform.

Extend your [pom.xml](https://github.com/cloudControl/java-spring-jsp-example-app/blob/memcached_guide/pom.xml) with required `spymemcached` dependency and embedded Jetty runner. Define [Procfile](https://github.com/cloudControl/java-spring-jsp-example-app/blob/memcached_guide/Procfile).

###Create memcached SASL connection:

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

###Use Memcached to track visits counter:

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

###Use Memcachier in [example application](https://github.com/cloudControl/java-spring-jsp-example-app/blob/memcached_guide/src/main/java/com/cloudcontrolled/sample/spring/web/IndexController.java):

~~~java
VisitCounter vc = new VisitCounter();
vc.getVisitCount();
vc.updateVisitCount();
~~~

###Push, add Memcachier Add-on and deply:
~~~bash
$ cctrlapp APP_NAME/default push
$ cctrlapp APP_NAME/default addon.add memcachier.PLAN
$ cctrlapp APP_NAME/default deploy --max=4
~~~

You can also find ready-to-deply example on [Github](https://github.com/cloudControl/java-spring-jsp-example-app/tree/memcached_guide).
