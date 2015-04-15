# Deploying Celery on cloudControl
[Celery] is an asynchronous task queue/job queue based on distributed message passing. It is focused on real-time operation, but supports scheduling as well.

In this tutorial we're going to show you how to deploy an example Celery app using the [CloudAMQP Add-on] and a [Worker] on [cloudControl].

## The Example App Explained
First, lets clone the example code from Github. It is based on the official [first steps with Celery guide][celeryguide] and also includes [Flower] the Celery web interface.

~~~bash
$ git clone git://github.com/cloudControl/python-celery-example-app.git
$ cd python-celery-example-app
~~~

The code from the example repository is ready to be deployed. Lets still go through the different files and their purpose real quick.

### Dependency Tracking
The [Python buildpack] tracks dependencies via pip and the `requirements.txt` file. It needs to be placed in the root directory of your repository. Our example app requires both `celery` itself aswell as `flower` Celery's monitoring web app. The `requirements.txt` you cloned as part of the example app looks like this:

~~~pip
celery==3.0.15
flower==0.4.2
~~~

### Process Type Definition
cloudControl uses a [Procfile] to know how to start the app's processes.

The example code also already includes a file called `Procfile` at the top level of your repository. It looks like this:

~~~
web: celery flower --port=$PORT --broker=$CLOUDAMQP_URL --auth=$FLOWER_AUTH_EMAIL
worker: celery -A tasks worker --loglevel=info
~~~

We have specified two process types here. One called `web` to start the web interface and additionally one called `worker` used to start the actual Celery worker.

### The Celery Task

The task is copied from the official Celery tutorial and simply returns the sum of two numbers. It was adjusted to read the Add-on credentials from the runtime environment.

~~~python
import json
from os import getenv

from celery import Celery

# read credentials from runtime environment
amqp_url = getenv('CLOUDAMQP_URL')

celery = Celery('tasks', broker=amqp_url)


@celery.task
def add(x, y):
    return x + y
~~~

## Creating the App and Adding the Required Add-ons
Choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create python
~~~

As we chose to use AMQP as a broker, we add the CloudAMQP Add-on now.

~~~bash
$ cctrlapp APP_NAME/default addon.add cloudamqp.lemur
~~~

Since we are reading the AMQP URL for the broker from the environment in both, the `Procfile` and the Python code we have to enable providing Add-on credentials as environment variables which is disabled per default for Python apps.

We also set another environment variable called `FLOWER_AUTH_EMAIL` that is passed to the Flower web process for authentication purposes. Without this, the web interface would be public showing your secret AMQP credentials and allowing people to stop your workers.

~~~bash
$ cctrlapp APP_NAME/default addon.add config.free --SET_ENV_VARS --FLOWER_AUTH_EMAIL=YOUR_EMAIL_HERE
# seperate multiple emails by comma
~~~

This is it. The example code will now find all necessary credentials to connect to the AMQP service automatically in the runtime environment.

## Pushing and Deploying the App
Now lets push your code to the application's repository, which triggers the deployment image build process. Your output will look similiar to the following, although we have shortened it for the sake of readability in this guide.

The first push will take a couple of seconds, because it will download and compile and install a number of dependencies. So please be patient. Dependencies will be cached for future pushes significantly speeding up the process.

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 6, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (4/4), done.
Writing objects: 100% (6/6), 605 bytes, done.
Total 6 (delta 0), reused 0 (delta 0)

-----> Receiving push
-----> Preparing Python interpreter (2.7.2)
-----> Creating Virtualenv version 1.7.2
       New python executable in .heroku/venv/bin/python2.7
       Also creating executable in .heroku/venv/bin/python
       Installing distribute..................................................................................................................................................................................................done.
       Installing pip................done.
       Running virtualenv with interpreter /usr/bin/python2.7
-----> Activating virtualenv
-----> Installing dependencies using pip version 1.2.1

       [...]

       Successfully installed celery flower billiard python-dateutil kombu tornado anyjson amqp
       Cleaning up...
