# Deploying a Sails.js Application

In this guide we're going to show you how to deploy a [Sails.js] application on [exoscale]. Sails.js is a real-time [Node.js] MVC framework, designed to mimic the pattern of frameworks like [Ruby on Rails]. It allows you to easily create applications with Node.js using the Model-View-Controller pattern to organize your code so that it is easier to maintain.

If you are new to Sails.js, first, check out the [Sails getting started page] for more info on how to install Sails.

## The Sails.js App Explained

### Get the App

First, clone the Sails.js application from our repository:

~~~bash
$ git clone https://github.com/cloudControl/nodejs-sails-example-app.git
$ cd nodejs-sails-example-app
~~~

Now you have a small, but fully functional Sails.js application.

### Dependency Tracking

Dependencies are tracked using [npm] and specified in a `package.json`-file in your project's root directory. 
The one you cloned as part of the example app looks like this:

~~~json
{
    "name": "sails-todomvc",
    "private": true,
    "version": "0.0.0",
    "description": "a Sails application",
    "dependencies": {
        "sails": "0.9.7",
        "grunt": "0.4.1",
        "sails-disk": "~0.9.0",
        "ejs": "0.8.4",
        "optimist": "0.3.4",
        "sails-mysql": "0.9.5"
    },
    "scripts": {
        "start": "node app.js",
        "debug": "node debug app.js"
    },
    "main": "app.js",
    "repository": "",
    "author": "",
    "license": ""
}
~~~

### Process Type Definition
exoscale uses a [Procfile] to start the application processes. The `Procfile` can be found at the root level of your repository.

To start the sails server, you need to use the `sails lift` command. This command is included in the procfile definition as shown below: 

~~~
web:  export NODE_ENV=production; sails lift
~~~

Left from the colon we specified the **required** process type called `web` for a web application and followed by the command that starts the Sails server.

### Connecting the Sails.js Application to a Database
Sails.js is database agnostic. It provides a simple data access layer that works, no matter what database you're using. All you have to do is plug in one of the adapters for your database. Here, we show you how to connect your Sails.js application to a MySQL database using the exoscale [Shared MySQL Add-on]. 

Have a look at the `config/adapter.js` file so you can find out how to [get the MySQL credentials] provided by MySQLs Add-on:

~~~javascript
module.exports.adapters = {

    'default': process.env.NODE_ENV || 'development',

    development: {
        module: 'sails-mysql',
        host: 'localhost',
        user: 'todouser',
        password: 'todopass',
        database: 'todomvc',
        pool: true,
        connectionLimit: 2,
        waitForConnections: true
    },

    production: {
        module: 'sails-mysql',
        host: process.env.MYSQLS_HOSTNAME,
        user: process.env.MYSQLS_USERNAME,
        password: process.env.MYSQLS_PASSWORD,
        database: process.env.MYSQLS_DATABASE,
        pool: true,
        connectionLimit: 2,
        waitForConnections: true
    }
};
~~~

### Socket.io and Websocket Support

In Sails.js, client-backend communication is done using [websockets]. For more details, take a look at the [exoscale websockets documentation].

## Pushing and Deploying your Sails.js App
To deploy your Sails.js application, choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the exoscale platform:

~~~bash
$ exoapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ exoapp APP_NAME/default push
Counting objects: 73, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (35/35), done.
Writing objects: 100% (73/73), 267.28 KiB | 0 bytes/s, done.
Total 73 (delta 30), reused 73 (delta 30)

-----> Receiving push
-----> Resolving engine versions

       Using Node.js version: 0.10.15
       Using npm version: 1.3.5
-----> Fetching Node.js binaries
-----> Installing dependencies with npm
       ...
       Dependencies installed
-----> Building runtime environment
-----> Building image
-----> Uploading image (17M)

To ssh://APP_NAME@app.exo.io/repository.git
 * [new branch]      master -> master
~~~

Add the [Shared MySQL Add-on]:
~~~bash
$ exoapp APP_NAME/default addon.add mysqls.free
~~~

Finally, deploy the Sails.js application:
~~~bash
$ exoapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Sails.js application running at
`http://APP_NAME.app.exo.io`.

[Node.js]: http://nodejs.org/
[Sails.js]: http://sailsjs.org/
[Sails getting started page]: http://sailsjs.org/#!getStarted
[Ruby on Rails]: http://rubyonrails.org/
[npm]: https://npmjs.org/
[exoscale]: http://www.exoscale.ch
[Procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[get the MySQL credentials]: https://www.exoscale.ch/dev-center/Guides/NodeJS/Add-on%20credentials
[websockets]: http://socket.io/
[exoscale websockets documentation]: https://www.exoscale.ch/dev-center/Platform%20Documentation#websockets
[Shared MySQL Add-on]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs
