# An Introduction to cloudControl for dotCloud Developers

Welcome to cloudControl! You'll find a lot of similarities here to
dotCloud, but some important differences too. We'll try to cover the
biggest differences here and we'll also provide more specific
information for each programming language and dotCloud service type.

## Can I still use my favorite language?

Yes, cloudControl is even more flexible than dotCloud on language
support. You'll find up to date versions of Python, PHP, NodeJS, Ruby,
and Java here, and custom applications can give you access to even
more languages like Go, Erlang, Lisp, Lua, C, Haskell, and Perl.

More information on the specifics of porting Python, PHP, NodeJS, Ruby
and Java to cloudControl are found in the guides here.

## Do I still have access to my favorite databases?

Yes, but on cloudControl, all stateful services are provided as
"Add-on" services. Some of them are run by cloudControl and some are
third party services, but cloudControl will be your single point of
contact for billing and support. 

More information about the specifics of porting MySQL, Redis, MongoDB,
PostGRE/PostGIS are in the relevant guides here.

## Do I still get loadbalancing, scaling, SSL, seamless deployment...

Yes! cloudControl still behaves in all the ways you'd expect.

# Where should I begin?

Go read the [Quick
Start](https://www.cloudcontrol.com/dev-center/Quickstart) and
[Platform
Documentation](https://www.cloudcontrol.com/dev-center/Platform%20Documentation)
to get familiar with the cloudControl PaaS. 

Then read the docs here which compare the dotCloud CLI to the
cloudControl CLI and `dotcloud.yml` to a `Procfile` (and the
build/deploy process in general).

## Missing Pieces

When you're familiar with all that cloudControl offers, you should
consider if you're using any of these features of dotCloud, because
these will require some important changes to your application:

* `~/data` for persistent data. cloudControl does not persist data
  between deployments, so you must use a database or other persistent
  file service like AWS S3. Whatever you push to cloudControl must be
  stateless.
* `nginx.conf` to create redirects, URL rewrites, basic auth, or
  serving static assets. While it is possible to run *nginx* in a
  cloudControl service, it is not typical. The strategy to work around
  this will depend on the language and framework you're using.
* `supervisord.conf` to run additional processes. While it is possible
  to install and run *supervisord* in a cloudControl service, it is
  not typical. Most services run only one process.
* `ssh` to manually tweak your running application. You can get
  something like *ssh* on cloudControl, but it is limited to a new
  instance which has been created just for your interactive
  session. You can't directly interact with an instance you've
  previously deployed. You can, however, run the same code in a new
  container and interact with the same databases and services, so you
  can still interactively manage and perform data migrations.

