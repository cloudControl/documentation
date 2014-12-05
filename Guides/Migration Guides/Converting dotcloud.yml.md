# Converting dotcloud.yml to a Next dotCloud Procfile
Your dotcloud.yml build file is a good place to start when converting your dotCloud application to run on Next dotCloud. This is where you've defined what services you need and sometimes their settings as well.

This document will show you what you need to know to convert a dotcloud.yml file to a Procfile on Next dotCloud. We’ll start with the most important differences between the two, then walk you through each section of a dotcloud.yml file with considerations for how to convert to Next dotCloud configurations.

The structure of this document will mostly follow the [Build file documentation](http://docs.dotcloud.com/guides/build-file/) in the dotCloud docs. You should be familiar with that document (or all the parts of your dotcloud.yml file) to use this document. **It may help to have your dotcloud.yml file open while reading this so you can follow along**.

## Services on dotCloud versus processes on Next dotCloud
To better understand the differences between the dotcloud.yml file and the Procfile on Next dotCloud, let’s back up a bit and talk about where the differences come from.

dotCloud applications are built around several service types which you can define in the dotcloud.yml file. Each service runs different a process and has a different end point. This means that a single dotCloud application can combine, for example, a Ruby on Rails and a Python Flask implementation as two services in the same app.

On Next dotCloud, each application runs one main process – a web process. Each app also has only one type that you define when you create the app – this depends on which language the app is written in, and determines which [buildpack](https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile) will create the image for your deployments on the platform.

To port apps with several different languages onto Next dotCloud, you’ll need to create several applications – one for each type. However, many dotCloud services that aren’t related to the code itself, such as databases, are handled as [Add-ons](https://next.dotcloud.com/dev-center/add-on-documentation) on Next dotCloud. These integrate directly with your application and don’t require you to create multiple applications.

You can run several [workers](https://next.dotcloud.com/dev-center/add-on-documentation/worker) within a single application on Next dotCloud. These are handled as background processes of the application and use exactly the same runtime environment as the app.

### App structure on dotCloud
~~~text
+--------------------------------------------------------------------------------------------------+
|  dotCloud application                                                                            |
+---------------------+-+---------------------+-+--------------------------+-+---------------------+
+---------------------+ +---------------------+ +--------------------------+ +---------------------+
|  service web        | |  service support    | |  service worker          | |  service storage    |
|                     | |                     | |                          | |                     |
|  type: ruby         | |  type: python       | |  type: python+worker     | |  type: mysql        |
|  port: 80           | |  port: 8300         | |  port: None              | |  port: 3306         |
|                     | |                     | |                          | |                     |
|                     | |                     | |                          | |                     |
|                     | |                     | |                          | |                     |
+---------------------+ +---------------------+ +--------------------------+ +---------------------+
~~~

### App structure on Next dotCloud
~~~text
+---------------------+ +--------------------------------------------------+                        
|  Next dotCloud app   | | Next dotCloud app                                 |                        
+---------------------+ +--------------------------------------------------+                        
+---------------------+ +--------------------------------------------------+                        
|  name: web          | |  name: support                                   |                        
|                     | |                                                  |                        
|  buildpack: ruby    | |  buildpack: python                               |                        
|  port: 80           | |  port: 80                                        |                        
|                     | |                                                  |                        
|                     | |  additional: python-worker                       |                        
|                     | |                                                  |                        
|                     | |                                                  |                        
+-----------+---------+ +-----------------------------------------+--------+                        
            |                                                     |                                 
            | automatically connected to web app                  |                                 
            | when Add-on is added                                |                                 
            |                                                     |                                 
+-----------+---------+                                           |                                 
|                     +-------------------------------------------+                                 
|  Add-on: MySQL      |  manually connected to python app                                           
|                     |  via custom config                                                          
+---------------------+                                                                             
~~~

## dotcloud.yml versus the Procfile
The dotcloud.yml file corresponds to the Procfile on Next dotCloud. Just like the dotcloud.yml, it will be located in the root of your repository. The format of the Procfile is much simpler than dotcloud.yml because most of the configuration is handled through the dcapp CLI and the Buildpacks. The Procfile in a Next dotCloud application is simply used to determine how to start the actual application in the container – both the web and worker processes.

You should create a new Procfile at the same level as your dotcloud.yml (in the root of your reposity). We'll talk about what to add to this file in the following sections.

## dotcloud.yml format
Here is the example dotcloud.yml from the dotCloud documentation. We'll walk through each section below. The sections of your own dotcloud.yml may appear in a different order.

~~~yaml
# Required parameters for a service: service name and type
servicename1:   # Any name up to 16 characters using a-z, 0-9 and _
  type: ruby     # Must be valid service type.

servicename2:
  type: python

  # ---------------------------------------------------------------
  # Optional parameters: All the following parameters are optional.

  # Define the location of this service's code
  approot: directory/relative/to/dotcloud_yml/  # Defaults to '.'

  # Build Hooks. Paths are relative to approot.
  prebuild: executable_name    # Defaults to undefined.
  postbuild: executable_name   # Defaults to undefined.
  postinstall: executable_name # Defaults to './postinstall'.

  # Ubuntu packages installed via apt-get install.
  systempackages:
    - packagename
    - another-packagename

  # Configuration for your service. See docs for each dotCloud Service.
  config:
    service_specific_parameter1: valueA
    service_specific_parameter2: valueB

  # Custom ports. HTTP ports are proxied.
  # Most services do not need custom ports.
  ports:
    portname1: http            # Name is arbitrary, type is (http|tcp|udp)
    portname2: tcp

  # Environment variables. Shared by all services.
  environment:
    EXAMPLEVAR1: EXAMPLE_VALUE
    EXAMPLEVAR2: EXAMPLE_VALUE_TOO

  # Supervisor.conf shortcuts
  # You can use one or the other of (process|processess), but not
  # both.
  # This is almost directly translatable to the Procfile
  process: executable_name  # Defaults to './run'
  processes: # For when you have more than one process to run.
    process_name1: path/to/executable1
    process_name2: path/to/executable2

  # List of dependencies, best for PERL/PHP but also Python and Ruby
  requirements: # Defaults to empty list.
    - dependency_package_name_1
    - dependency_package_2
~~~

This shows two services, servicename1 and servicename2. Each has a type, and servicename2 has a lot of other parameters related to it.

## Procfile format
For contrast, here is a sample Procfile for a Python Celery app:

~~~bash
web: celery flower --port=$PORT --broker=$CLOUDAMQP_URL --auth=$FLOWER_AUTH_EMAIL
worker_a: celery -A tasks worker --loglevel=info
worker_b: python otherworker.py
~~~

Note that in the Procfile, only the web and worker processes are defined. Each line in the Procfile is actually a shell command that will run just like you define it. This means you can customize the start options for your processes here.

## servicename and type
Your application on dotCloud can have multiple services, each with its own unique name that you define. On dotCloud, service names are fairly arbitrary. On Next dotCloud, services are handled quite differently because of how the platform is built. There are two types of processes on Next dotCloud, each of which is specifically defined: web processes, and worker processes. All other services are handled via [Add-ons](https://next.dotcloud.com/dev-center/platform-documentation#add-ons) and are not defined in the Procfile.

### Type: (language) / web process
On Next dotCloud, each application is based around one main, language-specific web process. This is because the Next dotCloud platform uses Buildpacks – a language-specific set of scripts that builds the images for your apps based on the language you specify.

As a result of this, **all the processes in a Procfile run in the same environment**. That means you can’t create servicename1 as a ruby type and servicename2 as a python type. **That's a big difference from a dotcloud.yml file**. If you need multiple languages in your project, you may need to create multiple applications.

On Next dotCloud, you specify the language when you `dcapp APP_NAME create` the application. You can specify one of the predefined types (buildpacks): java, nodejs, php, python, ruby or custom. This will define the environment for the entire app.

Once you’ve created the application, you need to set the web process and specify how it will be started by the shell. In the Procfile, this is an actual shell command. **The process with the name web will be the one which gets HTTP traffic**.

### Type: (language)-worker / worker process
On Next dotCloud, you can define multiple workers in the Procfile that will run as background processes in that application. They use the exact same runtime environment as the web process.

To use this functionality, you need to add the [Worker Add-on](https://next.dotcloud.com/dev-center/add-on-documentation/worker) to your app.

### Non-code services / Add-ons
In the dotcloud.yml file, you can define many different kinds of services within the app, including databases like MySQL, or other services like Redis. A Procfile on Next dotCloud only specifies your web and worker processes. This is very different from a dotcloud.yml file.

On Next dotCloud, "Data Services" like MySQL and Redis, as well as many other types of services like logging, monitoring and even SSL, are handled outside of the Procfile with [Add-ons](https://next.dotcloud.com/dev-center/platform-documentation#add-ons). You can simply add them to your web app in the CLI with `dcapp addon.add`. This automatically connects the services to your app.

To connect these services to an additional web app (for example if your existing dotCloud app was using multiple http-port code services in different languages), you can connect them manually using the [Custom Config Add-on](https://next.dotcloud.com/dev-center/add-on-documentation/config).


### Example dotcloud.yml to Procfile migration
In a Procfile, all processes run in the same language type and on the same code base. If the services in your dotCloud app only include one web process and one or more workers, then converting to a Procfile is trivial:

~~~yaml
servicename1:
   type: python

servicename2:
   type: python-worker

servicename3:
   type: rabbitmq
~~~
becomes (for a Python Celery app with a CloudAMQP Add-on for RabbitMQ):

~~~yaml
web: celery flower --port=$PORT --broker=$CLOUDAMQP_URL --auth=$FLOWER_AUTH_EMAIL
worker: celery -A tasks worker --loglevel=info
~~~

The entry for the web or worker process will be executed as a shell command.

For more specific details, please see the [porting guides](../) specific to your language of choice. Please note that we will continually update these guides to include all of the languages officially supported on Next dotCloud.

## approot
You may have used the approot directive in dotcloud.yml to tell the platform where to find the code to run, but the Next dotCloud Procfile lets you set this first command explicitly, so you don't need an approot. If you need to change directory before running your first statement, you can do that in the command of the Procfile:
~~~yaml
web: cd myapproot; sh startapp.sh
~~~
(myapproot and startapp.sh are arbitrary names, just for example)

If you want to change the approot for PHP-based applications, the process is slightly different. In this case, you’ll need to [manually set the Apache’s DocumentRoot](https://github.com/cloudControl/buildpack-php#manually-setting-the-documentroot).

Next dotCloud processes do not have any magically created directories like the dotCloud services. There are neither code nor current symbolic links. The root of your home directory has the same format and contents as the directory which contains your Procfile.

## prebuild, postbuild, postinstall: Build Hooks
### prebuild and postbuild scripts
The officially supported Buildpacks on Next dotCloud consist of a standard set of scripts that are run when the deployment image is being built. Because of this, prebuild and postbuild hooks are not natively supported.

If you have applications on dotCloud that use prebuild and postbuild hooks, it’s worthwhile to try pushing and deploying them using one of the officially supported Buildpacks first. The stack may already have the components you need installed and it may work out of the box.

If not, you may need make modifications to a standard buildpack and use it as a [custom Buildpack](https://next.dotcloud.com/dev-center/guides/third-party-buildpacks/third-party-buildpacks). First download the Buildpack for your app’s language type from [Next dotCloud’s public Github repo](https://github.com/cloudControl?query=buildpack) and modify the `bin/compile` script according to your needs. (Keep in mind that only the `/app` folder is writeable, so you can’t use the Ubuntu package manager to install libraries.) Upload your new custom Buildpack to a public non-ssh git repository, and create your application with the apptype `custom --buildpack BUILDPACK_URL`.

### postinstall scripts
If you have a postinstall script, you can run the same step as part of your Procfile command, e.g. Procfile:
~~~yaml
web: sh postinstall.sh; sh startapp.sh
~~~

## systempackages
Like prebuild and postbuild scripts, a systempackages section in your dotcloud.yml file may mean you need to modify a buildpack. See the above section on prebuild and postbuild scripts on how to create a custom buildpack.

## config
There are a couple of ways to migrate your dotcloud.yml config section, depending on what you are configuring.

If you’re using the config section to specify an interpreter version (e.g. Python 2.6 vs. Python 2.7), check the [Next dotCloud buildpack documentation on Github](https://github.com/cloudcontrol?query=buildpack) for how to do this with your specific buildpack. For example, you can specify the Python version by creating a runtime.txt file to replace your dotcloud.yml config: python_version.

If you’re using the dotcloud.yml config section to specify how to start your processes, you can accomplish this on Next dotCloud using the [Custom Config Add-on](https://next.dotcloud.com/dev-center/add-on-documentation/config) and the Procfile. First set the variables using Custom Config and add them as part of the shell command that starts the web and worker processes in the Procfile. Note that for some Add-on services, this is done automatically.

For more information, read our guide on [migrating environment variables](./migrating-environment) from dotCloud to Next dotCloud.

## ports
If you have a ports section in your dotcloud.yml then you should only have one port listed, a single http type port. That is the only kind of port allowed on the Next dotCloud PaaS. You can only have one process which listens to an HTTP port.

If you do have multiple services each with their own HTTP port, then you should consider how to either separate these into different applications or how to access each different function via a different URL path (e.g. if you used to have an "admin" interface as well as a public interface, move your "admin" interface to be part of your public interface on another path, like "www.example.com/admin").

Note that Next dotCloud containers do not expose an SSH port. See the [Secure Shell docs](https://next.dotcloud.com/dev-center/platform-documentation#secure-shell-ssh).

## environment
If you were setting environment variables in your dotcloud.yml then you should set these via `dcapp APP_NAME config.add` on Next dotCloud. Please read the [Add-on documentation](https://next.dotcloud.com/dev-center/add-on-documentation/config) and our [dedicated guide](./migrating-environment) on this topic.

Note that the same variables are set in all your application processes (web and worker) -- you cannot specify that a variable should only be set on one process (as you could in a dotcloud.yml file).

## requirements
On the dotCloud platform, a `requirements` section can help install
additional dependencies of your application during build time (after
`dotcloud push`). On Next dotCloud you should use the mechanism
provided by your Buildpack.

For more information on all supported languages, please check our [Guides](../..).

