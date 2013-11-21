<<<<<<< HEAD
# CloudControl Platform Documentation
=======
<aside>
<ul>
<li class=""><a href="#platform-access">Platform Access</a></li>
<li class=""><a href="#user-accounts">User Accounts</a></li>
<li class=""><a href="#apps-users-and-deployments">Apps, Users and Deployments</a></li>
<li class=""><a href="#version-control--images">Version Control & Images</a></li>
<li class=""><a href="#deploying-new-versions">Deploying New Versions</a></li>
<li class=""><a href="#emergency-rollback">Emergency Rollback</a></li>
<li class=""><a href="#non-persistent-filesystem">Non-Persistent Filesystem</a></li>
<li class=""><a href="#development-staging-and-production-environments">Development, Staging and Production Environments</a></li>
<li class=""><a href="#add-ons">Add-ons</a></li>
<li class=""><a href="#logging">Logging</a></li>
<li class=""><a href="#provided-subdomains-and-custom-domains">Provided Subdomains and Custom Domains</a></li>
<li class=""><a href="#routing-tier">Routing Tier</a></li>
<li class=""><a href="#scaling">Scaling</a></li>
<li class=""><a href="#performance--caching">Performance & Caching</a></li>
<li class=""><a href="#websockets">WebSockets</a></li>
<li class=""><a href="#scheduled-jobs-and-background-workers">Scheduled Jobs and Background Workers</a></li>
<li class=""><a href="#secure-shell-ssh">Secure Shell (SSH)</a></li>
<li class=""><a href="#stacks">Stacks</a></li>
</ul>
</aside>
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

