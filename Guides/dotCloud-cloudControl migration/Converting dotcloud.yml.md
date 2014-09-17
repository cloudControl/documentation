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
web: cd myapproot; run startapp.sh
```

(*myapproot* and *startapp.sh* are arbitrary names, just for example)

cloudControl services do not have any magically-created directories
like the dotCloud services. There are neither `code` nor `current`
symbolic links. The root of your home directory has the same format
and contents as the directory which contained your `Procfile`.


## prebuild, postbuild, postinstall: Build Hooks

If you are using a `prebuild` or `postbuild` script in your
`dotcloud.yml`, that could mean you need to create [your own
Buildpack](https://www.cloudcontrol.com/dev-center/Guides/Third-Party%20Buildpacks/Third-Party%20Buildpacks). You
can write a Buildpack from scratch, or you can compose multiple
Buildpacks together (using [a third party
meta-buildpack](https://github.com/ddollar/heroku-buildpack-multi)). In
any case, a Buildpack will enable you to create your own custom
service type with the software you want pre-installed (like a
`prebuild` script) and any build and post-build compilation steps you
need (like your `postbuild` script).

If you have a `postinstall` script, you can run the same step as part
of your Procfile command, e.g. Procfile:

```
web: cd myapproot; postinstall.sh; startapp.sh
```

## systempackages

Like `prebuild` and `postbuild` scripts, a `systempackages` section in
your `dotcloud.yml` file probably means you need to create your own
Buildpack. A custom Buildpack will enable you to set up the software
you need, though you may not be able to install it using `apt-get`
(which is how `systempackages` items get installed). You might need to
include the source code or binaries as part of your Buildpack. 

## config

There are a couple of replacements for a dotCloud `config` section,
depending on what needs configuring. Some configuration options may be
handled explicitly by the type of service -- you should read the
documentation for the Buildpack you're using. The cloudControl
buildpacks are [available on
GitHub](https://github.com/cloudcontrol?query=buildpack). For example,
you can specify the Python version to use by creating a `runtime.txt`
file to replace your `dotcloud.yml config: python_version`.

If your configuration could be replaced by setting an environment
variable, you can do that with the CLI: `cctrlapp config.add` (see
[the
docs.](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config))

## ports

If you have a `ports` section in your `dotcloud.yml` then hopefully
you only have one port listed, a single `http` type port. That is the
only kind of port allowed on the cloudControl PaaS. You can only have
one process which listens to an HTTP port. This is pretty common for
`custom` type apps on dotCloud.

If you do have multiple services each with their own HTTP port, then
you should consider how to either separate these into different
applications or how to access each different function via a different
URL path (e.g. if you used to have an "admin" interface as well as a
public interface, move your "admin" interface to be part of your
public interface on another path, like "www.example.com/admin").

Note that cloudControl containers do not expose an SSH port. See the
[Secure Shell docs](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#secure-shell-ssh).

## environment

If you were setting environment variables in your `dotcloud.yml` then
you should instead set these via `cctrlapp config.add` (see [the
docs](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config)).

## process

If your `dotcloud.yml` file includes a `process` or `processes`
section, you will probably need to install `supervisor` so that you
can run multiple processes. Other process managers are usable too,
like foreman (written in Ruby), but if you're coming from the dotCloud
environment, you're probably already familiar with `supervisor`
(written in Python).

Each service on the dotCloud platform could generally rely on the
presence of [`supervisord`](http://supervisord.org/) so it can start
up multiple processes in the same service. Typical cloudControl apps
only run one process per service. **But you can install supervisord**
yourself. This is especially easy on `python` type applications,
though you could add `python` and `pip` with your own Buildpack if
necessary. On a `python` type application, you can install supervisor
by adding this to your `requirements.txt` file:

```
supervisor
supervisor-stdout
```

Then you'll need to add a `supervisor.conf` file which lists each of the
processes you had in your `dotcloud.yml`.

Note that the dotCloud PaaS code services often ran both `supervisor`
and `nginx`. `nginx` was typically started by `init.d` and so was not
explicitly controllable by you, but on cloudControl you can run nginx
as another process under `supervisor` if you wish. This gets you
pretty close to a dotCloud environment on cloudControl.

## requirements

On the dotCloud platform, a `requirements` section can help install
additional dependencies of your application during build time (after
`dotcloud push`). On cloudControl you should use the mechanism
provided by your Buildpack (e.g. Python uses a `requirements.txt`
file, Ruby uses a `gemfile`).

TODO
----

# Comparison of build systems

Might be a separate doc.
http://docs.dotcloud.com/guides/build-file/#prebuild-postbuild-postinstall-build-hooks
