# Migrating dotCloud MySQL service to Next dotCloud platform

Next dotCloud offers two MySQL Add-ons, a shared and dedicated one. Both are based on [Google Cloud SQL][google cloud sql]. The [shared MySQL Add-on][shared mysql addon] is recommended for development and low-traffic apps while for medium to high-traffic apps we recommend one of the [dedicated MySQLd Add-on][dedicated mysql addon] plans. For more information on each, we recommend you looking in to the documentation of [shared MySQL Add-on][shared mysql addon doc] and [dedicated MySQLd Add-on][dedicated mysql addon doc].

This guide aims to show you the differences between the dotCloud MySQL service and the Next dotCloud MySQL Add-ons. For the purpose of this guide we will recommend you to use the MySQLd Add-on.

## Comparison

             | dotCloud mysql services               | Next dotCloud mysql add-ons
-------------|---------------------------------------|-------------------------------
Version| [5.1][mysql 5.1 doc] | [5.5][mysql 5.5 doc]
Access   | <ul><li>[MySQL service][dotcloud mysql service] runs in separate container</li><li>User can access it via SSH or connect directly to MySQL instance</ul> | <ul><li>MySQLd and MySQLs Add-ons offer only access to MySQL instance</li><li>No possibility to access a host</li><ul>
Configuration changes   | User owns full permissions to update MySQL configuration | <ul><li>Only possible for MySQLd Add-on</li><li>Configuration changes limited to [set of parameters](#supported-flags)</li><li>Changes are handled by support team</li></ul>
Backups   | <ul><li>Handled by users</li><li>Recommended solution is cron task running on MySQL node</li></ul> | <ul><li>Automatic backups are part of all Add-on plans</li><li>No user action required to set them up</li><li>Restore done by support team</li><li>MySQLd Add-on backups DB just before deletion and keeps it for 30 days</li></ul>
Failover  | master/slave with automatic failover | Your data is replicated in many geographic locations as standard. Failover between them is handled automatically by us. Your data is safe and your database is available even in the event of a major failure in one location
Scaling horizontally | Via CLI - `dotcloud scale data:instances=N` | Please contact us at support@dotcloud.com
Scaling vertically | Via CLI - `dotcloud scale data:memory=N` | Via CLI - `dcapp APP_NAME/DEP_NAME addon.upgrade PLAN1 PLAN2` - check MySQLs/MySQLd Add-on plans
Monitoring | Provided by [dotcloud dashboard][dotcloud dashboard] | Provided by [Next dotCloud console][next dotcloud console]
Accessing credentials | Application reads them from `environments.json` | Application reads them from `creds.json` or from system env variables - check our [documentation][addon credential doc] for details

## Migration steps

### Dump your databases

We recommend using [`mysqldump`][mysqldump] for this purpose:

SSH first to your MySQL container and execute below command:

~~~bash
mysqldump -u DOTCLOUD_[SERVICE_NAME]_MYSQL_LOGIN -pDOTCLOUD_[SERVICE_NAME]_MYSQL_PASSWORD DB_NAME > dump.sql
~~~

### Add Add-on to your deployment on Next dotCloud:

~~~bash
dcapp APP_NAME/DEP_NAME addon.add mysqld.PLAN
~~~

### Get SSL certificate for your instance

It is strictly recommended to always use encrypted remote connections to MySQL instances. Check Add-on MySQLd and MySQLs documentation for details how to download the certificate.

### Load dump:

Get the Add-on credentials:

~~~bash
dcapp APP_NAME/DEP_NAME addon.creds
{
    "MYSQLD": {
        "MYSQLD_DATABASE": "...",
        "MYSQLD_HOSTNAME": "...",
        "MYSQLD_PASSWORD": "...",
        "MYSQLD_PORT": ...,
        "MYSQLD_URL": "...",
        "MYSQLD_USERNAME": "..."
    }
}
~~~

Upload the dump to the new DB using Add-on credentials (you can do this directly from dotCloud MySQL container):

~~~bash
mysql -u MYSQLD_USERNAME -h MYSQLD_HOSTNAME -pMYSQLD_PASSWORD MYSQLD_DATABASE --ssl-ca=PATH_TO_CERTIFICATE/CERT_FILE.pem < dump.sql
~~~

Using MySQLD Add-on it is possible to create custom/multiple databases. By default Add-on creates one and sets its name under `MYSQLD_DATABASE`.

### Update application configuration

Update your application code/environment to use the Add-on credentials. You can find examples in the [migration guides](../converting-environment-dot-json).

## Supported Flags

These flags can be modified only for MySQLd Add-on:

* [`event_scheduler`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_event_scheduler)
* [`general_log`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_general_log)
* [`group_concat_max_len`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_group_concat_max_len)
* [`innodb_flush_log_at_trx_commit`](https://dev.mysql.com/doc/refman/5.5/en/innodb-parameters.html#sysvar_innodb_flush_log_at_trx_commit)
* [`innodb_lock_wait_timeout`](https://dev.mysql.com/doc/refman/5.5/en/innodb-parameters.html#sysvar_innodb_lock_wait_timeout)
* [`log_bin_trust_function_creators`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_log_bin_trust_function_creators)
* [`log_output`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_log_output)
* [`log_queries_not_using_indexes`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_log_queries_not_using_indexes)
* [`long_query_time`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_long_query_time)
* [`lower_case_table_names`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_lower_case_table_names)
* [`max_allowed_packet`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_max_allowed_packet)
* [`read_only`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_read_only)
* [`skip_show_database`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_skip_show_database)
* [`slow_query_log`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_slow_query_log)
* [`wait_timeout`](https://dev.mysql.com/doc/refman/5.5/en/server-system-variables.html#sysvar_wait_timeout)

If you wish to make any modifications to the configuration of your MySQLd add-on please contact us at support@dotcloud.com

[google cloud sql]: https://cloud.google.com/sql/
[shared mysql addon]: https://next.dotcloud.com/add-ons/mysqls
[dedicated mysql addon]: https://next.dotcloud.com/add-ons/mysqld
[shared mysql addon doc]: https://next.dotcloud.com/dev-center/add-on-documentation/mysqls
[dedicated mysql addon doc]: https://next.dotcloud.com/dev-center/add-on-documentation/mysqld
[mysql 5.1 doc]: https://dev.mysql.com/doc/refman/5.1/en/
[mysql 5.5 doc]: https://dev.mysql.com/doc/refman/5.5/en/
[dotcloud mysql service]: http://docs.dotcloud.com/services/mysql/
[dotcloud dashboard]: https://dashboard.dotcloud.com/
[next dotcloud console]: https://next.dotcloud.com/console
[addon credential doc]: https://next.dotcloud.com/dev-center/platform-documentation#add-on-credentials
[mysqldump]: http://dev.mysql.com/doc/refman/5.5/en/mysqldump.html

