# Third-Party Custom Buildpacks

[exoscale] officially supports the following application types via the [Pinky Stack][PinkyStack].

- Java-based (Java with Maven, Gradle, Grails, Scala, Play! or Clojure)
- Ruby
- PHP
- Python
- NodeJS
 
However, you can deploy apps developed on languages and technologies beyond the officially supported ones using the third-party custom buildpacks feature.

## Verified Buildpacks

Here is a list of verified and recommended buildpacks for the exoscale platform covering the following languages and technologies:

|Technology|Buildpack URL|
|:---------|:----------:|
|Go|[https://github.com/kr/heroku-buildpack-go][buildpack-go]|
|Erlang|[https://github.com/cloudControl/buildpack-erlang-kernel][buildpack-erlang]|
|Common Lisp|[https://github.com/mtravers/heroku-buildpack-cl][buildpack-common-lisp]|
|Lua|[https://github.com/leafo/heroku-buildpack-lua][buildpack-lua]|
|C|[https://github.com/atris/heroku-buildpack-C][buildpack-c]|
|Haskell|[https://github.com/pufuwozu/heroku-buildpack-haskell][buildpack-haskell]|
|Perl|[https://github.com/fern4lvarez/buildpack-perl][buildpack-perl]|

## Using a Custom Buildpack

In order to create an app using a custom buildpack you have to choose the `custom` app type and then provide the desired buildpack URL:

~~~bash
$ exoapp APP_NAME create custom --buildpack BUILDPACK_URL
~~~

**Note:** `BUILDPACK_URL` has to be a non-ssh git repository.

You can use any of the aforementioned buildpacks, fork them and make changes according to your needs or even create your own. Keep in mind that custom buildpacks have to follow heroku's [Buildpack API][buildpack-API].

Before using any third party buildpack you should inspect their source code and proceed with caution.

[exoscale]: https://www.exoscale.ch
[PinkyStack]: https://www.exoscale.ch/dev-center/Platform%20Documentation#stacks
[buildpack-java]: https://github.com/cloudControl/buildpack-java
[buildpack-python]: https://github.com/cloudControl/buildpack-python
[buildpack-ruby]: https://github.com/cloudControl/buildpack-ruby
[buildpack-php]: https://github.com/cloudControl/buildpack-php
[buildpack-nodejs]: https://github.com/heroku/heroku-buildpack-nodejs
[buildpack-clojure]: https://github.com/heroku/heroku-buildpack-clojure
[buildpack-gradle]: https://github.com/heroku/heroku-buildpack-gradle
[buildpack-grails]: https://github.com/heroku/heroku-buildpack-grails
[buildpack-scala]: https://github.com/heroku/heroku-buildpack-scala
[buildpack-play]: https://github.com/heroku/heroku-buildpack-play
[buildpack-erlang]: https://github.com/cloudControl/buildpack-erlang-kernel
[buildpack-go]: https://github.com/kr/heroku-buildpack-go
[buildpack-common-lisp]: https://github.com/mtravers/heroku-buildpack-cl
[buildpack-lua]: https://github.com/leafo/heroku-buildpack-lua
[buildpack-c]: https://github.com/atris/heroku-buildpack-C
[buildpack-haskell]: https://github.com/pufuwozu/heroku-buildpack-haskell
[buildpack-perl]: https://github.com/fern4lvarez/buildpack-perl
[buildpack-API]: https://devcenter.heroku.com/articles/buildpack-api
