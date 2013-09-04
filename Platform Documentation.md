# CloudControl Platform Documentation

CloudControl is a PaaS platform that enables seamless deployment and scaling of apps written in any programming language. With CloudControl, you can get your apps up and running in minutes without worrying about the underlying server infrastructure. In addition, CloudControl also gives you also get full control of your apps - simple management and monitoring of all your app deployments using the easy-to-use interface. 
This document serves as a guide to administrators and developers on how to configure, deploy, and run your apps on CloudControl. If you’re new to CloudControl and would like to get started 
with CloudControl, you should check out the [CloudControl quickstart guide](https://github.com/cloudControl/documentation/blob/master/Quickstart.md).

Before we go into the details of CloudControl, let’s go over the high level architecture of the platform - 

##CloudControl Platform Architecture
CloudControl is a multi-tenant PaaS platform that is architected for high resiliency, superior performance, and scale. Following are some components of CloudControl’s architecture that make it unique -

###Client Architecture - Connecting to the cloud 
Client server communication in the CloudControl platform is based on the REST protocol.  Using REST, the client must include all information for the server to fulfill the request including state.  

![](http://oi42.tinypic.com/n6uxrt.jpg)

There are several ways to connect to the CloudControl platform such as -  

####Command Line Interface (CLI)
The command line interface tool *cctrl* is the primary way of managing apps in CloudControl. It directly interacts with the RESTful API in the CloudControl environment, allowing you to control CloudControl environment features like creating users, configuring add-ons, logging, caching, and routing. In addition, you can also control the app deployment and lifecycle.  The CLI consists of 2 parts : *cctrlapp* and *cctrluser* as shown in the figures below.

![](http://oi44.tinypic.com/29ehaqg.jpg)
![](http://oi42.tinypic.com/dfeoft.jpg)

#####Installing the command line interface 
If you’re using *Windows*, we offer a setup exe that can be downloaded from [here](https://github.com/cloudControl/cctrl/downloads). 

For *Linux/Mac*, we recommend installing and updating cctrl via pip. As a prerequisite, cctrl requires [python 2.6](http://www.python.org/download/releases/2.6/) or higher.

~~~
$sudo pip install -U cctrl
~~~

If you don’t have pip, you can install pip using [easy_install](https://pythonhosted.org/setuptools/easy_install.html). To install pip -

~~~
$ sudo easy_install pip
$ sudo pip install -U cctrl
~~~

####Web Console
In addition to the command line interface, the CloudControl platform also provides a web console to manage and control your apps. As shown in the figure below, user *dodil* has deployed two apps - *donsampleapp* and *hellodon* app. The git repository for the app’s source is also listed in the console. 

![](http://oi42.tinypic.com/2mplwk2.jpg)

By clicking the name of the app, you can get more details about the app such as the number of containers used, memory consumed, add-ons applied, and the cost of running the app.

![](http://oi41.tinypic.com/24w7llu.jpg)

Using the web console, you can add users to your app, assign different roles to these users and drop users. If you are no longer using the app, you can also delete the app. 

![](http://oi44.tinypic.com/344pef4.jpg)

![](http://oi39.tinypic.com/2n8v96p.jpg) Every app needs to have an owner. If you delete an app on the CloudControl platform, it will be permanently deleted and you will not be able to access it on the '*.cloudcontrol.com' domain.

###Server Architecture - In the cloud
On the surface, CloudControl makes it easy for developers to build and deploy their apps on the platform. But, there are several components in the background that make this possible. This section goes over them -
 
####Routing Tier 
CloudControl apps run inside containers. Containers provide logical abstraction over servers and are self contained resources for running apps.  The role of the routing tier is to map external app requests to the app containers. When a request is received, the routing tier takes the request and routes them to the appropriate app container. It is designed to be robust against single node or even complete datacenter failures and is responsible for keeping latency low.
 
The routing layer resolves all subdomain requests of the form ‘*.cloudcontrolled.com’ in a round-robin fashion and maps them to node IP addresses. It does so by matching host header values in the request packet to the list of deployment aliases. The physical nodes are equally distributed in three different availability zones so the routing tier can route requests to any container in any other availability zone. This happens automatically in the event of a failure, if there is a problem with the code in the container, and for evenly spreading the load. 

To keep latency low, the routing tier first tries to route requests to containers in the same availability zone. If no containers are available in the same zone  to service the request, the request is then forwarded to containers in other zones. 

![](http://oi41.tinypic.com/119z8ye.jpg) Deployments running with --containers option as 1 only run in one container and therefore only in one availability zone. As a best practise, it is recommended to run your app with more than 1 container for higher availability and scale.

![](http://oi39.tinypic.com/2n8v96p.jpg) Deployments running on --containers 1 will be unavailable for a couple of minutes until a replacement container has been started. To avoid even short downtimes in the event of a single node or container failure set --containers >= 2.

Because of the elastic nature of the routing tier,  the list of routing tier addresses can change at any time. It is therefore highly discouraged to point custom domains directly to any of the routing tier IP addresses. Instead, CNAMEs should be used.  Refer to the custom domain section for more details on how to properly configure your DNS settings. 

####Caching Tier
Interactive apps need caching architectures for high performance. Caches are used to cache expensive database queries and HTML pages so that these expensive operations don’t need to happen over and over again.  

Typically, on the client,  javascript and css files are compressed and combined into one file, and sprites are used for images. On the server side, query responses are served from a cache instead of hitting the database on every single request. 

![](http://oi41.tinypic.com/119z8ye.jpg) It is generally recommended to cache as far away from your database as possible. On the client side, you can accomplish this by setting expiry header timestamps far in the future. 

#####Caching in the CloudControl platform
CloudControl provides caching directly in the load-balancing and routing tier. This is implemented as a Varnish caching proxy. To cache your requests and speed up the response time, you must set correct varnish cache control headers for the request. 

![](http://oi39.tinypic.com/2n8v96p.jpg) To avoid cache collisions and polluting the cache with large number of copies of the same data, Varnish does not cache a page if the cookie request-header or set-cookie response header is present. For this reason, you must use cookieless domains. 

To check whether a request was cached in Varnish, check the response’s  X-varnish-cache header value. A ‘HIT means that the response was served from the cache. A ‘MISS’ indicates that the response was not cached in varnish.

#####In-memory caching using MemCache
If you app uses cookies and you need caching, you can use CloudControl’s MemCachier add-on to cache arbitrary data from database query results to complete HTTP responses. Memcachier is an implementation of the Memcached protocol and is used for caching data. It manages and scales clusters of Memcached servers so that you can scale your app and reduce your server loads. 

![](http://oi39.tinypic.com/29ff04i.jpg)

![](http://oi41.tinypic.com/119z8ye.jpg) Since the CloudControl routing tier distributes requests across all available containers, it is recommended to use an in-memory caching so that the cached data can be shared across all the containers. 

For more about add-ons in CloudControl, check the [managing add-ons section](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#managing-add-ons). More details about Memcachier on how to use it with your language and framework of choice can be found [here](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MemCachier).

#####Cache Breakers
When caching requests on the client side or in a caching proxy, the URL is usually used as the cache identifier. The request can be answered from the cache as long as the URL stays the same, the key resides in the cache, and the cache response has not expired.

As part of every deployment, all containers are started from a clean image. This ensures that all containers have the latest app code including templates, css, image and javascript files. But if far future expire headers are used, and the response was cached at client or at the loadbalancer level, it might not get invalidated. To force a cache invalidation, so that all clients can see the latest and greatest version, the URL needs to be changed. This is commonly referred to as a cache breaker. 

As part of the set of environment variables in the deployment runtime environment the DEP_VERSION is made available to the app. If you want to force a refresh of the cache when a new version is deployed, you can use the DEP_VERSION environment variable to accomplish this. This technique works for URLs as well as keys in the in-memory cache such as memcached. 
For example, imagine you use memcached for caching. You need to keep some between deploys, and refresh the others on every deployment. By including the DEP_VERSION as part of the key of the cached values, you can only refresh a subset of the cached keys. 

####Execution Environment
The execution environment or runtime is the context in which the execution of a system takes place. It is a framework that ensures a predictable environment for applications running on the CloudControl platform. Before we discuss about the components of CloudControl’s execution environment, it is imperative to know about stacks and environment variables. 

*Stacks* define the common runtime environment.  They are based on ubuntu with stack names beginning with the first letter of ubuntu releases and named after a superhero sidekick. Following are the stacks supported by CloudControl - 

* *Luigi* based on [Ubuntu 10.04 LTS Lucid Lynx](http://releases.ubuntu.com/lucid/)
* *Pinky* based on [Ubuntu 12.04 LTS Precise Pangolin](http://releases.ubuntu.com/precise/)

![](http://oi39.tinypic.com/2n8v96p.jpg) Luigi only supports PHP. Pinky supports multiple languages according to the available [buildpacks](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile).

#####Which stack is your app deployed on?
If you want to check which stack is your current deployment on, use the cctrlapp details command as shown below - 

![](http://oi43.tinypic.com/2vwbn1i.jpg)

![](http://oi41.tinypic.com/119z8ye.jpg) Prior to moving an app into production on a different stack, changing stacks per deployment can come in handy for testing whether the app works on the new stack. 

#####An example of moving stacks 
If you have an app that is deployed on the pinki stack and you want to move it to a luigi stack, you can use the cctrlapp deploy command as shown below -

![](http://oi42.tinypic.com/1zf7i0y.jpg)

To verify that the deployment completed, you can use the cctrlapp details command -

![](http://oi40.tinypic.com/2qtjo2d.jpg)

![](http://oi41.tinypic.com/119z8ye.jpg) We try to keep our stacks close to Ubuntu release as possible, but do make changes when necessary for security or performance reasons to optimize the stack for your specific use-case.

*Environment variables* are variables that are configured for each application deployed on the CloudControl environment. These variables are the means by which the CloudControl execution environment communicates with each deployed application. For example, an app running on the CloudControl platform may need to know where temporary files are stored. To find this out, it can check for the TMPDIR environment variable which will have this information.
 
The following environment variables are available for use by each app running on the CloudControl platform - 



