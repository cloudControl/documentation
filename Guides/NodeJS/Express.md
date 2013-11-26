# Node.js and MongoDB on cloudControl

## Introduction
This example demonstrates how to build a simple cloudControl Node.js app with a MongoDB backend.  

## Prerequisites
Before we get started, you need to get access to the Node.js code for the app in Github. 

Make a clone of the Node.js app from the repository using - 
~~~bash
$ git clone git://github.com/cloudControl/node-js-sample node-js-mongodb-sample
$ cd node-js-mongodb-sample
~~~
Now you have a small, but fully functional Node.js application.

### Declaring dependencies using NPM
The next step is to declare app dependencies. Node.js tracks dependencies using [npm]. The dependency requirements must be specified in a `package.json`-file in your project's root directory.   Modify the dependencies section of the package.json file as shown below to add the app dependencies (jade and mongodb) - 
"dependencies": {
    "express": "~3.3.4",
    "mongodb": "1.3.19"
  }
In this example app, Express is the Node.js web framework, and MongoDB is the backend database.

### Starting processes in cloudControl
A [Procfile] is required to start processes on the cloudControl platform. There must be a file called `Procfile` at the top level of your repository.

In the case of the Node.js app, it is important to invoke the node process as shown below. Note that the process is of type web, which means it is a web app.
~~~
web: node web.js
~~~

### Creating the dataprovider.js file
Now we need to create our provider that will be capable to using MongoDB. Make sure this file is located in the same directory as web.js.

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

DataProvider = function() {
  var that = this;
    MongoClient.connect(process.env.MONGOLAB_URI, function(err, db){
    if(err) { return console.dir(err); }
    that.db = db;
  })
};
//Functions to read and write data to mongodb
db.open(function(err, db) {
  db.dropDatabase(function(err, result) {
    db.collection('test', function(err, collection) {     
      // Erase all records from the collection, if any
      collection.remove({}, function(err, result) {
        // Insert 3 records
        for(var i = 0; i < 3; i++) {
          collection.insert({'a':i});
        }
        
        collection.count(function(err, count) {
          console.log("There are " + count + " records in the test collection. Here they are:");

          collection.find(function(err, cursor) {
            cursor.each(function(err, item) {
              if(item != null) {
                console.dir(item);
                console.log("created at " + new Date(item._id.generationTime) + "\n")
              }
              // Null signifies end of iterator
              if(item == null) {               
                // Destory the collection
                collection.drop(function(err, collection) {
                  db.close();
                });
              }
            });
          });         
        });
      });     
    });
  });
});

### Pushing and deploying your app

Before you deploy your app, you have to give it a unique name (from now on called `APP_NAME`) for your application.

~~~bash
$ cctrlapp APP_NAME create nodemongo
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
...
~~~

Finally, don’t forget to add the mongoDB add-on for cloudControl and deploy the latest version of the app -

~~~bash
$ cctrlapp APP_NAME/default addon.add mongolab.free
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Node.js app running with mongoDB at
`http[s]://APP_NAME.cloudcontrolled.com`.

[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[MongoDB]: https://www.cloudcontrol.com/add-ons/mongodb/

## Next Steps
Read our platform docs for a technical overview of the concepts you’ll encounter while writing, configuring, deploying and running your Node.js applications.


## Building the Hello World app
To start building the Hello World app, you first need to get access to the Node.js code for the app in Github.

Make a clone of the Node.js app from the repository using - 

~~~bash
$ git clone git://github.com/cloudControl/node-js-sample
$ cd node-js-sample
~~~

Now you have a small but fully functional Node.js application.

### Declaring dependencies using NPM
The next step is to declare app dependencies. Node.js tracks dependencies using [npm]. The dependency requirements must be specified in a `package.json`-file in your project's root directory.  For the Hello World application, the only requirement is Express. This is shown in the json file below -

~~~json
{
  "name": "node-js-sample",
  "version": "0.0.1",
  "dependencies": {
    "express": "~3.3.4"
  },
  "engines": {
    "node": "0.10.13",
    "npm": "1.3.2"
  }
}
~~~

You should always specify the versions of your dependencies if you want your builds to be reproducible and to prevent unexpected errors caused by version changes.

### Starting processes in cloudControl
A [Procfile] is required to start processes on the cloudControl platform. There must be a file called `Procfile` at the top level of your repository.

In the case of the Node.js app, it is important to invoke the node process as shown below. Note that the process is of type web, which means it is a web app.
~~~
web: node web.js
~~~

### Pushing and deploying your app
Before you deploy your app, you have to give it a unique name (from now on called `APP_NAME`) for your application.

~~~bash
$ cctrlapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 307, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (261/261), done.
Writing objects: 100% (307/307), 202.14 KiB | 0 bytes/s, done.
Total 307 (delta 18), reused 307 (delta 18)

-----> Receiving push
-----> Resolving engine versions
       Using Node.js version: 0.10.13
       Using npm version: 1.3.2
-----> Fetching Node.js binaries
-----> Vendoring node into slug
-----> Installing dependencies with npm
       […]
       express@3.3.8 node_modules/express
       ├── methods@0.0.1
       ├── range-parser@0.0.4
       ├── cookie-signature@1.0.1
       ├── fresh@0.2.0
       ├── buffer-crc32@0.2.1
       ├── cookie@0.1.0
       ├── debug@0.7.2
       ├── mkdirp@0.3.5
       ├── commander@1.2.0 (keypress@0.1.0)
       ├── send@0.1.4 (mime@1.2.11)
       └── connect@2.8.8 (uid2@0.0.2, pause@0.0.1, qs@0.6.5,
       […]
       Dependencies installed
-----> Building runtime environment
-----> Building image
-----> Uploading image (4.3M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the cctrlapp deploy command:
~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Node.js app running at
`http[s]://APP_NAME.cloudcontrolled.com`.

[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile

## Next Steps
Building a data app with Node.js? Check out our next example on how to use Node.js with MongoDB. <Link>
Read our platform docs for a technical overview of the concepts you’ll encounter while writing, configuring, deploying and running your Node.js applications.
Good luck building your apps using Node.js and cloudControl.


[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
