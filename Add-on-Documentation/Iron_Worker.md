# IronWorker

IronWorker is a fully featured worker system that runs elastically on the cloud. Massive scale computing with no servers.

## Adding IronWorker

IronWorker can be added to every deployment with:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.add iron_worker.OPTION
~~~

## Upgrade IronWorker

Upgrading to another version of IronWorker is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.upgrade iron_worker.OPTION_OLD iron_worker.OPTION_NEW 
~~~

## Downgrade IronWorker

Downgrading to another version of IronWorker is easily done:

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.downgrade iron_worker.OPTION_OLD iron_worker.OPTION_NEW 
~~~

## Removing IronWorker

~~~
$ cctrlapp APP_NAME/DEP_NAME addon.remove iron_worker.OPTION
~~~

## Internal access credentials

It's recommended to the read database credentials from the creds.json file. The location of the file is available in the `CRED_FILE` environment variable. Reading the credentials from the creds.json file ensures your app is always using the correct credentials. For detailed instructions on how to use the creds.json file please refer to the section about [Add-on Credentials](https://www.cloudcontrol.com/dev-center/platform-documentation#add-ons) in the general documentation.

The JSON file has the following structure:

~~~
{
   "IRON_WORKER":{
      "IRON_WORKER_TOKEN":"AsdfASDFSasddffassddfssd",
      "IRON_WORKER_PROJECT_ID":"1337asdf1337ASDF1337"
   }
}
~~~

## IronWorker Code Examples

You will find examples on how to use IronWorker within your app at [Github](https://github.com/iron-io/iron_worker_examples/tree/master/php) with support for Ruby, PHP, Python, and more.

