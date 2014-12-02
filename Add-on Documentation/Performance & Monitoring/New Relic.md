# New Relic

New Relic is an on-demand performance management solution for PHP web applications. The New Relic plugin is seamlesly integrated in the dotCloud platform, enabling immediate and automatic access to comprehensive capabilities for monitoring, troubleshooting and tuning web applications. A complete list of features is displayed on [New Relic's feature overview](http://www.newrelic.com/web-app-monitoring-features.html).

## Adding New Relic

The New Relic add-on can be added to every deployment with:


~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add newrelic.OPTION
~~~

When added, NewRelic automatically creates a new account and login configuration including an
access token. You can access NewRelic for your deployment in the web console via Single Sign-On (SSO).
Navigate to the specific deployment, choose "Add-Ons" tab, click on "Settings" link and "Login to dashboard".
It might take a little while until New Relic has collected enough data to show you the first statistics.

With Single Sign-On, you are connecting to NewRelic as the deployment user.
If you prefer to login directly with your personal account, or you need to add more users,
simply create additional credentials once you have accessed NewRelic via SSO.

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

dotCloud takes care of the whole installation and settings process. Therefore, some settings can't be edited by you. However, you can optionally use a set of PHP functions for influencing its data collection and recording for an even better integration. A detailed description of the available methods that can be used is listed in [New Relic's PHP documentation](https://newrelic.com/docs/php/the-php-api).

