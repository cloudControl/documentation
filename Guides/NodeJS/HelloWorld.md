# Deploying a Node.js Application
[Node.js] is a platform built on Chrome's JavaScript runtime for building fast and scalable network applications. Its event-driven, non-blocking I/O model makes it a lightweight and efficient framework for building data-intensive real-time cloud apps.

In this tutorial, we're going to show you how to build and deploy a simple Hello World Node.js application on [cloudControl]. Check out the [Node.js buildpack] for supported features.

## The Node.js App Explained

### Get the App
First, let's clone the Node.js App from our repository on Github:

~~~bash
$ git clone https://github.com/cloudControl/nodejs-express-example-app.git
$ cd nodejs-express-example-app
~~~

Now you have a small, but fully functional Node.js application.

### Dependency Tracking
The next step is to declare app dependencies. Node.js tracks dependencies using [npm]. The dependency requirements must be specified in a `package.json`-file in your project's root directory.  For the Hello World application, the only requirement is Express. This is shown in the json file below:

~~~json
{
  "name": "nodejs-express-example-app",
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

You should always specify the versions of your dependencies if you want your builds to be reproducible and to prevent unexpected errors caused by version changes.

### Process Type Definition
A [Procfile] is required to start processes on the cloudControl platform. There must be a file called `Procfile` at the top level of your repository.

In the case of the Node.js app, it is important to invoke the node process as shown below. Note that the process is of type web, which means it is a web app.

~~~
web: node web.js
~~~

Left from the colon we specified the **required** process type called web followed by the command that starts the app and listens on the port specified by the environment variable `$PORT`.

## Pushing and Deploying your App
Before you deploy your app, you have to give it a unique name (from now on called `APP_NAME`) for your application and create it on the cloudControl platform:

~~~bash
$ cctrlapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment image build process:

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

Last but not least, deploy the latest version of the app with the cctrlapp deploy command:

~~~bash
$ cctrlapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Node.js app running at
`http[s]://APP_NAME.cloudcontrolled.com`.

## Next Steps
Building a data app with Node.js? Check out our next [example on how to use Node.js with MongoDB]. Read our [platform docs] for a technical overview of the concepts you’ll encounter while writing, configuring, deploying and running your Node.js applications.
Good luck building your apps using Node.js and cloudControl.


[example on how to use Node.js with MongoDB]: https://github.com/cloudControl/documentation/blob/master/Guides/NodeJS/Express.md
[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[cloudControl]: http://www.cloudcontrol.com
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[platform docs]: https://www.cloudcontrol.com/dev-center/Platform%20Documentation
