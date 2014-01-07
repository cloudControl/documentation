# Node.js Amazon S3 Integration 

[Amazon S3] is a Storage-as-a-Service solution. It provides a simple web service interface that can be used to store and retrieve data from anywhere on the web.

This guide shows how to integrate Amazon S3 with your Node.js app. 

## Amazon S3 SDK
For Node you can choose between different SDKs for Amazon S3:
* [Amazon S3]
* [Fog]
* [Knox S3]

## Getting Started
To use the official Amazon S3 SDK in your project, you should install the AWS SDK for Node.js using the [npm package manager]. 
To install the SDK, type the following into a terminal window: 

~~~bash
npm install aws-sdk
~~~

In addition to the AWS SDK, you also need to have AWS access credentials. If you do not already have one, follow the [Amazon Guide] to setup an account and get your [AWS access credentials].

## Example Usage 
S3 needs your AWS credentials for access. The recommended way to provide your AWS credentials to your app is via environment variables. To do this, use the [Config Add-on]:

~~~bash
cctrlapp APP_NAME/default config.add 
AWS_ACCESS_KEY_ID=[YOUR_SECRET_KEY] 
AWS_SECRET_ACCESS_KEY=[YOUR_ACCESS_KEY] 
AWS_REGION='eu-west-1' 
~~~

To load the AWS library in your Node.js app, use the require function as shown below:

~~~javascript
var AWS = require('aws-sdk');
var s3 = new AWS.S3();
~~~

Now, let's do some operations on S3 using Node.js. First, let's create a bucket called myBucket and put a new object in it with Key = 'myKey'. If any error occurs, the error is written to the console. If a key gets successfully added to the bucket, a success message gets written to the console.

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

## Next Steps
You can build rich Node.js apps using more advanced S3 operations. To learn more, check out the Node.js [Amazon Guide]. Good luck.

[Amazon S3]: http://aws.amazon.com/s3/
[Fog]: https://docs.appfog.com/languages/node
[Knox S3]: https://github.com/LearnBoost/knox
[npm package manager]: https://npmjs.org/
[Amazon Guide]: http://docs.aws.amazon.com/AWSJavaScriptSDK/guide/node-intro.html
[AWS access credentials]: http://aws.amazon.com/security-credentials
[Config Add-on]: https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config
