# cloudControl Documentation

## [Command line client, web console and API](#interfaces)

**TL;DR:**

 * The command line client cctrl is the primary interface.
 * We also offer a web console.
 * For most control and integration it's possible to talk directly to the RESTful API.

To control the platform we offer different interfaces. The primary way of controlling your apps and deployments is via the command line client called cctrl. In addition to the command line client we also offer a web console. Both the CLI as well as the web console however are merely frontends to our RESTful API. For maximum integration into your apps you can use one of our available API libraries.

Throughout this documentation we will use the CLI as the primary way of controlling the cloudControl platform. Installing cctrl is easy and works on Mac/Linux as well as on Windows. For installation instructions please refer to the [cctrl installation guide](/dev-center/guides/cctrl-installation-guide).

#### Quick Installtion Windows

For Windows we offer an installer. Please refer to the latest version of the installer at [Github](https://github.com/cloudControl/cctrl/downloads). The file is named cctrl-x.x-setup.exe.

#### Quick Installtion Linux/Mac

~~~
$ pip install cctrl
~~~

We recommned installing cctrl via pip. If you don't have pip you can install pip via easy_install (usually part of the python-setuptools package) and then install cctrl.

~~~
$ easy_install pip
$ pip install cctrl
~~~

The command line client features a detailed online help. Just append --help or -h to any command that you need more details on.

## [Apps, Users and Deployments](#appsandusers)

**TL;DR:**

 * Apps have a repository, deployments and users.
 * The repository is where your code lives organized in branches.
 * A deployment is one version from a branch accessible via a URL. Important: Branch and deployment names need to match.
 * Users can be added to apps to gain access to the repository, its branches and deployments.

cloudControl PaaS uses a distinct set of naming conventions. To understand how to work with the platform most effectively, it's important to understand the basic concept.

### Apps

Apps are a container for the repository and its branches, deployments and users. Creating an app allows you to add or remove users to an app giving them access to the source code as well as allowing them to manage the deployments.

Creating an app is easy. Simply specify a name and the desired type.

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
   ...
~~~

### Users

By adding users to an app you can grant fellow developers access to the source code in the repository and allow them to deploy and modify the deployments. Permissions are role based and there are currently two different roles.

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

 * Owner: Creating an app makes you the owner and gives you full access. The owner can not be removed from the app and gets charged for the apps' consumption. If you plan to have multiple developers work on the same app, it's advisable to have a seperate admin-like account as the owner of all your apps and add the additional developers including your own seperately.
 * Developer: The default role for users added to an app is the developer role. Developers have full access to the repository as well as all the deployments. Developers can add more developers or even remove existing ones. Developers however can not change the associated billing account or remove the owner.

#### Keys

For secure access to the apps repository each developers needs to authenticate via public/private key authentication. You can add a key to your user account using the command line client.

~~~
$ cctrluser key add
~~~

You can also list available keys' ids and remove an existing keys using that id.

~~~
$ cctrluser key
Keys
 Dohyoonuf7
$ cctrluser key.remove Dohyoonuf7
~~~

### Deployments

Deployments are a distinct version from one of your branches running on the platform and made accessible via a URL. The deployment name needs to match the branch name, with the exception of the master branch which is used by the default deployment. Deployments are completly seperated from each other including runtime environment, file system storage and also all Add-ons including e.g. databases and caches. This allows you to have different versions of your app running at the same time without interfering with each other. Please refer to the section about [development, staging and production environments](#environments) for more details.

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

## [Version Control & Images](#versioncontrol)

**TL;DR:**

 * Git and Bazaar are the supported version control systems.
 * When you push a branch a read only image of your code is built ready to be deployed.

### Supported Version Control Systems

For version control cloudControl supports Git and Bazaar. When you create an app we try to determine if the current working directory has a .git or .bzr directory. If so, we create the app with Git or Bazaar as version control respectively. If we can't determine this based on the current working directory we fall back to Git as the default. You can always overwrite this using the --repo command line switch.

~~~
$ cctrlapp APP_NAME create php --repo [git,bzr]
~~~

### Image Building

On each push to one of your branches a deployment image is built automatically. This image than can be deployed with the deploy command to the deployment matching the branch name. The deployment image includes your apps code as well as your [dependencies](#dependencies).

You can use the cctrlapp push command or your version control systems own push command. Please remember that deployment and branch names have to match. So to push to your dev deployment the following to commands are interchangeable. Also note, both require the existence of a branch called dev.

~~~
$ cctrlapp APP_NAME/dev push
$ git push cctrl dev #requires that you have added a git remote called cctrl already
~~~

## [Development, Staging and Production Environments](#environments)

**TL;DR:**

 * Leverage multiple deployments to support the complete application lifecycle.

## [Managing Dependencies](#dependencies)

**TL;DR:**

 * Dependencies are pulled in as part of the image building process.
 * Use your languages native way to specify your requirements.

## [Logging](#logging)

**TL;DR:**

 * There are four different log types (access, error, worker and deploy) available.

## [Add-ons](#add-ons)

**TL;DR:**

 * Add-ons give you access to services like databases and more.
 * Each deployment needs its own set of Add-ons.

## [Custom Domains](#domains)

**TL;DR:**

 * Custom domains are supported via the Alias Add-on.

## [Scaling](#scaling)

**TL;DR:**

 * You can scale up or down anytime by adding more containers or changing the container size.

## [Performance & Caching](#performance)

**TL;DR:**

 * 

## [Scheduled jobs](#scheduledjobs)

**TL;DR:**

 * Scheduled jobs are supported through different Add-ons.

## [Background workers](#workers)

**TL;DR:**

 * Web requests do have a timelimit of 120s.
 * Background workers are the recommended way of handling long running or asynchronous tasks.

## [Stacks](#stacks)

**TL;DR:**

 * Stacks define the common runtime environment.
 * They are based on Ubuntu and stack names match the Ubuntu releases first letter.


 A stack defines the common runtime environment for all deployments. By choosing the same stack for all your deployments, it's guaranteed that all your deployments find the same version of all OS components as well as all preinstalled libraries. Likewise you can use a seperate deployment to test a new stack before your migrate your live deployments.

 Stacks are based on Ubuntu releases and have the same first letter as the release they are based on. Each stack is named after a super hero sidekick. We try to keep them as close to the Ubuntu release as possible, but do make changes for security or performance reasons to optimize the stack for its specific purpose on our platform.
