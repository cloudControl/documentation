# Deploying a Sails.js Application

[Sails.js] is real time [Node.js] MVC framework, designed to mimic pattern of frameworks like [Ruby on Rails].

## The Example App Explained

### Get the App
First, let's clone the example code from Github.

~~~bash
$ git clone https://github.com/cloudControl/nodejs-sails-example-app.git
$ cd nodejs-sails-example-app
~~~

The code from the example repository is ready to be deployed. Let's still go
through the different files and their purpose real quick.

### Dependency Tracking

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
cloudControl uses a [Procfile] to know how to start the app's processes.

The example code already includes a file called `Procfile` at the top level of
your repository. It looks like this:
~~~
web:  export NODE_ENV=production; sails lift
~~~

Left from the colon we specified the **required** process type called `web`
followed by the command that starts the app.

### Production Database

In this tutorial we use the [Shared MySQL Add-on][mysqls]. Have a look at
`config/adapter.js` so you can find out how to [get the MySQL
credentials][get-conf] provided by MySQLs Add-on:
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

### Socket.io and websocket support

Client / backend communication is done via [socket.io](http://socket.io/) (websockets), so it is important to use `*.cloudcontrolapp.com` domain instead of `*.cloudcontrolled.com`. For more details please visit our [Websockets documentaion](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#websockets).

## Pushing and Deploying the App

Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the cloudControl platform:
~~~bash
$ cctrlapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment
image build process:
~~~bash
$ cctrlapp APP_NAME/default push
...
~~~

add mysql
~~~bash
$ cctrlapp APP_NAME/default addon.add mysqls.free
~~~

deploy
~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Sails.js app running at
`http://APP_NAME.cloudcontrolapp.com`.

[Node.js]: http://nodejs.org/
[Sails.js]: http://sailsjs.org/
[Ruby on Rails]: http://rubyonrails.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[get-conf]: https://www.cloudcontrol.com/dev-center/Guides/NodeJS/Add-on%20credentials
[mysqls]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Data%20Storage/MySQLs
