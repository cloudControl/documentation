# Migrating the dotCloud environment to Next dotCloud

## What is the Environment File in dotCloud?

When you create an application on dotCloud, a file named `environment.json`
is created in the home directory of each of your services.

This file contains a JSON-formatted dictionary with most of the configuration information
of the services in your application. You can use it to retrieve data or
credentials by reading the file.

## How does it work on Next dotCloud?

Next dotCloud stores config variables for each deployment in a single file called `creds.json`. It looks like this:

~~~json
{
    "CONFIG":{
        "CONFIG_VARS": {
            "KEY": "VALUE"
        }
    },
    "ADDON_NAME": {
        "ADDON_KEY1": "ADDON_VAR1",
        "ADDON_KEY2": "ADDON_VAR2",
        ...
        "ADDON_KEYN": "ADDON_VARN"
    }
}
~~~

You can obtain the path to this file by reading the `CRED_FILE` environment variable. The file consists
of two main elements: user-defined environment variables and Add-on (third party services) configuration
variables. User-defined variables are always stored under the `CONFIG_VARS` element. You can add variables using the [Custom Config Add-on](http://next.dotcloud.com/add-ons/config):

~~~bash
$ dcapp APP_NAME/DEP_NAME config.add KEY1=VALUE1 KEY2=VALUE2 ...
~~~

You can easily export all content in the `creds.json` file to the system environment variables by setting `SET_ENV_VARS`:

~~~bash
$ dcapp APP_NAME/DEP_NAME config.add SET_ENV_VARS
~~~

Each change to the config variables, either user-defined or forced through an Add-on upgrade, will prompt a redeploy with the most current config so your container always has up-to-date configuration.

Environment variables set for the deployment are also available in all workers belonging
to this deployment.

If you want to define specific variables for web deployment and workers you can always export
them into the Procfile with commands like these:

~~~
web: export WEB_SPECIFIC_VAR1=val1 WEB_SPECIFIC_VAR2=val2; start_web_cmd.sh
worker1: export WORKER1_SPECIFIC_VAR1=val1 WORKER1_SPECIFIC_VAR2=val2; start_worker1_cmd.sh
worker2: export WORKER2_SPECIFIC_VAR1=val1 WORKER2_SPECIFIC_VAR2=val2; start_worker2_cmd.sh
~~~

Please keep in mind that these variables will not be present in `creds.json` file.

## Migration

### Services & Add-ons credentials

* If you're using a dotCloud service like MySQL, you'll have an environment file that looks like this:

    ~~~json
    {
       "DOTCLOUD_ENVIRONMENT": "default",
       "DOTCLOUD_DB_MYSQL_LOGIN": "root",
       "DOTCLOUD_DB_MYSQL_URL": "mysql://root:pass@7a96f954.dotcloud.com:7780",
       "DOTCLOUD_DB_MYSQL_PASSWORD": "B61J14)]U4^L}.najnyE",
       "DOTCLOUD_PROJECT": "demodcapp",
       "DOTCLOUD_SERVICE_NAME": "www",
       "DOTCLOUD_DB_MYSQL_PORT": "7780",
       "DOTCLOUD_DB_MYSQL_HOST": "7a96f954.dotcloud.com",
       "DOTCLOUD_SERVICE_ID": "0"
    }
    ~~~

    You are probably reading the variables in a code snippet that looks like this:

    ~~~python
    import json

    with open('/home/dotcloud/environment.json') as f:
        env = json.load(f)

    print 'MySQL Host: {0}'.format(env['DOTCLOUD_DB_MYSQL_HOST'])
    ~~~

* On Next dotCloud, you can add a [MySQLd Add-on](http://next.dotcloud.com/add-ons/mysqld) (or alternately MySQLS) with:

    ~~~bash
    $ dcapp APP_NAME/DEP_NAME addon.add mysqld.OPTION
    ~~~

    Then you'll have a `creds.json` file that looks like this:

    ~~~json
    {
        "MYSQLD": {
            "MYSQLD_DATABASE": "...",
            "MYSQLD_HOST": "...",
            "MYSQLD_PASSWORD": "...",
            "MYSQLD_PORT": "3306",
            "MYSQLD_URL": "...",
            "MYSQLD_USER": "..."
        }
    }
    ~~~

    And you can read the variables with:

    ~~~python
    import os
    import json

    with open(os.environ['CRED_FILE']) as f:
        creds = json.load(f)

    print 'MySQL Host: {0}'.format(creds['MYSQLD']['MYSQLD_HOST'])
    ~~~

### Custom configuration variables

* Add all the custom configuration variables (those not injected by dotCloud) from all your custom services and processes to your deployment using the `Custom Config Add-on`:

    ~~~bash
    # List dotCloud env variables
    $ dotcloud env list
    KEY1=VALUE1
    KEY2=VALUE2
    ...

    # Add them to your deployment in Next dotCloud
    $ dcapp APP_NAME/DEP_NAME config.add KEY1=VALUE1 KEY2=VALUE2 ... SET_ENV_VARS
    ~~~

    Then you'll have a `creds.json` file that looks like this:

    ~~~json
    {
        "CONFIG":{
            "CONFIG_VARS": {
                "KEY1": "VALUE1",
                "KEY2": "VALUE2"
            }
        }
    }
    ~~~

    You can then read the variables by opening the file or by reading them directly from the environment:

    ~~~python
    import os
    import json

    with open(os.environ['CRED_FILE']) as f:
        creds = json.load(f)

    print 'Key 1: {0}'.format(creds['CONFIG']['CONFIG_VARS']['KEY1'])
    print 'Key 2: {0}'.format(os.getenv('KEY2'))
    ~~~
