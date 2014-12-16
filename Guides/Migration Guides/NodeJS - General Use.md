# Migrating dotCloud nodejs service to Next dotCloud platform

With Next dotCloud you can use the latest [Node.js] and [npm] versions without any hassle. Migrating an existing app should take only a few steps. Please look at our [Quickstart] and [Introduction for dotCloud Developers] to make yourself familiar with the platform differences.
In this guide we'll covers the Node.js specific changes:

* Procfile to start the server
* Versions and dependencies
* Server port assigned by container
* Migrating a custom service using Node.js


## Procfile to start the server
The new build process is based on [Buildpacks and Procfile]. The `Procfile` replaces the `dotcloud.yml` and is expected to be in the project root. It can be as simple as this:

~~~
web: node hellonode/server.js
~~~

For *web* containers we point the `node` command to start the `server.js` in the `hellonode/` directory. The [convert dotcloud.yml] guide contains more details about the changes.


## Versions and dependencies
Versions and dependencies are managed via `package.json`. Like the `Procfile` our buildpack looks for it in the project root. If you are using a subfolder like `hellonode/`, move your existing `package.json` just one directory up.

The Node.js version could be set in the `dotcloud.yml` before:

~~~
www:
  type: nodejs
  approot: hellonode
  config:
    node_version: v0.6.x
~~~

This should go into the `package.json` now, into the **engines** section. Please note, you can also set the npm version there.

~~~
...
    "dependencies": {
        ...
    },
    "engines": {
        "node": "0.10.33",
        "npm": "2.1.11"
    }
}
~~~

If you don't define any engines the buildpack will use the latest stable ones.


## Server port assigned by container
On the old dotCloud platform all Node.js apps were bound to listen on port `8080`. On Next dotCloud the container selects the port and exposes it via environment variable to the app. Please update your Node.js start file (`server.js`) to reflect these changes. You can use `process.env['PORT']` to read the value:

~~~js
http.createServer(function (req, res) {
...
}).listen(process.env['PORT'] || 8080);
~~~


## Migrating a custom service using Node.js
Due to the limited support for up to date Node.js versions on the old platform, many customers moved to a [custom service recipe]. With Next dotCloud we support all versions out of the box. Node.js is a first class citizen on our platform. 

You can follow the same steps above to migrate a custom service. As a working example we also migrated the custom service recipe. You can find all the [changes on github].
[Node.js]: http://nodejs.org/
[npm]: https://www.npmjs.com/
[Introduction for dotCloud Developers]: ./an-introduction
[Quickstart]: ../../quickstart
[Buildpacks and Procfile]: ../../platform-documentation#buildpacks-and-the-procfile
[convert dotcloud.yml]: ./converting-dotcloud-dot-yml
[custom service recipe]: https://github.com/dotcloud/node-on-dotcloud
[changes on github]: https://github.com/cloudControl/migrate-node-on-dotcloud-to-dotcloudapp/pull/1/files
