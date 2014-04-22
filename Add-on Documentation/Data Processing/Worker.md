# Worker Add-on

Workers are long running background processes. They are typically used for anything from sending emails to running heavy calculations or rebuilding caches in the background.

Each worker started via the Worker add-on runs in a seperate isolated container. The containers have exactly the same runtime environment defined by the stack chosen and the buildpack used and have the same access to all of the deployments add-ons.

## Adding the Worker Add-on

Before you can start a worker, add the add-on with the addon.add command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.add worker.single
~~~

## Starting a Worker

Workers can be started via the command line client's worker.add command.

To specify how to start a worker add a new line to your app's `Procfile` and then use that as the `WORKER_NAME`.

~~~
$ exoapp APP_NAME/DEP_NAME worker.add WORKER_NAME [WORKER_PARAMS]
~~~

Enclose multiple WORKER_PARAMS in double quotes.

~~~
$ exoapp APP_NAME/DEP_NAME worker.add WORKER_NAME "PARAM1 PARAM2 PARAM3"
~~~

## List Running Workers

To get a list of currently running workers use the worker command.

~~~
$ exoapp APP_NAME/DEP_NAME worker
Workers
 nr. wrk_id
   1 WRK_ID
~~~

You can also get all the worker details by appending the WRK_ID to the worker command.

~~~
$ exoapp APP_NAME/DEP_NAME worker WRK_ID
Worker
wrk_id   : WRK_ID
command  : WORKER_NAME
params   : "PARAM1 PARAM2 PARAM3"
~~~

## Stopping Workers

Workers can be either stopped via the command line client or by exiting the process with a zero exit code.

### Via Command Line

To stop a running worker via the command line use the worker.remove command.

~~~
$ exoapp APP_NAME/DEP_NAME worker.remove WRK_ID
~~~

To get the WRK_ID refer to the listing workers section above.

### Via Exit Codes

To stop a worker programatically use UNIX style exit codes. There are three distinct exit codes available.

 * exit (0); // Everything OK. Worker will be stopped.
 * exit (1); // Error. Worker will be restarted.
 * exit (2); // Error. Worker will be stopped.

For more details refer to the [PHP example](#php-worker-example) below.

## Worker log

As already explained in the [Logging section](https://www.exoscale.ch/dev-center/Platform%20Documentation#logging) all stdout and stderr output of workers is redirected to the worker log. To see the output in a tail -f like fashion use the log command.

~~~
$ exoapp APP_NAME/DEP_NAME log worker
[Fri Dec 17 13:39:41 2010] WRK_ID Started Worker (command: 'WORKER_NAME', parameter: 'PARAM1 PARAM2 PARAM3')
[Fri Dec 17 13:39:42 2010] WRK_ID Hello PARAM1 PARAM2 PARAM3
[...]
~~~

## Removing the Worker Add-on

To remove the Worker add-on use the addon.remove command.

~~~
$ exoapp APP_NAME/DEP_NAME addon.remove worker.single
~~~

## PHP Worker Example

The following example shows how to use the exit codes to restart or stop a worker.

~~~php
// read exit code parameter
$exitCode = isset($argv[1]) && (int)$argv[1] > 0 ? (int)$argv[1] : 0;
$steps = 5;

$counter = 1;
while(true) {
    print "step: " . ($counter) . PHP_EOL;
    if($counter == $steps){
        if($exitCode == 0) {
            print "All O.K. Exiting." . PHP_EOL;
        } else if ($exitCode == 2){
            print "An error occured. Exiting." . PHP_EOL;
        } else {
            print "An error occured. Restarting." .  PHP_EOL;
        }
        print "Exitcode: " . $exitCode . PHP_EOL . PHP_EOL;
        exit($exitCode);
    }
    sleep(1);
    $counter++;
}
~~~

Running this worker with the exit code set to 2 would result in the following output and the worker stopping itself.

~~~
$ exoapp APP_NAME/DEP_NAME worker.add WORKER_NAME 2
$ exoapp APP_NAME/DEP_NAME log worker
[Tue Apr 12 09:15:54 2011] WRK_ID Started Worker (command: 'WORKER_NAME', parameter: '2')
[Tue Apr 12 09:15:54 2011] WRK_ID step: 1
[Tue Apr 12 09:15:55 2011] WRK_ID step: 2
[Tue Apr 12 09:15:56 2011] WRK_ID step: 3
[Tue Apr 12 09:15:57 2011] WRK_ID step: 4
[Tue Apr 12 09:15:58 2011] WRK_ID step: 5
[Tue Apr 12 09:15:58 2011] WRK_ID An error occured. Exiting.
[...]
~~~

