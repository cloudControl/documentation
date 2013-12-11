# Autodetection for JVM-Based Applications

For most officially supported languages on the cloudControl platform, the application type defines exactly which buildpack is used, and thus which runtime is created. Due to the diversity of JVM-based applications and the mix of languages used, these types of apps are an exception to this rule.

To determine the right buildpack to use for JVM-based applications, each of the Java buildpacks defines a "detect" script. In order for the platform to determine the correct buildpack, your application must follow these conventions:

|Technology|Detection|
|:---------|:----------:|
|[Clojure][buildpack-clojure]|`/project.clj` exists|
|[Gradle][buildpack-gradle]|`/build.gradle` exists|
|[Grails][buildpack-grails]|`/grails-app/` exists|
|[Java][buildpack-java]|`/pom.xml` exists|
|[Scala][buildpack-scala]|`/*.sbt`, or `project/*.scala` or `.sbt/*.scala`|
|[Play!][buildpack-play]|`*/conf/application.conf` inside your application (not modules). |

For more details, check the `detect` script of the corresponding buildpack. 

[buildpack-clojure]: https://github.com/cloudControl/buildpack-clojure
[buildpack-gradle]: https://github.com/cloudControl/buildpack-gradle
[buildpack-grails]: https://github.com/cloudControl/buildpack-grails
[buildpack-java]: https://github.com/cloudControl/buildpack-java
[buildpack-scala]: https://github.com/cloudControl/buildpack-scala
[buildpack-play]: https://github.com/cloudControl/buildpack-play