-----> Building image
-----> Uploading image (4.3M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least deploy the latest version of the app with the cctrlapp deploy command.

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

At this point you can see web interface at `http://APP_NAME.cloudcontrolled.com`. But it hasn't got any workers yet.

## Scaling Celery Workers
Scaling Celery workers on cloudControl is easy enough luckily. We have already defined how to run one in the `Procfile` earlier. So we can now go ahead and start the first one.

### Adding Workers

~~~bash
$ cctrlapp APP_NAME/default worker.add worker
# you can always list running workers like this
$ cctrlapp APP_NAME/default worker
# and also check the worker's log output with
$ cctrlapp APP_NAME/default log worker
[TIMESTAMP] WRK_ID Started worker (command: 'celery -A tasks worker --loglevel=info ', parameter: '')
[TIMESTAMP] WRK_ID
[TIMESTAMP] WRK_ID  -------------- celery@HOSTNAME v3.0.15 (Chiastic Slide)
[TIMESTAMP] WRK_ID ---- **** -----
[TIMESTAMP] WRK_ID --- * ***  * -- [Configuration]
[TIMESTAMP] WRK_ID -- * - **** --- . broker:      amqp://CLOUDAMQP_URL
[TIMESTAMP] WRK_ID - ** ---------- . app:         tasks:0x1357890
[TIMESTAMP] WRK_ID - ** ---------- . concurrency: 2 (processes)
[TIMESTAMP] WRK_ID - ** ---------- . events:      OFF (enable -E to monitor this worker)
[TIMESTAMP] WRK_ID - ** ----------
[TIMESTAMP] WRK_ID - *** --- * --- [Queues]
[TIMESTAMP] WRK_ID -- ******* ---- . celery:      exchange:celery(direct) binding:celery
[TIMESTAMP] WRK_ID --- ***** -----
[TIMESTAMP] WRK_ID
[TIMESTAMP] WRK_ID [Tasks]
[TIMESTAMP] WRK_ID   . tasks.add
[TIMESTAMP] WRK_ID [TIMESTAMP: WARNING/MainProcess] celery@HOSTNAME ready.
[TIMESTAMP] WRK_ID [TIMESTAMP: INFO/MainProcess] consumer: Connected to amqp://CLOUDAMQP_URL
[TIMESTAMP] WRK_ID [TIMESTAMP: INFO/MainProcess] Events enabled by remote.
~~~

If you refresh the web interface at `http://APP_NAME.cloudcontrolled.com` you should be able to see the worker now.

To handle more tasks simultaneously you can always just add more workers. (Please note that only the first worker is free, adding additional workers requires a billing account.)

~~~bash
# call worker.add to start additional workers one at a time
$ cctrlapp APP_NAME/default worker.add worker
~~~

### Removing Workers

To stop a worker you can stop it from the command line.

~~~bash
# use the worker list command to get the WRK_ID
$ cctrlapp APP_NAME/default worker
$ cctrlapp APP_NAME/default worker.remove WRK_ID
~~~

You can also stop the Celery worker from the web interface, which will also stop the container. Check the worker log output for details.

~~~bash
$ cctrlapp APP_NAME/default log worker
[...]
[TIMESTAMP] WRK_ID [TIMESTAMP: WARNING/MainProcess] Got shutdown from remote
[TIMESTAMP] WRK_ID Container stopped
[TIMESTAMP] WRK_ID Stopping worker by itself
[TIMESTAMP] WRK_ID Worker removed by itself
~~~

## Celery Commands

To run Celery commands use the cctrlapp run command. It will launch an additional container and connect you via SSH. You can then use the Celery commands in the an identical environment as the web interface and the workers itself.

~~~bash
$ cctrlapp APP_NAME/default run bash
Connecting...
USER@HOSTNAME:~/www$ celery --broker=$CLOUDAMQP_URL status
-> WORKER_HOSTNAME: OK

1 node online.
USER@HOSTNAME:~/www$ exit
Connection to ssh.cloudcontrolled.net closed.
~~~

## Résumé

This guide showed how to run both Flower aswell as a Celery worker on cloudControl by specifying the commands in the `Procfile` and how to connect to a AMQP broker provided by the CloudAMQP Add-on with the credentials provided in the app's runtime environment. Additionally we learned how we can use the cctrlapp run command to use the Celery command line tool.

[Celery]: http://celeryproject.org/
[CloudAMQP Add-on]: https://www.cloudcontrol.com/add-ons/cloudamqp
[Worker]: https://www.cloudcontrol.com/dev-center/platform-documentation#workers
[cloudControl]: http://www.cloudcontrol.com
[celeryguide]: http://docs.celeryproject.org/en/latest/getting-started/first-steps-with-celery.html
[Flower]: http://docs.celeryproject.org/en/latest/userguide/monitoring.html#flower-real-time-celery-web-monitor
[Python buildpack]: https://github.com/cloudControl/buildpack-python
[Procfile]: https://www.cloudcontrol.com/dev-center/platform-documentation#buildpacks-and-the-procfile
