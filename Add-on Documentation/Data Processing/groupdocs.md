#Groupdocs

This quickstart will get you going with the GroupDocs Java sample app on cloudcontrol.

## Prerequisites

* A cloudcontrol user account with [cloudcontrol toolbelt](https://toolbelt.cloudcontrol.com/) installed on the local workstation.
* Add GroupDocs addon to your app.

## Add GroupDocs Add-on to your app

	:::term
    $ cctrlapp APP_NAME/DEPLOYMENT addons.add groupdocs.PLAN
    -----> Adding groupdocs to sharp-mountain-4006... done

## Clone the sample repository to your local folder

	:::term
	$ git clone git://github.com/groupdocs/groupdocs-cloudcontrol-examples-for-python.git

## Store Your App in Git

    :::term
	$ cd groupdocs-cloudcontrol-examples-for-python
    $ git init
    $ git add .
    $ git commit -m "init"

## Deploy to cloudcontrol/default

Create the app on the default stack:

    :::term
    $ cctrlapp APP_NAME create python
    Git remote cloudcontrol added

Deploy your code:

    :::term
    $ cctrlapp APP_NAME push
	$ cctrlapp APP_NAME deploy

## Live running app
This sample app is also running live on cloudcontrol. To view and try, please open [http://groupdocspython.cloudcontrolapp.com/](http://groupdocspython.cloudcontrolapp.com/).
