# IronMQ

IronMQ is an elastic message queue for managing data and event flow within cloud applications and between systems.

## Adding IronMQ

IronMQ can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add iron_mq.OPTION
~~

## Upgrade IronMQ

Upgrading to another version of IronMQ is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade iron_mq.OPTION_OLD iron_mq.OPTION_NEW 
~~~

## Downgrade IronMQ

Downgrading to another version of IronMQ is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade iron_mq.OPTION_OLD iron_mq.OPTION_NEW 
~~~

## Removing IronMQ

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove iron_mq.OPTION
~~~

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-on-credentials) in the general documentation.

The JSON file has the following structure:

~~~
{
   "IRON_MQ":{
      "IRON_MQ_TOKEN":"13371234ASDFasdffdsaqwetrt12334",
      "IRON_MQ_PROJECT_ID":"123345678899900asdf1233"
   }
}
~~~

## IronMQ Code Examples

You will find examples on how to use IronMQ within your app at [Github](https://github.com/iron-io/iron_mq_php) with support for Ruby, PHP, Python, and more.

