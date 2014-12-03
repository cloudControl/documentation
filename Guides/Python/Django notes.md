# Notes for Django Developers
This document contains information for Django programmers deploying their applications on [dotCloud].

## Managing Dependencies
The [python buildpack] uses [pip] to manage dependencies. Specify your dependencies in a file called `requirements.txt` in the project root directory.

## Defining the Process Type
dotCloud uses a [Procfile][procfile] to know how to start your processes. This file specifies a _web_ command that will be executed to start the server once the app is deployed. It optionally also specifies [worker] types that can be used to execute long running tasks.

The `Procfile` for a Django app using gunicorn as web server can look like this:
~~~
web: python manage.py run_gunicorn --config gunicorn_config.py -b 0.0.0.0:$PORT
manage: python manage.py
~~~

## Executing Management Tasks
Use the `run` command to execute tasks like `syncdb`. This starts an interactive [SSH-session].
~~~bash
cctrlapp APP_NAME/DEP_NAME run "python manage.py syncdb"
~~~

## Databases
To use a database, you should choose an Add-on from [the Data Storage category][data-storage-addons]. To get the credentials of your database, refer to the [Add-on credentials][add-on-credentials] article.

## Email
You can't use a local SMTP server, instead choose one of our [email Add-ons][messaging-addons].

[SSH-session]: https://next.dotcloud.com/dev-center/platform-documentation#secure-shell-ssh
[python buildpack]: https://github.com/cloudControl/buildpack-python
[pip]: http://www.pip-installer.org/
[procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[messaging-addons]: https://next.dotcloud.com/dev-center/add-on-documentation/messaging-&-mobile/
[data-storage-addons]: https://next.dotcloud.com/dev-center/add-on-documentation/data-storage/
[add-on-credentials]: https://next.dotcloud.com/dev-center/guides/python/add-on-credentials
[dotCloud]: https://next.dotcloud.com/
[worker]: https://next.dotcloud.com/dev-center/platform-documentation#scheduled-jobs-and-background-workers
