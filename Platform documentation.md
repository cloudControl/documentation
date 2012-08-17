# cloudControl Documentation

## Command line client, web console and API

**TL;DR:**

 * The command line client cctrl is the primary interface.
 * We also offer a web console.
 * For full control and integration it's possible to talk directly to the RESTful API.

To control the platform we offer different interfaces. The primary way of controlling your apps and deployments is via the command line client called *cctrl*. Additionally we also offer a [web console](https://console.cloudcontrolled.com). Both the CLI as well as the web console however are merely frontends to our RESTful API. For deep integration into your apps you can optionally use one of our available [API libraries](https://github.com/cloudControl).

Throughout this documentation we will use the CLI as the primary way of controlling the cloudControl platform. Installing cctrl is easy and works on Mac/Linux as well as on Windows. For detailed installation instructions please refer to the [cctrl installation guide](https://www.cloudcontrol.com/dev-center/guides/cctrl-installation-guide).

#### Quick Installtion Windows

For Windows we offer an installer. Please download the latest version of the installer from [Github](https://github.com/cloudControl/cctrl/downloads). The file is named cctrl-x.x-setup.exe.

#### Quick Installtion Linux/Mac

On Linux and Mac OS we recommned installing cctrl via pip. *cctrl* requires [Python 2.6+](http://python.org/download/).

~~~
$ pip install cctrl
~~~

If you don't have pip you can install pip via easy_install (on Linux usually part of the python-setuptools package) and then install cctrl.

~~~
$ easy_install pip
$ pip install cctrl
~~~

The command line client features a detailed online help. Just append --help or -h to any command.

## Apps, Users and Deployments

**TL;DR:**

 * Apps have a repository, deployments and users.
 * The repository is where your code lives organized in branches.
 * A deployment is a running version of a branch accessible via a URL. Important: Branch and deployment names need to match.
 * Users can be added to apps to gain access to the repository, its branches and deployments.

cloudControl PaaS uses a distinct set of naming conventions. To understand how to work with the platform most effectively, it's important to understand the following few basic concepts.

### Apps

Apps are a container for the repository and its branches, deployments and users. Creating an app allows you to add or remove users to an app giving them access to the source code as well as allowing them to manage the deployments.

Creating an app is easy. Simply specify a name and the desired type to determine which [buildpack](#buildpacks) to use.

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

By adding users to an app you can grant fellow developers access to the source code in the repository, allow them to [deploy new versions](#deploy-new-versions) and modify the deployments including their [Add-ons](#managing-add-ons). Permissions are based on the users [role](#roles).

You can list, add and remove app users using the command line client.

~~~
$ cctrlapp APP_NAME user
Users
 Name                                     Email               
 user1                                    user1@example.com
 user2                                    user2@example.com
 user3                                    user3@example.com
~~~

To add a user please use his email address. If the user already is registered with that address he will be added to the app. If not, he will first receive an email invitation and will be added as soon as he has registered and activated his account.

~~~
$ cctrlapp APP_NAME user.add user4@example.com
~~~

To remove a user, please use his username.

~~~
$ cctrlapp APP_NAME user.remove user3
~~~

#### Roles

 * **Owner**: Creating an app makes you the owner and gives you full access. The owner can not be removed from the app and gets charged for all his apps' consumption. If you plan to have multiple developers work on the same app, it's recommended to have a seperate admin-like account as the owner of all your apps and add the additional developers including your own seperately.
 * **Developer**: The default role for users added to an app is the developer role. Developers have full access to the repository as well as all the deployments. Developers can add more developers or even remove existing ones. They can even delete deployments and also the app itself. Developers however can not change the associated billing account or remove the owner.

#### Keys

For secure access to the apps repository each developer needs to authenticate via public/private key authentication. Please refer to the [SSH key guide](https://www.cloudcontrol.com/dev-center/guides/ssh-keys) for details how to create and manage your keys. You can simply add your default key to your user account using the command line client. If it can't find one, cctrl will try to help you create a key.

~~~
$ cctrluser key add
~~~

You can also list the available keys' ids and remove an existing key using that id.

~~~
$ cctrluser key
Keys
 Dohyoonuf7
$ cctrluser key.remove Dohyoonuf7
~~~

### Deployments

Deployments are a running version of your branch made accessible via a [provided subdomain](#provided-subdomains-and-custom-domains). The deployment name needs to match the branch name, with the exception of the master branch which is used by the default deployment. Deployments are started in secure unprivileged linux containers (LXC) completly seperated from each other including runtime environment, file system storage and also all Add-ons like e.g. databases and caches. This allows you to have different versions of your app running at the same time without interfering with each other. Please refer to the section about [development, staging and production environments](#development-staging-and-production-environments) for why this is a good idea.

You can list all app deployments with the details command.

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

 * Git and Bazaar are the supported version control systems.
 * When you push a branch an image of your code is built ready to be deployed.

### Supported Version Control Systems

For version control cloudControl supports Git ([quick Git tutorial](http://rogerdudler.github.com/git-guide/)) and Bazaar ([Bazaar in five minutes](http://doc.bazaar.canonical.com/latest/en/mini-tutorial/)). When you create an app we try to determine if the current working directory has a .git or .bzr directory. If so, we create the app with Git or Bazaar as version control respectively. If we can't determine this based on the current working directory we fall back to Git as the default. You can always overwrite this using the --repo command line switch.

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
If yours starts with `ssh://` and ends with `.git` it's using Git. If it starts with `bzr+ssh://` it's using Bazaar.

### Image Building

On each push to one of your branches a deployment image is built automatically. This image than can be deployed with the deploy command to the deployment matching the branch name. Remember for Git the default deployment uses the master branch. The deployment image includes your apps code as well as your dependencies pulled in by the [buildpack](#buildpacks).

You can use the cctrlapp push command or your version control systems own push command. Please remember that deployment and branch names have to match. So to push to your dev deployment the following commands are interchangeable. Also note, both require the existence of a branch called dev.

~~~
# the cctrlapp push command automatically detects Git or Bazaar
$ cctrlapp APP_NAME/dev push

# it's also possible to push using Git directly
$ git remote add cctrl REPO_URL #adding the remote is required only the first time
$ git push cctrl dev

# or push using Bazaar directly
# the --remember parameter remembers the REPO_URL as a default
$ bzr push --remember REPO_URL
~~~

Images are limited to 100mb (compressed) in size. Smaller images result in faster deploys both while deploying a new version as well as automatic recovy from a node failure. We recommend to keep images below 50mb. The image size is printed as part of the image build processes' output.

#### Buildpacks

During the push a hook is fired that runs the buildpack. A buildpack is a set of scripts that determine how a specific language or framework has to be prepared for and deployed on the cloudControl platform. Most of the buildpacks have originally been created for the Heroku platform, but to make it easier for the open source community to write custom buildpacks for specific frameworks we support the same [buildpack API](https://devcenter.heroku.com/articles/buildpack-api).

Part of the buildpack scripts is also to pull in dependencies according to the languages or frameworks native way. E.g. pip and a requirements.txt for Python, Maven for Java, npm for node, Composer for PHP and so on. This allows you to fully control the libraries and versions available to your app in the final runtime environment.

Which buildpack is going to be used is determined by the application type set when creating the app.

## Deploying New Versions

The cloudControl platform supports zero downtime deploys for all deployments. To deploy a new version use the command line's deploy command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy
~~~

To deploy a specific version append your version control systems identifier (a hash for Git or an integer for Bazaar). If not specified deploy defaults to the latest image available (the one built during the last push).

Every time a new version is deployed, the latest or the specified image is downloaded to as many of the platform's nodes as required by the --min setting (refer to the [scaling section](#scaling) for details) and started according to the buildpack's default or the [Procfile](#procfile). After the new containers are up and running the loadbalancing tier stops sending requests to the old containers and instead sends them to the new version. A log message in the [deploy log](#deploy-log) informs when this process has finished.

**Important:** All data that has been written during runtime of the old version into the old container's file system will be lost. This is very handy for code, templates, css, images, javascript files and the like, because it ensures they are always the latest version after each deploy, but prevents use of the filesystem for storage of user uploads.

## Emergency Rollback

If for some reason a new version does not work as expected you can rollback any deployment to a previous version in a matter of seconds. To do so you can check the [deploy log](#deploy-log) for the previously deployed version and then simply use the Git or Bazaar version identifier that's part of the log output to redeploy this version using the deploy command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy THE_LAST_WORKING_VERSION
~~~

## Non Persistent Filesystem

**TL;DR:**

 * Each container has its own filesystem.
 * The filesystem is not persistent.
 * Don't store uploads on the filesystem.

Deployments on the cloudControl platform have access to a writable filesystem. This filesystem however is not persistent. Data written may or may not be accessible again in future requests, depending on how the routing tier routes requests accross available containers, and is deleted after each deploy. This does include deploys you trigger to deploy a new version as well as deploys triggered by the platform's failover system to recover from node failures.

For customer uploads like e.g. user profile pictures and more we recommend object stores like Amazon S3 or the GridFS feature available as part of the [MongoLab Add-on](https://www.cloudcontrol.com/add-ons/mongolab).

## Development, Staging and Production Environments

**TL;DR:**

 * Leverage multiple deployments to support the complete application lifecycle.
 * Each deployment has a set of environment variables to help you configure your app.
 * Various configuration files are available to adjust runtime settings.

### Development, Staging and Production: The Application Lifecycle

Most apps share a common application lifecycle consisting of development, staging and production phases. The cloudControl platform is designed from the ground up to support this. As we explained earlier each app can have multiple deployments. Those deployments match the branches in the version control system. The reason for this is very simple. To work on new feature it is advisable to create a new branch. This new version can then be deployed as its own deployment making sure the new feature development is not interfering with the existing deployments. More important even these development/feature or staging deployments also ensure that the new code will work because each deployment using the same [stack](#stacks) is guaranteed to result in an identical runtime environment.

### Environment Variables

To enable you to determine programatically which deployment your app currently runs in, e.g. to enable debugging output in development deployments but disable it in production deployments, each deployment makes the following set of environment variables available to the apps.

 * **TMP_DIR**: The path to the tmp directory.
 * **CRED_FILE**: The path of the creds.json file containing the Add-on credentials.
 * **DEP_VERSION**: The Git or Bazaar version.
 * **DEP_NAME**: The deployment name in the same format as used by the command line client. E.g. myapp/default. This one stays the same even when undeploying and creating a new deployment with the same name.
 * **DEP_ID**: The internal deployment ID. This one stays the same for the deployments lifetime but changes when undeploying and creating a new deployment with the same name.

### Configuration Files

#### .ccconfig.yaml

#### Procfile

## Logging

**TL;DR:**

 * There are four different log types (access, error, worker and deploy) available.

To see the log output in a *tail -f* like fashion use the log command. The log command initially shows the last 500 log messages and then appends new messages as they arrive.

~~~
$ cctrlapp APP_NAME/DEP_NAME log [access,error,worker,deploy]
[...]
~~~

### Access Log

The access log shows each access to your app in an Apache compatible log format.

### Error Log

The error log shows errors from all components of your app. It also includes markers for when a new version has been deployed to make it easy to determine if a problem existed already before or only after the last deploy. More detailed information on deploys can be found in the [deploy log](#deploy-log).

### Worker Log

Workers are long running background processes. As such, they are not accessible via http from the outside. To make worker output accessible to you, stdout and stderr is redirected to this log. The worker log shows the timestamp of when the message was written, the *wrk_id* of the worker the message came from as well as the actual log line.

### Deploy Log

The deploy log gives detailed information on the deploy process. With it you can see on which and how many nodes your deployment is deployed. How long it took each node to get the deployment image and start the container and also when the loadbalancers started sending traffic to the [new version](#deploying-new-versions).

## Add-ons

**TL;DR:**

 * Add-ons give you access to additional services like databases and more.
 * Each deployment needs its own set of Add-ons.
 * Add-on credentials are automatically available to your app via the *creds.json* file.

### Managing Add-ons

Add-ons add additional services to your deployment. The [Add-on marketplace](https://www.cloudcontrol.com/add-ons) offers a wide variety of different Add-ons. Think of it as an app store dedicated to developers. Add-ons can be different databases technologies, caching, performance monitoring or logging services or even complete backend APIs or billing solutions.

Each deployment needs its own set of Add-ons. So if your app needs a MySQL database and you have a production, a development and a staging environment all three need their own MySQL Add-ons. Each Add-on comes in different plans allowing you to chose a more powerful database for your high traffic production deployment and a smaller one for the development or staging environments.

You can see the available Add-on plans on the [Add-on marketplace website](https://www.cloudcontrol.com/add-ons) or with the addon.list command.

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

To upgrade or downgrade ann Add-on use the respective command followed by the Add-on name you upgrade from to the Add-on name you upgrade to.

~~~
# upgrade
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade FROM_SMALL_ADDON TO_BIG_ADDON
# downgrade
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade FROM_BIG_ADDON TO_SMALL_ADDON
~~~
**Remember:** As in all examples during this documentation replace all uppercase placeholders with their respective values.

### Add-on Credentials

Of course adding an Add-on is only the first step. You also need to implement the functionality in your application code. To make this super easy also accross the different deployments it's highly recommended to always read the credentials from the *creds.json* file. This ensures, that your app is always talking to the right database and you can freely merge your branches without having to worry about keeping the credentials in sync.

The path to the *creds.json* is always available through the CRED_FILE environment variable. Here's a quick example in PHP how to read the file and parse the JSON.

~~~php
<?php

# read the credentials file
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

# the file contains a JSON string, decode it and return an associative array
$creds = json_decode($string, true);

# now use the $creds array to configure your app e.g.:
$MYSQL_HOSTNAME = $creds['MYSQLS']['MYSQLS_HOSTNAME'];

?>
~~~

The [guides section](https://www.cloudcontrol.com/dev-center/guides/) has detailed examples how to configure various frameworks using the *creds.json* file. To see the format and contents of the *creds.json* file locally use the addon.creds command.

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

## Provided Subdomains and Custom Domains

**TL;DR:**

 * Each deployment is provided a `.cloudcontrolled.com` subdomain.
 * Custom domains are supported via the Alias Add-on.

Each deployment gets a `.cloudcontrolled.com` subdomain. The default deployment always answers at `APP_NAME.cloudcontrolled.com` while any additional deployments get a `DEP_NAME.APP_NAME.cloudcontrolled.com` subdomain.

You can use custom domains to access your deployments. To add a domain like `www.example.com`, `app.example.com` or `secure.example.com` to one of your deployments simply add each one as an alias and add a CNAME for each pointing to your deployments subdomain. So to point `www.example.com` to the default deployment of the app called *awesomeapp* add a CNAME for `www.example.com` pointing to `awesomeapp.cloudcontrolled.com`. The [Alias Add-on](https://www.cloudcontrol.com/add-ons/alias) also support mapping wildcard domains like `*.example.com` to one of your deployments.

All custom domains need to be verified before they start working. To verify a domain it is required to also add the cloudControl verfification code as a TXT record.

Changes to DNS can take up to 24 hours until they have effect. Please refer to the [Alias Add-on Documentation](https://www.cloudcontrol.com/dev-center/add-on-documentation/alias) for detailed instructions on how to setup CNAME and TXT records.

## Scaling

**TL;DR:**

 * You can scale up or down anytime by adding more containers (horizontal scaling) or changing the container size (vertical scaling).
 * Use performance monitoring and load testing to determine the optimal scaling settings for your app.

When scaling your apps you have two options. You can either scale horizontally by adding more containers, or scale vertically by changing the container size. When you scale horizontally the cloudControl loadbalancing and routing tier ensures efficient distribution of incoming requests accross all available containers.

### Horizontal Scaling

Horizontal scaling is controlled by the --min parameter. It specifies the number of containers you have running. Raising --min also increases the availabiltiy in case of node failures. Deployments with --min 1 (the default) are unavailable for a few minutes after a node failure until the failover process has finished. Set --min >=2 if you want to avoid downtime like this.

### Vertical Scaling

In addition to controlling the number of containers you can also specify the size of a container. Container sizes are specificed using the --max parameter. Valid values are 1 <= x <= 8 and result in x times 128mb. So setting --max to 1 will result in 128mb of RAM available to each one of your containers, while --max 4 or 8 will give you 512mb or 1024mb RAM respectively. To determine the optimal --max value for your deployment you can use the New Relic Add-on to analyze the memory consumption of your app.

### Choosing Optimal Settings

You can use the Blitz.io and New Relic Add-ons to run synthetic load tests against your deployments and analyze how well they perform with the current --min and --max settings under load to determine the optimal scaling settings and adjust accordingly. We have a [tutorial](https://www.cloudcontrol.com/blog/best-practice-running-and-analyzing-load-tests-on-your-cloudcontrol-app) that explains this in more detail.

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

The loadbalancing and routing tier that's in front of all deployments includes a [Varnish](https://www.varnish-cache.org/) caching proxy. To have your requests cached directly in Varnish and speed up the response time through this, ensure you have set correct cache control headers for the request. Also ensure, that the request does not include a cookie. Cookies are often used to keep state accross requests (e.g. if a user is logged in). To avoid caching responses for logged in users and returning them to other users Varnish is configured to never cache requests with cookies. To be able to cache requests in Varnish for apps that rely on cookies we recommend using a cookieless domain.

You can check if a request was cached in Varnish by checking the response's *X-varnish-cache* header. The value HIT means the respons was answered directly from cache, and MISS means it was not.

#### In-Memory Caching

To make requests that can't use a cookieless domain faster you can use in memory caching to store arbitrary data from database query results to complete http responses. Since the cloudControl routing tier distributes requests accross all available containers it is recommended to cache data in a way that makes it available also for requests that are routed to different containers. A battle tested solution for this is Memcached which is available via the [Memcachier Add-on](https://www.cloudcontrol.com/add-ons/memcachier). Refer to the [managing Add-ons](#managing-add-ons) section on how to add it. Also the [Memcachier Documentation](https://www.cloudcontrol.com/dev-center/add-on-documentation/memcachier) has detailed instructions on how to use it within your language and framework of choice.

### Cache Breakers

When caching requests client side or in a caching proxy, the URL is usually used as the cache identifier. As long as the URL stays the same and and the cached response has not expired, the request is answered from cache. As part of every deploy all containers are started from a clean image. This ensures that all containers have the latest app code including templates, css, image and javascript files. But when using far future expire headers as recommended above this doesn't change anything if the response was cached at client or loadbalancer level. To ensure clients get the latest and greatest version it is recommend to include a changing parameter into the URL. This is commonly referred to as a cache breaker.

As part of the set of [environment variables](#environment-variables) in the deployment runtime environment the DEP_VERSION is made available to the app. If you want to force a refresh of the cache when a new version is deployed you can use the DEP_VERSION to accomplish this.

This technique works for URLs as well as keys in in-memory caches like Memcached. Imagine you have cached values in Memcached that you want to keep between deploys and have values in Memcached that you want refreshed for each new version. Since Memcached only allows flushing the complete cache you would lose all cached values. Including the DEP_VERSION as part of the key of the cached values you want refreshed is an easy way to ensure the cache gets refreshed.

## Scheduled Jobs and Background Workers

**TL;DR:**

 * Scheduled jobs are supported through different Add-ons.
 * Web requests do have a timelimit of 120s.
 * Background workers are the recommended way of handling long running or asynchronous tasks.

## Stacks

**TL;DR:**

 * Stacks define the common runtime environment.
 * They are based on Ubuntu and stack names match the Ubuntu release's first letter.

A stack defines the common runtime environment for all deployments. By choosing the same stack for all your deployments, it's guaranteed that all your deployments find the same version of all OS components as well as all preinstalled libraries.

Stacks are based on Ubuntu releases and have the same first letter as the release they are based on. Each stack is named after a super hero sidekick. We try to keep them as close to the Ubuntu release as possible, but do make changes when necessary for security or performance reasons to optimize the stack for its specific purpose on our platform.

### Available Stacks

 * **Luigi** based on [Ubuntu 10.04 LTS Lucid Lynx](http://releases.ubuntu.com/lucid/)
 * **Pinky** based on [Ubuntu 12.04 LTS Precise Pangolin](http://releases.ubuntu.com/precise/)

You can change the stack per deployment. This is handy for testing new stacks before migrating the production deployment. To see what stack a deployment is using refer to the deployment details.

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
