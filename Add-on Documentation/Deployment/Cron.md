# Cron Add-on

## What are cronjobs?

On UNIX systems [cronjobs](http://en.wikipedia.org/wiki/Cron) are commands that
are periodically executed. On dotCloud however, there is no one node that
the cronjob can run on. Therefore cronjobs on dotCloud are periodical calls
to a URL you specify.

## How does it work?

The Cron Add-on allows you to call an URL in a specific interval, e.g. daily or
hourly. When you add an hourly cron at 2.45pm, the next call will run at
3.45pm. For the daily Cron it would reoccur the next day at 2.45pm. The Cron
Add-on does not guarantee a URL is only called once per interval.

Cronjobs are regular requests against your app and are subject to the same 120s
timelimit.

If you need more control over when and how often tasks are run and/or have
tasks that take longer than 120 seconds we recommend using the
[Worker](https://next.dotcloud.com/add-ons/worker). There is also
[CloudAMQP](https://next.dotcloud.com/add-ons/cloudamqp) message queue
available that can be used to dispatch tasks to the workers.

## Adding the Cron Add-on

Before you can add a Cron job, the Add-on itself has to be added:

~~~
$ dcapp APP_NAME/DEP_NAME addon.add cron.OPTION
~~~

As always the different options are listed on the [Cron
Add-on](https://next.dotcloud.com/add-ons/cron) page.

## Adding a URL for the Cron job

To call an URL with the specific interval you write it as the parameter:

~~~
# for the default deployment
$ dcapp APP_NAME/DEP_NAME cron.add http[s]://[user:password@]APP_NAME.cloudcontrolled.com
# for any additional deployment
$ dcapp APP_NAME/DEP_NAME cron.add http[s]://[user:password@]DEP_NAME.APP_NAME.cloudcontrolled.com
~~~

You can only add cron jobs calling a verified alias of the deployment. It is
recommended to use https when sending credentials. Please, note that URL must be fully encoded.

## List Cron overview

Get an overview of all your Cron jobs:

~~~
$ dcapp APP_NAME/DEP_NAME cron
~~~

## Cron details

Get the details of a specific Cron job:

~~~
$ dcapp APP_NAME/DEP_NAME cron CRON_ID
Cronjob
 job_id   : jobkqy7rdmg
 url      : http://APP_NAME.cloudcontrolled.com
 next_run : 2011-05-09 19:39:39
 created  : 2011-05-05 19:39:39
 modified : 2011-05-05 19:39:39
~~~

## Removing a Cron job:

You can remove a Cron job by the job_id

~~~
$ dcapp APP_NAME/DEP_NAME cron.remove JOB_ID
~~~

## Upgrading / downgrading the Cron addon

In order to switch from a daily to hourly Cron or vice versa, use the up- or
downgrade function

~~~
$ dcapp APP_NAME/DEP_NAME addon.upgrade cron.free cron.hourly
~~~

or

~~~
$ dcapp APP_NAME/DEP_NAME addon.downgrade cron.hourly cron.free
~~~

Crons added with the free Add-on will stay daily and crons added with the
hourly Add-on will stay hourly.

## Removing the Cron Add-on

Removing the Add-on itself can be done with:

~~~
$ dcapp APP_NAME/DEP_NAME addon.remove cron.OPTION
~~~

Please note: Removing the Add-on will not automatically remove all Cron jobs.

