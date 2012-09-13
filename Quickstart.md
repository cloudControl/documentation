# cloudControl Quickstart

Get up and running in 5 minutes. It's easy to start with cloudControl.

1. [Using the web console](#using-the-web-console)
1. [Using the command line client](#using-the-command-line-client)

## Using the web console

### Set up your user account
* Install git ([quick Git tutorial](http://rogerdudler.github.com/git-guide/))
* If you don't have a SSH key: Generate one using this tutorial
* Sign up at https://console.cloudcontrolled.com
* Log in to https://console.cloudcontrolled.com
* Click on **Settings** in the left navgation bar 
* Paste your SSH key in the field labeled **Add new SSH Key** and press `enter`
### Create an example app
* In the web console, click on **Applications** in the left navigation bar
* Enter a name for a new application in the field **Application name** and press `enter`

Now you have created a fresh repository which runs on managed cloudControl nodes. By default, each deployment gets 128MB RAM on one node which runs your App’s code free of charge. After your App is created you now can access the tab **information** where you will find the repository URL. cloudControl provides an example application at (http:// github.com/cloudControl/cctrl_tutorial_app/downloads)

* Init your git repository
  * Open a command line or terminal window 
  * Change to your project's directory: `$ cd PROJECTDIR`
  * Init git repository `$ git init`
  * Add files to the git repository `$ git add .`
  * Commit files `$ git commit -m “COMMITMSG“`

The example App is now ready to get pushed to the cloudControl platform. But before that, we add a MySQL database to the default deployment since the example App is using MySQL. This is done with the command `$ cctrlapp app_name/default addon.add mysqls.free` (replace **app_name** with the name of your App)  This command adds the MySQLs Add-on to the deployment and creates a free shared MySQL database (on an Amazon RDS instance actually). The database‘s credentials can now be accessed via the web interface (select the default deployment, then go to the Add-ons tab). Clicking on Credentials will bring up the credentials.

### Deploying the App
Now the local preparation is complete and the example App can be pushed and deployed on cloudControl’s platform using the following commands (replace **app_name** with the name of your App)
* `$ git remote add cctrl ssh://app_name@cloudcontrolled.com/repository.git`
* `$ git push cctrl master`
* `$ cctrlapp app_name/default deploy`
  Now you can access your new App a**t app_na**me.cloudcontrolled.com

## Using the command line client

### Cheatsheet
[Our cheatsheet (PDF)](http://example.org) contains an overview over all commands of the command line client.

### Install the cloudControl CLI client and set up your user account
* Install git ([quick Git tutorial](http://rogerdudler.github.com/git-guide/))
* Install the command line client `$ sudo easy_install cctrl`
* Create an user account command line client `$ cctrluser create [--name USERNAME] [--email EMAIL] [--password PWD]`
* Activate your user account with `$ cctrluser activate USERNAME ACTIVATION_CODE`
* If you don't have a SSH key: Generate one using this tutorial
* Add your public SSH key with `$ cctrluser key.add ~/.ssh/id_rsa.pub`

### Create a new app, push code to cloudControl and add add-ons
* Create a new application with `$ cctrlapp app_name create app_type`. The parameter `app_type` can be `java`, `php`, `python`, `ruby` or any other project type as listed [here](http://www.example.org)
* Init your git repository
  * Change to your project's directory: `$ cd PROJECTDIR`
  * Init git repository `$ git init`
  * Add files to the git repository `$ git add .`
  * Commit files `$ git commit -m “COMMITMSG“`
* Push your code to cloudControl with `$ cctrlapp app_name/dep_name push`
* Add required Add-ons with `$ cctrlapp app_name/dep_name addon.add add-on.size`

### Deploy your app
* Deploy with `$ cctrlapp app_name/dep_name deploy [--min 1..8] [--max 1..8] [VERSION]`
  * `--min 1..8` is the number of redundant nodes your deployment should run on
  * `--max 1..8` is number of 128MB-containers per node
  * `VERSION` is the deployment version so you can do an easy roll-back