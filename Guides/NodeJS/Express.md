# Deploying an Express Application

This example demonstrates how to build a simple Express app on [dotCloud]. The
app uses [Express], which is a [Node.js] web framework, and MongoDB as the
backend database.

## The Express Application Explained

### Get the App
First, clone the Express application from our
Github repository by executing the following commands from your command line:

~~~bash
$ git clone git@github.com:cloudControl/nodejs-express-mongodb-example-app.git
$ cd nodejs-express-mongodb-example-app
~~~

Now you have a small, but fully functional Express application.

### Dependency Tracking

The Node.js buildpack tracks dependencies using
[npm]. The dependency requirements are defined in a `package.json` file which needs to be located
in the root of your repository. The one you cloned as part of the example app looks like this:

~~~json
{
  "name": "ExpressTut",
  "version": "0.0.1",
  "private": true,
  "scripts": {
    "start": "node app"
  },
  "dependencies": {
    "express": "3.4.0",
    "jade": "*",
    "mongodb": "1.3.19"
  }
}
~~~

### Process Type Definition

A [Procfile] is required to start processes on the dotCloud platform. There
must be a file called `Procfile` at the root of your repository. In the example
code you already cloned it looks like this:

~~~
web: node app.js
~~~

Left of the colon, we specified the **required** process type called `web` followed by the command that starts the app.

### MongoDB Database

Node.js and MongoDB are an excellent combination because JSON
(JavaScript Object Notation) is a subset of JavaScript, making storage and
retrieval of the objects very simple. MongoDB is provided by [MongoSoup]
which can be found in dotCloud's Add-on Marketplace under the
category [Data Storage].

This example uses the MongoSoup Add-on. In the
`employeeprovider.js` file, you can find how the connection to the database is
established:

~~~javascript
var Db = require('mongodb').Db,
    MongoClient = require('mongodb').MongoClient,
    Server = require('mongodb').Server,
    ReplSetServers = require('mongodb').ReplSetServers,
    ObjectID = require('mongodb').ObjectID,
    Binary = require('mongodb').Binary,
    GridStore = require('mongodb').GridStore,
    Grid = require('mongodb').Grid,
    Code = require('mongodb').Code,
    BSON = require('mongodb').pure().BSON,
    assert = require('assert');

EmployeeProvider = function() {
  var that = this;
  mongodbUri = process.env.MONGOSOUP_URL || 'mongodb://localhost';
  MongoClient.connect(mongodbUri, function(err, db){
    if(err) { return console.dir(err); }
    that.db = db;
  })
};
~~~

For more information related to getting Add-on credentials in JavaScript, you
can refer to the Node.js Add-on credentials [guide][get-conf].


## Pushing and Deploying your Express App

Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the dotCloud platform:

~~~bash
$ cctrlapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment
image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 73, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (35/35), done.
Writing objects: 100% (73/73), 267.28 KiB | 0 bytes/s, done.
Total 73 (delta 30), reused 73 (delta 30)

-----> Receiving push
-----> Resolving engine versions

       WARNING: No version of Node.js specified in package.json,

       Using Node.js version: 0.10.15
       Using npm version: 1.3.5
-----> Fetching Node.js binaries
-----> Vendoring node into slug
-----> Installing dependencies with npm
       npm WARN package.json ExpressTut@0.0.1 No repository field.
       npm http GET https://registry.npmjs.org/jade
       npm http GET https://registry.npmjs.org/mongodb/1.3.19
       npm http GET https://registry.npmjs.org/express/3.4.0
       ...
       Dependencies installed
-----> Building runtime environment
-----> Building image
-----> Uploading image (17M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Finally, don’t forget to add the MongoSoup Add-on for dotCloud and deploy the
latest version of the app:

~~~bash
$ cctrlapp APP_NAME/default addon.add mongosoup.sandbox
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Express app running with MongoDB at
`http[s]://APP_NAME.cloudcontrolled.com`.


## Next Steps
Read our [platform documentation] for a technical overview of the concepts you’ll
encounter while writing, configuring, deploying and running your Node.js
applications.


[Node.js]: http://nodejs.org/
[Express]: http://expressjs.com/
[npm]: https://npmjs.org/
[dotCloud]: http://next.dotcloud.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[get-conf]: https://next.dotcloud.com/dev-center/guides/nodejs/add-on-credentials
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[platform documentation]: https://next.dotcloud.com/dev-center/platform-documentation
[Data Storage]: https://next.dotcloud.com/add-ons?c=1
[MongoSoup]: https://next.dotcloud.com/add-ons/mongosoup
