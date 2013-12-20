# Node Amazon S3 integration 

## Introduction
[Amazon S3] is a Storage-as-a-Service solution. It provides a simple web service interface that can be used to store and retrieve data from anywhere on the web.

This article show how to integrate Amazon S3 with your Node.js app. 

## Getting Started
To use the official Amazon S3 SDK in your project, you should install the AWS SDK for Node.js using the [npm package manager] for Node.js. 
To install the SDK, type the following into a terminal window : 

~~~bash
npm install aws-sdk
~~~

## Example Usage 
S3 needs your AWS credentials for access. The recommended way to provide your AWS credentials to your app is via environment variables. To do this, use the [Config Add-on]:

~~~bash
cctrlapp APP_NAME/default config.add 
AWS_ACCESS_KEY_ID=[YOUR_SECRET_KEY] 
AWS_SECRET_ACCESS_KEY=[YOUR_ACCESS_KEY] 
AWS_REGION='eu-west-1' 
~~~

To load the AWS library in your Node app, use the require function as shown below:

~~~javascript
var AWS = require('aws-sdk');
var s3 = new AWS.S3();
~~~

Now let's do some operations on S3 buckets and objects:

~~~javascript
 s3.createBucket({Bucket: 'myBucket'}, function() {
   var params = {Bucket: 'myBucket', Key: 'myKey', Body: 'Hello!'};
   s3.putObject(params, function(err, data) {
       if (err)       
           console.log(err)     
       else       console.log("Successfully uploaded data to myBucket/myKey");   
    });
 });
~~~

