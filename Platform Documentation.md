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
<li class=""><a href="#performance">Performance</a></li>
<li class=""><a href="#websockets">WebSockets</a></li>
<li class=""><a href="#scheduled-jobs-and-background-workers">Scheduled Jobs and Background Workers</a></li>
<li class=""><a href="#secure-shell-ssh">Secure Shell (SSH)</a></li>
<li class=""><a href="#stacks">Stacks</a></li>
</ul>
</aside>

# exoscale Documentation

## Platform Access

**TL;DR:**

 * The command line client is the primary interface.
 * We also offer a web console.
 * For full control and integration it's possible to talk directly to the RESTful API.

To control the platform we offer different interfaces. The primary way of controlling your apps and deployments is via [the command-line interface](http://en.wikipedia.org/wiki/Command-line_interface) (CLI) called *exoapp* and *exouser*. Additionally we also offer a [web console]. Both the CLI as well as the web console however are merely frontends to our RESTful API. For deep integration into your apps you can optionally use one of our available [API libraries].

Throughout this documentation we will use the CLI as the primary way of controlling the exoscale platform. The CLI consists of 2 parts: *exoapp* and *exouser*. To get help for the command line client, just append --help or -h to any of the commands.

Installing the command line clients is easy and works on Mac/Linux as well as on Windows.

#### Quick Installation Windows

For Windows we offer an installer. Please download [the latest version] of the installer from S3. The file is named cctrl-x.x-setup.exe.

#### Quick Installation Linux/Mac

On Linux and Mac OS we recommend installing and updating the command line clients via pip. They require [Python 2.6+].

~~~
$ sudo pip install -U cctrl
~~~

If you don't have pip you can install pip via easy_install (on Linux usually part of the python-setuptools package):

~~~
$ sudo easy_install pip
$ sudo pip install -U cctrl
~~~

## User Accounts

User accounts are created on [exoscale.ch](http://exoscale.ch).

## Apps and Deployments

**TL;DR:**

 * Applications (apps) have a repository and deployments.
 * The repository is where your code lives, organized in branches.
 * A deployment is a running version of your application, based on the branch with the same name. Exception: the default deployment is based on the master branch.

exoscale PaaS uses a distinct set of naming conventions. To understand how to
work with the platform effectively, it's important to understand the following
few basic concepts.

### Apps

An app consists of a repository (with branches) and deployments.

Creating an app is easy. Simply specify a name and the desired type to determine which [buildpack](#buildpacks-and-the-procfile) to use.

~~~
$ exoapp APP_NAME create php
~~~

You can always list your existing apps using the command line client too.

~~~
$ exoapp -l
Apps
 Nr  Name                           Type
   1 myfirstapp                     php
   2 nextbigthing                   php
   [...]
~~~

### User Keys

For secure access to the app's repository, each developer needs to authenticate
via public/ private key authentication. Please refer to GitHub's article on
[generating SSH keys] for details on how to create a key. You can simply add
your default key to your user account using the *web console* or the command
line client. If no default key can be found, exoapp will offer to create one.

~~~
$ exouser key.add
~~~

You can also list the available key ids and remove existing keys using the key id.

~~~
$ exouser key
Keys
 Dohyoonuf7

$ exouser key Dohyoonuf7
ssh-rsa AAA[...]

$ exouser key.remove Dohyoonuf7
~~~

### Deployments

A deployment is the running version of one of your branches made accessible via a [provided subdomain](#provided-subdomains-and-custom-domains).
It is based on the branch of the same name. Exception: the default deployment is based on the master branch.

Deployments run independently from each other, including separate runtime environments, file system storage and Add-ons (e.g. database).
This allows you to have different versions of your app running at the same time without interfering with each other.
Please refer to the section about [development, staging and production environments](#development-staging-and-production-environments) to understand why this is a good idea.

You can list all the deployments with the *details* command.

~~~
$ exoapp APP_NAME details
App
 Name: APP_NAME                       Type: php        Owner: EMAIL_ADDRESS
 Repository: ssh://APP_NAME@app.exo.io/repository.git

 [...]

 Deployments
   APP_NAME/default
   APP_NAME/dev
   APP_NAME/stage
~~~


## Version Control & Images

**TL;DR:**

 * Git is the supported VCS.
 * When you push an updated branch, an image of your code gets built, ready to be deployed.
 * Image sizes are limited to 200MB (compressed). Use a `.cctrlignore` file to exclude development assets.

### Image Building

Whenever you push an updated branch, a deployment image is built automatically.
This image can then be deployed with the *deploy* command to the deployment
matching the branch name.  The content of the image is generated by the
[buildpack](#buildpacks-and-the-procfile) including your application code in a
runnable form with all the dependencies.

You can either use the exoapp push command or git's push command. Please
remember that deployment and branch names have to match.  So to push to your
dev deployment the following commands are interchangeable.  Also note, both
require the existence of a branch called dev.

~~~
# with exoapp:
$ exoapp APP_NAME/dev push

# get the REPO_URL from the output of exoapp APP_NAME details

# with git:
$ git remote add exo REPO_URL
$ git push exo dev
~~~

The repositories support all other remote operations like pulling and cloning
as well.

The compressed image size is limited to 200MB.  Smaller images can be deployed
faster, so we recommend to keep the image size below 50MB.  The image size is
printed at the end of the build process; if the image exceeds the limit, the
push gets rejected.

You can decrease your image size by making sure that no unneeded files (e.g.
caches, logs, backup files) are tracked in your repository. Files that need to
be tracked but are not required in the image (e.g. development assets or source
code files in compiled languages), can be added to a `.cctrlignore` file in the
project root directory.  The format is similar to the `.gitignore`, but without
the negation operator `!`. Here’s an example `.cctrlignore`:

~~~
*.psd
*.pdf
test
spec
~~~

#### Buildpacks and the Procfile

During the push a hook is fired that runs the buildpack. A buildpack is a set
of scripts that determine how an app in a specific language or framework has to
be prepared for deployment on the exoscale platform. With custom buildpacks,
support for new programming languages can be added or custom runtime
environments can be build. To support many PaaS with one buildpack, we
recommend following the [Heroku buildpack API] which is compatible with
exoscale and other platforms.

Part of the buildpack scripts is also to pull in dependencies according to the
languages or frameworks native way. E.g. pip and a requirements.txt for Python,
Maven for Java, npm for Node.js, Composer for PHP and so on. This allows you to
fully control the libraries and versions available to your app in the final
runtime environment.

Which buildpack is going to be used is determined by the application type set
when creating the app.

A required part of the image is a file called `Procfile` in the root directory.
It is used to determine how to start the actual application in the container.
Some of the buildpacks can provide a default Procfile. But it is recommended to
explicitly define the Procfile in your application to match your individual
requirements better. For a container to be able to receive requests from the
routing tier it needs at least the following content:

~~~
web: COMMAND_TO_START_THE_APP_AND_LISTEN_ON_A_PORT --port $PORT
~~~

For more specific examples of a `Procfile` please refer to the language and framework [guides].

At the end of the buildpack process, the image is ready to be deployed.


## Deploying New Versions

The exoscale platform supports zero downtime deploys for all deployments. To deploy a new version use either the *web console* or the `deploy` command.

~~~
$ exoapp APP_NAME/DEP_NAME deploy
~~~

To deploy a specific version, append your version control systems identifier (full commit-SHA1).
If not specified, the version to be deployed defaults to the latest image available (the one built during the last successful push).

For every deploy, the image is downloaded to as many of the platform’s nodes as required by the [--containers setting](#scaling) and started according to the buildpack’s default or the [Procfile](#buildpacks-and-the-procfile).
After the new containers are up and running the load balancing tier stops sending requests to the old containers and instead sends them to the new version.
A log message in the [deploy log](#deploy-log) appears when this process has finished.

### Container Idling

Deployments running on a single web container with one unit of memory (128MB/h) are automatically idled when they are not receiving HTTP requests for 1 hour or more. This
results in a temporary suspension of the container where the application is
running. It does not affect the Add-ons or workers related to this deployment.

Once a new HTTP request is sent to this deployment, the application is automatically re-engaged. This process causes a slight delay until the
first request is served. All following requests will perform normally.

You can see the state of your application with the following command:
~~~
$ exoapp APP_NAME/DEP_NAME details
Deployment
 name: APP_NAME/DEP_NAME
 [...]
 current state: idle
 [...]
~~~

Scaling your deployment will prevent idling, which is recommended for
any production system.

## Emergency Rollback

If your newest version breaks unexpectedly, you can use the rollback command to revert to the previous version in a matter of seconds:

~~~
$ exoapp APP_NAME/DEP_NAME rollback
~~~

It is also possible to deploy any other prior version. To find the version identifier you need, simply check the [deploy log](#deploy-log) for a previously deployed version, or get it directly from the version control system. You can redeploy this version using the deploy command:

~~~
$ exoapp APP_NAME/DEP_NAME deploy THE_LAST_WORKING_VERSION_HASH
~~~


## Non-Persistent Filesystem

**TL;DR:**

 * Each container has its own filesystem.
 * The filesystem is not persistent.
 * Don't store uploads on the filesystem.

Deployments on the exoscale platform have access to a writable filesystem. This
filesystem however is not persistent. Data written may or may not be accessible
again in future requests, depending on how the [routing tier](#routing-tier)
routes requests across available containers, and is deleted after each deploy.
This does include deploys you trigger manually, but also re-deploys done by the
platform itself during normal operation.

For customer uploads (e.g. user profile pictures) we recommend object stores like Amazon S3 or similar.


## Development, Staging and Production Environments

**TL;DR:**

 * Leverage multiple deployments to support the complete application lifecycle.
 * Each deployment has a set of environment variables to help you configure your app.
 * Various configuration files are available to adjust runtime settings.

### Development, Staging and Production: The Application Lifecycle

Most apps share a common application lifecycle consisting of development,
staging and production phases. The exoscale platform is designed from the
ground up to support this. As we explained earlier, each app can have multiple
deployments. Those deployments match the branches in the version control
system. The reason for this is very simple. To work on a new feature it is
advisable to create a new branch. This new version can then be deployed as its
own deployment making sure the new feature development is not interfering with
the existing deployments. More importantly even, these development/feature or
staging deployments also help ensure that the new code will work in production
because each deployment using the same [stack](#stacks) has the same runtime
environment.

### Environment Variables

Sometimes you have environment specific configuration, e.g. to enable debugging output in development deployments but disable it in production deployments. This can be done using the environment variables that each deployment provides to the app. The following environment variables are available:

 * **TMPDIR**: The path to the tmp directory.
 * **CRED_FILE**: The path of the creds.json file containing the Add-on credentials.
 * **DEP_VERSION**: The Git version the image was built from.
 * **DEP_NAME**: The deployment name in the same format as used by the command line client. E.g. myapp/default. This one stays the same even when undeploying and creating a new deployment with the same name.
 * **DEP_ID**: The internal deployment ID. This one stays the same for the deployments lifetime but changes when undeploying and creating a new deployment with the same name.
 * **WRK_ID**: The internal worker ID. Only set for worker containers.

## Add-ons

**TL;DR:**

 * Add-ons give you access to additional services like databases.
 * Each deployment needs its own set of Add-ons.
 * Add-on credentials are available to your app via the JSON formatted `$CRED_FILE` (and via environment variables depending on the programming language).

### Managing Add-ons

Add-ons add additional services to your deployment. The [Add-on marketplace]
offers a variety of different Add-ons. Think of it as an app store
dedicated to developers. Add-ons range from database offerings to data processing services
or deployment solutions.

Each deployment has its own set of Add-ons. If your app needs a MySQL database
and you have a production, a development and a staging environment, all three
must have their own MySQL Add-ons. Each Add-on comes with different plans
allowing you to choose  a more powerful database for your high traffic
production deployment and smaller ones for the development or staging
environments.

You can see the available Add-on plans on the Add-on marketplace website or with the `exoapp addon.list` command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.list
[...]
~~~

Adding an Add-on is just as easy.
~~~
$ exoapp APP_NAME/DEP_NAME addon.add ADDON_NAME.ADDON_OPTION
~~~

As always replace the placeholders written in uppercase with their respective values.

To get the list of current Add-ons for a deployment use the addon command.
~~~
$ exoapp APP_NAME/DEP_NAME addon
Addon                    : alias.free

Addon                    : config.free
[...]

Addon                    : cron.hourly
[...]

Addon                    : mysqls.free
[...]
~~~

To upgrade or downgrade an Add-on use the respective command followed by the Add-on plan you upgrade from and the Add-on plan you upgrade to.

~~~
# upgrade
$ exoapp APP_NAME/DEP_NAME addon.upgrade FROM_SMALL_ADDON TO_BIG_ADDON
# downgrade
$ exoapp APP_NAME/DEP_NAME addon.downgrade FROM_BIG_ADDON TO_SMALL_ADDON
~~~
**Remember:** As in all examples in this documentation, replace all the uppercase placeholders with their respective values.

### Add-on Credentials
For many Add-ons you require credentials to connect to their service. The credentials are exported to the deployment in
a JSON formatted config file. The path to the file can be found in the `CRED_FILE` environment variable. Never
hardcode these credentials in your application, because they differ over deployments and can change after any redeploy
without notice.

We provide detailed code examples how to use the config file in our guides section.

#### Enabling/disabling credentials environment variables
We recommend using the credentials file for security reasons but credentials can also be accessed through environment variables.
This is disabled by default for PHP and Python apps.
Accessing the environment is more convenient in most languages, but some reporting tools or wrong security settings in
your app might print environment variables to external services or even your users. This also applies to any child processes
of your app if they inherit the environment (which is the default). When in doubt, disable this feature and use
the credentials file.

Set the variable `SET_ENV_VARS` using the [Custom Config Add-on] to either `false` or `true` to explicitly enable or disable
this feature.

The guides section has detailed examples about how to get the credentials in
different languages
([Ruby](https://www.exoscale.ch/dev-center/Guides/Ruby/Add-on%20credentials),
[Python](https://www.exoscale.ch/dev-center/Guides/Python/Add-on%20credentials),
[Node.js](https://www.exoscale.ch/dev-center/Guides/NodeJS/Add-on%20credentials),
[Java](https://www.exoscale.ch/dev-center/Guides/Java/Add-on%20credentials),
[PHP](https://www.exoscale.ch/dev-center/Guides/PHP/Add-on%20credentials)).
To see the format and contents of the credentials file locally, use the `addon.creds` command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.creds
{
    "CONFIG": {
        "CONFIG_VARS": {
            "FOO": "BAR"
        }
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

## Logging

**TL;DR:**

 * There are four different log types (access, error, worker and deploy) available.

To see the log output in a `tail -f`-like fashion use the exoapp log command. The log command initially shows the last 500 log messages and then appends new messages as they arrive.

~~~
$ exoapp APP_NAME/DEP_NAME log [access,error,worker,deploy]
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

The [Custom Config Add-on] can be used to forward error and worker logs to the external logging services.

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
$ exoapp APP_NAME/DEP_NAME config.add RSYSLOG_REMOTE=custom_remote.cfg
~~~

From now on all the new logs should be visible in your custom syslog remote.


## Provided Subdomains and Custom Domains

**TL;DR:**

 * Each deployment is provided with a `*.app.exo.io` subdomain.
 * Custom domains are supported via the Alias Add-on.

Each deployment is provided per default with a `*.app.exo.io` subdomain. The `APP_NAME.app.exo.io` will point to the `default` deployment while any additional deployment can be accessed with a prefixed subdomain: `DEP_NAME-APP_NAME.app.exo.io`.

You can also use custom domains to access your deployments. To add a domain like `www.example.com`, `app.example.com` or `secure.example.com` to one of your deployments, simply add each one as an alias and add a CNAME for each pointing to your deployment's subdomain. So to point `www.example.com` to the default deployment of the app called *awesomeapp*, add a CNAME for `www.example.com` pointing to `awesomeapp.app.exo.io`. The [Alias Add-on] also supports mapping wildcard domains like `*.example.com` to one of your deployments.

All custom domains need to be verified before they start working. To verify a domain, it is required to also add the exoscale verification code as a TXT record.

Changes to DNS can take up to 24 hours until they have effect. Please refer to the Alias Add-on Documentation for detailed instructions on how to setup CNAME and TXT records.


## Routing Tier

**TL;DR:**

 * All HTTP requests are routed via our routing tier.
 * Within the routing tier, requests are routed via the `*.app.exo.io` subdomain.
 * The `*.app.exo.io` subdomain provides WebSocket support.
 * Requests are routed based on the `Host` header.
 * Use the `X-Forwarded-For` header to get the client IP.

All HTTP requests made to apps on the platform are routed via our routing tier. The routing tier is designed as a cluster of reverse proxy loadbalancers which orchestrate the forwarding of user requests to your applications. It takes care of routing the request to one of the application's containers based on matching the `Host` header against the list of the deployment's aliases. This is accomplished via the `*.app.exo.io` subdomain.

The routing tier is designed to be robust against single node and even complete datacenter failures while still keeping the added latency as low as possible.

### Elastic Addresses

Because of the elastic nature of the routing tier, the list of routing tier addresses can change at any time. It is therefore highly discouraged to point custom domains directly to any of the routing tier IP addresses. Please use a CNAME instead. Refer to the [custom domain section](#provided-subdomains-and-custom-domains) for more details on the correct DNS configuration.

### Remote Address

Given that client requests don't hit your application directly, but are forwarded via the routing tier, you can't access the client's IP by reading the remote address. The remote address will always be the internal IP of one of the routing nodes. To make the origin remote address available, the routing tier sets the `X-Forwarded-For` header to the original client's IP.

### Reverse Proxy timeouts

Our routing tier uses a cluster of reverse proxy loadbalancers to manage the acceptance and forwarding of user requests to your applications. To do this in an efficient way, we set strict timeouts to the read/ write operations. You can find them below.

 * __Connect timeout__ - time within a connection to your application has to be established. If your containers are up, but hanging, then this timeout will not apply as the connection to the endpoints has already been made.
 * __Read timeout__ - time to retrieve a response from your application. It determines how long the routing tier will wait to get the response to a request. The timeout is established not for an entire response, but only between two operations of reading.
 * __Send timeout__ - maximum time between two write operations of a request. If your application does not take new data within this time, the routing tier will shut down the connection.

#### Timeouts for `*.app.exo.io` subdomain:

|Parameter|Value [s]|
|:---------|:----------:|
|Connect timeout|20|
|Send timeout|55|
|Read timeout|55|

### Requests distribution

Our smart [DNS](https://en.wikipedia.org/wiki/Domain_Name_System) provides a fast and reliable service resolving domain names in a round robin fashion. All nodes are equally distributed to the three different availability zones but can route requests to any container in any other availability zone. To keep latency low, the routing tier tries to route requests to containers in the same availability zone unless none are available. Deployments running on --containers 1 (see the [scaling section](#scaling) for details) only run on one container and therefore are only hosted in one availability zone.

### High Availability

The routing tier provides a Health Checker to ensure high availability. Because this mechanism depends on having multiple containers available to route requests, only deployments with more than one container running (see the [scaling section](#scaling) for details) can take advantage of high availability.

In the event of a single node or container failure, the platform will start a replacement container. Deployments running on --containers 1 will be unavailable for a few minutes while the platform starts the replacement. To avoid even short downtimes, set the --containers option to at least 2.

#### Health Checker

For the `*.app.exo.io` subdomain, failed requests will cause an error message to be returned to the user once, but the "unhealthy" container will be actively monitored by a health checker. This signals the routing tier to temporarily remove the unhealthy container from the list of containers receiving requests. Subsequent requests are routed to an available container of the deployment. Once the health checker notices that the container has recovered, the container will be re-included in the list to receive requests.

Because the health checker actively monitors containers where an application is running into timeouts or returning [http error codes](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.5) `501`, `502` or `greater 503`, you may see requests to `/CloudHealthCheck` coming from a `cloudControl-HealthCheck` agent.


## Scaling

**TL;DR:**

 * You can scale up or down at any time by adding more containers (horizontal scaling) or changing the container size (vertical scaling).
 * Use performance monitoring and load testing to determine the optimal scaling settings for your app.

When scaling your apps you have two options. You can either scale horizontally by adding more containers, or scale vertically by changing the container size. When you scale horizontally, the exoscale loadbalancing and [routing tier](#routing-tier) ensures efficient distribution of incoming requests accross all available containers.

### Horizontal Scaling

Horizontal scaling is controlled by the --containers parameter.
It specifies the number of containers you have running.
Raising --containers also increases the availability in case of node failures.
Deployments with --containers 1 (the default) are unavailable for a few minutes in the event of a node failure until the failover process has finished. Set --containers value to at least 2 if you want to avoid downtime in such situations.

### Vertical Scaling

In addition to controlling the number of containers you can also specify the memory size of a container. Container sizes are specified using the --memory parameter, being possible to choose from 128MB to 1024MB.


## Performance

**TL;DR:**

 * Reduce the total number of requests that make up a page view.

### Reducing the Number of Requests

Perceived web application performance is mostly influenced by the frontend. It's very common that the highest optimization potential lies in reducing the overall number of requests per page view. One common technique to accomplish this is combining and minimizing javascript and css files into one file each and using sprites for images.


## WebSockets

**TL;DR:**

 * WebSockets are supported via the Routing Tier.
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

Please note that Secure WebSockets connections can only be established using `*.app.exo.io` subdomains, not custom ones. It is highly recommended to use them, not only for data security reasons. Secure WebSockets are 100% proxy transparent, which puts your containers in full control of WebSocket `upgrade handshake` in case some of the proxies do not handle it properly.


## Scheduled Jobs and Background Workers

**TL;DR:**

 * Web requests are subject to a time limit of 55s.
 * Scheduled jobs are supported through different Add-ons.
 * Background workers are the recommended way of handling long running or asynchronous tasks.

Since a web request taking longer than 55s is killed by the routing tier, longer running tasks have to be handled asyncronously.

### Cron

For tasks that are guaranteed to finish within the time limit, the [Cron add-on] is a simple solution to call a predefined URL daily or hourly and have that task called periodically. For a more detailed documentation on the Cron Add-on, please refer to the [Cron Add-on documentation].

### Workers

Tasks that will take longer than 55s to execute, or that are triggered by a user request and should be handled asyncronously to not keep the user waiting, are best handled by the [Worker add-on]. Workers are long-running processes started in containers. Just like the web processes but they are not listening on any port and therefore do not receive http requests. You can use workers, for example, to poll a queue and execute tasks in the background or handle long-running periodical calculations. More details on usage scenarios and available queuing Add-ons are available as part of the [Worker Add-on documentation].


## Secure Shell (SSH)

The distributed nature of the exoscale platform means it's not possible to SSH
into the actual server. Instead, we offer the run command, that allows you to
launch a new container and connect to that via SSH.

The container is identical to the web or worker containers but starts an SSH
daemon instead of one of the Procfile commands. It's based on the same stack
image and deployment image and does also provides the Add-on credentials.

### Examples

To start a shell (e.g. bash) use the `run` command.

~~~
$ exoapp APP_NAME/DEP_NAME run bash
Connecting...
Warning: Permanently added '[10.62.45.100]:25832' (RSA) to the list of known hosts.
u25832@DEP_ID-25832:~/www$ echo "interactive commands work as well"
interactive commands work as well
u25832@DEP_ID-25832:~/www$ exit
exit
Connection to 10.62.45.100 closed.
Connection to sshforwarder.app.exo.io closed.
~~~

It's also possible to execute a command directly and have the container shutdown after the command is finished. This is very useful for database migrations and other one-time tasks.

For example, passing the `"env | sort"` command will list the environment variables. Note that the use of the quotes is required for a command that includes spaces.
~~~
$ exoapp APP_NAME/DEP_NAME run "env | sort"
Connecting...
Warning: Permanently added '[10.250.134.126]:10346' (RSA) to the list of known hosts.
CRED_FILE=/srv/creds/creds.json
DEP_ID=DEP_ID
DEP_NAME=APP_NAME/DEP_NAME
DEP_VERSION=9d5ada800eff9fc57849b3102a2f27ff43ec141f
DOMAIN=app.exo.io
GEM_PATH=vendor/bundle/ruby/1.9.1
HOME=/srv
HOSTNAME=DEP_ID-10346
LANG=en_US.UTF-8
LOGNAME=u10346
MAIL=/var/mail/u10346
OLDPWD=/srv
PAAS_VENDOR=exoscale
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
Connection to sshforwarder.app.exo.io closed.
~~~

## Stacks

**TL;DR:**

 * Stacks define the common runtime environment.
 * They are based on Ubuntu and stack names match the Ubuntu release's first letter.
 * Pinky is the current stack and supports multiple languages according to the available [buildpacks](#buildpacks-and-the-procfile).

A stack defines the common runtime environment for all deployments using it.
This guarantees that all your deployments find the same version of all OS
components as well as all pre-installed libraries.

Stacks are based on Ubuntu releases and have the same first letter as the
release they are based on. Each stack is named after a super hero sidekick. We
try to keep them as close to the Ubuntu release as possible, but do make
changes when necessary for security or performance reasons to optimize the
stack for its specific purpose on our platform.

### Available Stacks

 * **Pinky** based on [Ubuntu 12.04 LTS Precise Pangolin]

Details about the current stack are available via the `exoapp` command line interface.
~~~
$ exoapp APP_NAME/DEP_NAME details
 name: APP_NAME/DEP_NAME
 stack: pinky
 [...]
~~~

[generating SSH keys]: https://help.github.com/articles/generating-ssh-keys
[Custom Config Add-on]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Deployment/Custom%20Config
[web console]: https://www.exoscale.ch/console
[API libraries]: https://github.com/cloudControl
[the latest version]: http://cctrl.s3-website-eu-west-1.amazonaws.com/#windows/
[Python 2.6+]: http://python.org/download/
[quick Git tutorial]: http://rogerdudler.github.com/git-guide/
[Heroku buildpack API]: https://devcenter.heroku.com/articles/buildpack-api
[guides]: https://www.exoscale.ch/dev-center/Guides
[Add-on marketplace]: https://www.exoscale.ch/add-ons
[Deployment category]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Deployment
[rsyslog]: http://www.rsyslog.com/
[TLS]: http://en.wikipedia.org/wiki/Transport_Layer_Security
[Alias Add-on]: https://www.exoscale.ch/add-ons/alias
[Cron Add-on]: https://www.exoscale.ch/add-ons/cron
[Cron Add-on documentation]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Deployment/Cron
[Worker Add-on]: https://www.exoscale.ch/add-ons/worker
[Worker Add-on documentation]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Data%20Processing/Worker
[Ubuntu 12.04 LTS Precise Pangolin]: http://releases.ubuntu.com/precise/
