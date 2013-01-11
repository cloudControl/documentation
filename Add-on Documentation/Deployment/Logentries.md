# Logentries

[Logentries](https://logentries.com) provides logs collection, analysis, storage and presentation in a professional and meaningful way.

## Adding Logentries
The Logentries Add-On can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add logentries.PLAN
~~~

When added, Logentries automatically creates a new account and log configuration including access token. After Add-on creation all stdout, stderr and syslog output within the container will be available in Logentries. You can access Logentries for your deployment within the [web console](https://console.cloudcontrolled.com) (go to the specific deployment, choose "Add-Ons" tab and click Logentries login).

## Removing Logentries

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove logentries.PLAN
~~~

## Internal access credentials

You can view Logentries token via:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon logentries.PLAN
~~~

~~~
Addon                    : logentries.PLAN
   
 Settings
   LOGENTRIES_TOKEN         : 10af1a9e-112c-4075-88c9-e06412f0cd8e
~~~