# cloudControl Documentation

## Platform Access

**TL;DR:**

 * The command line client cctrl is the primary interface.
 * We also offer a web console.
 * For full control and integration it's possible to talk directly to the RESTful API.

To control the platform we offer different interfaces. The primary way of controlling your apps and deployments is via [the command-line interface](http://en.wikipedia.org/wiki/Command-line_interface) (CLI) called *cctrl*. Additionally we also offer a [web console]. Both the CLI as well as the web console however are merely frontends to our RESTful API. For deep integration into your apps you can optionally use one of our available [API libraries].

Throughout this documentation we will use the CLI as the primary way of controlling the cloudControl platform. The CLI consists of 2 parts: *cctrlapp* and *cctrluser*. To get help for the command line client, just append --help or -h to any of the commands.

Installing *cctrl* is easy and works on Mac/Linux as well as on Windows.

#### Quick Installation Windows

For Windows we offer an installer. Please download [the latest version] of the installer from S3. The file is named cctrl-x.x-setup.exe.

#### Quick Installation Linux/Mac

On Linux and Mac OS we recommend installing and updating cctrl via pip. *cctrl* requires [Python 2.6+].

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
 * User accounts can be created via the *web console* or via ``cctrluser create``
 * User accounts can be deleted via the *web console* or via ``cctrluser delete``
 * The CLI can be configured via ``cctrluser setup``

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

### Setup CLI

With the `cctrluser setup` command you can create or modify your CLI configuration whenever you need to. We do not ask for configurations changes anymore, instead you will have explicit control over this by using this command.

Usually you only need to run `cctrluser setup` to get this job done. In the first run you will be asked for your email. For all other setup options, e.g. ssh-key-path, the default values are taken.

The command has three different options to modify each of the existing values on the user configuration:

- `--email` will set the email on your configuration. This email is used to login on the platform.

- `--ssh-auth` will enable the ssh public key authentication if set to `yes` and disable it when `no` (it defaults to `yes`). See more for [ssh public key authentication](#ssh-public-key-authentication).

- `--ssh-key-path` specifies the path of your ssh public key used for the authentication. If the flag is not set, it defaults to `HOME_DIR/.ssh/id_rsa.pub`. The CLI will try to upload the public key to the platform. We only support RSA keys. Details can be found under [Keys](#keys).

The whole command as example:

~~~
cctrluser setup --email user1@example.com --ssh-auth yes --ssh-key-path /path/to/your/publickey.pub
~~~

### Password Reset

You can [reset your password], in case you forgot it.

## CLI Authentication

At the moment our CLI allows users to authenticate to the platform with two methods.

### SSH Public Key Authentication

With this method you can authenticate to the platform in a more convenient way than using [email / password authentication](#email--password-authentication). After adding your SSH key to the platform, its location is remembered by the CLI and will be used in the future requests. You can add a public key to the platform and/or change the public key used for the authentication by [setup the CLI](#setup-cli).

~~~bash
cctrluser setup --ssh-key-path /path/to/your/publickey.pub
~~~

If you set a passphrase for your SSH key, which is strongly recommended, than you have to add the key to your ssh-agent by:

~~~bash
# start ssh-agent
eval `ssh-agent`
ssh-add /path/to/your/privatekey
~~~

### Email / Password Authentication

The email / password authentication is an alternative to SSH public key authentication. To enable it, simply [setup the CLI](#setup-cli), setting the `--ssh-auth` parameter to `no`.

~~~
cctrluser setup --ssh-auth no
~~~

From now, whenever you want to authenticate to the platform you have to put your password.

Alternatively, to get this process less verbose, you can set the password as shell environment variable:

~~~bash
export CCTRL_PASSWORD=yourpassword
~~~

Disadvantage of this approach is the fact that your password is exposed to your environment, that is why we encourage using SSH public key authentication instead.

## Apps, Users and Deployments

**TL;DR:**

 * Applications (apps) have a repository, deployments and users.
 * The repository is where your code lives, organized in branches.
 * A deployment is a running version of your application, based on the branch with the same name. Exception: the default deployment is based on the master (Git) / trunk (Bazaar).
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

By adding users to an app you can grant fellow developers access to the source code in the repository, allow them to [deploy new versions](#deploying-new-versions) and modify the deployments including their [Add-ons](#managing-add-ons). Permissions are based on the user's [roles](#roles). Users can be added to applications or more fine grained to deployments.

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

#### Roles

 * **Owner**: Creating an app makes you the owner and gives you full access. The owner can not be removed from the app and gets charged for all their apps' consumption.
 * **Admin**: The default role for users added to an app is the Admin role. Admins have full access to the repository and to all deployments. Admins can add more Admin or Read-only users or remove existing ones. They can delete deployments and even the app itself. Admins however can not change the associated billing account or remove the owner.
 * **Read-only** The Read-only role allows you to see the application details, deployments and logs. Any update operation is forbidden.

You can provide the role with the `user.add` command.

~~~
$ cctrlapp APP_NAME user.add user5@example.com --role readonly
~~~

#### Keys

For secure access to the app's repository, each developer needs to authenticate via public/ private key authentication. Please refer to GitHub's article on [generating SSH keys] for details on how to create a key. You can simply add your default key to your user account using the *web console* or the command line client. If no default key can be found, cctrlapp will offer to create one.

We only support RSA ssh keys. The key must be a one-liner and start with "ssh-rsa AAAAB3NzaC1yc2E" (OpenSSH compatible).

~~~
$ cctrluser key.add
~~~

You can also list the available key ids and remove existing keys using the key id.

~~~
$ cctrluser key
Keys
 Dohyoonuf7

$ cctrluser key Dohyoonuf7
ssh-rsa AAA[...]

$ cctrluser key.remove Dohyoonuf7
~~~

### Deployments

A deployment is the running version of one of your branches made accessible via a [provided subdomain](#provided-subdomains-and-custom-domains). It is based on the branch of the same name. Exception: the default deployment is based on the master (Git) / trunk (Bazaar).

Deployments run independently from each other, including separate runtime environments, file system storage and Add-ons (e.g. databases and caches). This allows you to have different versions of your app running at the same time without interfering with each other. Please refer to the section about [development, staging and production environments](#development-staging-and-production-environments) to understand why this is a good idea.

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

The platform supports Git ([quick Git tutorial]) and Bazaar ([Bazaar in five minutes]). When you create an app we try to determine if the current working directory has a .git or .bzr directory. If it does, we create the app with the detected version control system. If we can't determine this based on the current working directory, Git is used as the default. You can always overwrite this with the --repo command line switch.

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

Whenever you push an updated branch, a deployment image is built automatically. This image can then be deployed with the *deploy* command to the deployment matching the branch name. The content of the image is generated by the [buildpack](#buildpacks-and-the-procfile) including your application code in a runnable form with all the dependencies.

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

The compressed image size is limited to 200MB. Smaller images can be deployed faster, so we recommend to keep the image size below 50MB. The image size is printed at the end of the build process; if the image exceeds the limit, the push gets rejected.

You can decrease your image size by making sure that no unneeded files (e.g. caches, logs, backup files) are tracked in your repository. Files that need to be tracked but are not required in the image (e.g. development assets or source code files in compiled languages), can be added to a `.cctrlignore` file in the project root directory. The format is similar to the `.gitignore`, but without the negation operator `!`. Here’s an example `.cctrlignore`:

~~~
*.psd
*.pdf
test
spec
~~~

#### Buildpacks and the Procfile

During the push a hook is fired that runs the buildpack. A buildpack is a set of scripts that determine how an app in a specific language or framework has to be prepared for deployment on the cloudControl platform. With custom buildpacks, support for new programming languages can be added or custom runtime environments can be build. To support many PaaS with one buildpack, we recommend following the [Heroku buildpack API] which is compatible with cloudControl and other platforms.

Part of the buildpack scripts is also to pull in dependencies according to the languages or frameworks native way. E.g. pip and a requirements.txt for Python, Maven for Java, npm for Node.js, Composer for PHP and so on. This allows you to fully control the libraries and versions available to your app in the final runtime environment.

Which buildpack is going to be used is determined by the application type set when creating the app.

A required part of the image is a file called `Procfile` in the root directory. It is used to determine how to start the actual application in the container. Some of the buildpacks can provide a default Procfile. But it is recommended to explicitly define the Procfile in your application to match your individual requirements better. For a container to be able to receive requests from the routing tier it needs at least the following content:

~~~
web: COMMAND_TO_START_THE_APP_AND_LISTEN_ON_A_PORT --port $PORT
~~~

For more specific examples of a `Procfile` please refer to the language and framework [guides].

At the end of the buildpack process, the image is ready to be deployed.


## Deploying New Versions

The cloudControl platform supports zero downtime deploys for all deployments. To deploy a new version use either the *web console* or the `deploy` command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy
~~~

To deploy a specific version, append your version control systems identifier (full commit-SHA1 for Git or an integer for Bazaar).
If not specified, the version to be deployed defaults to the latest image available (the one built during the last successful push).

For every deploy, the image is downloaded to as many of the platform’s nodes as required by the [--containers setting](#scaling) and started according to the buildpack’s default or the [Procfile](#buildpacks-and-the-procfile). After the new containers are up and running the load balancing tier stops sending requests to the old containers and instead sends them to the new version. A log message in the [deploy log](#deploy-log) appears when this process has finished.

### Container Idling

Deployments running on a single web container with one unit of memory (128MB/h) are automatically idled when they are not receiving HTTP requests for 1 hour or more. This results in a temporary suspension of the container where the application is running. It does not affect the Add-ons or workers related to this deployment.

Once a new HTTP request is sent to this deployment, the application is automatically re-engaged. This process causes a slight delay until the first request is served. All following requests will perform normally.

You can see the state of your application with the following command:

~~~
$ cctrlapp APP_NAME/DEP_NAME details
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
$ cctrlapp APP_NAME/DEP_NAME rollback
~~~

It is also possible to deploy any other prior version. To find the version identifier you need, simply check the [deploy log](#deploy-log) for a previously deployed version, or get it directly from the version control system. You can redeploy this version using the deploy command:

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy THE_LAST_WORKING_VERSION_HASH
~~~


## Non-Persistent Filesystem

**TL;DR:**

 * Each container has its own filesystem.
 * The filesystem is not persistent.
 * Don't store uploads on the filesystem.

Deployments on the cloudControl platform have access to a writable filesystem. This filesystem however is not persistent. Data written may or may not be accessible again in future requests, depending on how the [routing tier](#routing-tier) routes requests across available containers, and is deleted after each deploy. This does include deploys you trigger manually, but also re-deploys done by the platform itself during normal operation.

For customer uploads (e.g. user profile pictures) we recommend object stores like Amazon S3 or the GridFS feature available as part of the [MongoLab Add-on].


## Development, Staging and Production Environments

**TL;DR:**

 * Leverage multiple deployments to support the complete application lifecycle.
 * Each deployment has a set of environment variables to help you configure your app.
 * Various configuration files are available to adjust runtime settings.

### Development, Staging and Production: The Application Lifecycle

Most apps share a common application lifecycle consisting of development, staging and production phases. The cloudControl platform is designed from the ground up to support this. As we explained earlier, each app can have multiple deployments. Those deployments match the branches in the version control system. The reason for this is very simple. To work on a new feature it is advisable to create a new branch. This new version can then be deployed as its own deployment making sure the new feature development is not interfering with the existing deployments. More importantly even, these development/feature or staging deployments also help ensure that the new code will work in production because each deployment using the same [stack](#stacks) has the same runtime environment.

### Environment Variables

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

To upgrade or downgrade an Add-on use the respective command followed by the Add-on plan you upgrade from and the Add-on plan you upgrade to.

~~~
# upgrade
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade FROM_SMALL_ADDON TO_BIG_ADDON
# downgrade
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade FROM_BIG_ADDON TO_SMALL_ADDON
~~~

**Remember:** As in all examples in this documentation, replace all the uppercase placeholders with their respective values.

### Add-on Credentials
For many Add-ons you require credentials to connect to their service. The credentials are exported to the deployment in a JSON formatted config file. The path to the file can be found in the `CRED_FILE` environment variable. Never hardcode these credentials in your application, because they differ over deployments and can change after any redeploy without notice.

We provide detailed code examples how to use the config file in our guides section.

#### Enabling/disabling credentials environment variables
We recommend using the credentials file for security reasons but credentials can also be accessed through environment variables. This is disabled by default for PHP and Python apps. Accessing the environment is more convenient in most languages, but some reporting tools or wrong security settings in your app might print environment variables to external services or even your users. This also applies to any child processes of your app if they inherit the environment (which is the default). When in doubt, disable this feature and use the credentials file.

Set the variable `SET_ENV_VARS` using the [Custom Config Add-on] to either `false` or `true` to explicitly enable or disable
this feature.

The guides section has detailed examples about how to get the credentials in different languages ([Ruby](https://www.cloudcontrol.com/dev-center/guides/ruby/add-on-credentials-2), [Python](https://www.cloudcontrol.com/dev-center/guides/python/add-on-credentials), [Node.js](https://www.cloudcontrol.com/dev-center/guides/nodejs/add-on-credentials-1), [Java](https://www.cloudcontrol.com/dev-center/guides/java/add-on-credentials-3), [PHP](https://www.cloudcontrol.com/dev-center/guides/php/add-on-credentials-4)).
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

The `access` log shows each request to your app in an Apache compatible log format.

### Error Log

The `error` log shows all output your app prints to stdout, stderr and syslog. This log is probably the best place to look at when your app is not doing well. We also show new deployments here to give you more context but you can always refer to the [deploy log](#deploy-log) for detailed information on deploys.

### Worker Log

Workers are long running background processes. They are not accessible via http from outside. To make the workers output visible to you, its stdout, stderr and syslog output is captured in this log. The worker log contains the timestamp of the event, the *wrk_id* of the worker as well as the actual log line.

### Deploy Log

The `deploy` log provides detailed information about the deploy process. It shows on how many nodes your deployment is running with additional information about the nodes, startup times and when the loadbalancers begins sending traffic to the [new version](#deploying-new-versions).

### Limits

We use [message
reduction](http://www.rsyslog.com/doc/rsconf1_repeatedmsgreduction.html), i.e.
duplicate lines get reduced to "Last line repeated n times". The number of
messages is limited to 200 within the last 5 seconds.

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
$ cctrlapp APP_NAME/DEP_NAME config.add RSYSLOG_REMOTE=custom_remote.cfg
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

### Root Domains

Root domains (e.g. "example.com") can also be added but are not directly supported. While you theoretically can add a CNAME record for your root domain, you have to be aware that no other record for this domain can be set then. ("A CNAME record is not allowed to coexist with any other data", http://tools.ietf.org/html/rfc1912). From the point you set a CNAME, all standard-compliant DNS servers will ignore any other entry you might have set for your zone (e.g. SOA, NS or MX records).

You can circumvent this limitation by using a DNS provider which provides CNAME-like functionality for root domains, often called ANAME or ALIAS.

An alternative is to use a redirection service to send users from the root to the configured subdomain (e.g. example.org -> www.example.org).

## Routing Tier

**TL;DR:**

 * All HTTP requests are routed via our routing tier.
 * Within the routing tier, you can choose to route requests via the `*.cloudcontrolled.com` or `*.cloudcontrolapp.com` subdomains.
 * The `*.cloudcontrolled.com` subdomain provides support for HTTP caching via Varnish.
 * The `*.cloudcontrolapp.com` subdomain provides WebSocket support.
 * Requests are routed based on the `Host` header.
 * Use the `X-Forwarded-For` header to get the client IP.

All HTTP requests made to apps on the platform are routed via our routing tier. The routing tier is designed as a cluster of reverse proxy loadbalancers which orchestrate the forwarding of user requests to your applications. It takes care of routing the request to one of the application's containers based on matching the `Host` header against the list of the deployment's aliases. This is accomplished via the `*.cloudcontrolled.com` or `*.cloudcontrolapp.com` subdomains.

The routing tier is designed to be robust against single node and even complete datacenter failures while still keeping the added latency as low as possible.

### SSL

Transport Layer Security (TLS / SSL) is available to encrypt traffic between users and applications.

As part of the provided `.cloudcontrolled.com` subdomain, all deployments have access to piggyback SSL using a `*.cloudcontrolled.com` wildcard certificate. To use this, simply point your browser to:
* `https://APP_NAME.cloudcontrolled.com` for the default deployment
* `https://DEP_NAME-APP_NAME.cloudcontrolled.com` for non-default deployments

    Please note the **dash** between DEP_NAME and APP_NAME.

SSL support for custom domains is available through the
[SSL add-on](https://www.cloudcontrol.com/add-ons/ssl).

Instructions on how to add HTTPS redirects to your application can be
found in the [SSL add-on documentation](https://www.cloudcontrol.com/dev-center/add-on-documentation/ssl#https-redirects).

### Elastic Addresses

Because of the elastic nature of the routing tier, the list of routing tier addresses can change at any time. It is therefore highly discouraged to point custom domains directly to any of the routing tier IP addresses. Please use a CNAME instead. Refer to the [custom domain section](#provided-subdomains-and-custom-domains) for more details on the correct DNS configuration.

### Remote Address

Given that client requests don't hit your application directly, but are forwarded via the routing tier, you can't access the client's IP by reading the remote address. The remote address will always be the internal IP of one of the routing nodes. To make the origin remote address available, the routing tier sets the `X-Forwarded-For` header to the original client's IP.

### Reverse Proxy timeouts

Our routing tier uses a cluster of reverse proxy loadbalancers to manage the acceptance and forwarding of user requests to your applications. To do this in an efficient way, we set strict timeouts to the read/ write operations. The values differ slightly between the `*.cloudcontrolled.com` and `*.cloudcontrolapp.com` subdomains. You can find them below.

 * __Connect timeout__ - time within a connection to your application has to be established. If your containers are up, but hanging, then this timeout will not apply as the connection to the endpoints has already been made.
 * __Send timeout__ - maximum time between two write operations of a request. If your application does not take new data within this time, the routing tier will shut down the connection.
 * __Read timeout__ - time to retrieve a response from your application. It determines how long the routing tier will wait to get the response to a request. The timeout is established not for an entire response, but only between two operations of reading.

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

Horizontal scaling is controlled by the --containers parameter. It specifies the number of containers you have running. Raising --containers also increases the availability in case of node failures. Deployments with --containers 1 (the default) are unavailable for a few minutes in the event of a node failure until the failover process has finished. Set --containers value to at least 2 if you want to avoid downtime in such situations.

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

This technique works for URLs as well as for the keys in in-memory caches like `Memcached`. Imagine you have cached values in Memcached that you want to keep between deploys and have values in Memcached that you want refreshed for each new version. Since Memcached only allows flushing the complete cache, you would lose all cached values. Including the DEP_VERSION in the key is an easy way to ensure that the cache is clear for a new version without flushing.

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
 * Scheduled jobs are supported through the Cron Add-on or workers.
 * Background workers are the recommended way of handling long running or asynchronous tasks.

Since a web request taking longer than 120s is killed by the routing tier, longer running tasks have to be handled asyncronously.

### Cron

For tasks that are guaranteed to finish within the time limit, the [Cron add-on] is a simple solution to call a predefined URL daily or hourly and have that task called periodically. For a more detailed documentation on the Cron Add-on, please refer to the [Cron Add-on documentation].

### Workers

Tasks that will take longer than 120s to execute, or that are triggered by a user request and should be handled asyncronously to not keep the user waiting, are best handled by workers. Workers are long-running processes started in containers. Just like the web processes but they are not listening on any port and therefore do not receive http requests. You can use workers, for example, to poll a queue and execute tasks in the background or handle long-running periodical calculations.

Each worker runs in a seperate isolated container. The containers have exactly
the same runtime environment defined by the stack chosen and the buildpack used
and have the same access to all of the deployments add-ons.

Note: Workers sometimes get interrupted and restarted on a different host for
the following reasons:
 - single instances can run into issues and need to be replaced
 - containers are redistributed to provide the best performance
 - security updates are applied

This means all your worker operations should be idempotent. If possible a `SIGTERM`
signal is send to your worker before the shutdown.

#### Starting a Worker

Workers can be started via the command line client's worker.add command.

For the Luigi stack (only supporting PHP), use the PHP filename as the `WORKER_NAME`. But for apps on the Pinky stack, first specifiy how to start the worker by adding a new line to your app's `Procfile` and then use that as the `WORKER_NAME`.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker.add WORKER_NAME [WORKER_PARAMS]
~~~

Enclose multiple WORKER_PARAMS in double quotes.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker.add WORKER_NAME "PARAM1 PARAM2 PARAM3"
~~~

#### List Running Workers

To get a list of currently running workers use the worker command.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker
Workers
 nr. wrk_id
   1 WRK_ID
~~~

You can also get all the worker details by appending the WRK_ID to the worker command.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker WRK_ID
Worker
wrk_id   : WRK_ID
command  : WORKER_NAME
params   : "PARAM1 PARAM2 PARAM3"
~~~

#### Stopping Workers

Workers can be either stopped via the command line client or by exiting the process with a zero exit code.

##### Via Command Line

To stop a running worker via the command line use the worker.remove command.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker.remove WRK_ID
~~~

To get the WRK_ID refer to the listing workers section above.

##### Via Exit Codes

To stop a worker programatically use UNIX style exit codes. There are three distinct exit codes available.

 * exit (0); // Everything OK. Worker will be stopped.
 * exit (1); // Error. Worker will be restarted.
 * exit (2); // Error. Worker will be stopped.

For more details refer to the [PHP example](#php-worker-example) below.

#### Worker log

As already explained in the [Logging section](https://www.cloudcontrol.com/dev-center/platform-documentation#logging) all stdout and stderr output of workers is redirected to the worker log. To see the output in a tail -f like fashion use the log command.

~~~
$ cctrlapp APP_NAME/DEP_NAME log worker
[Fri Dec 17 13:39:41 2010] WRK_ID Started Worker (command: 'WORKER_NAME', parameter: 'PARAM1 PARAM2 PARAM3')
[Fri Dec 17 13:39:42 2010] WRK_ID Hello PARAM1 PARAM2 PARAM3
[...]
~~~

#### PHP Worker Example

The following example shows how to use the exit codes to restart or stop a worker.

~~~php
// read exit code parameter
$exitCode = isset($argv[1]) && (int)$argv[1] > 0 ? (int)$argv[1] : 0;
$steps = 5;

$counter = 1;
while(true) {
    print "step: " . ($counter) . PHP_EOL;
    if($counter == $steps){
        if($exitCode == 0) {
            print "All O.K. Exiting." . PHP_EOL;
        } else if ($exitCode == 2){
            print "An error occured. Exiting." . PHP_EOL;
        } else {
            print "An error occured. Restarting." .  PHP_EOL;
        }
        print "Exitcode: " . $exitCode . PHP_EOL . PHP_EOL;
        exit($exitCode);
    }
    sleep(1);
    $counter++;
}
~~~

Running this worker with the exit code set to 2 would result in the following output and the worker stopping itself.

~~~
$ cctrlapp APP_NAME/DEP_NAME worker.add WORKER_NAME 2
$ cctrlapp APP_NAME/DEP_NAME log worker
[Tue Apr 12 09:15:54 2011] WRK_ID Started Worker (command: 'WORKER_NAME', parameter: '2')
[Tue Apr 12 09:15:54 2011] WRK_ID step: 1
[Tue Apr 12 09:15:55 2011] WRK_ID step: 2
[Tue Apr 12 09:15:56 2011] WRK_ID step: 3
[Tue Apr 12 09:15:57 2011] WRK_ID step: 4
[Tue Apr 12 09:15:58 2011] WRK_ID step: 5
[Tue Apr 12 09:15:58 2011] WRK_ID An error occured. Exiting.
[...]
~~~

## Secure Shell (SSH)

The distributed nature of the cloudControl platform means it's not possible to SSH into the actual server. Instead, we offer the run command, that allows you to launch a new container and connect to that via SSH.

The container is identical to the web or worker containers but starts an SSH daemon instead of one of the Procfile commands. It's based on the same stack image and deployment image and does also provides the Add-on credentials.

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

It's also possible to execute a command directly and have the container shutdown after the command is finished. This is very useful for database migrations and other one-time tasks.

For example, passing the `"env | sort"` command will list the environment variables. Note that the use of the quotes is required for a command that includes spaces.

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

 * **Luigi** based on [Ubuntu 10.04 LTS Lucid Lynx]
 * **Pinky** based on [Ubuntu 12.04 LTS Precise Pangolin]

You can change the stack per deployment. This is handy for testing new stacks before migrating the production deployment. Details are available via the `cctrlapp` command line interface.

~~~
$ cctrlapp APP_NAME/DEP_NAME details
 name: APP_NAME/DEP_NAME
 stack: luigi
 [...]
~~~

To change the stack of a deployment simply append the --stack command line option to the `deploy` command.

~~~
$ cctrlapp APP_NAME/DEP_NAME deploy --stack [luigi,pinky]
~~~

[generating SSH keys]: https://help.github.com/articles/generating-ssh-keys
[Custom Config Add-on]: https://www.cloudcontrol.com/dev-center/add-on-documentation/custom-config
[web console]: https://www.cloudcontrol.com/console
[API libraries]: https://github.com/cloudControl
[the latest version]: https://www.cloudcontrol.com/download/win
[Python 2.6+]: http://python.org/download/
[reset your password]: https://api.cloudcontrol.com/reset_password/
[quick Git tutorial]: http://rogerdudler.github.com/git-guide/
[Bazaar in five minutes]: http://doc.bazaar.canonical.com/latest/en/mini-tutorial/
[Heroku buildpack API]: https://devcenter.heroku.com/articles/buildpack-api
[guides]: https://www.cloudcontrol.com/dev-center/guides
[MongoLab Add-on]: https://www.cloudcontrol.com/add-ons/mongolab
[Add-on marketplace]: https://www.cloudcontrol.com/add-ons
[Deployment category]: https://www.cloudcontrol.com/dev-center/add-on-documentation#deployment
[rsyslog]: http://www.rsyslog.com/
[TLS]: http://en.wikipedia.org/wiki/Transport_Layer_Security
[Alias Add-on]: https://www.cloudcontrol.com/add-ons/alias
[Blitz.io]: https://www.cloudcontrol.com/dev-center/add-on-documentation/blitz-dot-io
[MemCachier Add-on]: https://www.cloudcontrol.com/add-ons/memcachier
[Varnish]: https://www.varnish-cache.org/
[MemCachier Documentation]: https://www.cloudcontrol.com/dev-center/add-on-documentation/memcachier
[New Relic Add-ons]: https://www.cloudcontrol.com/dev-center/add-on-documentation/new-relic
[tutorial]: https://www.cloudcontrol.com/blog/best-practice-running-and-analyzing-load-tests-on-your-cloudcontrol-app
[Cron Add-on]: https://www.cloudcontrol.com/add-ons/cron
[Cron Add-on documentation]: https://www.cloudcontrol.com/dev-center/add-on-documentation/cron
[Ubuntu 10.04 LTS Lucid Lynx]: http://releases.ubuntu.com/lucid/
[Ubuntu 12.04 LTS Precise Pangolin]: http://releases.ubuntu.com/precise/
