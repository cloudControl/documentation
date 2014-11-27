# Migrating Python Applications from dotCloud to Next dotCloud

Before you read this document, you should read the document on *Converting dotcloud.yml* first. It provides a framework for porting your application as a whole over to Next dotCloud. Please return here to learn how to port your Python services.

## dotCloud Features

The first task is to determine what dotCloud features your Python service is using. 

### Nginx

Please check your Python service's `approot` to see if you have an `nginx.conf` file. The most common use for this is to define redirections and error handlers for the Nginx web server. If you have this, then your first migration will probably require Nginx in your Next dotCloud application as well. Later you may find other ways to work around your use of Nginx, but for now we can install Nginx in your Next dotCloud application.

### Supervisord

Does your `approot` contain `supervisord.conf`? Or does your `dotcloud.yml` include a `process` or `processes` section in your Python service definition? If so, then you will also require `supervisord` on Next dotCloud.

### uWSGI

Does your `approot` contain uWSGI configuration information (`*uwsgi.conf`) or does your `dotcloud.yml` define environment variables for `UWSGI_*`? If so, then you'll need to run `uwsgi` on your Next dotCloud application.

## Choose Your Path

If you're not using Nginx, Supervisord, or uWSGI, then you can definitely use the standard Next dotCloud Python service! 
For all the other cases, you can use a custom buildpack instead of the default Python service: https://github.com/metalivedev/buildpack-python-cloudcontrol.git#dotcloud

The basic information about how to use that buildpack is in the buildpack's Readme.md.
