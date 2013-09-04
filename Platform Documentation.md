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



## Platform Access

**TL;DR:**

 * The command line client cctrl is the primary interface.
 * We also offer a web console.
 * For full control and integration it's possible to talk directly to the RESTful API.

To control the platform we offer different interfaces. The primary way of controlling your apps and deployments is via [the command-line interface](http://en.wikipedia.org/wiki/Command-line_interface) (CLI) called *cctrl*. Additionally we also offer a [web console](https://console.cloudcontrolled.com). Both the CLI as well as the web console however are merely frontends to our RESTful API. For deep integration into your apps you can optionally use one of our available [API libraries](https://github.com/cloudControl).

Throughout this documentation we will use the CLI as the primary way of controlling the cloudControl platform. Installing cctrl is easy and works on Mac/Linux as well as on Windows.
Throughout this documentation we will use the CLI as the primary way of controlling the cloudControl platform. The CLI consists of 2 parts: *cctrlapp* and *cctrluser*. To get help for the command line client, just append --help or -h to any of the commands.

Installing cctrl is easy and works on Mac/Linux as well as on Windows.

#### Quick Installation Windows

For Windows we offer an installer. Please download [the latest version](http://cctrl.s3-website-eu-west-1.amazonaws.com/#windows/) of the installer from S3. The file is named cctrl-x.x-setup.exe.

#### Quick Installation Linux/Mac

On Linux and Mac OS we recommend installing and updating cctrl via pip. *cctrl* requires [Python 2.6+](http://python.org/download/).

~~~
$ sudo pip install -U cctrl
~~~

If you don't have pip you can install pip via easy_install (on Linux usually part of the python-setuptools package) and then install cctrl.

~~~
$ sudo easy_install pip
$ sudo pip install -U cctrl
~~~

## User Accounts

**TL;DR:**

 * Every developer has their own user account
 * User accounts can be created via the [web console](https://console.cloudcontrolled.com/register) or via ``cctrluser create``
 * User accounts can be deleted via ``cctrluser delete``

To work on and manage your applications on the platform, a user account is needed. User accounts can be created via the [Console](https://console.cloudcontrolled.com/register) or using the following CLI command:
~~~
cctrluser create
~~~

After this, an activation email is sent to the given email address. Click the link in the email or use the following CLI command to activate the account:

~~~
cctrluser activate USER_NAME ACTIVATION_CODE
~~~

If you want to delete your user account, please use the following CLI command:
~~~
$ cctrluser delete
~~~

### Password Reset

You can [reset your password](https://api.cloudcontrol.com/reset_password/), in case you forgot it.

## Apps, Users and Deployments

**TL;DR:**

 * Applications (apps) have a repository, deployments and users.
 * The repository is where your code lives, organized in branches.
 * A deployment is a running version of your application, based on the branch with the same name. Exception: the default deployment is based on the master branch.
 * Users can be added to apps to gain access to the repository, branches and deployments.

cloudControl PaaS uses a distinct set of naming conventions. To understand how to work with the platform effectively, it's important to understand the following few basic concepts.

### Apps

An app consists of a repository (with branches), deployments and users. Creating an app allows you to add or remove users to that app, giving them access to the source code as well as allowing them to manage the deployments.

Creating an app is easy. Simply specify a name and the desired type to determine which [buildpack](#buildpacks-and-the-procfile) to use.

~~~
$ cctrlapp APP_NAME create php
~~~

You can always list your existing apps using the command line client too.

~~~
$ cctrlapp -l
Apps
 Nr  Name                           Type
   1 myfirstapp                     php
   2 nextbigthing                   php
   [...]
~~~

### Users

By adding users to an app you can grant fellow developers access to the source code in the repository, allow them to [deploy new versions](#deploying-new-versions) and modify the deployments including their [Add-ons](#managing-add-ons). Permissions are based on the user's [roles](#roles).

You can list, add and remove app users using the command line client.

~~~
$ cctrlapp APP_NAME user
Users
 Name                                     Email
 user1                                    user1@example.com
 user2                                    user2@example.com
 user3                                    user3@example.com
~~~

To add a user please use their email address. If the user is already registered with that address, they will be added to the app. If not, they will first receive an email invitation and will be added after activating their account.

~~~
$ cctrlapp APP_NAME user.add user4@example.com
~~~

To remove a user, please use their username.

~~~
$ cctrlapp APP_NAME user.remove user3
~~~

#### Roles

 * **Owner**: Creating an app makes you the owner and gives you full access. The owner can not be removed from the app and gets charged for all their apps' consumption. If you plan on having multiple developers working on the same app, it's recommended to have a separate admin-like account as the owner of all your apps and add the additional developers (including yourself) separately.
 * **Developer**: The default role for users added to an app is the developer role. Developers have full access to the repository and to all deployments. Developers can add more developers or even remove existing ones. They can even delete deployments and also the app itself. Developers however can not change the associated billing account or remove the owner.

#### Keys

For secure access to the app's repository, each developer needs to authenticate via public/private key authentication. Please refer to GitHub's article on [generating SSH keys](https://help.github.com/articles/generating-ssh-keys) for details on how to create a key. You can simply add your default key to your user account using the command line client. If the default key can not be found, cctrlapp will offer to create one.

~~~
$ cctrluser key add
~~~

You can also list the available key ids and remove existing keys using those key ids.

~~~
$ cctrluser key
Keys
 Dohyoonuf7
$ cctrluser key Dohyoonuf7
ssh-rsa AAA[...]
$ cctrluser key.remove Dohyoonuf7
~~~

### Deployments

A deployment is the running version of one of your branches made accessible via a [provided subdomain](#provided-subdomains-and-custom-domains).
It is based on the branch of the same name, with the exception of the master branch which is used by the default deployment.

Deployments run independently from each other, including seperate runtime environments, file system storage and Add-ons (e.g. databases and caches).
This allows you to have different versions of your app running at the same time without interfering with each other.
Please refer to the section about [development, staging and production environments](#development-staging-and-production-environments) to understand why this is a good idea.

You can list all the deployments with the *details* command.

~~~
$ cctrlapp APP_NAME details
App
 Name: APP_NAME                       Type: php        Owner: user1
 Repository: ssh://APP_NAME@cloudcontrolled.com/repository.git

 [...]

 Deployments
   APP_NAME/default
   APP_NAME/dev
   APP_NAME/stage
~~~

## Version Control & Images

**TL;DR:**

 * Git and Bazaar are supported.
 * When you push an updated branch, an image of your code gets built, ready to be deployed.
 * Image sizes are limited to 200MB (compressed). Use a `.cctrlignore` file to exclude development assets.

### Supported Version Control Systems

The platform supports Git ([quick Git tutorial](http://rogerdudler.github.com/git-guide/)) and Bazaar ([Bazaar in five minutes](http://doc.bazaar.canonical.com/latest/en/mini-tutorial/)). When you create an app we try to determine if the current working directory has a .git or .bzr directory. If it does, we create the app with the detected version control system. If we can't determine this based on the current working directory Git is used as the default. You can always overwrite this with the --repo command line switch.

~~~
$ cctrlapp APP_NAME create php [--repo [git,bzr]]
~~~

It's easy to tell what version control system an existing app uses based on the repository URL provided as part of the app details.

~~~
$ cctrlapp APP_NAME details
App
 Name: APP_NAME                       Type: php        Owner: user1
 Repository: ssh://APP_NAME@cloudcontrolled.com/repository.git
 [...]
~~~
If yours starts with `ssh://` and ends with `.git` then Git is being used. If it starts with `bzr+ssh://`, Bazaar is being used.

### Image Building

Whenever you push an updated branch, a deployment image is built automatically.
This image can then be deployed with the *deploy* command to the deployment matching the branch name.
The contents of the image get generated by the [buildpack](#buildpacks-and-the-procfile) and usually include your application code in a runnable form and any dependencies that where installed by the buildpack.

You can use the cctrlapp push command or the normal git/bzr push command.

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

You can decrease your image size by making sure that no unneeded files (e.g. caches, logs, backup files) are tracked
in your repository. Files that need to be tracked but are not required in the image (e.g. development assets or
source code files in compiled languages), can be added to a `.cctrlignore` file in the project root directory.
The format is similar to the `.gitignore`, but without the negation operator `!`. Here’s an example `.cctrlignore`:

~~~
*.psd
*.pdf
test
spec
~~~

#### Buildpacks and the Procfile

During the push a hook is fired that runs the buildpack. A buildpack is a set of scripts that determine how an app in a specific language or framework has to be prepared for deployment on the cloudControl platform. With custom buildpacks, support for new programming languages can be added or custom runtime environments can be build. To support many PaaS with one buildpack, we recommend following the [Heroku buildpack API](https://devcenter.heroku.com/articles/buildpack-api) which is compatible with cloudControl and other platforms.

Part of the buildpack scripts is also to pull in library dependencies. The concrete method of doing this varies between different languages and frameworks. E.g. pip and a requirements.txt are used for Python, Maven for Java, npm for node.js, Composer for PHP etc. This allows you to fully control the libraries and versions available to your app in the final runtime environment.

Which buildpack is going to be used is determined by the application type set when creating the app.

A required part of the image is a file called `Procfile` in the root directory of the repository. It is used to determine how to start the actual application in the container. For a container to be able to receive requests from the routing tier it needs at least the following content:

~~~
web: COMMAND_TO_START_THE_APP_AND_LISTEN_ON_A_PORT --port $PORT
~~~

For more specific examples of a `Procfile` please refer to the language and framework [guides](https://www.cloudcontrol.com/dev-center/Guides).

At the end of the buildpack process, the image is ready to be deployed.

## Deploying New Versions

The cloudControl platform supports zero downtime deploys for all deployments. To deploy a new version use the cctrlapp deploy command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy
~~~

To deploy a specific version, append your version control systems identifier (full commit-SHA1 for Git or an integer for Bazaar).
If not specified, the version to be deployed defaults to the latest image available (the one built during the last successful push).

For every deploy, the image is downloaded to as many of the platform’s nodes as required by the [--containers setting](#scaling) and started according to the buildpack’s default or the [Procfile](#buildpacks-and-the-procfile).
After the new containers are up and running the loadbalancing tier stops sending requests to the old containers and instead sends them to the new version.
A log message in the [deploy log](#deploy-log) appears when this process has finished.

## Emergency Rollback

If for some reason a new version does not work as expected, you can rollback any deployment to a previous version in a matter of seconds. To do so you can check the [deploy log](#deploy-log) for the previously deployed version (or get it from the version control system directly) and then simply use the Git or Bazaar version identifier that's part of the log output to redeploy this version using the deploy command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy THE_LAST_WORKING_VERSION
~~~

## Non Persistent Filesystem

**TL;DR:**

 * Each container has its own filesystem.
 * The filesystem is not persistent.
 * Don't store uploads on the filesystem.

Deployments on the cloudControl platform have access to a writable filesystem. This filesystem however is not persistent. Data written may or may not be accessible again in future requests, depending on how the [routing tier](#routing-tier) routes requests across available containers, and is deleted after each deploy. This does include deploys you trigger manually, but also re-deploys done by the platfom itself during normal operation.

For customer uploads (e.g. user profile pictures) we recommend object stores like Amazon S3 or the GridFS feature available as part of the [MongoLab Add-on](https://www.cloudcontrol.com/add-ons/mongolab).

## Development, Staging and Production Environments

**TL;DR:**

 * Leverage multiple deployments to support the complete application lifecycle.
 * Each deployment has a set of environment variables to help you configure your app.
 * Various configuration files are available to adjust runtime settings.

### Development, Staging and Production: The Application Lifecycle

Most apps share a common application lifecycle consisting of development, staging and production phases. The cloudControl platform is designed from the ground up to support this. As we explained earlier, each app can have multiple deployments. Those deployments match the branches in the version control system. The reason for this is very simple. To work on a new feature it is advisable to create a new branch. This new version can then be deployed as its own deployment making sure the new feature development is not interfering with the existing deployments. More importantly even, these development/feature or staging deployments also help ensure that the new code will work in producion because each deployment using the same [stack](#stacks) has the same runtime environment.

### Environment Variables

Sometime it's useful for the app to check the deployment it currently runs in, e.g. to enable debugging output in development deployments but disable it in production deployments. This can be done by inspecting the environment variables that each deployment makes available to the app. The following environment variables are available:

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
 * Add-on credentials are available to your app via the JSON formatted `$CRED_FILE` (and via environment variables depending on the app's language).

### Managing Add-ons

Add-ons add additional services to your deployment. The [Add-on marketplace](https://www.cloudcontrol.com/add-ons) offers a wide variety of different Add-ons. Think of it as an app store dedicated to developers. Add-ons can be different database offerings, caching, performance monitoring or logging services or even complete backend APIs or billing solutions.

Each deployment needs its own set of Add-ons. If your app needs a MySQL database and you have a production, a development and a staging environment, all three need their own MySQL Add-ons. Each Add-on comes in a few different plans allowing you to choose a more powerful database for your high traffic production deployment and a smaller one for the development or staging environments.

You can see the available Add-on plans on the [Add-on marketplace website](https://www.cloudcontrol.com/add-ons) or with the cctrlapp addon.list command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.list
[...]
~~~

Adding an Add-on is just as easy.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add ADDON_NAME.ADDON_OPTION
~~~
As always replace the placeholders written in uppercase with their respective values.

To get the list of current Add-ons for a deployment use the addon command.

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

To upgrade or downgrade an Add-on use the respective command followed by the Add-on name you upgrade from and the Add-on name you upgrade to.

~~~
# upgrade
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade FROM_SMALL_ADDON TO_BIG_ADDON
# downgrade
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade FROM_BIG_ADDON TO_SMALL_ADDON
~~~
**Remember:** As in all examples in this documentation, replace all the uppercase placeholders with their respective values.

### Add-on Credentials
For many Add-ons you require credentials to connect to their service. The credentials are exported to the deployment in
a JSON formatted config file. The path to the file can be found in the `CRED_FILE` environment variable. Never
hardcode these credentials in your application, because they differ over deployments and can change after any redeploy
without notice.

A quick example to get MySQL credentials in PHP:
~~~php
# read the credentials file
$string = file_get_contents($_ENV['CRED_FILE']);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

# the file content is in JSON format, decode it and return an associative array
$creds = json_decode($string, true);

# now use the $creds array to configure your app e.g.:
$MYSQL_HOSTNAME = $creds['MYSQLS']['MYSQLS_HOSTNAME'];
~~~

#### Enabling/disabling credentials environment variables
We recommend using the credentials file for security reasons but credentials can also be accessed through environment variables.
This is disabled by default for PHP and Python apps.
Accessing the environment is more convenient in most languages, but some reporting tools or wrong security settings in
your app might print environment variables to external services or even your users. This also applies to any child processes
of your app if they inherit the environment (which is the default). When in doubt, disable this feature and use
the credentials file.

Set the variable `SET_ENV_VARS` using the [Custom Config Add-on] to either `False` or `True` to explicitly enable or disable
this feature.

The [guides section](https://www.cloudcontrol.com/dev-center/Guides/) has detailed examples about how to get the credentials in different languages ([Ruby](https://www.cloudcontrol.com/dev-center/Guides/Ruby/Add-on%20credentials), [Python](https://www.cloudcontrol.com/dev-center/Guides/Python/Add-on%20credentials), [Java](https://www.cloudcontrol.com/dev-center/Guides/Java/Add-on%20credentials)).
To see the format and contents of the credentials file locally, use the `addon.creds` command.

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

## Logging

**TL;DR:**

 * There are four different log types (access, error, worker and deploy) available.

To see the log output in a `tail -f`-like fashion use the cctrlapp log command. The log command initially shows the last 500 log messages and then appends new messages as they arrive.

~~~
$ cctrlapp APP_NAME/DEP_NAME log [access,error,worker,deploy]
[...]
~~~

### Access Log

The access log shows each access to your app in an Apache compatible log format.

### Error Log

The error log shows all output your app prints to stdout, stderr and syslog. It also shows when a new version has been deployed to make it easy to determine if a problem existed already before or only after the last deploy. More detailed information on deploys can be found in the [deploy log](#deploy-log).

### Worker Log

Workers are long running background processes. As such, they are not accessible via http from the outside. To make worker output accessible to you, its stdout, stderr and syslog output is redirected to this log. The worker log shows the timestamp of when the message was written, the *wrk_id* of the worker the message came from as well as the actual log line.

### Deploy Log

The deploy log gives detailed information on the deploy process. It shows on how many nodes your deployment is deployed and lists the nodes themselves, how long it took for each of the nodes to start the container and get the deployment running and also when the loadbalancers started sending traffic to the [new version](#deploying-new-versions).

### Customizing logging

Some Add-ons in the [Deployment category](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment) as well as the [Custom Config Add-on] can be used to forward error and worker logs to the external logging services.

#### Adding custom syslog logging with Custom Config Add-on

The Custom Config Add-on can be used to specify an additional endpoint where error and worker logs will be sent.
This is done by setting the config variable "RSYSLOG_REMOTE". The content should contain valid [rsyslog](http://www.rsyslog.com/) configuration and can span multiple lines.

E.g. to forward the logs to custom syslog remote over a [TLS](http://en.wikipedia.org/wiki/Transport_Layer_Security) connection, create a temporary file with the following content:
~~~
$DefaultNetstreamDriverCAFile /app/CUSTOM_CERTIFICATE_PATH
$ActionSendStreamDriver gtls
$ActionSendStreamDriverMode 1
$ActionSendStreamDriverAuthMode x509/name
$template CustomFormat, "%syslogtag%%msg%\n"
*.* @@SERVER_ADDRESS:PORT;CustomFormat
~~~
where "SERVER_ADDRESS" and "PORT" should be replaced with the concrete values and "CUSTOM_CERTIFICATE_PATH" should be the path to a certificate file for the custom syslog remote in you repository.

Use that file's name (let's say it's named `custom_remote.cfg`) as a value for the "RSYSLOG_REMOTE" config variable:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add config.free --RSYSLOG_REMOTE=custom_remote.cfg
~~~

From now on all the new logs should be visible in your custom syslog remote.

## Provided Subdomains and Custom Domains

**TL;DR:**

 * Each deployment is provided a `.cloudcontrolled.com` subdomain.
 * Custom domains are supported via the Alias Add-on.

Each deployment gets a `.cloudcontrolled.com` subdomain. The default deployment always answers at `APP_NAME.cloudcontrolled.com` while any additional deployments get a `DEP_NAME-APP_NAME.cloudcontrolled.com` subdomain.

You can use custom domains to access your deployments. To add a domain like `www.example.com`, `app.example.com` or `secure.example.com` to one of your deployments simply add each one as an alias and add a CNAME for each pointing to your deployment's subdomain. So to point `www.example.com` to the default deployment of the app called *awesomeapp* add a CNAME for `www.example.com` pointing to `awesomeapp.cloudcontrolled.com`. The [Alias Add-on](https://www.cloudcontrol.com/add-ons/alias) also supports mapping wildcard domains like `*.example.com` to one of your deployments.

All custom domains need to be verified before they start working. To verify a domain it is required to also add the cloudControl verfification code as a TXT record.

Changes to DNS can take up to 24 hours until they have effect. Please refer to the [Alias Add-on Documentation](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Alias) for detailed instructions on how to setup CNAME and TXT records.

## Scaling

**TL;DR:**

 * You can scale up or down anytime by adding more containers (horizontal scaling) or changing the container size (vertical scaling).
 * Use performance monitoring and load testing to determine the optimal scaling settings for your app.

When scaling your apps you have two options. You can either scale horizontally by adding more containers, or scale vertically by changing the container size. When you scale horizontally the cloudControl loadbalancing and [routing tier](#routing-tier) ensures efficient distribution of incoming requests accross all available containers.

### Horizontal Scaling

Horizontal scaling is controlled by the --containers parameter.
It specifies the number of containers you have running.
Raising --containers also increases the availability in case of node failures.
Deployments with --containers 1 (the default) are unavailable for a few minutes in the event of a node failure until the failover process has finished. Set --containers value to at least 2 if you want to avoid downtime in such situations.

### Vertical Scaling

In addition to controlling the number of containers you can also specify the memory size of a container. Container sizes are specificed using the --memory parameter, being possible to choose from 128MB to 1024MB. To determine the optimal --memory value for your deployment you can use the New Relic Add-on to analyze the memory consumption of your app.

### Choosing Optimal Settings

You can use the [Blitz.io](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Performance%20&%20Monitoring/Blitz.io) and New [Relic Add-ons](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Performance%20&%20Monitoring/New%20Relic) to run synthetic load tests against your deployments and analyze how well they perform with the current --containers and --memory settings under expected load to determine the optimal scaling settings and adjust accordingly. We have a [tutorial](https://www.cloudcontrol.com/blog/best-practice-running-and-analyzing-load-tests-on-your-cloudcontrol-app) that explains this in more detail.

## Routing Tier

**TL;DR:**

 * All HTTP requests are routed via the routing tier.
 * `*.cloudcontrolled.com` is round robin across available routing tier nodes.
 * Requests are routed based on the `Host` header.
 * Use the `X-Forwarded-For` header to get the client IP.

All HTTP requests made to apps on the platform are routed via the routing tier. It takes care of routing the request to one of the app's containers based on matching the `Host` header against the list of the deployments aliases.

The routing tier is designed to be robust against single node and even complete datacenter failures while still keeping the added latency as low as possible.

The `*.cloudcontrolled.com` subdomains resolve in a round robin fashion to the current list of routing tier node IP addresses. All nodes are equally distributed to the three different availability zones but can route requests to any container in any other availability zone. To keep latency low, the routing tier tries to route requests to containers in the same availability zone unless none are available. Deployments running on --containers 1 (see the [scaling section](#scaling) for details) only run in one container and therefore only in one availability zone.

Because of the elastic nature of the routing tier the list of routing tier addresses can change at any time. It is therefore highly discouraged to point custom domains directly to any of the routing tier IP addresses. Please use a CNAME instead. Refer to the [custom domain section](#provided-subdomains-and-custom-domains) for more details on the correct DNS configuration.

If a container is not available due to an underlying node failure or a problem with the code in the container itself, the routing tier automatically routes requests to the other available containers of the deployment. Deployments running on --containers 1 will be unavailable for a couple of minutes until a replacement container has been started. To avoid even short downtimes in the event of a single node or container failure set the --containers option to at least 2.

### Remote Address

Because client requests don't hit your app directly, but are forwarded via the routing tier, you can't access the clients IP by reading the remote address. The remote address will always be the internal IP of one of the routing nodes. To make the origin remote address available the routing tier sets the `X-Forwarded-For` header to the original clients IP.

## Performance & Caching

**TL;DR:**

 * Reduce the total number of requests that make up a page view.
 * Cache as far away from your database as possible.
 * Try to rely on cache breakers instead of flushing.

### Reduce the Number of Requests

Perceived web application performance is mostly influenced by the frontend. It's very common that the highest optimization potential lies in reducing the overall number of requests per page view. Common techniques to do this is combining and minimizing javascript and css files into one file each and using sprites for images.

### Caching Early

After you have reduced the total number of requests it's recommended to cache as far away from your database as possible. Using far future expire headers to avoid that browsers request ressources at all. The next best way of reducing the number of requests that hit your backends is to cache complete responses in the loadbalancer. For this we offer caching directly in the loadbalancing and routing tier.

#### Caching Proxy

The loadbalancing and routing tier that is in front of all deployments includes a [Varnish](https://www.varnish-cache.org/) caching proxy. To have your requests cached directly in Varnish and speed up the response time through this, ensure you have set correct cache control headers for the request. Also ensure, that the request does not include a cookie. Cookies are often used to keep state accross requests (e.g. if a user is logged in). To avoid caching responses for logged in users and returning them to other users Varnish is configured to never cache requests with cookies. To be able to cache requests in Varnish for apps that rely on cookies we recommend using a cookieless domain.

You can check if a request was cached in Varnish by checking the response's *X-varnish-cache* header. The value HIT means the respons was answered directly from the cache, and MISS means it was not.

#### In-Memory Caching

To make requests that can't use a cookieless domain faster you can use in memory caching to store arbitrary data from database query results to complete http responses. Since the cloudControl routing tier distributes requests accross all available containers it is recommended to cache data in a way that makes it available also for requests that are routed to different containers. A battle tested solution for this is Memcached which is available via the [MemCachier Add-on](https://www.cloudcontrol.com/add-ons/memcachier). Refer to the [managing Add-ons](#managing-add-ons) section on how to add it. Also the [MemCachier Documentation](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MemCachier) has detailed instructions on how to use it for your language and framework of choice.

### Cache Breakers

When caching requests on client side or in a caching proxy, the URL is usually used as the cache identifier. As long as the URL stays the same and the cached response has not expired, the request is answered from cache. As part of every deploy all containers are started from a clean image. This ensures that all containers have the latest app code including templates, css, image and javascript files. But when using far future expire headers as recommended above this doesn't change anything if the response was cached at client or loadbalancer level. To ensure clients get the latest and greatest version it is recommend to include a changing parameter into the URL. This is commonly referred to as a cache breaker.

As part of the set of [environment variables](#environment-variables) in the deployment runtime environment the DEP_VERSION is made available to the app. If you want to force a refresh of the cache when a new version is deployed you can use the DEP_VERSION to accomplish this.

This technique works for URLs as well as for the keys in in-memory caches like Memcached.
Imagine you have cached values in Memcached that you want to keep between deploys and have values in Memcached that you want refreshed for each new version.
Since Memcached only allows flushing the complete cache you would lose all cached values.
Including the DEP_VERSION as part of the key of the cached values you want refreshed is an easy way to ensure that the cache gets refreshed.

## Scheduled Jobs and Background Workers

**TL;DR:**

 * Web requests are subject to a time limit of 120s.
 * Scheduled jobs are supported through different Add-ons.
 * Background workers are the recommended way of handling long running or asynchronous tasks.

Since a web request taking longer than 120s is killed by the routing tier, longer running tasks have to be handled asyncronously.

### Cron

For tasks that are guaranteed to finish within the timelimit, the [Cron add-on](https://www.cloudcontrol.com/add-ons/cron) is a simple solution to call a predefined URL daily or hourly and have that task called periodically. For a more detailed documentation on the Cron add-on please refer to the [Cron add-on documentation](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Cron)

### Workers

Tasks that will take longer than 120s to execute or that are triggered by a user request and should be handled asyncronously to not keep the user waiting are best handled by the [Worker add-on](https://www.cloudcontrol.com/add-ons/worker). Workers are long running processes started in containers just like the web processes but are not listening on a port and do not receive http requests. You can use workers to e.g. poll a queue and execute tasks in the background or handle long running periodical calculations. More details on usage scenarios and available queuing add-ons are available as part of the [Worker add-on documentation](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Processing/Worker).

## Secure Shell (SSH)

The distributed nature of the cloudControl platform means it's not possible to SSH into the actual server. Instead, we offer the run command, that allows to launch a new container and connect to that via SSH.

The container is identical to the web or worker containers but starts an SSH daemon instead of one of the Procfile commands. Its based on the same stack image and deployment image and does also provides the Add-on credentials.

### Examples

To start a shell (e.g. bash) use the `run` command.

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

It's also possible to execute a command directly and have the container exit after the command finished. This is very useful for database migrations and other one-time tasks.

Listing the environment variables using `"env | sort"` works. Note that the use of the quotes is required for a command that includes spaces.
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

## Stacks

**TL;DR:**

 * Stacks define the common runtime environment.
 * They are based on Ubuntu and stack names match the Ubuntu release's first letter.
 * Luigi supports only PHP. Pinky supports multiple languages according to the available [buildpacks](#buildpacks-and-the-procfile).

A stack defines the common runtime environment for all deployments using it. By choosing the same stack for all your deployments, it's guaranteed that all your deployments find the same version of all OS components as well as all preinstalled libraries.

Stacks are based on Ubuntu releases and have the same first letter as the release they are based on. Each stack is named after a super hero sidekick. We try to keep them as close to the Ubuntu release as possible, but do make changes when necessary for security or performance reasons to optimize the stack for its specific purpose on our platform.

### Available Stacks

 * **Luigi** based on [Ubuntu 10.04 LTS Lucid Lynx](http://releases.ubuntu.com/lucid/)
 * **Pinky** based on [Ubuntu 12.04 LTS Precise Pangolin](http://releases.ubuntu.com/precise/)

You can change the stack per deployment. This is handy for testing new stacks before migrating the production deployment. To see the stack a deployment is using, refer to the deployment details.

~~~
$ cctrlapp APP_NAME/DEP_NAME details
 name: APP_NAME/DEP_NAME
 stack: luigi
 [...]
~~~

To change the stack of a deployment simply append the --stack command line option to the deploy command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy --stack [luigi,pinky]
~~~

[Custom Config Add-on]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config
