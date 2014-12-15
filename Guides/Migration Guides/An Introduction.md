# An Introduction to Next dotCloud for dotCloud Developers

Welcome to Next dotCloud! You'll find a lot of similarities here to
dotCloud, but some important differences too. We'll try to cover the
biggest differences here and we'll also provide more specific
information for each programming language and dotCloud service type.

## Can I still use my favorite language?

Yes, Next dotCloud is even more flexible than dotCloud on language
support. You'll find up to date versions of Python, PHP, NodeJS, Ruby,
Java, Scala, Clojure, Play and Gradle here, and custom buildpacks
can give you access to even more languages like Go, Erlang, Lisp, Lua,
C, Haskell, and Perl.

More information on the specifics of porting applications in specific
languages and frameworks are available here. Note that we are continuously
expanding these guides to include all dotCloud services.

## Do I still have access to my favorite databases?

Yes, but on Next dotCloud, all stateful services are provided as
"Add-on" services. Some of them are run by Next dotCloud and some are
third party services, but Next dotCloud will be your single point of
contact for billing and support.

More information about the specifics of porting Add-ons like MySQL,
Redis, MongoDB, Postgre/PostGIS are in the relevant guides here.
Note that we will continually expand these guides to include all dotCloud
services.

## Can I still create multiple services for my application?
Yes, but in a different way. Next dotCloud does not provide services
as we know them in dotCloud. Instead, each application can have
multiple deployments, which run one separate web process each.
Furthermore, each deployment can serve multiple worker processes using the
[Worker "Add-on"](http://next.dotcloud.com/dev-center/add-on-documentation/worker) and other stateful services via "Add-on"
services.

## Can I keep using the same Version Control System for my code?
It depends. You can only push code on Next dotCloud when it's
version-controlled by Git. So if you are using Mercurial, Subversion or
no VCS at all, a migration to Git is required.  For more information on
how to migrate, see these resources on converting from [Mercurial to Git](http://hivelogic.com/articles/converting-from-mercurial-to-git/),
and from [Subversion to Git](http://www.subgit.com/).

## Do I still get loadbalancing, scaling, SSL, seamless deployment...

Yes! Next dotCloud still behaves in all the ways you'd expect.

# Where should I begin?

A good place to start is to read the [Quickstart](https://next.dotcloud.com/dev-center/quickstart)
and [Platform Documentation](https://next.dotcloud.com/dev-center/platform-documentation)
to get familiar with the Next dotCloud PaaS.

Then read the docs [here](./cli-cheatsheet) comparing the dotCloud
CLI to the Next dotCloud CLI and `dotcloud.yml` to a `Procfile` (including
a comparison of build/deploy processes in general).

## Missing Pieces

When you're familiar with all that Next dotCloud offers, you should
consider if you're using any of the following features of dotCloud, because
these will require some important changes to your application:

* `~/data` for persistent data. Next dotCloud does not persist data
  between deployments, so you need to use a database or other persistent
  file service like Google Cloud Storage. Whatever you push to Next dotCloud
  needs to be stateless.
* `nginx.conf` to create redirects, URL rewrites, basic auth, or
  serving static assets. While it is possible to run *nginx* in a
  Next dotCloud deployment, it is not typical. The strategy to work around
  this will depend on the language and framework you're using.
* `supervisord.conf` to run additional processes. While it is possible
  to install and run *supervisord* in a Next dotCloud deployment, it is
  not typical. Most deployments run only one process.
* `ssh` to manually tweak your running application. You can get
  something like *ssh* on Next dotCloud, but it is limited to a new
  instance which has been created just for your interactive
  session. You can't directly interact with an instance you've
  previously deployed. You can, however, run the same code in a new
  container and interact with the same databases and deployments, so you
  can still interactively manage and perform data migrations.
* *older versions*: On dotCloud the OS and most of the software runs on
  Ubuntu 10.04LTS (circa 2010). You may find that you need to
  update your code to use newer versions of your libraries and other
  dependencies on Next dotCloud, which runs on Ubuntu 12.04.

If you have mission-critical applications that are affected by some of these
“missing pieces”, please contact us at support@dotcloud.com and we will do our
very best to guide you through a solution for your use case.
