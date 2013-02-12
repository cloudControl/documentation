# Java Amazon S3 integration

[Amazon S3](http://aws.amazon.com/s3/) is a Storage-as-a-Service solution. It provides a simple web service interface that can be used to store and retrieve any amount of data, at any time, from anywhere on the web.

## Amazon S3 SDK

There are a few java SDKs for Amazon S3, the first being the official one:
* [Amazon S3 Java SDK](http://aws.amazon.com/sdkforjava/)
* [JetS3t](http://jets3t.s3.amazonaws.com/index.html)
* [s3lib](http://code.google.com/p/s3lib/)
* [jclouds](http://www.jclouds.org/)

## Getting started

To use the official Amazon S3 SDK in your project just specify additional maven dependency in your `pom.xml`:

~~~xml
<dependency>
	<groupId>com.amazonaws</groupId>
	<artifactId>aws-java-sdk</artifactId>
	<version>1.3.27</version>
</dependency>
~~~

Follow the [Amazon Guide](http://docs.aws.amazon.com/AWSSdkDocsJava/latest/DeveloperGuide/java-dg-setup.html) to setup an account and get the [AWS access credentials](http://aws.amazon.com/security-credentials).

## Example usage:

First create an AWS account and make your credentials accessible by your application. The recommended way is to provide them via environment variables. To do this, use [Config Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config):

~~~bash
$ cctrlapp APP_NAME/default addon.add config.free --AWS_SECRET_KEY=[YOUR_SECRET_KEY] --AWS_ACCESS_KEY=[YOUR_ACCESS_KEY]
~~~

Now try out some operations on buckets and objects:

~~~java

// Get access credentials from environment variables
AWSCredentials creds = new AWSCredentials(){
    @Override
    public String getAWSAccessKeyId() {
        return System.getenv("AWS_ACCESS_KEY");
    }

    @Override
    public String getAWSSecretKey() {
        return System.getenv("AWS_SECRET_KEY");
    }
};

// S3 client connection
AmazonS3 s3 = new AmazonS3Client(creds);
String BUCKET = "testbucket" + UUID.randomUUID(), KEY = "key";

// Create bucket
s3.createBucket(BUCKET);

// List buckets
List<Bucket> buckets = s3.listBuckets();
System.out.println("Buckets: "+buckets);

// Put object
s3.putObject(new PutObjectRequest(BUCKET, KEY, new File("tmp.txt")));

// Read object
S3Object object = s3.getObject(new GetObjectRequest(BUCKET, KEY));
String contentType = object.getObjectMetadata().getContentType();
S3ObjectInputStream objectStream = object.getObjectContent();
String content = getContent(objectStream);
System.out.println("Type: " + contentType + "\nContent: " + content);

// Delete object
s3.deleteObject(BUCKET, KEY);

// Delete bucket
s3.deleteBucket(new DeleteBucketRequest(BUCKET));
~~~

Here is a simple helper function that can be used to read the content of the S3 object:

~~~java
private static String getContent(S3ObjectInputStream fin) throws IOException {
    int ch;
    StringBuilder builder = new StringBuilder();
    while ((ch = fin.read()) != -1) {
        builder.append((char) ch);
    }
    return builder.toString();
}
~~~
