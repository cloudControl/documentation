Migrating a Django Celery app from dotCloud to [nextdotCloud]
===========================================================

This guide is intended to help migrating a [Django] app, using [Celery], and is
deployed on [dotCloud], to the [[nextdotCloud]] PaaS.

# Getting Started

First you need to clone the transformed application from
https://github.com/cloudControl/django-celery-migration-app to your local
machine:
~~~
$ git clone https://github.com/cloudControl/django-celery-migration-app
$ cd django-celery-migration-app
~~~

Looking at the commit history of this application you can see the
transformations needed in order to get the application deployed on [nextdotCloud].
Those are summed up in the following paragraphs.

## Add a Procfile

The [Procfile] is an important part of your [nextdotCloud] application since it
describes the way to execute your application. It can be compared to the
`dotcloud.yml`.

In there we define the command to run the django application but also the
commands to start the workers:
~~~
web: gunicorn -b 0.0.0.0:$PORT --log-file - minestrone.wsgi
celerycam: python manage.py celerycam
celeryd: python manage.py celeryd -E -l info -c 2
~~~

## Update the Django settings

The Django settings needed to be updated in order to match the [nextdotCloud]
services, including the database and message queue configuration, alongside the
serving of static files and templates.

## Update requirements.txt

The requirements are also updated in order to include [gunicorn] – the in use
`wsgi` server – `MySQL-python` since we use MySQL as a database and a python
plugin for serving static assets.

## General code changes

The application included code improvements and changes in order to match the
latest versions of its dependencies, plus a basic restructure in respect to
those changes.

# Deploy your app on [nextdotCloud]

After having these changes in place you can migrate your app to [nextdotCloud].
The first step is about creating your application. In this case you need to pick
a name and define the application type which in our case is python:
~~~
$ dcapp APP_NAME create python
~~~

After the app is successfully created you can push your code on the platform:
~~~
$ dcapp APP_NAME push
~~~

Then you will need to choose the services needed for your application. In
[nextdotCloud] those are called [add-ons] and in this case we will need a database
add-on and a RabbitMQ add-on. In this example we will use the [MySQLd] and the
[CloudAMQP] add-ons:
~~~
$ dcapp APP_NAME addon.add mysqld.micro
$ dcapp APP_NAME addon.add cloudamqp.tiger
~~~

MySQLd takes a few minutes until the instance is up. To check its status you can
use the `addon` command:
~~~
$ dcapp APP_NAME addon
~~~

After the service are in place you can finally deploy your application:
~~~
$ dcapp APP_NAME deploy
~~~

Django needs an initial database setup. For that you can use the [run] command
which swaps an instance of your application and allows you to execute code in
it. In order to run `syncdb` you can execute the following:
~~~
$ dcapp APP_NAME run 'python manage.py syncdb'
~~~

Follow the instructions to create your admin user. When this is done you can
finally add your background workers which execute the jobs published in the
message queue. Add the [Worker] add-on:
~~~
$ dcapp APP_NAME addon.add worker.single
~~~

Then add the workers defined into your Procfile like this:
~~~
$ dcapp APP_NAME worker.add celeryd
$ dcapp APP_NAME worker.add celerycam
~~~

Your app should be now fully set up. You can open it in your browser with the
`open` command:
~~~
$ dcapp APP_NAME open
~~~

Now you can add jobs and the workers will execute them. To monitor the worker
activity you can check the [logs]:
~~~
$ dcapp APP_NAME log worker
~~~

In the end the finished jobs are put into the task table. You can see that by
logging into the admin interface of your application using the credentials you
defined on the syncdb step. Login at https://APP_NAME.dotcloudapp.com/admin to
proceed into the django admin panel.

Finally, you can always monitor your application by checking the error log with:
~~~
$ dcapp APP_NAME log error
~~~

# Conclusion

With a few transformations in order to match the [nextdotCloud] platform
requirements and some steps to create and set up your application, you can
easily move from dotCloud without missing any services and most importantly
having everything under control.

[Django]: https://www.djangoproject.com/
[Celery]: http://www.celeryproject.org/
[dotCloud]: https://www.dotcloud.com/
[[nextdotCloud]]: https://www.next.dotcloud.com/
[Procfile]: https://www.next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[gunicorn]: http://gunicorn.org/
[add-ons]: https://www.next.dotcloud.com/add-ons
[MySQLd]: https://www.next.dotcloud.com/add-ons/mysqld
[CloudAMQP]: https://www.next.dotcloud.com/add-ons/cloudamqp
[run]: https://www.next.dotcloud.com/dev-center/platform-documentation#secure-shell-ssh
[Worker]: https://www.next.dotcloud.com/add-ons/worker
[logs]: https://www.next.dotcloud.com/dev-center/platform-documentation#logging
