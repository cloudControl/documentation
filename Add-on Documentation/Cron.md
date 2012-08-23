# Cron Add-on

## What are cronjobs?

On UNIX systems [cronjobs](http://en.wikipedia.org/wiki/Cron) are commands that are periodically executed. On cloudControl however, there is no one node that the cronjob can run on. Therefore cronjobs on cloudControl are periodical calls to a URL you specify.

## How does it work?

The Cron add-on allows you to call an URL in a specific interval, e.g. daily or hourly. When you add an hourly cron at 2.45pm, the next call will run at 3.45pm. For the daily Cron it would reoccur the next day at 2.45pm. The Cron add-on does not guarantee a URL is only called once per interval.

## Adding the Cron add-on

Before you can add a Cron job, the add-on itself has to be added:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add cron.OPTION
~~~

As always the different options are listed on the [Cron add-on](https://www.cloudcontrol.com/add-ons/cron) page.

## Adding a url for the Cron job

To call an URL with the specific interval you write it as the parameter:

~~~
$ cctrlapp APP_NAME/DEP_NAME cron.add http[s]://[user:password@]APP_NAME.cloudcontrolled.com
~~~

You can only add cron jobs calling a verified alias of the deployment.

## List Cron overview

Get an overview of all your Cron jobs:

~~~
$ cctrlapp APP_NAME/DEP_NAME cron
~~~

## Cron details

Get the details of a specific Cron job:

~~~
$ cctrlapp APP_NAME/DEP_NAME cron CRON_ID
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
$ cctrlapp APP_NAME/DEP_NAME cron.remove JOB_ID 
~~~

## Upgrading / downgrading the Cron addon

In order to switch from a daily to hourly Cron or vice versa, use the up- or downgrade function

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade cron.free cron.hourly
~~~

or

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade cron.hourly cron.free
~~~

Crons added with the free add-on will stay daily and crons added with the hourly add-on will stay hourly.

## Removing the Cron add-on

Removing the add-on itself can be done with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove cron.OPTION
~~~

Please note: Removing the add-on will not automatically remove all Cron jobs.

