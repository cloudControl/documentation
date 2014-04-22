# Deploying a Node.js Application
[Node.js] is a platform built on Chrome's JavaScript runtime for building fast and scalable network applications. Its event-driven, non-blocking I/O model makes it a lightweight and efficient framework for building data-intensive real-time cloud apps.

This tutorial demonstrates how to build and deploy a simple Hello World Node.js application on [exoscale]. Check out the [Node.js buildpack] for supported features.

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
A [Procfile] is required to start processes on the exoscale platform. There must be a file called `Procfile` at the root of your repository. In the example code you already cloned it looks like this:

~~~
web: node web.js
~~~

Left from the colon, we specified the **required** process type called `web` followed by the command that starts the app.

## Pushing and Deploying your App
Choose a unique name to replace the `APP_NAME` placeholder for your application
and create it on the exoscale platform:

~~~bash
$ exoapp APP_NAME create nodejs
~~~

Push your code to the application's repository, which triggers the deployment image build process:

~~~bash
$ exoapp APP_NAME/default push
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

To ssh://APP_NAME@app.exo.io/repository.git
 * [new branch]      master -> master
~~~

Last but not least, deploy the latest version of the app with the exoapp deploy command:

~~~bash
$ exoapp APP_NAME/default deploy
~~~

Congratulations, you can now see your Node.js app running at
`http[s]://APP_NAME.app.exo.io`.


[Node.js]: http://nodejs.org/
[npm]: https://npmjs.org/
[exoscale]: http://www.exoscale.ch
[Node.js buildpack]: https://github.com/cloudControl/buildpack-nodejs
[Procfile]: https://www.exoscale.ch/dev-center/Platform%20Documentation#buildpacks-and-the-procfile
[platform documentation]: https://www.exoscale.ch/dev-center/Platform%20Documentation
