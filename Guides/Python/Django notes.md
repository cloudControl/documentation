# Notes for Django developers

This document contains information for Django programmers deploying their applications on [cloudControl].

## Managing dependencies
The [python buildpack] uses [pip] to manage dependencies. Specify your dependencies in
a file called `requirements.txt` in the project root directory.

## Defining the process type

cloudControl uses a [Procfile] to know how to start your processes.
This file specifies a _web_ command that will be executed to start the server
once the app is deployed.
It optionally also specifies [worker] types that can be used to execute long running tasks.

A `Procfile` for a Django app using gunicorn as web server might look like this:
~~~
web: python manage.py run_gunicorn --config gunicorn_config.py -b 0.0.0.0:$PORT
manage: python manage.py
~~~

## Executing management tasks
Use the [Run command][ssh-session] to execute tasks like `syncdb`. This starts an interactive SSH
session in the same environment that your app runs in.
~~~bash
cctrlapp APP_NAME/DEPLOYMENT_NAME run "python manage.py syncdb"
~~~

## Databases
To use a database, you should choose an Add-on from [the Data Storage category][data-storage-addons].
To get the credentials of your database, refer to the [Read Configuration][read-config] article.

## Email
You can't use a local SMTP server, instead choose one of our [email Add-ons][messaging-addons].

[ssh-session]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#secure-shell-ssh
[python buildpack]: https://github.com/cloudControl/buildpack-python
[pip]: http://www.pip-installer.org/
[procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[messaging-addons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Messaging%20&%20Mobile/
[data-storage-addons]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/
[read-config]: https://www.cloudcontrol.com/dev-center/Guides/Python/Read%20configuration
[cloudControl]: https://www.cloudcontrol.com/
