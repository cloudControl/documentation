# Deploying a Node.js application
[Node.js] is a platform built on Chrome's JavaScript runtime for easily building fast, scalable network applications. Node.js uses an event-driven, non-blocking I/O model that makes it lightweight and efficient, perfect for data-intensive real-time applications that run across distributed devices.

In this tutorial we're going to show you how to deploy a Hello World Node.js application on [cloudControl]. Check out the [Node.js buildpack] for supported features.

## Cloning a Hello World application
First, clone the hello world app from our repository:

~~~bash
$ git clone git://github.com/cloudControl/node-js-sample
$ cd node-js-sample
~~~

### Dependency declaration with NPM
The requirements of your application must be defined in a `package.json`-file in your project's root directory. 
For this simple app the only requirement is Express itself:

~~~json
{
  "name": "node-js-sample",
  "version": "0.0.1",
  "dependencies": {
    "express": "~3.3.4"
  },
  "engines": {
    "node": "0.10.13",
    "npm": "1.3.2"
  }
}
~~~

You should always specify the versions of your dependencies, if you want your builds to be reproducable and to prevent unexpected errors caused by version changes.

### Process type definitions
cloudControl uses a [Procfile] to know how to start your processes.

There must be a file called `Procfile` at the top level of your repository, with the following content:

~~~
web: node web.js
~~~

The web process type is required and specifies the command that will be executed when the app is deployed.

## Pushing and deploying your app
Choose a unique name (from now on called `APP_NAME`) for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which creates a deployment image:

~~~bash
$ cctrlapp APP_NAME/default push
Counting objects: 307, done.
Delta compression using up to 8 threads.
Compressing objects: 100% (261/261), done.
Writing objects: 100% (307/307), 202.14 KiB | 0 bytes/s, done.
Total 307 (delta 18), reused 307 (delta 18)

-----> Receiving push
-----> Resolving engine versions
       Using Node.js version: 0.10.13
       Using npm version: 1.3.2
-----> Fetching Node.js binaries
-----> Vendoring node into slug
-----> Installing dependencies with npm
       […]
       express@3.3.8 node_modules/express
       ├── methods@0.0.1
       ├── range-parser@0.0.4
       ├── cookie-signature@1.0.1
       ├── fresh@0.2.0
       ├── buffer-crc32@0.2.1
       ├── cookie@0.1.0
       ├── debug@0.7.2
       ├── mkdirp@0.3.5
       ├── commander@1.2.0 (keypress@0.1.0)
       ├── send@0.1.4 (mime@1.2.11)
       └── connect@2.8.8 (uid2@0.0.2, pause@0.0.1, qs@0.6.5, 
       […]
       Dependencies installed
-----> Building runtime environment
-----> Building image
-----> Uploading image (4.3M)

To ssh://APP_NAME@cloudcontrolled.com/repository.git
 * [new branch]      master -> master
~~~

Deploy your app:

~~~bash
$ cctrlapp APP_NAME/default deploy 
~~~

Congratulations, you should now be able to reach your application at `http://APP_NAME.cloudcontrolled.com`.

[Node.js]: http://nodejs.org/
[cloudControl]: http://www.cloudcontrol.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
