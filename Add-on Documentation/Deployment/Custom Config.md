# Custom Config Add-on

The custom config Add-on allows you to add custom credentials to the standard creds.json file provided for each of your deployments. This allows you to keep code in branches and additional config parameters needed for the deployment seperated from each other.

An example for such a parameter could be the Amazon S3 credentials. You would probably want to use different ones for production and development and the custom config Add-on allows you to do this.

## Setting Config Parameters

To set config parameters simply append them to the end of the addon.add command.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add config.free --PARAMETER_NAME=PARAMETER_VALUE
~~~

Replace APP_NAME, DEP_NAME, PARAMETER_NAME and PARAMETER_VALUE with the desired values and they will be added to your deployment's cred.json file.

To set multiple values at once simply append more than one parameter.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add config.free --PARAM1=VALUE1 --PARAM2=VALUE2 [...]
~~~

Config parameters are accepted in three formats and result in the respective JSON format:


<table>
<tbody>
    <tr>
        <td>CLI parameter</td>
        <td>JSON representation</td>
    </tr>
    <tr>
        <td>--key=value</td>
        <td>{"key": "value"}</td>
    </tr>
    <tr>
        <td>--key value</td>
        <td>{"key": "value"}</td>
    </tr>
    <tr>
        <td>--key</td>
        <td>{"key": true}</td>
    </tr>
</tbody>
</table>

### Setting the content from a file

For the value a filename can be used. In that case the whole content of the file
is read and assigned to specified config variable.

Let's say there exists a file `test.txt` with the following content:
~~~
This is a test file
with multiple lines.
~~~

To add the file's content to a variable SOME_VAR, run the following command:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add config.free --SOME_VAR=test.txt
~~~


## Listing Config Parameters

You can list currently set config parameters by calling

~~~
$ cctrlapp APP_NAME/DEP_NAME addon config.free
Addon                    : config.free
   
 Settings
   CONFIG_VARS              : {"PARAM2": "VALUE2", "PARAM1": "VALUE1"}
~~~

## Removing Config Parameters

Removing custom config is as easy as removing the Add-on.

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove config.free
~~~

This will remove all custom config values.

## Adding custom syslog logging

Config addon can be used to specify an additional endpoint where error and worker logs will be sent.
This is done by setting the config variable "RSYSLOG_REMOTE". The content of this variable will be appended to the [rsyslog](http://www.rsyslog.com/) configuration used for error and worker logs. The content should contain valid rsyslog configuration and can span multiple lines.

E.g. to add manually forwarding of logs to [Logentries](https://logentries.com/) over TLS connection, create a temp file with the following content:
~~~
$DefaultNetstreamDriverCAFile URI_TO_LOGENTRIES_CERTIFICATE
$ActionSendStreamDriver gtls
$ActionSendStreamDriverMode 1
$ActionSendStreamDriverAuthMode x509/name
$template LogentriesFormat, "LOGENTRIES_TOKEN %syslogtag%%msg%\n"
*.* @@api.logentries.com:20000;LogentriesFormat
~~~
where "URI_TO_LOGENTRIES_CERTIFICATE" and "LOGENTRIES_TOKEN" should be replaced with the proper values.

Use that file's name (let's say it's named `logentries.cfg`) as a value of "RSYSLOG_REMOTE" config variable:
~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add config.free --RSYSLOG_REMOTE=logentries.cfg
~~~

From now on all new logs should be visible in logentries.
