# Ruby Amazon S3 integration

[Amazon S3](http://aws.amazon.com/s3/) is a Storage-as-a-Service solution. It provides a simple web service interface that can be used to store and retrieve data from anywhere on the web.


## Amazon S3 SDK

For Ruby you can choose between different SDKs for Amazon S3:
* [Amazon S3 Ruby SDK]
* [Fog]
* [AWS-S3]
* [RightAWS]
* [S3]


## Getting started

To use the official Amazon S3 SDK in your project, add the gem to your [Gemfile].

~~~ruby
source 'https://rubygems.org'
gem 'aws-sdk'
~~~

Follow the [Amazon Guide](http://docs.aws.amazon.com/AWSSdkDocsJava/latest/DeveloperGuide/java-dg-setup.html) to setup an account and get your [AWS access credentials](http://aws.amazon.com/security-credentials).

## Example usage:

The recommended way to provide your AWS credentials to your app is via environment variables. To do this, use the [Config Add-on](https://www.cloudcontrol.com/dev-center/Add-on%20Documentation/Deployment/Custom%20Config):

~~~bash
$ cctrlapp APP_NAME/default addon.add config.free --AWS_ACCESS_KEY_ID=[YOUR_SECRET_KEY] --AWS_SECRET_ACCESS_KEY=[YOUR_ACCESS_KEY] --AWS_REGION='eu-west-1'
~~~

Now let's show some operations on buckets and objects:

~~~ruby
require 'aws-sdk'
require 'securerandom'

s3 = AWS::S3.new
bucket = s3.buckets.create('testbucket' + SecureRandom.uuid)

# List buckets
s3.buckets.each do |bucket|
    puts bucket.name
end

# Put object
bucket.objects['key'].write(Pathname.new('tmp.txt'))

# Read object
puts bucket.objects['key'].read


# Delete object
bucket.objects['key'].delete

# Delete bucket
bucket.delete
~~~


[Amazon S3 Ruby SDK]: https://aws.amazon.com/sdkforruby/
[Fog]: https://github.com/fog/fog
[AWS-S3]: https://rubygems.org/gems/aws-s3
[RightAWS]: https://rubygems.org/gems/right_aws
[S3]: https://github.com/qoobaa/s3
[Gemfile]: http://bundler.io/v1.3/gemfile.html
