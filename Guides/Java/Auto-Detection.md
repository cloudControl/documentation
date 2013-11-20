# Autodetection for JVM-Based applications

By default on cloudControl, the apptype defines exactly what buildpack is used and so what runtime is created. Due to the language mixing and diversity for JVM-based applications, the behavior is different for these apps. 

Each of the Java-buildpacks defines a so-called "detect" script which is used to determine the buildpack to use for your application. 

In short the application has to follow these conventions: 

|Technology|Detection|
|:---------|:----------:|
|[Clojure][buildpack-clojure]|`/project.clj` exists|
|[Gradle][buildpack-gradle]|`/build.gradle` exists|
|[Grails][buildpack-grails]|`/grails-app/` exists|
|[Java][buildpack-java]|`/pom.xml` exists|
|[Scala][buildpack-scala]|`/*.sbt`, or `project/*.scala` or `.sbt/*.scala`|
|[Play!][buildpack-play]|`*/conf/application.conf` inside your application (not modules). |

For more detail, check the `detect` script of the corresponding buildpack. 

[buildpack-clojure]: https://github.com/cloudControl/buildpack-clojure
[buildpack-gradle]: https://github.com/cloudControl/buildpack-gradle
[buildpack-grails]: https://github.com/cloudControl/buildpack-grails
[buildpack-java]: https://github.com/cloudControl/buildpack-java
[buildpack-scala]: https://github.com/cloudControl/buildpack-scala
[buildpack-play]: https://github.com/cloudControl/buildpack-play
