# Custom Buildpacks

[cloudControl](https://www.cloudcontrol.com) supports Java, Ruby, PHP and Python apps natively via [Pinky stack](https://www.cloudcontrol.com/dev-center/Platform%20Documentation#stacks). However you can deploy apps developed on languages and technologies beyond the default ones using the custom buildpacks feature.

## Verified buildpacks

Here is a list of verified and recommended buildpacks for the cloudControl platform covering the following languages and technologies:

|Technology|Buildpack URL|
|:---------|:----------:|
|Java|[https://github.com/cloudControl/buildpack-java](https://github.com/cloudControl/buildpack-java)|
|Python|[https://github.com/cloudControl/buildpack-python](https://github.com/cloudControl/buildpack-python)|
|Ruby|[https://github.com/cloudControl/buildpack-ruby.git](https://github.com/cloudControl/buildpack-ruby.git)|
|PHP|[https://github.com/cloudControl/buildpack-php](https://github.com/cloudControl/buildpack-php)|
|Node.js|[https://github.com/heroku/heroku-buildpack-nodejs](https://github.com/heroku/heroku-buildpack-nodejs)|
|Clojure|[https://github.com/heroku/heroku-buildpack-clojure](https://github.com/heroku/heroku-buildpack-clojure)|
|Gradle|[https://github.com/heroku/heroku-buildpack-gradle](https://github.com/heroku/heroku-buildpack-gradle)|
|Grails|[https://github.com/heroku/heroku-buildpack-grails](https://github.com/heroku/heroku-buildpack-grails)|
|Scala|[https://github.com/heroku/heroku-buildpack-scala](https://github.com/heroku/heroku-buildpack-scala)|
|Play|[https://github.com/heroku/heroku-buildpack-play](https://github.com/heroku/heroku-buildpack-play)|
|Erlang|[https://github.com/cloudControl/buildpack-erlang-kernel](https://github.com/cloudControl/buildpack-erlang-kernel)|
|Go|[https://github.com/kr/heroku-buildpack-go](https://github.com/kr/heroku-buildpack-go)|
|JRuby|[https://github.com/jruby/heroku-buildpack-jruby](https://github.com/jruby/heroku-buildpack-jruby)|
|Common Lisp|[http://github.com/mtravers/heroku-buildpack-cl.git](http://github.com/mtravers/heroku-buildpack-cl.git)|
|Lua|[https://github.com/leafo/heroku-buildpack-lua](https://github.com/leafo/heroku-buildpack-lua)|
|C|[https://github.com/atris/heroku-buildpack-C](https://github.com/atris/heroku-buildpack-://github.com/atris/heroku-buildpack-C)|
|Haskell|[https://github.com/pufuwozu/heroku-buildpack-haskell](https://github.com/pufuwozu/heroku-buildpack-haskell)|
|Perl|[https://github.com/fern4lvarez/buildpack-perl](https://github.com/fern4lvarez/buildpack-perl)|

## Using a custom Buildpack

In order to create an app using a custom buildpack you have to choose the `custom` app type and then provide the desired buildpack URL:

~~~bash
$ cctrlapp APP_NAME create custom --buildpack BUILDPACK_URL
~~~

**Note:** Buildpack URL has to be non-ssh git repository.

You can use any of the forementioned buildpacks, fork them and make changes according to your needs or even create your own. Custom buildpacks have to follow heroku's [Buildpack API](https://devcenter.heroku.com/articles/buildpack-api).

Before using any third party buildpack you should inspect their source code and proceed with caution.
