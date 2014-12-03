# Deploying a Node.js Application
[Node.js] is a platform built on Chrome's JavaScript runtime for building fast
and scalable network applications. Its event-driven, non-blocking I/O model
makes it a lightweight and efficient framework for building data-intensive
real-time cloud apps.

This tutorial demonstrates how to build and deploy a simple Hello World Node.js
application on [dotCloud]. Check out the [Node.js buildpack] for supported
features.

## The Node.js App Explained

### Get the App
First, clone the Node.js App from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/nodejs-express-example-app.git
$ cd nodejs-express-example-app
~~~

Now you have a small, but fully functional Node.js application.

### Dependency Tracking
The Node.js buildpack tracks dependencies using [npm]. The dependency
requirements are defined in a `package.json` file which needs to be located in
the root of your repository. For the Hello World application, the only
requirement is Express. The `package.json` you cloned as part of the example
app looks like this:

~~~json
{
  "name": "nodejs-express-example-app",
  "version": "0.0.1",
  "dependencies": {
    "express": "4.10.2",
    "ejs": "1.0.0"
  },
  "engines": {
    "node": "0.10.13",
    "npm": "1.3.2"
  }
}
~~~

You should always specify the versions of your dependencies if you want your
builds to be reproducible and to prevent unexpected errors caused by version
changes.

### Process Type Definition
A [Procfile] is required to start processes on the dotCloud platform.
There must be a file called `Procfile` at the root of your repository. In the
example code you already cloned it looks like this:

~~~
web: node web.js
~~~

Left from the colon, we specified the **required** process type called `web`
followed by the command that starts the app.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the dotCloud platform:

~~~bash
$ dcapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment
image build process:

~~~bash
$ dcapp APP_NAME push
Counting objects: 344, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (294/294), done.
Writing objects: 100% (344/344), 461.55 KiB | 412.00 KiB/s, done.
Total 344 (delta 24), reused 337 (delta 20)
       
-----> Receiving push
-----> Requested node range:  0.10.13
-----> Resolved node version: 0.10.13
-----> Downloading and installing node
-----> Installing dependencies
       […]
       express@4.10.2 node_modules/express
       ├── utils-merge@1.0.0
       ├── merge-descriptors@0.0.2
       ├── fresh@0.2.4
       ├── escape-html@1.0.1
       ├── cookie@0.1.2
       ├── range-parser@1.0.2
       ├── cookie-signature@1.0.5
       ├── finalhandler@0.3.2
       ├── vary@1.0.0
       ├── media-typer@0.3.0
       ├── methods@1.1.0
       ├── parseurl@1.3.0
       ├── serve-static@1.7.1
       ├── content-disposition@0.5.0
       ├── path-to-regexp@0.1.3
       ├── depd@1.0.0
       ├── qs@2.3.2
       ├── debug@2.1.0 (ms@0.6.2)
       ├── on-finished@2.1.1 (ee-first@1.1.0)
       ├── proxy-addr@1.0.4 (forwarded@0.1.0, ipaddr.js@0.1.5)
       ├── etag@1.5.1 (crc@3.2.1)
       ├── send@0.10.1 (destroy@1.0.3, ms@0.6.2, mime@1.2.11)
       ├── type-is@1.5.3 (mime-types@2.0.3)
       └── accepts@1.1.3 (negotiator@0.4.9, mime-types@2.0.3)
-----> Caching node_modules directory for future builds
-----> Cleaning up node-gyp and npm artifacts
-----> Building runtime environment
-----> Building image
-----> Uploading image (5.9 MB)


To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the dcapp deploy command:

~~~bash
$ dcapp APP_NAME deploy
~~~

Congratulations, you can now see your Node.js app running at
`http[s]://APP_NAME.cloudcontrolled.com`.

## Next Steps
Building a data app with Node.js? Check out our next [example on how to use Node.js with MongoDB]. Read our [platform documentation] for a technical overview of the concepts you’ll encounter while writing, configuring, deploying and running your Node.js applications.
Good luck building your apps using Node.js and dotCloud.


[example on how to use Node.js with MongoDB]: https://next.dotcloud.com/dev-center/guides/nodejs/express
[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[dotCloud]: http://next.dotcloud.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://next.dotcloud.com/dev-center/platform-documentation#buildpacks-and-the-procfile
[platform documentation]: https://next.dotcloud.com/dev-center/platform-documentation
