# dotCloud to cloudControl CLI Cheat Sheet

Here are the dotCloud CLI commands translated to their equivalents in
the cloudControl CLI, listed in the same order as they appear in
`dotcloud -h`.

`dotcloud -A APP_NAME`| `cctrlapp APP_NAME[/DEP_NAME]`
---------------------:|-------------------------------
`-h, --help, -?` | `-h, --help`
`setup` | to persist login information, export it to your environment through `CCTRL_EMAIL` and `CCTRL_PASSWORD` or you will be prompted for this information every 15 minutes.
`check` | N/A
`list` | `-l`
`connect / disconnect / app` | N/A. `APP_NAME` must always be specified explicitly as you might have done with `dotcloud -A APP_NAME`.
`create APP_NAME` | `create [Java, PHP, Python, Ruby]` for these pre-defined language types.
`create APP_NAME` (custom service) | `create custom --buildpack BUILDPACK_REPO_URL` The repo must be accessible without SSH access.
`destroy` | `delete`. **Note**: `delete` alone would delete an APP. For a specific deployment, the command would be `cctrlapp APP_NAME/DEP_NAME undeploy`
`destroy SERVICE` | `addon.remove` **Note**: uses ADDON_NAME.OPTION
`activity` | N/A
`info` | `details`, `addon`, `worker`. **Note**: Include DEP_NAME for details about specific deployments.
`url` | `alias` **Note**: requires DEP_NAME
`open` | `open`  **Note**: requires DEP_NAME
`run` | `run` **Note**: deploys new container just for running command.
`memory` | N/A, use a Performance & Monitoring Add-on
`traffic` | N/A, use a Performance & Monitoring Add-on
`push` | `push` **Note**: use `push --ship` if you want it to deploy in the same step. Use DEP_NAME to push something besides the default.
`push --clean` | `push --clear-buildpack-cache`
`deploy` | `deploy`
`deploy previous` | `rollback`
`dlist` | `log deploy` **Note**: only the last 500 lines of deployment log are available. Does not include build information from push.
`dlogs` | `log deploy`
`env list` | `config` and `addon.creds` **Note**: config is documented as a free Add-on. Changing values does not redeploy as on dotCloud.
`env set` | `config.add`
`env unset` | `config.remove`
`scale instances=N memory=Y` | `deploy --containers N --memory Y` or `addon.upgrade / addon.downgrade` **Note**: Add-ons have their own tiers and scaling capabilities.
`restart` | N/A. `deploy` if you need it.
`domain list` | `alias`
`domain add` | `alias.add`
`domain rm` | `alias.remove`
`revisions` | N/A. You can `deploy` any of your last 10 pushed git hashes.
`upgrade` | `deploy --stack STACK_NAME`
`--debug, -D` | N/A
`--assume-yes, --assume-no` | N/A. Use the API if you need full scriptability. cloudControl's API is fully supported.
`--version` | `-v`
 
