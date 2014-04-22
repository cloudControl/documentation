# Custom Config Add-on

The Custom Config Add-on allows you to add custom credentials to the standard
creds.json file provided for each of your deployments. This makes it possible
for you to keep your code in separate branches, each with their own configuration settings.

## Adding Configuration Settings

To add configuration settings, simply invoke the config command with the add
option, and append the desired `key` / `value` pairs.
~~~bash
$ exoapp APP_NAME/DEP_NAME config.add KEY=VALUE
~~~

This will automatically add the Config Add-on to your deployment.

Replace APP_NAME, DEP_NAME, KEY and VALUE with the desired values and they will
be added to your deployment's cred.json file.

To set multiple settings at once, simply append more than one `key` / `value` pair.
~~~bash
$ exoapp APP_NAME/DEP_NAME config.add KEY1=VALUE1 KEY2=VALUE2 [...]
~~~

Config parameters can be set using the format shown in first column of the following table. They are then stored in JSON format, as shown in the second column. Multiline arguments can be set using the `\n` escape character.

CLI parameter|JSON representation
---|---
key=value|{"key": "value"}
key="multiline\nvalue"|{"key": "multiline\\\\nvalue"}
key=path_to_file.txt|{"key": "content\nof\nfile\n"}
key|{"key": true}

Note: It is recommended to use double quotes `"` for setting multispace or
multiline values to make sure they are stored properly.

## Listing Configuration Settings

You can list the existing set of configuration settings by invoking the config
command:
~~~bash
$ exoapp APP_NAME/DEP_NAME config
KEY1=VALUE1
KEY2=VALUE2
~~~

To show the value of a specific key, simply append the desired key name:
~~~bash
$ exoapp APP_NAME/DEP_NAME config KEY
VALUE
~~~

## Updating Configuration Settings

To add or remove settings to your custom config, simply use the `add` or
`remove` option of the config command and append the parameters you need.
~~~bash
$ exoapp APP_NAME/DEP_NAME config.add [-f|--force] NEW_PARAM=NEW_VALUE [...]
$ exoapp APP_NAME/DEP_NAME config.remove PARAM1 PARAM2 [...]
~~~

Updating the existing settings is also possible using the `add` command. This
will require your confirmation unless you use the `-f` or `--force` flag after
the add command.

## Removing the Config Add-on

Deleting all the existing configuration settings from a deployment can be done by
removing the Add-on.
~~~bash
$ exoapp APP_NAME/DEP_NAME addon.remove config.free
~~~

This will remove all the custom configuration settings.

