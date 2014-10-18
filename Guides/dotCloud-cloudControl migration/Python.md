# Migrating Python Applications from dotCloud to cloudControl

Before you read this document, you should read the document on *Converting dotcloud.yml* first. It provides a framework for porting your application as a whole over to cloudControl. Please return here to learn how to port your Python services.

## dotCloud Features

The first task is to determine what dotCloud features you Python service is using. 

1. **Nginx**

Please check your Python service's `approot` to see if you have an `nginx.conf` file. The most common use for this is to define redirections and error handlers for the Nginx web server. If you have this, then your first migration will probably require Nginx in your cloudControl application as well. Later you may find other ways to work around your use of Nginx, but for now we can install Nginx in your cloudControl application.

2. **Supervisord**

Does your `approot` contain `supervisord.conf`? Or does your `dotcloud.yml` include a `process` or `processes` section in your Python service definition? If so, then you will also require `supervisord` on cloudControl.

3. **uWSGI**

Does your `approot` contain uWSGI configuration information (`*uwsgi.conf`) or does your `dotcloud.yml` define environment variables for `UWSGI_*`? If so, then you'll need to run `uwsgi` on your cloudControl application.

