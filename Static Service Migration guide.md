# Migrating dotCloud static services to Next dotCloud platform

In case you were using [static services] on dotCloud, Next dotCloud has
you covered. Deploying this kind of applications is an easy task and we'll
show you how to do it in few easy steps.

## Getting Started

The static service is a simple web server that can be used to host static content (images, packages...) efficiently.

This makes it ideal to serve:
    
- Static "maintenance" pages or placeholders for future services
- URL routers, to dispatch requests to other services
- Static assets that you want to keep on a separate domain, for hardcore optimization reasons (like cookie separation)


## Migration steps

### Application on dotCloud

On dotCloud, assuming you followed the dotCloud tutorial for the [static services],
you have an application called `ramen-on-dotcloud` that you want to migrate to
Next dotCloud.

~~~
ramen-on-dotcloud
└── hellostatic
    └── index.html
~~~

Where index.html has this content:

~~~html
<html>
<head><title>Hello World!</title></head>
<body>This is a static service running on dotCloud.</body>
</html>
~~~

You probably have a `www` service specified this way:

~~~
www:
  type: static
  approot: hellostatic
~~~

### Create a static application on Next dotCloud

In order to start migrating your static web application, create an
application using our custom `nginx` buildpack:

~~~
$ dcapp APP_NAME create custom --buildpack https://github.com/cloudControl/buildpack-nginx.git
~~~

By default, `nginx` will serve the content of your root directory. In order to
migrate our dotCloud application, we want to serve the content of the `hellostatic`
directory instead. So let's create a short configuration file to give this order
to our `nginx` server:

~~~
$ mkdir -p .nginx/conf.d
$ echo "root hellostatic;" > .nginx/conf.d/custom.conf
~~~

### Push and deploy to Next dotCloud

Now that you all files in place you can push your code on the platform:

~~~
$ dcapp APP_NAME push
Counting objects: 4, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (3/3), done.
Writing objects: 100% (4/4), 357 bytes | 0 bytes/s, done.
Total 4 (delta 1), reused 0 (delta 0)
       
-----> Receiving push
-----> Downloading nginx ...
...
-----> Building image
-----> Custom buildpack provided
-----> Uploading image (1.1 MB)
       
To ssh://APP_NAME@dotcloudapp.com/repository.git
   497fcf3..ab5e9e4  master -> master
~~~

After the service is in place you can finally deploy your application and
open it:

~~~
$ dcapp APP_NAME deploy
$ dcapp APP_NAME open
~~~

You should be able to see the content of your `index.html` file on
your browser.

### Denying access to files or directories

In case you're storing secret content in your application files, it is
easy to deny access to them via `nginx` configuration:

~~~
$ echo "location /SECRET_DIR { deny all; }" >> .nginx/conf.d/custom.conf
$ echo "location = /SECRET_FILE { deny all; }" >> .nginx/conf.d/custom.conf
~~~


[static services]: http://docs.dotcloud.com/services/static/
