# Notes for Django Developers
This document contains information for Django programmers deploying their applications on [CloudKilat].

## Managing Dependencies
The [python buildpack] uses [pip] to manage dependencies. Specify your dependencies in a file called `requirements.txt` in the project root directory.

## Defining the Process Type
CloudKilat uses a [Procfile][procfile] to know how to start your processes. This file specifies a _web_ command that will be executed to start the server once the app is deployed. It optionally also specifies [worker] types that can be used to execute long running tasks.

The `Procfile` for a Django app using gunicorn as web server can look like this:
~~~
web: python manage.py run_gunicorn --config gunicorn_config.py -b 0.0.0.0:$PORT
manage: python manage.py
~~~

## Executing Management Tasks
Use the `run` command to execute tasks like `syncdb`. This starts an interactive [SSH-session].
~~~bash
ironcliapp APP_NAME/DEP_NAME run "python manage.py syncdb"
~~~

## Databases
To use a database, have a look at the [Shared MySQL Add-on][Shared MySQL Add-on]. To get the credentials of your database, refer to the [Add-on credentials][add-on-credentials] article.

[SSH-session]: /Platform%20Documentation.md/#secure-shell-ssh
[python buildpack]: https://github.com/cloudControl/buildpack-python
[pip]: http://www.pip-installer.org/
[procfile]: /Platform%20Documentation.md/#buildpacks-and-the-procfile
[Shared MySQL Add-on]: /Add-on%20Documentation/Data%20Storage/MySQLs.md
[add-on-credentials]: /Guides/Python/Add-on%20credentials.md
[CloudKilat]: http://www.cloudkilat.com/
[worker]: /Add-on%20Documentation/Data%20Processing/Worker.md
