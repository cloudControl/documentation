# Notes for Django Developers
This document contains information for Django programmers deploying their applications on [cloudControl].

## Managing Dependencies
The [python buildpack] uses [pip] to manage dependencies. Specify your dependencies in a file called `requirements.txt` in the project root directory.

## Defining the Process Type
cloudControl uses a [Procfile][procfile] to know how to start your processes. This file specifies a _web_ command that will be executed to start the server once the app is deployed. It optionally also specifies [worker] types that can be used to execute long running tasks.

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

## Serving Static Files

Since there is no webserver , i.e. Apache or Nginx running inside the container
to serve the static files. We recommend to use[dj-static][dj-static] or [whitenoise][whitenoise] in combination with a WSGI
server like Gunicorn. Another approach is using a CDN, e.g. Amazon S3 to serve static files for
your application with [django-storage][django-storage].
For further details, checkout the official package documentations.

The following code snippets showing how to use dj-static or whitenoise:

### dj-static

- Add the following line to the requirement.txt:

~~~python
dj-static==0.0.6
~~~

- Modify your settings.py:

~~~python
STATIC_ROOT = 'staticfiles'
STATIC_URL = '/static/'
~~~

- Update your wsgi.py with the following line:

~~~python
from django.core.wsgi import get_wsgi_application
from dj_static import Cling

application = Cling(get_wsgi_application())
~~~

Please make sure that your files getting served properly, by adding an empty
dummy text file in the static folder of your repository, for testing purposes.


### whitenoise

- Add the following line to the requirement.txt:

~~~python
whitenoise==2.0.3
~~~

- Modify your settings.py:

~~~python
STATICFILES_STORAGE = 'whitenoise.django.GzipManifestStaticFilesStorage'
~~~

- Update your wsgi.py with the following line:

~~~python
from django.core.wsgi import get_wsgi_application
from whitenoise.django import DjangoWhiteNoise

application = get_wsgi_application()
application = DjangoWhiteNoise(application)
~~~



[SSH-session]: https://www.cloudcontrol.com/dev-center/platform-documentation#secure-shell-(ssh)
[python buildpack]: https://github.com/cloudControl/buildpack-python
[pip]: http://www.pip-installer.org/
[procfile]: https://www.cloudcontrol.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[messaging-addons]: https://www.cloudcontrol.com/dev-center/add-on-documentation#messaging-mobile
[data-storage-addons]: https://www.cloudcontrol.com/dev-center/add-on-documentation#data-storage
[add-on-credentials]: https://www.cloudcontrol.com/dev-center/guides/python/add-on-credentials
[cloudControl]: https://www.cloudcontrol.com/
[worker]: https://www.cloudcontrol.com/dev-center/platform-documentation#scheduled-jobs-and-background-workers
[dj-static]: https://github.com/kennethreitz/dj-static
[whitenoise]: https://warehouse.python.org/project/whitenoise/
[django-storage]: https://django-storages.readthedocs.org/en/latest/
