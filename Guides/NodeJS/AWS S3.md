# Node.js Amazon S3 Integration 

[Amazon S3] is a Storage-as-a-Service solution. It provides a simple web service interface that can be used to store and retrieve data from anywhere on the web.

This guide shows how to integrate Amazon S3 with your Node.js app. 

## Amazon S3 SDK
For Node.js you can choose between different SDKs for Amazon S3:
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
exoapp APP_NAME/default config.add 
AWS_ACCESS_KEY_ID=[YOUR_SECRET_KEY] 
AWS_SECRET_ACCESS_KEY=[YOUR_ACCESS_KEY] 
AWS_REGION='eu-west-1' 
~~~

To load the AWS library in your Node.js app, use the require function as shown below:

~~~javascript
var AWS = require('aws-sdk');
var s3 = new AWS.S3();
~~~

Now, let's do some operations on S3 using Node.js. In the example below, we show how to create a new bucket, list existing buckets, add a key into a bucket, read the key from the bucket, delete the key from the bucket, and delete a bucket.   

~~~javascript
   //Create an S3 bucket named myBucket
   s3.createBucket({Bucket: 'myBucket'}, function(err, data) {
    if (err) throw new Error(err);
   });
    
   //List existing S3 buckets
   s3.ListBuckets(function(err, data) {
    if (err) throw new Error(err);

    var buckets = data.Body.ListAllMyBucketsResult.Buckets.Bucket;
    buckets.forEach(function(bucket) {
        console.log('%s : %s', bucket.CreationDate, bucket.Name);
    });
   });

   //Add a key to myBucket
   var putparams = {Bucket: 'myBucket', Key: 'myKey', Body: 'Hello!'};
   s3.putObject(putparams, function(err, data) {
       if (err)       
           console.log(err)     
       else       console.log("Successfully uploaded data to myBucket/myKey");   
    });

   //Read the key from myBucket
   var getparams = {Bucket: 'myBucket', Key: 'myKey'};
   s3.getObject(getparams, function (err, url) {
  	if (err)
	   console.log(err)
	else	  console.log("The key is", url);
   });

   //Delete key from myBucket
   var delparams = {Bucket: 'myBucket', Key: 'myKey'};
   s3.deleteObject(delparams, function(err, data) {
        console.log(err, data)
   });

   //Delete bucket myBucket
   s3.deleteBucket({Bucket: bucket}, function (err, data) {
   if (err)
           console.log("error deleting bucket " + err);
   else    console.log("delete the bucket " + data);
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
[Config Add-on]: https://www.exoscale.ch/dev-center/Add-on%20Documentation/Deployment/Custom%20Config
