# dotCloud to cloudControl CLI Cheat Sheet

Here are the dotCloud CLI commands translated to their equivalents in
the cloudControl CLI, listed in the same order as they appear in
`dotcloud -h`.

## User specific commands

`dotcloud`| `dcapp`
---------------------:|-------------------------------
`setup` | You will be prompted for credentials after every 15 minutes of inactivity. To prevent this, export `DC_EMAIL` and `DC_PASSWORD` into your environment.
`check` | N/A
`list` | `-l`
`create APP_NAME` | `create APP_NAME (java, nodejs, php, python, ruby)` for these pre-defined language types.
`create APP_NAME` (custom service) | `create custom --buildpack BUILDPACK_REPO_URL` The url has to be a non-ssh, public git repository.
`-h, --help` | `-h, --help`
`--version` | `-v`

## Application specific commands

`dotcloud -A APP_NAME`| `dcapp APP_NAME[/DEP_NAME]`
---------------------:|-------------------------------
`connect / disconnect / app` | N/A. `APP_NAME` must always be specified explicitly as you may have done with `dotcloud -A APP_NAME`.
`destroy` | `undeploy` for deployments and `delete` for apps
`destroy SERVICE` | `addon.remove ADDON_NAME.OPTION`
`activity` | N/A
`info` | `details / addon / worker`
`url` | `alias`
`open` | `open`
`run` | `run COMMAND` **Note**: deploys new container just for running command.
`memory` | N/A, use a Performance & Monitoring Add-on
`traffic` | N/A, use a Performance & Monitoring Add-on
`push` | `push` **Note**: use `push --ship` if you want it to deploy in the same step. Use DEP_NAME to push something besides the default.
`push --clean` | `push --clear-buildpack-cache`
`deploy` | `deploy`
`deploy previous` | `rollback`
`dlist` | `log deploy`
`dlogs` | `log deploy`
`env list` | `config` and `addon.creds` **Note**: config is implemented as a free Add-on.
`env set` | `config.add`
`env unset` | `config.remove`
`scale instances=N memory=Y` | `deploy --containers N --memory Y` or `addon.upgrade / addon.downgrade` **Note**: Add-ons have their own tiers and scaling capabilities.
`restart` | `deploy`
`domain list` | `alias`
`domain add` | `alias.add`
`domain rm` | `alias.remove`
`revisions` | N/A. You can `deploy` any of your last 10 pushed git hashes.
`upgrade` | `deploy --stack STACK_NAME`
`--debug, -D` | N/A
`--assume-yes, --assume-no` | N/A. Use the API if you need full scriptability. cloudControl's API is fully supported.