CloudControl is a PaaS platform that enables seamless deployment and scaling of apps written in any programming language. With CloudControl, you can get your apps up and running in minutes without worrying about the underlying server infrastructure. In addition, CloudControl also gives you also get full control of your apps - simple management and monitoring of all your app deployments using the easy-to-use interface. 
This document serves as a guide to administrators and developers on how to configure, deploy, and run your apps on CloudControl. If you’re new to CloudControl and would like to get started 
with CloudControl, you should check out the [CloudControl quickstart guide](https://github.com/cloudControl/documentation/blob/master/Quickstart.md).

Before we go into the details of CloudControl, let’s go over the high level architecture of the platform - 

##CloudControl Platform Architecture
CloudControl is a multi-tenant PaaS platform that is architected for high resiliency, superior performance, and scale. Following are some components of CloudControl’s architecture that make it unique -

###Client Architecture - Connecting to the cloud 
Client server communication in the CloudControl platform is based on the REST protocol.  Using REST, the client must include all information for the server to fulfill the request including state.  

<<<<<<< HEAD
![](http://oi42.tinypic.com/n6uxrt.jpg)

There are several ways to connect to the CloudControl platform such as -  

####Command Line Interface (CLI)
The command line interface tool *cctrl* is the primary way of managing apps in CloudControl. It directly interacts with the RESTful API in the CloudControl environment, allowing you to control CloudControl environment features like creating users, configuring add-ons, logging, caching, and routing. In addition, you can also control the app deployment and lifecycle.  The CLI consists of 2 parts : *cctrlapp* and *cctrluser* as shown in the figures below.
=======
To control the platform we offer different interfaces. The primary way of controlling your apps and deployments is via [the command-line interface](http://en.wikipedia.org/wiki/Command-line_interface) (CLI) called *cctrl*. Additionally we also offer a [web console]. Both the CLI as well as the web console however are merely frontends to our RESTful API. For deep integration into your apps you can optionally use one of our available [API libraries].

Throughout this documentation we will use the CLI as the primary way of controlling the cloudControl platform. The CLI consists of 2 parts: *cctrlapp* and *cctrluser*. To get help for the command line client, just append --help or -h to any of the commands.

Installing *cctrl* is easy and works on Mac/Linux as well as on Windows.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

![](http://oi44.tinypic.com/29ehaqg.jpg)
![](http://oi42.tinypic.com/dfeoft.jpg)

<<<<<<< HEAD
#####Installing the command line interface 
If you’re using *Windows*, we offer a setup exe that can be downloaded from [here](https://github.com/cloudControl/cctrl/downloads). 

For *Linux/Mac*, we recommend installing and updating cctrl via pip. As a prerequisite, cctrl requires [python 2.6](http://www.python.org/download/releases/2.6/) or higher.
=======
For Windows we offer an installer. Please download [the latest version] of the installer from S3. The file is named cctrl-x.x-setup.exe.

#### Quick Installation Linux/Mac

On Linux and Mac OS we recommend installing and updating cctrl via pip. *cctrl* requires [Python 2.6+].
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

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

<<<<<<< HEAD
By clicking the name of the app, you can get more details about the app such as the number of containers used, memory consumed, add-ons applied, and the cost of running the app.

![](http://oi41.tinypic.com/24w7llu.jpg)

Using the web console, you can add users to your app, assign different roles to these users and drop users. If you are no longer using the app, you can also delete the app. 

![](http://oi44.tinypic.com/344pef4.jpg)
=======
 * Every developer has their own user account
 * User accounts can be created via the *web console* or via ``cctrluser create``
 * User accounts can be deleted via the *web console* or via ``cctrluser delete``

To work on and manage your applications on the platform, a user account is needed. User accounts can be created via the *web console* or using the following CLI command:
~~~
$ cctrluser create
~~~

After this, an activation email is sent to the given email address. Click the link in the email or use the following CLI command to activate the account:

~~~
$ cctrluser activate USER_NAME ACTIVATION_CODE
~~~

If you want to delete your user account, please use either the *web console* or the following CLI command:
~~~
$ cctrluser delete
~~~
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

![](http://oi39.tinypic.com/2n8v96p.jpg) Every app needs to have an owner. If you delete an app on the CloudControl platform, it will be permanently deleted and you will not be able to access it on the '*.cloudcontrol.com' domain.

<<<<<<< HEAD
###Server Architecture - In the cloud
On the surface, CloudControl makes it easy for developers to build and deploy their apps on the platform. But, there are several components in the background that make this possible. This section goes over them -
 
####Routing Tier 
CloudControl apps run inside containers. Containers provide logical abstraction over servers and are self contained resources for running apps.  The role of the routing tier is to map external app requests to the app containers. When a request is received, the routing tier takes the request and routes them to the appropriate app container. It is designed to be robust against single node or even complete datacenter failures and is responsible for keeping latency low.
 
The routing layer resolves all subdomain requests of the form ‘*.cloudcontrolled.com’ in a round-robin fashion and maps them to node IP addresses. It does so by matching host header values in the request packet to the list of deployment aliases. The physical nodes are equally distributed in three different availability zones so the routing tier can route requests to any container in any other availability zone. This happens automatically in the event of a failure, if there is a problem with the code in the container, and for evenly spreading the load. 
=======
You can [reset your password], in case you forgot it.

>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

To keep latency low, the routing tier first tries to route requests to containers in the same availability zone. If no containers are available in the same zone  to service the request, the request is then forwarded to containers in other zones. 

![](http://oi41.tinypic.com/119z8ye.jpg) Deployments running with --containers option as 1 only run in one container and therefore only in one availability zone. As a best practise, it is recommended to run your app with more than 1 container for higher availability and scale.

<<<<<<< HEAD
![](http://oi39.tinypic.com/2n8v96p.jpg) Deployments running on --containers 1 will be unavailable for a couple of minutes until a replacement container has been started. To avoid even short downtimes in the event of a single node or container failure set --containers >= 2.
=======
 * Applications (apps) have a repository, deployments and users.
 * The repository is where your code lives, organized in branches.
 * A deployment is a running version of your application, based on the branch with the same name. Exception: the default deployment is based on the master (Git) / trunk (Bazaar).
 * Users can be added to apps to gain access to the repository, branches and deployments.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

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

<<<<<<< HEAD
![](http://oi39.tinypic.com/29ff04i.jpg)
=======
By adding users to an app you can grant fellow developers access to the source code in the repository, allow them to [deploy new versions](#deploying-new-versions) and modify the deployments including their [Add-ons](#managing-add-ons). Permissions are based on
the user's [roles](#roles). Users can be added to applications or more fine grained to deployments.

You can list, add and remove app users using the command line client.

~~~
$ cctrlapp APP_NAME user

Users
 Name		Email				 	Role		Deployment
 user1		user1@example.com		admin		(app)
 user2		user2@example.com		readonly	production
 user3		user3@example.com		admin		staging
~~~


Add a user to an app by providing their email address. If the user is already registered they will be added to the app immediately. Otherwise they will receive an invitation email first.

~~~
$ cctrlapp APP_NAME user.add user4@example.com
~~~

To remove a user, please use their email address.
~~~
$ cctrlapp APP_NAME user.remove user3@example.com
~~~

On deployment level:
~~~
$ cctrlapp APP_NAME/DEP_NAME user.add user5@example.com
$ cctrlapp APP_NAME/DEP_NAME user.remove user5@example.com
~~~
Please note: a user can either be added to the application or to one or more deployments.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

![](http://oi41.tinypic.com/119z8ye.jpg) Since the CloudControl routing tier distributes requests across all available containers, it is recommended to use an in-memory caching so that the cached data can be shared across all the containers. 

<<<<<<< HEAD
For more about add-ons in CloudControl, check the [managing add-ons section](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#managing-add-ons). More details about Memcachier on how to use it with your language and framework of choice can be found [here](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MemCachier).
=======
 * **Owner**: Creating an app makes you the owner and gives you full access. The owner can not be removed from the app and gets charged for all their apps' consumption.
 * **Admin**: The default role for users added to an app is the Admin role. Admins have full access to the repository and to all deployments. Admins can add more Admin or Read-only users or remove existing ones. They can delete deployments and even the app itself. Admins however can not change the associated billing account or remove the owner.
 * **Read-only** The Read-only role allows you to see the application details, deployments and logs. Any update operation is forbidden.

You can provide the role with the `user.add` command.

~~~
$ cctrlapp APP_NAME user.add user5@example.com --role readonly
~~~
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

#####Cache Breakers
When caching requests on the client side or in a caching proxy, the URL is usually used as the cache identifier. The request can be answered from the cache as long as the URL stays the same, the key resides in the cache, and the cache response has not expired.

<<<<<<< HEAD
As part of every deployment, all containers are started from a clean image. This ensures that all containers have the latest app code including templates, css, image and javascript files. But if far future expire headers are used, and the response was cached at client or at the loadbalancer level, it might not get invalidated. To force a cache invalidation, so that all clients can see the latest and greatest version, the URL needs to be changed. This is commonly referred to as a cache breaker. 
=======
For secure access to the app's repository, each developer needs to authenticate via public/ private key authentication. Please refer to GitHub's article on [generating SSH keys] for details on how to create a key. You can simply add your default key to your user account using the *web console* or the command line client. If no default key can be found, cctrlapp will offer to create one.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

As part of the set of environment variables in the deployment runtime environment the DEP_VERSION is made available to the app. If you want to force a refresh of the cache when a new version is deployed, you can use the DEP_VERSION environment variable to accomplish this. This technique works for URLs as well as keys in the in-memory cache such as memcached. 
For example, imagine you use memcached for caching. You need to keep some between deploys, and refresh the others on every deployment. By including the DEP_VERSION as part of the key of the cached values, you can only refresh a subset of the cached keys. 

<<<<<<< HEAD
####Execution Environment
The execution environment or runtime is the context in which the execution of a system takes place. It is a framework that ensures a predictable environment for applications running on the CloudControl platform. Before we discuss about the components of CloudControl’s execution environment, it is imperative to know about stacks and environment variables. 

*Stacks* define the common runtime environment.  They are based on ubuntu with stack names beginning with the first letter of ubuntu releases and named after a superhero sidekick. Following are the stacks supported by CloudControl - 
=======
You can also list the available key ids and remove existing keys using the key id.

~~~
$ cctrluser key
Keys
 Dohyoonuf7

$ cctrluser key Dohyoonuf7
ssh-rsa AAA[...]

$ cctrluser key.remove Dohyoonuf7
~~~
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

* *Luigi* based on [Ubuntu 10.04 LTS Lucid Lynx](http://releases.ubuntu.com/lucid/)
* *Pinky* based on [Ubuntu 12.04 LTS Precise Pangolin](http://releases.ubuntu.com/precise/)

<<<<<<< HEAD
![](http://oi39.tinypic.com/2n8v96p.jpg) Luigi only supports PHP. Pinky supports multiple languages according to the available [buildpacks](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile).

#####Which stack is your app deployed on?
If you want to check which stack is your current deployment on, use the cctrlapp details command as shown below - 
=======
A deployment is the running version of one of your branches made accessible via a [provided subdomain](#provided-subdomains-and-custom-domains).
It is based on the branch of the same name. Exception: the default deployment is based on the master (Git) / trunk (Bazaar).

Deployments run independently from each other, including separate runtime environments, file system storage and Add-ons (e.g. databases and caches).
This allows you to have different versions of your app running at the same time without interfering with each other.
Please refer to the section about [development, staging and production environments](#development-staging-and-production-environments) to understand why this is a good idea.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

![](http://oi43.tinypic.com/2vwbn1i.jpg)

![](http://oi41.tinypic.com/119z8ye.jpg) Prior to moving an app into production on a different stack, changing stacks per deployment can come in handy for testing whether the app works on the new stack. 

#####An example of moving stacks 
If you have an app that is deployed on the pinki stack and you want to move it to a luigi stack, you can use the cctrlapp deploy command as shown below -

![](http://oi42.tinypic.com/1zf7i0y.jpg)

<<<<<<< HEAD
To verify that the deployment completed, you can use the cctrlapp details command -
=======

## Version Control & Images
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

![](http://oi40.tinypic.com/2qtjo2d.jpg)

![](http://oi41.tinypic.com/119z8ye.jpg) We try to keep our stacks close to Ubuntu release as possible, but do make changes when necessary for security or performance reasons to optimize the stack for your specific use-case.

**Environment variables** are variables that are configured for each application deployed on the CloudControl environment. These variables are the means by which the CloudControl execution environment communicates with each deployed application. For example, an app running on the CloudControl platform may need to know where temporary files are stored. To find this out, it can check for the TMPDIR environment variable which will have this information.
 
The following environment variables are available for use by each app running on the CloudControl platform - 

<<<<<<< HEAD
<table>
  <tr>
    <th>Environment variable</th><th>Description</th>
  </tr>
  <tr>
    <td>TMPDIR</td><td>The path to the TMP directory</td>
  </tr>
  <tr>
    <td>CRED_FILE</td><td>Path of the creds.json file containing add-on credentials</td>
  </tr>
  <tr>
    <td>DEP_VERSION</td><td>GIT or BAZAAR version the image was build from</td>
  </tr>
  <tr>
    <td>DEP_NAME</td><td>The deployment name in the same format as used by the command client. For example: myapp/default. This one stays the same even when undeploying and creating a new deployment with the same name</td>
  </tr>
  <tr>
    <td>DEP_ID</td><td>Internal deployment ID. This one stays the same for deployments lifetime but changes when undeploying and deploying with same name</td>
  </tr>
  <tr>
    <td>WRK_ID</td><td>Internal worker ID. Only set for worker containers</td>
  </tr>
</table>
=======
The platform supports Git ([quick Git tutorial]) and Bazaar ([Bazaar in five minutes]). When you create an app we try to determine if the current working directory has a .git or .bzr directory. If it does, we create the app with the detected version control system. If we can't determine this based on the current working directory, Git is used as the default. You can always overwrite this with the --repo command line switch.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

####Containers
Containers are used to run apps in the CloudControl environment. They receive requests from the routing tier. Following are the different kinds of containers available on the CloudControl platform - 

**Web Containers** run web components for apps on the CloudControl platform. If you’re running a web app, the routing tier routes requests to the web container on specific ports specified in the http requests. For a container to be able to receive requests from the routing tier, you need to start the app in the container to listen on a port using the following command - 

~~~
web: COMMAND_TO_START_THE_APP_AND_LISTEN_ON_A_PORT --port $PORT
~~~

**Worker containers** run long running asynchronous processes. They are typically used for executing background tasks such as sending emails to running heavy calculations or rebuilding caches. Increasing the number of workers increases the amount of background work done.

<<<<<<< HEAD
On the CloudControl platform, each worker is started via the worker add-on and runs in a separate container. Each container has exactly the same runtime environment as defined by the stack chosen and the buildpack that is used. Each container also has the same access to all of the deployment add-ons. 

Before you can start a single worker, add the worker add-on with the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add worker.single
~~~
=======
Whenever you push an updated branch, a deployment image is built automatically.
This image can then be deployed with the *deploy* command to the deployment matching the branch name.
The content of the image is generated by the [buildpack](#buildpacks-and-the-procfile) including your application code in a runnable form with all the dependencies.

You can either use the cctrlapp push command or your version control system's push command. Please remember that deployment and branch names have to match. So to push to your dev deployment the following commands are interchangeable. Also note, both require the existence of a branch called dev.

~~~
# with cctrlapp:
$ cctrlapp APP_NAME/dev push

# get the REPO_URL from the output of cctrlapp APP_NAME details

# with git:
$ git remote add cctrl REPO_URL
$ git push cctrl dev

# with bzr:
$ bzr push --remember REPO_URL
~~~

The repositories support all other remote operations like pulling and cloning as well.

The compressed image size is limited to 200MB.
Smaller images can be deployed faster, so we recommend to keep the image size below 50MB.
The image size is printed at the end of the build process; if the image exceeds the limit, the push gets rejected.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

Multiple workers can be started by setting the WORKER_PARAMS value in the worker.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker.add WORKER_NAME [WORKER_PARAMS]
~~~

<<<<<<< HEAD
Workers can be either stopped via the command line client or by exiting the process with a zero exit code. To stop a running worker via the command line use the worker.remove command.
=======
#### Buildpacks and the Procfile

During the push a hook is fired that runs the buildpack. A buildpack is a set of scripts that determine how an app in a specific language or framework has to be prepared for deployment on the cloudControl platform. With custom buildpacks, support for new programming languages can be added or custom runtime environments can be build. To support many PaaS with one buildpack, we recommend following the [Heroku buildpack API] which is compatible with cloudControl and other platforms.

Part of the buildpack scripts is also to pull in dependencies according to the languages or frameworks native way. E.g. pip and a requirements.txt for Python, Maven for Java, npm for node.js, Composer for PHP and so on. This allows you to fully control the libraries and versions available to your app in the final runtime environment.

Which buildpack is going to be used is determined by the application type set when creating the app.

A required part of the image is a file called `Procfile` in the root directory. It is used to determine how to start the actual application in the container. Some of the buildpacks can provide a default Procfile. But it is recommended to explicitly define the Procfile in your application to match your individual requirements better. For a container to be able to receive requests from the routing tier it needs at least the following content:
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$ cctrlapp APP_NAME/DEP_NAME worker.remove WRK_ID
~~~

<<<<<<< HEAD
To remove the worker add-on use the addon.remove command.
=======
For more specific examples of a `Procfile` please refer to the language and framework [guides].

At the end of the buildpack process, the image is ready to be deployed.


## Deploying New Versions

The cloudControl platform supports zero downtime deploys for all deployments. To deploy a new version use either the *web console* or the `deploy` command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove worker.single
~~~

####File systems
To store, update and retrieve data files for your app, you need to have filesystem. Typically, one file system per container. CloudControl supports both persistent and non-persistent file systems.

<<<<<<< HEAD
**Persistent file systems** like Amazon’s S3 or MongoLab’s GridFS are available on the CloudControl platform through add-ons. 

GridFS is a module for MongoDB that allows to store large files in the database. It breaks large files into manageable chunks. When you query for a file, GridFS queries the chunks and returns the file one piece at a time. GridFS is useful especially for storing files over 4MB. 

The MongoLab add-on can be added to any deployment from the cctrlapp command line using -
=======
For every deploy, the image is downloaded to as many of the platform’s nodes as required by the [--containers setting](#scaling) and started according to the buildpack’s default or the [Procfile](#buildpacks-and-the-procfile).
After the new containers are up and running the load balancing tier stops sending requests to the old containers and instead sends them to the new version.
A log message in the [deploy log](#deploy-log) appears when this process has finished.


## Emergency Rollback

If for some reason a new version does not work as expected, you can rollback any deployment to a previous version in a matter of seconds. To do so you can check the [deploy log](#deploy-log) for the previously deployed version (or get it from the version control system directly) and then simply use the Git or Bazaar version identifier that's part of the log output. You can redeploy this version using the deploy command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add mongolab.OPTION
~~~

<<<<<<< HEAD
For OPTION, select one of MongoLab's plan offerings: free, small, medium, large, or xlarge. When added, MongoLab automatically creates a new user account and launches a MongoDB database on an Amazon EC2 instance. By clicking the MongoLab add-on entry on the apps’s deployment page, you can manage the MongoDB database and take a look at the data stored. 
The MongoLab database connection URI is provided in the CRED_FILE environment variable, a JSON document that stores credentials for the add-on providers. You can find more information about MongoLab add-on for CloudControl [here](https://www.cloudcontrol.com/add-ons/mongolab).
=======

## Non-Persistent Filesystem

**TL;DR:**
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

S3 is an online file storage service from Amazon. You can store arbitrary objects up to 5Tb in size. Files in S3 are organized into buckets and identified within each bucket by a unique key. Files can be created, listed, and retrieved using a REST interface. S3 can be used for a wide variety of uses, ranging from Web applications to media files. To learn more about how you can add S3 credentials to your deployment, check the custom config add-on for CloudControl [here](https://www.cloudcontrol.com/add-ons/config).

<<<<<<< HEAD
![](http://oi39.tinypic.com/2n8v96p.jpg) For customer uploads like profile pictures, user profiles, and other info, you should use a persistent file system such as Amazon’s S3 or MongoLab’s GridFS.

**Non-persistent file systems** hold temporary data that may or may not be accessible again in future requests. Depending on how the routing tier routes requests across available containers, data might not be available and could be deleted after each deploy. This does not include just manual deploys but also re-deploys that are automatically done by the platform during normal operation.
=======
Deployments on the cloudControl platform have access to a writable filesystem. This filesystem however is not persistent. Data written may or may not be accessible again in future requests, depending on how the [routing tier](#routing-tier) routes requests across available containers, and is deleted after each deploy. This does include deploys you trigger manually, but also re-deploys done by the platform itself during normal operation.

For customer uploads (e.g. user profile pictures) we recommend object stores like Amazon S3 or the GridFS feature available as part of the [MongoLab Add-on].

>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

###The lifecycle of an app on the CloudControl platform
Applications typically have a common lifecycle consisting of development, staging and production phases. CloudControl is built from the ground-up to meets these requirements and powers the entire application lifecycle by bridging the gap between software testing and deployment. CloudControl supports multiple deployments via git thus enabling each deployment to be delivered from a different git branch. This provides more agility for your development teams.

![](http://oi41.tinypic.com/2pq5hck.jpg)

![](http://oi41.tinypic.com/119z8ye.jpg) To work on new feature start by creating a new branch in git. The new version can then be deployed as its own deployment ensuring that the newly deployed feature does not interfere with the older versions that already exist.

![](http://oi39.tinypic.com/2n8v96p.jpg) More importantly, it also provides a way to ensure that the new code will run without any issues because each deployment uses the same stack and runs in an identical runtime environment.

<<<<<<< HEAD
###Add-Ons
Add-ons enrich the capabilities of your app and make them more powerful. There are over 50 different add-ons for CloudControl platform including databases, caching, performance monitoring, logging and even APIs for billing. Each deployment on the CloudControl platform needs its own set of add-ons.
=======
Most apps share a common application lifecycle consisting of development, staging and production phases. The cloudControl platform is designed from the ground up to support this. As we explained earlier, each app can have multiple deployments. Those deployments match the branches in the version control system. The reason for this is very simple. To work on a new feature it is advisable to create a new branch. This new version can then be deployed as its own deployment making sure the new feature development is not interfering with the existing deployments. More importantly even, these development/feature or staging deployments also help ensure that the new code will work in production because each deployment using the same [stack](#stacks) has the same runtime environment.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

![](http://oi41.tinypic.com/119z8ye.jpg) If your app needs a MySQL database and you have a production, a development and a staging environment, all three need their own MySQL add-ons. Each add-on has different options, allowing you to choose a more powerful database for your high traffic production deployment and a smaller database for your development and staging needs.

<<<<<<< HEAD
####Managing Add-ons
You can see the list of available add-ons for CloudControl on the [add-on marketplace website](https://www.cloudcontrol.com/add-ons) or with the addon.list command.
=======
Sometimes you have environment specific configuration, e.g. to enable debugging output in development deployments but disable it in production deployments. This can be done using the environment variables that each deployment provides to the app. The following environment variables are available:

 * **TMPDIR**: The path to the tmp directory.
 * **CRED_FILE**: The path of the creds.json file containing the Add-on credentials.
 * **DEP_VERSION**: The Git or Bazaar version the image was built from.
 * **DEP_NAME**: The deployment name in the same format as used by the command line client. E.g. myapp/default. This one stays the same even when undeploying and creating a new deployment with the same name.
 * **DEP_ID**: The internal deployment ID. This one stays the same for the deployments lifetime but changes when undeploying and creating a new deployment with the same name.
 * **WRK_ID**: The internal worker ID. Only set for worker containers.

## Add-ons

**TL;DR:**

 * Add-ons give you access to additional services like databases.
 * Each deployment needs its own set of Add-ons.
 * Add-on credentials are available to your app via the JSON formatted `$CRED_FILE` (and via environment variables depending on the programming language).

### Managing Add-ons

Add-ons add additional services to your deployment. The [Add-on marketplace] offers a wide variety of different Add-ons. Think of it as an app store dedicated to developers. Add-ons can be different database offerings, caching, performance monitoring or logging services or even complete backend APIs or billing solutions.

Each deployment has its own set of Add-ons. If your app needs a MySQL database and you have a production, a development and a staging environment, all three must have their own MySQL Add-ons. Each Add-on comes with different plans allowing you to choose  a more powerful database for your high traffic production deployment and smaller ones for the development or staging environments.

You can see the available Add-on plans on the Add-on marketplace website or with the `cctrlapp addon.list` command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$cctrlapp APP_NAME/DEP_NAME addon.list
[...]
~~~

<<<<<<< HEAD
To add an add-on to your deployment, use the following command - 

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add ADDON_NAME.ADDON_OPTION
~~~

To get the list of current Add-ons for a deployment use the add-on command.

=======
Adding an Add-on is just as easy.
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add ADDON_NAME.ADDON_OPTION
~~~

As always replace the placeholders written in uppercase with their respective values.

To get the list of current Add-ons for a deployment use the addon command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c
~~~
$ cctrlapp APP_NAME/DEP_NAME addon
Addon                    : alias.free
Addon                    : newrelic.standard
[...]
Addon                    : blitz.250
[...]
Addon                    : memcachier.dev
[...]
~~~

<<<<<<< HEAD
To upgrade an add-on, use the respective command - 
=======
To upgrade or downgrade an Add-on use the respective command followed by the Add-on plan you upgrade from and the Add-on plan you upgrade to.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
# upgrade
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade FROM_SMALL_ADDON TO_BIG_ADDON
~~~

In the example above, FROM_SMALL_ADDON is the name of the add-on you have upgraded from. TO_BIG_ADDON is the name of the add-on you have upgraded to.

Similarly, to downgrade an add-on, use the respective command - 

~~~
# downgrade
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade FROM_BIG_ADDON TO_SMALL_ADDON
~~~

Many add-ons require you to have credentials to connect to their service. The credentials are exported to the deployment in a JSON formatted config file and is accessed via the CRED_FILE environment variable using the app’s language. Here's a quick example in PHP how to read the file and parse the JSON.

<<<<<<< HEAD
~~~
# read the credentials file
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

# the file contains a JSON string, decode it and return an associative array
$creds = json_decode($string, true);

# now use the $creds array to configure your app e.g.:
$MYSQL_HOSTNAME = $creds['MYSQLS']['MYSQLS_HOSTNAME'];
~~~
=======
We provide detailed code examples how to use the config file in our guides section.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

Reading the credentials from the creds.json file as shown above ensures that your app is always talking to the right database and you can freely merge your branches without having to worry about keeping the credentials in sync.

![](http://oi39.tinypic.com/2n8v96p.jpg) The path to the add-on credential file can be found using the CRED_FILE environment variable. You should never hard-code credentials or path of the credential file in your app. The path of the credential file can change over deployments or during automatic redeploy by the CloudControl platform.

To get more information about the different add-ons supported on the CloudControl platform, check the add-ons page [here](https://www.cloudcontrol.com/add-ons). 

<<<<<<< HEAD
The [guides section](https://www.cloudcontrol.com/dev-center/Guides/) has detailed examples about how to read the creds.json file in different languages or frameworks. To see the format and contents of the creds.json file locally use the addon.creds command as shown below.
=======
The guides section has detailed examples about how to get the credentials in different languages ([Ruby](https://www.cloudcontrol.com/dev-center/Guides/Ruby/Add-on%20credentials), [Python](https://www.cloudcontrol.com/dev-center/Guides/Python/Add-on%20credentials), [Java](https://www.cloudcontrol.com/dev-center/Guides/Java/Add-on%20credentials), [PHP](https://www.cloudcontrol.com/dev-center/Guides/PHP/Add-on%20credentials)).
To see the format and contents of the credentials file locally, use the `addon.creds` command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.creds
{
    "BLITZ": {
        "BLITZ_API_KEY": "SOME_SECRET_API_KEY",
        "BLITZ_API_USER": "SOME_USER_ID"
    },
    "MEMCACHIER": {
        "MEMCACHIER_PASSWORD": "SOME_SECRET_PASSWORD",
        "MEMCACHIER_SERVERS": "SOME_HOST.eu.ec2.memcachier.com",
        "MEMCACHIER_USERNAME": "SOME_USERNAME"
    },
    "MYSQLS": {
        "MYSQLS_DATABASE": "SOME_DB_NAME",
        "MYSQLS_HOSTNAME": "SOME_HOST.eu-west-1.rds.amazonaws.com",
        "MYSQLS_PASSWORD": "SOME_SECRET_PASSWORD",
        "MYSQLS_PORT": "3306",
        "MYSQLS_USERNAME": "SOME_USERNAME"
    }
}
~~~

<<<<<<< HEAD
###Scheduling and background tasks
=======
## Logging

**TL;DR:**

 * There are four different log types (access, error, worker and deploy) available.

To see the log output in a `tail -f`-like fashion use the cctrlapp log command. The log command initially shows the last 500 log messages and then appends new messages as they arrive.

~~~
$ cctrlapp APP_NAME/DEP_NAME log [access,error,worker,deploy]
[...]
~~~

### Access Log

The `access` log shows each request to your app in an Apache compatible log format.

### Error Log

The `error` log shows all output your app prints to stdout, stderr and syslog. This log is probably the best place to look at when your app is not doing well. We also show new deployments here to give you more context but you can always refer to the [deploy log](#deploy-log) for detailed information on deploys.

### Worker Log

Workers are long running background processes. They are not accessible via http from outside. To make the workers output visible to you, its stdout, stderr and syslog output is captured in this log. The worker log contains the timestamp of the event, the *wrk_id* of the worker as well as the actual log line.

### Deploy Log

The `deploy` log provides detailed information about the deploy process. It shows on how many nodes your deployment is running with additional information about the nodes, startup times and when the loadbalancers begins sending traffic to the [new version](#deploying-new-versions).

### Customizing logging

Some Add-ons in the [Deployment category] as well as the [Custom Config Add-on] can be used to forward error and worker logs to the external logging services.

#### Adding custom syslog logging with Custom Config Add-on

The Custom Config Add-on can be used to specify an additional endpoint to receive error and worker logs.
This is done by setting the config variable "RSYSLOG_REMOTE". The content should contain valid [rsyslog] configuration and can span multiple lines.

E.g. to forward the logs to custom syslog remote over a [TLS] connection, create a temporary file with the following content:
~~~
$DefaultNetstreamDriverCAFile /app/CUSTOM_CERTIFICATE_PATH
$ActionSendStreamDriver gtls
$ActionSendStreamDriverMode 1
$ActionSendStreamDriverAuthMode x509/name
$template CustomFormat, "%syslogtag%%msg%\n"
*.* @@SERVER_ADDRESS:PORT;CustomFormat
~~~
Where "SERVER_ADDRESS" and "PORT" should be replaced with the concrete values and "CUSTOM_CERTIFICATE_PATH" should be the path to a certificate file for the custom syslog remote in you repository.

Use the name of the file (for example `custom_remote.cfg`) as a value for the "RSYSLOG_REMOTE" config variable:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add config.free --RSYSLOG_REMOTE=custom_remote.cfg
~~~

From now on all the new logs should be visible in your custom syslog remote.


## Provided Subdomains and Custom Domains

**TL;DR:**

 * Each deployment is provided with both a `*.cloudcontrolled.com` and `*.cloudcontrolapp.com` subdomain.
 * Custom domains are supported via the Alias Add-on.

Each deployment is provided per default with both a `*.cloudcontrolled.com` and `*.cloudcontrolapp.com` subdomain. The `APP_NAME.cloudcontrolled.com` or `APP_NAME.cloudcontrolapp.com` will point to the `default` deployment while any additional deployment can be accessed with a prefixed subdomain: `DEP_NAME-APP_NAME.cloudcontrolled.com` or `DEP_NAME-APP_NAME.cloudcontrolapp.com`.

You can also use custom domains to access your deployments. To add a domain like `www.example.com`, `app.example.com` or `secure.example.com` to one of your deployments, simply add each one as an alias and add a CNAME for each pointing to your deployment's subdomain. So to point `www.example.com` to the default deployment of the app called *awesomeapp*, add a CNAME for `www.example.com` pointing to `awesomeapp.cloudcontrolled.com` or `awesomeapp.cloudcontrolapp.com`. The [Alias Add-on] also supports mapping wildcard domains like `*.example.com` to one of your deployments.

All custom domains need to be verified before they start working. To verify a domain, it is required to also add the cloudControl verification code as a TXT record.

Changes to DNS can take up to 24 hours until they have effect. Please refer to the Alias Add-on Documentation for detailed instructions on how to setup CNAME and TXT records.


## Routing Tier
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

CloudControl supports scheduling of short tasks that need to be run periodically as well as long running transactions. Scheduled jobs are supported through different add-ons as discussed below. For long running asynchronous tasks, background workers should be used.

<<<<<<< HEAD
**Short timespan cron tasks** 
For tasks that are guaranteed to finish within a time limit, the cron add-on offers a simple solution. It calls a predefined URL daily or hourly and executes a task periodically.

**Asynchronous task workers** 
Tasks that will take longer than 120 seconds to execute are killed by the routing tier. Tasks triggered by user requests should be handled asynchronously by a worker add-on. Workers are long running processes that started in containers just like the web processes but do not listen on a port or receive http requests. 

For example, workers can be used to poll a queue and execute tasks in the background or handle long running periodical calculations.

![](http://oi39.tinypic.com/2n8v96p.jpg) The maximum timeout limit for a web request to complete is 120 seconds. If a web request takes longer than 120 seconds to execute, it should be handled asynchronously by a worker task.

###Secure Shell (SSH)
CloudControl is a distributed cloud platform. This makes it hard to SSH into the actual physical server. Instead, we offer a run command, that allows users to launch a new container and connect to that via SSH. This container is identical to the web or worker containers but it starts an SSH daemon instead of one of the Procfile commands. It is based on the same stack  and runs the same deployment image without any add-on credentials.

####To start an SSH shell on the CloudControl platform -
=======
 * All HTTP requests are routed via our routing tier.
 * Within the routing tier, you can choose to route requests via the `*.cloudcontrolled.com` or `*.cloudcontrolapp.com` subdomains.
 * The `*.cloudcontrolled.com` subdomain provides support for HTTP caching via Varnish.
 * The `*.cloudcontrolapp.com` subdomain provides WebSocket support.
 * Requests are routed based on the `Host` header.
 * Use the `X-Forwarded-For` header to get the client IP.

All HTTP requests made to apps on the platform are routed via our routing tier. The routing tier is designed as a cluster of reverse proxy loadbalancers which orchestrate the forwarding of user requests to your applications. It takes care of routing the request to one of the application's containers based on matching the `Host` header against the list of the deployment's aliases. This is accomplished via the `*.cloudcontrolled.com` or `*.cloudcontrolapp.com` subdomains.

The routing tier is designed to be robust against single node and even complete datacenter failures while still keeping the added latency as low as possible.

### Elastic Addresses

Because of the elastic nature of the routing tier, the list of routing tier addresses can change at any time. It is therefore highly discouraged to point custom domains directly to any of the routing tier IP addresses. Please use a CNAME instead. Refer to the [custom domain section](#provided-subdomains-and-custom-domains) for more details on the correct DNS configuration.

### Remote Address

Given that client requests don't hit your application directly, but are forwarded via the routing tier, you can't access the client's IP by reading the remote address. The remote address will always be the internal IP of one of the routing nodes. To make the origin remote address available, the routing tier sets the `X-Forwarded-For` header to the original client's IP.

### Reverse Proxy timeouts

Our routing tier uses a cluster of reverse proxy loadbalancers to manage the acceptance and forwarding of user requests to your applications. To do this in an efficient way, we set strict timeouts to the read/ write operations. The values differ slightly between the `*.cloudcontrolled.com` and `*.cloudcontrolapp.com` subdomains. You can find them below.

 * __Connect timeout__ - time within a connection to your application has to be established. If your containers are up, but hanging, then this timeout will not apply as the connection to the endpoints has already been made.
 * __Read timeout__ - time to retrieve a response from your application. It determines how long the routing tier will wait to get the response to a request. The timeout is established not for an entire response, but only between two operations of reading.
 * __Send timeout__ - maximum time between two write operations of a request. If your application does not take new data within this time, the routing tier will shut down the connection.

#### Timeouts for `*.cloudcontrolled.com` subdomain:

|Parameter|Value [s]|
|:---------|:----------:|
|Connect timeout|60|
|Send timeout|60|
|Read timeout|120|

#### Timeouts for `*.cloudcontrolapp.com` subdomain:

|Parameter|Value [s]|
|:---------|:----------:|
|Connect timeout|20|
|Send timeout|55|
|Read timeout|55|

### Requests distribution

Our smart [DNS](https://en.wikipedia.org/wiki/Domain_Name_System) provides a fast and reliable service resolving domain names in a round robin fashion. All nodes are equally distributed to the three different availability zones but can route requests to any container in any other availability zone. To keep latency low, the routing tier tries to route requests to containers in the same availability zone unless none are available. Deployments running on --containers 1 (see the [scaling section](#scaling) for details) only run on one container and therefore are only hosted in one availability zone.

### High Availability

The routing tier provides two mechanisms to ensure high availability, depending on the provided subdomain. These are Failover (for the `*.cloudcontrolled.com` subdomain) and Health Checker (for the `*.cloudcontrolapp.com` subdomain). Because these mechanisms depend on having multiple containers available to route requests, only deployments with more than one container running (see the [scaling section](#scaling) for details) can take advantage of high availability.

In the event of a single node or container failure, the platform will start a replacement container. Deployments running on --containers 1 will be unavailable for a few minutes while the platform starts the replacement. To avoid even short downtimes, set the --containers option to at least 2.

#### `*.cloudcontrolled.com` subdomain

For the `*.cloudcontrolled.com` subdomain, failed requests are automatically re-routed to alternate containers via a failover mechanism.  Requests will be retried with a different container within the set timeouts. It will also ensure the next request is not sent to the slow/faulty container for a given amount of time.

#### `*.cloudcontrolapp.com` subdomain

For the `*.cloudcontrolapp.com` subdomain, failed requests will cause an error message to be returned to the user once, but the "unhealthy" container will be actively monitored by a health checker. This signals the routing tier to temporarily remove the unhealthy container from the list of containers receiving requests. Subsequent requests are routed to an available container of the deployment. Once the health checker notices that the container has recovered, the container will be re-included in the list to receive requests.

Because the health checker actively monitors containers where an application is running into timeouts or returning [http error codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.5) `501`, `502` or `greater 503`, you may see requests to `/CloudHealthCheck` coming from a `cloudControl-HealthCheck` agent.


## Scaling

**TL;DR:**

 * You can scale up or down at any time by adding more containers (horizontal scaling) or changing the container size (vertical scaling).
 * Use performance monitoring and load testing to determine the optimal scaling settings for your app.

When scaling your apps you have two options. You can either scale horizontally by adding more containers, or scale vertically by changing the container size. When you scale horizontally, the cloudControl loadbalancing and [routing tier](#routing-tier) ensures efficient distribution of incoming requests accross all available containers.

### Horizontal Scaling

Horizontal scaling is controlled by the --containers parameter.
It specifies the number of containers you have running.
Raising --containers also increases the availability in case of node failures.
Deployments with --containers 1 (the default) are unavailable for a few minutes in the event of a node failure until the failover process has finished. Set --containers value to at least 2 if you want to avoid downtime in such situations.

### Vertical Scaling

In addition to controlling the number of containers you can also specify the memory size of a container. Container sizes are specified using the --memory parameter, being possible to choose from 128MB to 1024MB. To determine the optimal --memory value for your deployment you can use the New Relic Add-on to analyze the memory consumption of your app.

### Choosing Optimal Settings

You can use the [Blitz.io] and [New Relic Add-ons] to run synthetic load tests against your deployments and analyze how well they perform with the current --containers and --memory settings under expected load to determine the optimal scaling settings and adjust accordingly. We have a [tutorial] that explains this in more detail.


## Performance & Caching

**TL;DR:**

 * Reduce the total number of requests that make up a page view.
 * Cache as far away from your database as possible.
 * Try to rely on cache breakers instead of flushing.

### Reducing the Number of Requests

Perceived web application performance is mostly influenced by the frontend. It's very common that the highest optimization potential lies in reducing the overall number of requests per page view. One common technique to accomplish this is combining and minimizing javascript and css files into one file each and using sprites for images.

### Caching Early

After you have reduced the total number of requests, it's recommended to cache as far away from your database as possible. Using far-future `expires` headers avoids that browsers request resources at all. The next best way of reducing the number of requests that hit your containers is to cache complete responses in the loadbalancer. For this we offer caching directly in the routing tier.

#### Caching Proxy

The routing tier that is in front of all deployments includes a [Varnish] caching proxy. To use this feature, it is necessary to use the `*.cloudcontrolled.com` subdomain. To have your requests cached directly in Varnish and speed up the response time through this, ensure you have set correct [cache control headers](http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html) (`Cache-Control`, `Expires`, `Age`) for the request. Also, ensure that the request does not include a cookie. Cookies are often used to keep state across requests (e.g. if a user is logged in). To avoid caching responses for logged-in users and returning them to other users, Varnish is configured to never cache requests with cookies.

To be able to cache requests in Varnish for apps that rely on cookies, we recommend using a [cookieless domain](http://www.ravelrumba.com/blog/static-cookieless-domain/). In this case, you have to register a new domain and configure your DNS database with a `CNAME` record that points to your `APP_NAME.cloudcontrolled.com` subdomain `A` record. Then you can update your web application's configuration to serve static resources from your new domain.

You can check if a request was cached in Varnish by checking the response's *X-varnish-cache* header. The value HIT means the respons was answered directly from the cache, and MISS means it was not.

#### In-Memory Caching

To speed up requests that can't use a cookieless domain, you can use in-memory caching to store arbitrary data from database query results to complete http responses. Since the cloudControl routing tier distributes requests across all available containers, it is recommended to cache data in a way that the cache is also available for requests that are routed to different containers. A battle-tested solution for this is Memcached, which is available via the [MemCachier Add-on]. Refer to the [managing Add-ons](#managing-add-ons) section on how to add it. In addition the [MemCachier Documentation] has detailed instructions on how to use it for your language and framework of choice.

### Cache Breakers

When caching requests on client side or in a caching proxy, the URL is usually used as the cache identifier. As long as the URL stays the same and the cached response has not expired, the request is answered from cache. As part of every deployment, all containers are started from a clean image. This ensures that all containers have the latest app code including templates, css, image and javascript files. However, when using far-future `expires` headers as recommended above, this doesn't change anything if the response was cached at client or loadbalancer level. To ensure clients get the latest and greatest version, it is recommend to include a changing parameter into the URL. This is commonly referred to as a cache breaker.

The [environment variables](#environment-variables) of the deployment runtime environment contain the DEP_VERSION of the app. If you want to force a refresh of the cache when a new version is deployed you can use the DEP_VERSION to accomplish this.

This technique works for URLs as well as for the keys in in-memory caches like `Memcached`.
Imagine you have cached values in Memcached that you want to keep between deploys and have values in Memcached that you want refreshed for each new version. Since Memcached only allows flushing the complete cache, you would lose all cached values.
Including the DEP_VERSION in the key is an easy way to ensure that the cache is clear for a new version without flushing.

### Caching in cloudcontrolapp.com subdomain

Requests via the `*.cloudcontrolapp.com` subdomain cannot be cached in the routing tier. However, it is still possible to provide caching for static assets by utilizing a separate cookieless domain as a CNAME of the `*.cloudcontrolled.com`subdomain. For example, you can serve the dynamic requests of your application via www.example.com (a CNAME FOR `example.cloudcontrolapp.com`) and serve the static assets like CSS, JS and images via `static.example.com` (a CNAME for `example.cloudcontrolled.com`).


## WebSockets

**TL;DR:**

 * WebSockets are supported via the `*.cloudcontrolapp.com` subdomain.
 * WebSockets allow real-time, bidirectional communication between clients and servers
 * Additional steps are necessary to secure WebSocket connections
 * It is highly recommended to use the secure `wss://` protocol rather than the insecure `ws://`.

WebSockets allow you to enable real-time, bidirectional communication channels between clients and servers. WebSocket connections use standard HTTP ports (80 and 443) like normal browsers. In order to establish a WebSocket connection on our platform, the client has to explicitly set `Upgrade` and `Connection` [hop-by-hop](http://tools.ietf.org/html/rfc2616#section-13.5.1) headers in the request. Those headers instruct our reverse-proxy to upgrade the protocol from HTTP to WebSocket. Once the protocol upgrade handshake is completed, data frames can be sent between the client and the server in full-duplex mode.

All the request timeouts described above also apply for WebSocket connections, but with different effects:

|Parameter|Value [s]|Description|
|:--------|:--------|:---------:|
|Send timeout|55|Timeout between two consecutive chunks of data being sent by the client|
|Read timeout|55|Timeout between two consecutive chunks of data being sent back to the client|

To overcome any timeout limitations, you can explicitly implement the WebSocket [Ping-Pong control](http://tools.ietf.org/html/rfc6455#page-36) mechanism, which keeps connections alive. Nevertheless, many of the WebSocket libraries or clients implemented in many languages already offer this feature out of the box.

### Secure WebSockets

Conventional WebSockets do not offer any kind of protocol specific authentication or data encryption. You are encouraged to use standard HTTP authentication mechanisms like cookies, basic/diggest or TLS. The same goes for data encryption where SSL is your obvious choice. While a conventional WebSocket connection is established via HTTP, a protected one uses HTTPS. The distinction is based on the URI schemes:

~~~
Normal connection: ws://{host}:{port}/{path to the server}
Secure connection: wss://{host}:{port}/{path to the server}
~~~

Please note that Secure WebSockets connections can only be established using `*.cloudcontrolapp.com` subdomains, not custom ones. It is highly recommended to use them, not only for data security reasons. Secure WebSockets are 100% proxy transparent, which puts your containers in full control of WebSocket `upgrade handshake` in case some of the proxies do not handle it properly.


## Scheduled Jobs and Background Workers

**TL;DR:**

 * Web requests are subject to a time limit of 120s.
 * Scheduled jobs are supported through different Add-ons.
 * Background workers are the recommended way of handling long running or asynchronous tasks.

Since a web request taking longer than 120s is killed by the routing tier, longer running tasks have to be handled asyncronously.

### Cron

For tasks that are guaranteed to finish within the time limit, the [Cron add-on] is a simple solution to call a predefined URL daily or hourly and have that task called periodically. For a more detailed documentation on the Cron Add-on, please refer to the [Cron Add-on documentation].

### Workers

Tasks that will take longer than 120s to execute, or that are triggered by a user request and should be handled asyncronously to not keep the user waiting, are best handled by the [Worker add-on]. Workers are long-running processes started in containers. Just like the web processes but they are not listening on any port and therefore do not receive http requests. You can use workers, for example, to poll a queue and execute tasks in the background or handle long-running periodical calculations. More details on usage scenarios and available queuing Add-ons are available as part of the [Worker Add-on documentation].


## Secure Shell (SSH)

The distributed nature of the cloudControl platform means it's not possible to SSH into the actual server. Instead, we offer the run command, that allows you to launch a new container and connect to that via SSH.

The container is identical to the web or worker containers but starts an SSH daemon instead of one of the Procfile commands. It's based on the same stack image and deployment image and does also provides the Add-on credentials.

### Examples

To start a shell (e.g. bash) use the `run` command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$ cctrlapp APP_NAME/DEP_NAME run bash
Connecting...
Warning: Permanently added '[10.62.45.100]:25832' (RSA) to the list of known hosts.
u25832@DEP_ID-25832:~/www$ echo "interactive commands work as well"
interactive commands work as well
u25832@DEP_ID-25832:~/www$ exit
exit
Connection to 10.62.45.100 closed.
Connection to ssh.cloudcontrolled.net closed.
~~~

<<<<<<< HEAD
It is also possible to execute a command directly and have the container exit after the command has finished. This can come in handy one time tasks like database migrations. You can also list and sort environment variables in the shell using “env | sort”.

=======
It's also possible to execute a command directly and have the container shutdown after the command is finished. This is very useful for database migrations and other one-time tasks.

For example, passing the `"env | sort"` command will list the environment variables. Note that the use of the quotes is required for a command that includes spaces.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c
~~~
$ cctrlapp APP_NAME/DEP_NAME run "env | sort"
Connecting...
Warning: Permanently added '[10.250.134.126]:10346' (RSA) to the list of known hosts.
CRED_FILE=/srv/creds/creds.json
DEP_ID=DEP_ID
DEP_NAME=APP_NAME/DEP_NAME
DEP_VERSION=9d5ada800eff9fc57849b3102a2f27ff43ec141f
DOMAIN=cloudcontrolled.com
GEM_PATH=vendor/bundle/ruby/1.9.1
HOME=/srv
HOSTNAME=DEP_ID-10346
LANG=en_US.UTF-8
LOGNAME=u10346
MAIL=/var/mail/u10346
OLDPWD=/srv
PAAS_VENDOR=cloudControl
PATH=bin:vendor/bundle/ruby/1.9.1/bin:/usr/local/bin:/usr/bin:/bin
PORT=10346
PWD=/srv/www
RACK_ENV=production
RAILS_ENV=production
SHELL=/bin/sh
SSH_CLIENT=10.32.47.197 59378 10346
SSH_CONNECTION=10.32.47.197 59378 10.250.134.126 10346
SSH_TTY=/dev/pts/0
TERM=xterm
TMP_DIR=/srv/tmp
TMPDIR=/srv/tmp
USER=u10346
WRK_ID=WRK_ID
Connection to 10.250.134.126 closed.
Connection to ssh.cloudcontrolled.net closed.
~~~

###CloudControl for Administrators
The CloudControl web console provides IT admins with the tools they need to manage apps on CloudControl. Administrators have privileges to manage users accounts, apps and deployments. 
This section goes over these capabilities.

####Creating an app
Creating an app makes you the app owner and gives you full access. The owner can not be removed from the app and is responsible for the billing associated with the app. If you plan on having multiple developers working on the same app, it's recommended to have a separate admin-like account for the owner of all your apps and add the additional developers (including yourself) for each app separately.

For example, as shown in the figure below, donsampleapp was created by user *dodil* and *dodil* is the owner of the app. 

![](http://oi41.tinypic.com/208bqfl.jpg)

To add another developer to this app, another CloudControl user account must be created and this user must be added to the app. 

####User accounts
To work on and manage your applications on the CloudControl platform, a user account is needed. User accounts on the CloudControl platform can be created via the web console or via cctrluser create. 	

<<<<<<< HEAD
~~~
$cctrluser create
~~~

After the account is created using cctrluser create, an activation e-mail is sent to the e-mail address associated with the account. The account can be activated either by clicking the link in the e-mail or using the CLI command to activate the account.
=======
 * **Luigi** based on [Ubuntu 10.04 LTS Lucid Lynx]
 * **Pinky** based on [Ubuntu 12.04 LTS Precise Pangolin]
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

You can change the stack per deployment. This is handy for testing new stacks before migrating the production deployment. Details are available via the `cctrlapp` command line interface.
~~~
$cctrluser USER_NAME_ACTIVATION_CODE
~~~

<<<<<<< HEAD
If the user account is no longer need, it can also be deleted via the web console or via cctrluser delete. 
=======
To change the stack of a deployment simply append the --stack command line option to the `deploy` command.
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c

~~~
$cctrluser delete
~~~

<<<<<<< HEAD
In cases where you might have forgotten your password and might need to reset it, visit the password reset page [here](https://api.cloudcontrol.com/reset_password/) to reset your password.

####Tying it all together - Apps, Users and Deployment
Apps have a repository, deployments and users. The repository is where your code lives, organized in branches. A deployment is a running version of your application, based on the branch with the same name. 

![](http://oi39.tinypic.com/2n8v96p.jpg) The default deployment is based on the master branch.

Users can be added to apps to gain access to the repository, branches and deployments. Creating an app allows you to add or remove users to that app, giving them access to the source code as well as allowing them to manage the deployments. Creating an app is easy. Simply specify a name and the desired type to determine which buildpack to use.

~~~
$ cctrlapp APP_NAME create php
~~~ 

You can also create an app using the web console as shown below -

![](http://oi42.tinypic.com/23tpc91.jpg)

By adding users to an app you can grant your fellow app developers access to the source code in the repository, allow them to deploy new versions and modify the deployments including their add-ons. User are based on the user's roles.

There are two kinds of user roles in CloudControl - *admin* and *read-only* roles. Admin accounts have full access to the control surfaces for the app as well as source code repositories associated with the app. Read-only users only have read only access to the app’s source code. 	

To add a user to your app in a specific role, you can use the admin console as shown below. Type in the e-mail address associated with the user’s account, select the role and add the user to your app.

![](http://oi42.tinypic.com/oab4lk.jpg)

After adding the user as an administrator, you will see something like the following - 

![](http://oi43.tinypic.com/jjbdad.jpg)

The owner can list, add and remove app users using the command line client.

~~~
$ cctrlapp APP_NAME user
~~~

A deployment is the running version of one of your branches made accessible via a provided subdomain. The name of the subdomain is based on the branch, with the master branch used for the default deployment. Deployments run independently from each other. They have  separate runtime environments, file system storage and add-ons. This allows you to have different versions of your app running at the same time without them interfering with each other. You can list all the deployments with the details command.

~~~
$ cctrlapp APP_NAME details
~~~

###CloudControl for Developers

Developers write apps on the CloudControl platform. By default, when users are added to an app, they are added into the developer role. They have full access to the code repository and to all deployments. Developers can add more developers and remove existing ones. They can also delete the deployment as well as the app. Developers cannot remove or change the owner of the app.

The cloudControl platform supports zero downtime deploys for all deployments. To deploy a new version use the following cctrlapp deploy command -

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy
~~~

To deploy a specific version, append your version control systems identifier (full commit-SHA1 for Git or an integer for Bazaar). If not specified, the version to be deployed defaults to the latest image available (the one built during the last successful push).

When an app is deployed, the image is downloaded to as many of the platform’s nodes based on the container settings and started according to default buildpack’s configuration. After the new containers are up and running, the loadbalancing tier stops sending requests to the old containers and instead sends them to the new version. A log message in the deploy log appears when this process has finished.

To scale your apps on the CloudControl platform, you have two options - you can either scale horizontally by adding more containers, or scale vertically by changing the container size. When you scale horizontally the cloudControl loadbalancing and routing tier ensures efficient distribution of incoming requests across all available containers.

To scale horizontally, you can control the number of containers you have for an app with the --containers parameter. In addition, you can also scale vertically by controlling the memory size of a container. Container sizes are specified using the --memory parameter and can range from 128MB to 1024MB. Determining the optimal memory size for your container can be challenging. One option is to use the [New Relic add-on](https://www.cloudcontrol.com/add-ons/newrelic) to monitor the memory consumption for your app and tune these settings accordingly after sufficient testing. 

####Rolling back to the last working version
If for some reason a new version does not work as expected, you can *rollback* a deployment to a previous version in a matter of seconds. To rollback, you can check the deploy log for the previously deployed version (or get it from the version control system directly) and then simply use the Git or Bazaar version identifier which is part of the log output to redeploy this version using the deploy command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy THE_LAST_WORKING_VERSION
~~~

####Version control and images
The CloudControl platform supports two source control systems - Git and Bazaar. When an app gets created, the command line tools try to determine the source control system you are using by checking for the existence of a .git or .bzr directory in your current working directory. It then creates the app using the detected version control system. If it can't determine the version control system you are using, Git is used as the default but it can always be overwritten using the --repo command line switch.

~~~
$ cctrlapp APP_NAME create php [--repo [git,bzr]]
~~~

It is easy to tell what version control system an existing app uses based on the repository URL provided as part of the app details.

~~~
$ cctrlapp APP_NAME details
~~~

To push the app, you can use the cctrlapp push command or the source control push command (git/bzr push). The repositories support all other remote operations like pulling and cloning as well.Whenever an updated branch is pushed, an image is built. The image is compressed and is limited to 200MB in size. The image can be deployed with the deploy command to the deployment matching the branch name. The contents of the image gets generated using the buildpack and usually includes the binary code for the app and any dependencies that were installed by the buildpack. 

![](http://oi41.tinypic.com/119z8ye.jpg) Smaller images can be deployed faster, so it is recommended to keep the image size below 50MB. The image size is printed at the end of the build process; if the image exceeds the limit, the push gets rejected.

You can decrease the size of your image by making sure that unnecessary files such as  caches, logs, and backup files are not included in the image. If you need files to be tracked in the source control repository but not included in the image, add them to the *.cctrlignore* file in the project root directory.

####Logging
If your app suddenly fails abnormally or if you are just troubleshooting your app, error logs can really be handy. There are four different types of logging on the CloudControl platform -  access, error, worker and deploy. 

* Access Log
The access log shows each access to your app in an Apache compatible log format.

* Error Log
The error log shows all output your app prints to stdout, stderr and syslog. It also shows when a new version has been deployed to make it easy to determine if a problem existed already before or only after the last deploy. More detailed information on deploys can be found in the deploy log.

* Worker Log
To make worker output accessible to you, its stdout, stderr and syslog output is redirected to this log. The worker log shows the timestamp of when the message was written, the wrk_id of the worker the message came from as well as the actual log line.

* Deploy Log
The deploy log gives detailed information on the deploy process. It shows on how many nodes your deployment is deployed and lists the nodes themselves, how long it took for each of the nodes to start the container and get the deployment running and also when the load balancers kicked in and started sending traffic to the new version.

**Customized Logging**, 
Some add-ons in the deployment category as well as the custom-config add-on can be used to forward error and worker logs to the external logging services.

####Subdomains and Custom domains
Each deployment gets a *.cloudcontrolled.com* subdomain. The default deployment always found at *APP_NAME.cloudcontrolled.com*. Other additional deployments get the *DEP_NAME-APP_NAME.cloudcontrolled.com* subdomain.

Custom domains can be used to access the deployments. To add a domain like *www.example.com*, *app.example.com* or *secure.example.com* to one of the deployments, simply add each one as an alias and add a CNAME for each entry that points to your deployment's subdomain. For example, if you want to point www.example.com to the default deployment of the app called awesomeapp add a CNAME for www.example.com pointing to awesomeapp.cloudcontrolled.com. 

The alias add-on also supports mapping wildcard domains like *.example.com to one of your deployments. All custom domains need to be verified before they start working. To verify a domain it is required to also add the cloudControl verification code as a TXT record. Changes to DNS can take up to 24 hours until they have effect.

=======
[generating SSH keys]: https://help.github.com/articles/generating-ssh-keys
[Custom Config Add-on]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config
[web console]: https://www.cloudcontrol.com/console
[API libraries]: https://github.com/cloudControl
[the latest version]: http://cctrl.s3-website-eu-west-1.amazonaws.com/#windows/
[Python 2.6+]: http://python.org/download/
[reset your password]: https://api.cloudcontrol.com/reset_password/
[quick Git tutorial]: http://rogerdudler.github.com/git-guide/
[Bazaar in five minutes]: http://doc.bazaar.canonical.com/latest/en/mini-tutorial/
[Heroku buildpack API]: https://devcenter.heroku.com/articles/buildpack-api
[guides]: https://www.cloudcontrol.com/dev-center/Guides
[MongoLab Add-on]: https://www.cloudcontrol.com/add-ons/mongolab
[Add-on marketplace]: https://www.cloudcontrol.com/add-ons
[Deployment category]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment
[rsyslog]: http://www.rsyslog.com/
[TLS]: http://en.wikipedia.org/wiki/Transport_Layer_Security
[Alias Add-on]: https://www.cloudcontrol.com/add-ons/alias
[Blitz.io]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Performance%20&%20Monitoring/Blitz.io
[MemCachier Add-on]: https://www.cloudcontrol.com/add-ons/memcachier
[Varnish]: https://www.varnish-cache.org/
[MemCachier Documentation]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MemCachier
[New Relic Add-ons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Performance%20&%20Monitoring/New%20Relic
[tutorial]: https://www.cloudcontrol.com/blog/best-practice-running-and-analyzing-load-tests-on-your-cloudcontrol-app
[Cron Add-on]: https://www.cloudcontrol.com/add-ons/cron
[Cron Add-on documentation]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Cron
[Worker Add-on]: https://www.cloudcontrol.com/add-ons/worker
[Worker Add-on documentation]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Processing/Worker
[Ubuntu 10.04 LTS Lucid Lynx]: http://releases.ubuntu.com/lucid/
[Ubuntu 12.04 LTS Precise Pangolin]: http://releases.ubuntu.com/precise/
>>>>>>> 2680423c3c02d88a9bd94b621898ef053ff88d3c
