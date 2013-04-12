# New Relic

New Relic is an on-demand performance management solution for PHP web applications. The New Relic plugin is seamlesly integrated in the cloudControl platform, enabling immediate and automatic access to comprehensive capabilities for monitoring, troubleshooting and tuning web applications. A complete list of features is displayed on [New Relic's feature overview](http://www.newrelic.com/web-app-monitoring-features.html).

## Adding New Relic

The New Relic add-on can be added to every deployment with:


~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add newrelic.OPTION
~~~

When added, cloudControl automatically creates a new user account with your email adress at New Relic. You will be notified by New Relic via email and can log on to New Relic's console to monitor your deployment's performance. It might take a little while until New Relic has collected enough data to show you the first statistics.

## Upgrade New Relic

Upgrading to another version of New Relic is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade newrelic.OPTION_OLD newrelic.OPTION_NEW 
~~~

## Downgrade New Relic

Downgrading to another version of New Relic is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade newrelic.OPTION_OLD newrelic.OPTION_NEW 
~~~
## Removing New Relic

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove newrelic.OPTION
~~~

## PHP API

cloudControl takes care of the whole installation and settings process. Therefore, some settings can't be edited by you. However, you can optionally use a set of PHP functions for influencing its data collection and recording for an even better integration. A detailed description of the available methods that can be used is listed in [New Relic's PHP documentation](https://newrelic.com/docs/php/the-php-api).

