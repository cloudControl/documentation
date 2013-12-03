# Deploying an Express  Application

## Introduction
This example demonstrates how to build a simple cloudControl Express app. The app uses Express, which is a Node.js web framework, and MongoDB as the backend database.

## Prerequisites
Before we get started, you need to get access to the app code in Github.

To make a clone of the Express app from the repository, execute the following commands using bash:

~~~bash
$ git clone git://github.com/cloudControl/node-js-sample node-js-mongodb-sample
$ cd node-js-mongodb-sample
~~~

Now you have a small, but fully functional Express application.

### Declaring Dependencies Using NPM
The next step is to declare app dependencies. Node.js tracks dependencies using [npm]. The dependency requirements must be specified in a `package.json`-file in your project's root directory.   

Modify the dependencies section of the package.json file as shown below to add the app dependencies (`express` and `mongodb`):

~~~json
"dependencies": {
    "express": "~3.3.4",
    "mongodb": "1.3.19"
  }
~~~

### Starting Processes in cloudControl
A [Procfile] is required to start processes on the cloudControl platform. There must be a file called `Procfile` at the top level of your repository.

In the case of the Node.js app, it is important to invoke the node process as shown below. Note that the process is of type web, which means it is a web app.

~~~
web: node web.js
~~~

### Creating the dataprovider.js File
Now we need to create our provider that will be capable to using MongoDB. Make sure this file is located in the same directory as web.js.

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
~~~

### Pushing and Deploying your Express App

Before you deploy your app, you have to give it a unique name (from now on called `APP_NAME`) for your application.

~~~bash
$ cctrlapp APP_NAME create nodemongo
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ cctrlapp APP_NAME/default push
...
~~~

Finally, don’t forget to add the MongoDB Add-on for cloudControl and deploy the latest version of the app:

~~~bash
$ cctrlapp APP_NAME/default addon.add mongolab.free
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Express app running with MongoDB at
**`http[s]://APP_NAME.cloudcontrolled.com`**.


## Next Steps
Read our [platform docs] for a technical overview of the concepts you’ll encounter while writing, configuring, deploying and running your Node.js applications.


[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[platform docs]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation
[MongoDB]: https://www.cloudcontrol.com/add-ons/mongodb/