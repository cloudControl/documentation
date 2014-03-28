# Exceptiontrap

Exceptiontrap is an Add-on for providing exception and error tracking to your Ruby on Rails and PHP applications.

Exceptiontrap catches occuring errors and exceptions in your application and notifies you about them in real-time. The errors are sent to the Exceptiontrap service that groups the incoming errors automatically and provides you with all the information (request params, environment variables, stacktrace, etc.) you need to resolve them.

For example: If your email provider is down and your application crashes while users are trying to sign up, Exceptiontrap informs you of it immediately.

## Adding Exceptiontrap

The Exceptiontrap Add-on can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add exceptiontrap.OPTION
~~~

When added, cloudControl automatically creates a new user account with your email address at New Relic. You will be notified by New Relic via email and can log on to New Relic's console to monitor your deployment's performance. It might take a little while until New Relic has collected enough data to show you the first statistics.

## Using with Rails 2.3+, Rails 3 and Rails 4

You can find the documentation on our [Exceptiontrap Gem](https://github.com/itmLABS/exceptiontrap) GitHub page. The API key is already set in the cloudControl environment. So you can just use "cloudcontrol" as the API key string while generating the Exceptiontrap config file.

## Using with PHP, Zend Framework & other frameworks

You can find the documentation on our [Exceptiontrap Library](https://github.com/itmLABS/exceptiontrap-php) GitHub page.
The easiest way to get the API key automatically set by cloudControl and our library is to set the variable `SET_ENV_VARS` to `true` for your application using the `Custom Config Add-on`, which is desribed [here](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#add-ons) under "Add-on Credentials".

If you don't want to do this, use the following code snippet to "manually" extract the Exceptiontrap API key from the cloudControl environment variables file.

~~~php
<?php
# This reads the exceptiontrap api key from the cloudcontrol credentials file
if ($cred_file = file_get_contents(getenv('CRED_FILE'), false)) {
  $credentials = json_decode($cred_file, true);
  $exceptiontrap_api_key = isset($credentials['EXCEPTIONTRAP']) ? $credentials['EXCEPTIONTRAP']['EXCEPTIONTRAP_API_KEY'] : '';
}

# Just use the $exceptiontrap_api_key variable now to set up the exceptiontrap library.
?>
~~~

## Removing Exceptiontrap

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove exceptiontrap.OPTION
~~~

## Further Information

Visit [Exceptiontrap](https://exceptiontrap.com) or [write us a message](mailto:info@exceptiontrap.com)
