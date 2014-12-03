# CloudAMQP (Beta)

CloudAMQP is a hosted RabbitMQ service, with high availability and blazing performance.

## Adding or removing the CloudAMQP Add-on

The Add-on comes in different sizes and prices. It can be added by executing the command addon.add:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add cloudamqp.OPTION
~~~
".option" represents the plan size, e.g. cloudamqp.lemur

## Upgrade the CloudAMQP Add-on

Upgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade cloudamqp.OPTION_OLD cloudamqp.OPTION_NEW
~~~

## Downgrade the CloudAMQP Add-on

Downgrading to another option can easily be done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade cloudamqp.OPTION_OLD cloudamqp.OPTION_NEW
~~~

## Removing the CloudAMQP Add-on

Similarily, an Add-on can also be removed from the deployment easily. The costs only apply for the time the Add-on was active:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove cloudamqp.OPTION
~~~

## Add-on credentials

It's recommended to read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://next.dotcloud.com/dev-center/platform-documentation#add-ons) in the general documentation.

## PHP AMQP Example Application

For further information on how to use CloudAMQP in your PHP app, please refer to their documentation on [Github](https://github.com/cloudamqp/php-amqp-example).

