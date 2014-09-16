# Converting dotcloud.yml to cloudControl

Your `dotcloud.yml` build file is a good place to start when
converting your dotCloud application to run on cloudControl. This is
where you've defined what services you need and sometimes their
settings as well. This document will walk you through each section of
a `dotcloud.yml` file with considerations for how to convert to
cloudControl configurations. The structure of this document will
mostly follow the [Build file
documentation](http://docs.dotcloud.com/guides/build-file/) in the
dotCloud docs. You should be familiar with that document (or all the
parts of your `dotcloud.yml` file) to use this document.

Since the dotcloud.yml file also defines some of the build time
behavior, we will also compare the dotCloud and cloudControl build
processes.

## Location

Your `dotcloud.yml` file is located in the root of your source
tree. For example:

```
myapp/
├── dotcloud.yml
├── admin/ (source code for adminstration backend)
└── frontend/ (source code for frontend)
```

You should open that file and read through it as you read through this
document.

The corresponding file on cloudControl is the `Procfile`. The format
of the Procfile is much simpler than `dotcloud.yml` because most of
the configuration is handled through the `cctrlapp` CLI and
Buildpacks, but the basic idea is the same: list services in the
application and define some behavior.

You should create a new `Procfile` text file at the same level as your
`dotcloud.yml`. We'll talk about what to add to this file below.



## `dotcloud.yml` format

Here is the example `dotcloud.yml` from the dotCloud
documentation. We'll walk through each section below. The sections of
your own `dotcloud.yml` may appear in a different order.

```
# Required parameters for a service: service name and type
servicename1:    # Any name up to 16 characters using a-z, 0-9 and _
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
```

This shows two services, `servicename1` and `servicename2`. Each has a
type, and `servicename2` has a lot of other parameters related to it.

For contrast, here is a similar `Procfile` with two Python services
instead of a mix of Ruby and Python. We'll talk about why that is
important in the *type* section below. 

Python celery example:

```
web: celery flower --port=$PORT --broker=$CLOUDAMQP_URL --auth=$FLOWER_AUTH_EMAIL
worker: celery -A tasks worker --loglevel=info
```

## *servicename*: A Little Magic

Your application on dotCloud can have multiple services, each with its
own unique name, and the same is true on cloudControl. Where the
service names on dotCloud are fairly arbitrary, on cloudControl there
is one magic name, `web`. 

**A service with the name `web` will be the one* service which gets
  HTTP traffic.**

Names for other services can be arbitrary. 

## type: Very Different on cloudControl

In a `Procfile` the service name is actually called a "process type"
because the name can affect its behavior in the special case of
`web`. Procfiles don't acually have the idea of a `type` of service
**because all the services in a Procfile run in the same type of
environment**. That is, you cannot create `servicename1` as a `ruby`
type and `servicename2` as a `python` type. **That's a big difference
from a `dotcloud.yml` file.**

### Running Code

In a Procfile, both `servicename1` and `servicename2` must run in the
same "type". Since there is only one type for an entire Procfile, you
specify the type when you `cctrlapp create` the application. You can
specify one of the predefined types (buildpacks): `java`, `nodejs`,
`php`, `python`, `ruby` or `custom`. If all of your code runs with the
same language and you only have one web service and the rest are
workers, then converting to a Procfile is trivial:

```
servicename1:
   type: python

servicename2:
   type: python-worker

servicename3:
   type: rabbitmq
```

becomes (for a Python Celery app with a CloudAMQP Add-on for RabbitMQ):

```
web: celery flower --port=$PORT --broker=$CLOUDAMQP_URL --auth=$FLOWER_AUTH_EMAIL
worker: celery -A tasks worker --loglevel=info
```

(for more specific details, please see the porting guide specific to
Python or your language of choice).

If you need to run some code in a mix of languages, e.g. Ruby and
Python (as in the example `dotcloud.yml` file above), then you will
need to create a custom buildpack to install the languages you need.

### Non-code services

But what if some of your `type`s in your dotcloud.yml are not
programming languages? What if they are services like MySQL or Redis?

**A `Procfile` only specifies the "code services" of your
application. This is very different from a `dotcloud.yml` file.**

"Data Services" like MySQL and Redis, as well as many other types of
services like logging, monitoring, and even SSL are handled outside of
the `Procfile` with
[Add-ons](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons)
via `cctrlapp addon.add`.

## approot

You may have used the `approot` directive in `dotcloud.yml` to tell
the platform where to find the code to run, but the cloudControl
`Procfile` lets you set this first command explicitly, so **you don't
need an `approot`**. If you need to change directory before running
your first statement, you can do that in the command of the
`Procfile`:

```
web: cd mydir; run myscript.sh
```

cloudControl services do not have any magically-created directories
like the dotCloud services. There are neither `code` nor `current`
symbolic links. The root of your home directory has the same format
and contents as the directory which contained your `Procfile`.


TODO
====

## prebuild, postbuild, postinstall: Build Hooks

prebuild and postbuild can be part of a compile step in a buildpack
but there isn't a simpler equivalent.

postinstall can be part of your procfile command.

## systempackages

no support for apt-get using a privileged user, so you'll have to
install everything to user-writeable directories. Should users include
these binaries as part of their push? Need to test.

## config

can be set via cctrlapp config.add
can have multiple configs, one for each deploy
deploys are tied to branches in git

## ports

Cannot set ports. An application can only receive connections from
port 80, and only one service can receive these. There are no
workarounds.

## environment

same as config

## process

maps directly to Procfile command, though "processes" isn't quite the
same because on CC each would run in its own container instead of as
separate processes in the same container. Can run own supervisord to
workaround this (early tests look good, but want to confirm there
aren't any crazy unexpected behaviors in logging or signals) 

## requirements

as per buildpacks

# Comparison of build systems

Might be a separate doc.
http://docs.dotcloud.com/guides/build-file/#prebuild-postbuild-postinstall-build-hooks
