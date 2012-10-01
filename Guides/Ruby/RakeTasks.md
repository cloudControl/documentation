# Executing rake tasks

In this tutorial we are going to show you how to execute rake tasks.

To run rake tasks we are going to use worker addon. More information on how to setup worker [addon](https://www.cloudcontrol.com/documentation/add-ons/worker).

First we need to create the helper script. We will name it execute_rake_task.rb and put it in lib/ directory. Here is the script:
~~~ruby
# lib/execute_rake_task.rb
if ARGV.size != 1
  puts "[ERROR] expects exactly one string argument"
  exit
end

%x/ rake #{ ARGV.first } /
~~~

This script just executes task given as an argument.

Next we need to add another line to the Procfile that just executes previously created script:

    rake: ruby lib/execute_rake_task.rb

Finally, to run "db:migrate" task, just run the following command in the command line:

    $ cctrlapp APPLICATION_NAME/DEPLOYMENT_NAME worker.add rake "db:migrate"

And check logs to see if there is some output:

    $ cctrlapp APPLICATION_NAME/DEPLOYMENT_NAME log worker
