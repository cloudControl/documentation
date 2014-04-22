# Notes for Django Developers
This document contains information for Django programmers deploying their applications on [exoscale].

## Managing Dependencies
The [python buildpack] uses [pip] to manage dependencies. Specify your dependencies in a file called `requirements.txt` in the project root directory.

## Defining the Process Type
exoscale uses a [Procfile][procfile] to know how to start your processes. This file specifies a _web_ command that will be executed to start the server once the app is deployed. It optionally also specifies [worker] types that can be used to execute long running tasks.

The `Procfile` for a Django app using gunicorn as web server can look like this:
~~~
web: python manage.py run_gunicorn --config gunicorn_config.py -b 0.0.0.0:$PORT
manage: python manage.py
~~~

## Executing Management Tasks
Use the `run` command to execute tasks like `syncdb`. This starts an interactive [SSH-session].
~~~bash
exoapp APP_NAME/DEP_NAME run "python manage.py syncdb"
~~~

## Databases
To use a database, have a look at the [Shared MySQL Add-on][Shared MySQL Add-on]. To get the credentials of your database, refer to the [Add-on credentials][add-on-credentials] article.

[SSH-session]: https://www.exoscale.ch/dev-center/Platform%20Documentation#secure-shell-ssh
[python buildpack]: https://github.com/cloudControl/buildpack-python
[pip]: http://www.pip-installer.org/
[procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[messaging-addons]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Messaging%20&%20Mobile/
[Shared MySQL Add-on]: ../../Add-on%20Documentation/Data%20Storage/MySQLs
[add-on-credentials]: https://www.exoscale.ch/dev-center/Guides/Python/Add-on%20credentials
[exoscale]: https://www.exoscale.ch/
[worker]: https://www.exoscale.ch/dev-center/Platform%20Documentation#scheduled-jobs-and-background-workers
