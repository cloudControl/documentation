# Deploying a Sails.js Application

[Sails.js] is real time [Node.js] MVC framework, designed to mimic pattern of frameworks like [Ruby on Rails]. It allows you to easily create applications with Node.js using the Model-View-Controller pattern to organize your code so that it is easier to maintain.

## Prerequisites
If you are new to Sails.js, first, check out the [Sails getting started page] for more info on how to install Sails.

cloudControl supports running Sails.js applications through the Node.js buildpack. Before we get started, you need to get access to the sample application code in Github.

To make a clone of the Sails.js application from the repository, execute the following commands using bash:

~~~bash
$ git clone https://github.com/cloudControl/nodejs-sails-example-app.git
$ cd nodejs-sails-example-app
~~~

The code from the example repository is ready to be deployed.

### Dependency Tracking Using NPM
The next step is to declare application dependencies. Dependencies are tracked using [npm] and specified in a `package.json`-file in your project's root directory.   

Modify the dependencies section of the package.json file as shown below: 

[npm] depedencies
`package.json`:
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
cloudControl uses a [Procfile] to know how to start the application processes. The `Procfile` can be found at the top level of your repository.

To start the sails server, you need to use the `sails lift` command. This command can be included in the procfile definition as shown below: 

~~~
web:  export NODE_ENV=production; sails lift
~~~

Left from the colon we specified the **required** process type called `web` for a web application and followed by the command that starts the Sails server.

### Connecting the Sails.js Application to a Database
Sails.js is database agnostic. It provides a simple data access layer that works, no matter what database you're using. All you have to do is plug in one of the adapters for your database. In this guide, we will show you how to connect your Sails.js application to a MySQL database using the cloudControl [Shared MySQL Add-on]. 

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

In Sails.js, client-backend communication is done using [websockets]. In order to use sockets, it is important to use `*.cloudcontrolapp.com` domain instead of `*.cloudcontrolled.com`. For more details, take a look at the [cloudControl websockets documentation].

## Pushing and Deploying the App
To deploy your Sails.js application, choose a unique name to replace the `APP_NAME` placeholder for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
...
~~~

Add the add [MySQL] add-on
~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.free
~~~

Deploy the Sails.js application
~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Sails.js application running at
`http://APP_NAME.cloudcontrolapp.com`.

[Node.js]: http://nodejs.org/
[Sails.js]: http://sailsjs.org/
[Sails getting started page]: http://sailsjs.org/#!getStarted
[Ruby on Rails]: http://rubyonrails.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[get the MySQL credentials]: https://www.cloudcontrol.com/dev-center/Guides/NodeJS/Add-on%20credentials
[websockets]: http://socket.io/
[cloudControl websockets documentation]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#websockets
[MySQL]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs
