# Deploying a Django application
[Django][django] is the Web framework for perfectionists with deadlines.

In this tutorial we're going to show you how to migrate an existing Django
application to the [cloudControl] platform. You can find the complete
[source code][example-app] of the application on Github.

All the steps described in this tutorial can be followed in the git repository using the commit history.


## Prerequisites
*   [cloudControl user account][cloudControl-doc-user]
*   [cloudControl command line client][cloudControl-doc-cmdline]
*   [git]
*   familiarity with the Django framework


## Original application

The goal of this tutorial is to migrate a [Django tutorial] application to
the cloudControl platform. The application allows to create, use and manage simple polls.

To start, first clone the application from the previously mentioned git repository:
~~~bash
$ git clone git://github.com/cloudControl/python-django-example-app.git
$ cd python-django-example-app
~~~

Prepare the database by running:
~~~bash
$ python manage.py syncdb
~~~
When asked, create an admin user.

Finally, run the server locally to make sure that the app is working:
~~~bash
$ python manage.py runserver
~~~

Now you can access [/polls](http://localhost:8000/polls/) or [/admin](http://localhost:8000/admin/) to test the app locally. It's time to prepare it for deployment on the platform.


## Creating the app on cloudControl

Choose a unique name (from now on called APP_NAME) for your application and create
it on the platform. Be sure that you're inside of the git repository when
running the command:
~~~bash
$ cctrlapp APP_NAME create python
~~~


### Managing dependencies
The [python buildpack] uses [pip] to manage dependencies.

Create a `requirements.txt` file with the following content:
~~~
Django==1.4.3
~~~

Install all the necessary dependencies via `sudo pip install -r requirements.txt` command.


### Production server

In a production environment you normally don't want to use the development server.
In this tutorial you are going to use [gunicorn] as the production server.

To do so, add the following line to the `requirements.txt` file:
~~~
gunicorn==0.17.2
~~~

And finally add `gunicorn` to the list of installed applications (`INSTALLED_APPS`
in `mysite/settings.py`).


### Defining the process type

cloudControl uses a [Procfile] to know how to start your processes.

Create a file called `Procfile` with the following content:
~~~
web: python manage.py run_gunicorn -b 0.0.0.0:$PORT
~~~

This file specifies a _web_ command that will be executed to start the server
once the app is deployed.


### Production database

Now it's time to configure the production database.

The application currently uses SQLite as the database in all environments, even the production one.
It is not possible to use a SQLite database on cloudControl because the filesystem is [not persistent][filesystem].

To use a database, you should choose an Add-on from [the Data Storage category][data-storage-addons].

Let's use the [Shared MySQL Add-on][mysqls] with the free option.
To add this Add-on, run the following command:
~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.free
~~~

Now modify the `requirements.txt` to its final version:
~~~
Django==1.4.3
gunicorn==0.17.2
MySQL-python==1.2.4
~~~
Don't forget to run the `sudo pip install -r requirements.txt` command again.

Next, modify the `mysite/settings.py` file to [get the MySQL credentials][get-conf] when running
on the platform. The required changes can be seen in the [respective commit in the example repository][db-commit].

Do not forget to commit the changes:
~~~bash
$ git add .
$ git commit -m 'Migrate to cloudControl'
~~~

As a final step, you can compare your working directory with the `migrated` branch
to be sure you didn't make any mistakes along the way:
~~~bash
$ git diff migrated
~~~

Now the app is ready to be deployed on the platform.
To do this, run the following commands:
~~~bash
$ git remote add cctrl ssh://APP_NAME@cloudcontrolled.com/repository.git
$ git push cctrl dev:master
$ cctrlapp APP_NAME/default deploy
~~~

Finally, prepare the database using the [Run command][ssh-session]:
~~~bash
$ cctrlapp APP_NAME/default run "python manage.py syncdb"
~~~

Congratulations, you should now be able to reach the app at APP_NAME.cloudcontrolled.com.

You can login to the admin console at APP_NAME.cloudcontrolled.com/admin and
look at the polls at APP_NAME.cloudcontrolled.com/polls.

For additional information take a look at [Django Notes][django-notes] and
other [python-specific documents][python-guides].


[django]: https://www.djangoproject.com/
[cloudControl]: http://www.cloudcontrol.com
[cloudControl-doc-user]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#user-accounts
[cloudControl-doc-cmdline]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#command-line-client-web-console-and-api
[procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[git]: https://help.github.com/articles/set-up-git
[filesystem]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#non-persistent-filesystem
[data-storage-addons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/
[mysqls]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs
[example-app]: https://github.com/cloudControl/python-django-example-app
[django-notes]: https://www.cloudcontrol.com/dev-center/Guides/Python/Django%20notes
[get-conf]: https://www.cloudcontrol.com/dev-center/Guides/Python/Read%20configuration
[Django tutorial]: https://docs.djangoproject.com/en/1.4/intro/tutorial01/
[python-guides]: https://www.cloudcontrol.com/dev-center/Guides/Python
[python buildpack]: https://github.com/cloudControl/buildpack-python
[pip]: http://www.pip-installer.org/
[gunicorn]: http://gunicorn.org/
[worker]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#scheduled-jobs-and-background-workers
[db-commit]: https://github.com/cloudControl/python-django-example-app/commit/983f45e46ce0707476cec167ea062e19adcb53c9
[ssh-session]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#secure-shell-ssh
