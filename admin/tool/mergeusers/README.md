# Merge users plugin for Moodle

This admin tool merges two Moodle user accounts, namely from user A into user B.
The intent of the plugin is to assign all activity and records from user A
(user to remove) into user B (user to keep). This will give the effect of
user B seeming to have done everything both users have ever done in Moodle. 


# Why this plugin exists?

In some institutions, people are provided with **user identifiers that do not change
over time**. If you are in some of these institutions, you are **lucky**:
you do not need this plugin.

However, some institutions provide people some **user identifiers that may
vary over time** or that there are several units and ways to introduce
new users into the system. Imagine cases where peoples' identifiers are based on
passport numbers or similar cases, that may vary over time, or the person providing
different valid id numbers to different enrolment units, or also human errors
introducing the person details into the system.

In those cases, you finally may have the same person with at least two different
user accounts into your Moodle instance.

And **this plugin tries to do its best to aggregate all person's activity
into a single user account.**


# Moodle requirements and release notes

This plugin version supports Moodle 4.5 onwards.

Check the `CHANGES.md` file for any other kind of requirements
and any news related to the current release of this plugin.


# How merge users plugin works

In brief:

1. There is an initialization, loading a set of configuration settings and
   loading the list of all Moodle database tables.
2. Then, a set of entities named `table merger` process every single Moodle database
   table in the necessary way.
3. Once all database tables are processed, then some operations are performed
   that are not strictly related to table contents, but the Moodle or plugin logics.
   Examples are regrading properly the involved users or force recalculating
   course completions.
4. The merge concludes:
   1. In presence of some PHP Exception or error message recorded during the process,
      the merge is considered as failed. By default, any change to the database
      is rollback.
   2. Otherwise, it was ok and changes to database are applied.


## Configuration initialization

This plugin uses two kinds of settings:

1. General settings, that define the main behaviour of the plugin, like how to
 manage quiz attempts or if the user to remove is suspended when the merge
 is success.
2. Database settings, that define database-related options **necessary** for
 the plugin.
   1. `gathering`: the class to use on the CLI merger.
   2. `exceptions`: list of table names that are excluded from processing.
   3. `compoundindexes`: list of compound indexes per table, necessary
       to manage conflicting cases.
   4. `userfieldnames`: list of field/column names per table that are related
       to the user.id field. The special `default` table name is used when
       the table is not explicitly present on this list.
   5. `tablemergers`: list of implementations of table mergers. The `default`
       table name is used when the table is not explicitly present on this list.
   6. `alwaysrollback`: (false by default) whether to rollback always the
       merge.
   7. `debugdb`: (false by default) set it to true to enable the `$DB->debug(true)`
      to see all database operations performed during the merge.


### Customizing configuration

On latest version from `MOODLE_405_STABLE` branch there are the following ways
to customize the settings:

1. General settings: only from the web administration of the plugin.
2. Database settings: there are these possibilities to customize these settings:
   1. From the web administration, updating the setting `tool_mergeusers/customdbsettings`.
   2. From callbacks to the `add_settings_before_merging` hook.
   3. Making a PR to this plugin asking to update some default database setting.

Regarding database settings, this is the priority among them, in order:

1. The setting `tool_mergeusers/customdbsettings` has the highest priority 
   and is kept always over other database settings.
   1. Its goal is to provide Moodle administrators the possibility to say
      the last word about this plugin customization.
2. Settings from callbacks to the `add_settings_before_merging` hook.
   1. These settings are though to live in code, in place, where the
      knowledge exists, like inside any other plugin or Moodle subsystem.
3. Default plugin settings. These settings have the lowest priority and
   provide a normal and sufficient operation by any Moodle standard instance.


### Check customized configuration

**Before and without running the merge** you as administrator can check the current
database-related settings into the web administration.

In the `Database settings` tab of this plugin settings, 
there is a section where this plugin **shows you the calculated settings**.
This helps you double-check if the calculated settings (including
the `tool_mergeusers/customdbsettings` setting, the settings from 
the hook callbacks, and the default plugin settings)
provide the expected customization you need.


## Processing database tables

The `table merger` entities process every single Moodle table to move
the user-related data from the user to remove into the user to keep.

In general, tables can contain any number of records related to the
same user.id.

However, there are some situations where the table cannot contain two or more
records for the same user.id. That restriction may appear on:

1. The definition of the database table, like in the form of a unique index, or
2. The side of the PHP code that manages that table. For instance, that code 
   may force just a single record per user or assumes that always
   there will be just 0 or 1 record per user.

In those cases, this `table mergers` has to delete records from that table.
To define which of them to keep, the general setting 
`tool_mergeusers/uniquekeynewidtomaintain` lets you define which of the two
users involved will keep those conflicting records. The default setting is 
to keep the records related to the user to keep, and remove the conflicting 
records from the user to remove.

To see the list of detected conflicting cases, you can check the value
of the database setting named `compoundindexes`, on the 
`classes/local/default_db_config.php` file, or on the calculated
database settings from the web administration.


## Post-process

If all tables are processed properly without error, we arrive at this point.

This step is meant to help process tasks that are not strictly related
to individual database tables, or that requires some Moodle or
plugin internal to process some kind of aggregation or trigger some
kind of (adhoc) task in the Moodle cron.

This post-proces is implemented by a hook: `after_merged_all_tables`,
to express that merge is not concluded yet, and some other task
may be executed.

The callbacks for this hook are meant to process any kind of operations
from Moodle internals or plugin specific tasks, that are transversal,
(operations not specific for a single table) or any kind of
aggregation operation, not updated by the table mergers.

To provide you an example, we have moved the regrading of the users and
the course recompletions into callbacks for this hook.

We think this hook will help Moodle and plugin developers to adjust the
merge users tool to better fit any Moodle instance (with a variable
number of custom Moodle changes and plugins).


## Concluding the merge

A merge may conclude with:

1. Failure: when some PHP Exception is thrown during the process,
   or some error message is recorded.
   1. PHP Exceptions abort the merge at that specific moment.
   2. Error messages, however, abort the merge just at the end
      of the merge.
2. Success.

The plugin stores a log with the whole detail of the merge:

1. On failure: it just records all error messages being recorded.
2. On success: all actions recorded during the merge.

On any case, an event is emitted to react to that situation.
We use the success event to suspend the user to remove when
the setting `tool_mergeusers/suspenduser` is enabled.


# An important note about provided hooks

Providing callbacks for both hooks, Moodle core and plugins
can make work this plugin as they need to merge users properly.

**Why?**

This plugin provides a generic way to merge users, but internals from
Moodle core (subsystems, and so) and plugins really know how user's
information is managed.

So, their maintainers have the full knowledge of the internal parts
of Moodle and their plugins. And these internals will vary
over time. So, Moodle and plugins maintainers can provide 
callbacks for both hooks:

1. Callbacks for `add_settings_before_merging` hook may help providing specific
   database-related settings: mainly table mergers (setting `tablemergers`),
   compound indexes (setting `compoundindexes`) or user-related table columns
   (setting `userfieldnames`), but the others settings are allowed to be
   provided too.
2. Callbacks for `after_merged_all_tables` hook may help providing specific
   post-processes.

All this by just placing the necessary callbacks and related stuff in the
maintainers' code, to ensure merge users is processed properly, without
modifying this plugin anymore.


# Command-line scripts

## cli/climerger.php

A `cli/climerger.php` script helps you merge users from command line.

By default, an interative `CLIGathering` is set on the database setting `gathering`.
It asks you iteratively the user.ids from the user to remove and to keep, 
all in a loop, until you ask to exit.

You can modify the `alwaysrollback` per CLI execution, and also the `debugdb`.
When `alwaysrollback` is enabled, the CLI execution ends with the first merge,
always.


### Customizing the cli/climerger.php

You can customize how to iterate over the users to merge. This will let you use the same
`cli/climerger.php` but with a different behaviour. In our case, we have a
local customization that asks users to merge to an external database every night.

Let us explain how to do it step by step:

1. Create a class implementing the `Gathering` interface. See `lib/cligathering.php` for an example.
2. Inform about the new `Gathering` implementation to use:
   * Either using the setting `tool_mergeusers/customdbsettings`, with a content similar to the example from below,
   * Or using a callback for the `add_setting_before_merging` hook, informing about your new `Gathering`.
3. From the command line, run the `cli/climerger.php` script.

Example of JSON content for informing the new `Gathering`:
```json lines
{
  'gathering': 'MyGathering'
}
```


## cli/listuserfields.php

This script is a read-only script that loads the Moodle database XML schema,
and provides a list of tables with fields/columns that are related to
the `user.id` field.

Its goal is to help keeping up-to-date the default plugin settings, and also,
help plugin users to detect missing database-related settings.

The user-related fields are informed by:

1. Having a name that contains `user` as part of its name. It may return false
   positives.
2. Foreign keys from the database XML schema, pointing to the `user.id` field.
   This will not provide false positives and must be considered all of them.

We have to still consider fields that contain `user` on its name, since
there is not a consistent state inside the Moodle database, nor also
third-party plugins.


# Correct way of testing this plugin

First of all, check plugin settings for the description of the setting 
`tool_mergeusers/transactions_only`. This will inform you
**if your database type and version supports transcations**. If so,
**no action will actually be committed if something goes wrong**.

Mainly, these are the main steps to test this plugin:

1. You should have a replica of your Moodle instance, with a full replica of 
   your Moodle database where you run this plugin.
2. Run a sufficient amount of user merging to check if anything goes wrong.
3. What if...?
    1. ... all was ok? You are almost confident that all will be ok also in your
       production instance of Moodle.
    2. ... something went wrong? There are several reasons for that:
        1. Non-core plugins installed on your Moodle and not assumed in this plugin.
        2. Local database changes on Moodle that may affect to the normal execution of this plugin.
        3. Some compound index not detected yet.

If in your tests or already in production something went wrong, please, report the whole detail,
including the error log on the
[github repository](https://github.com/jpahullo/moodle-tool_mergeusers/issues).
And if you have some PHP skill, you can try to solve it and share both the error and the patch to solve it ;-)


# Your database supports transactions?

Before running this plugin, it is highly recommended to back up your database.
That will help you to restore the state before any merge was done.

This plugin stores a log for any merge, with the list of actions done or
errors produced. **If your database supports transactions**,
automatic rollbacks are done at database level: **your database state
remains consistent** in presence of failures.

However, running this plugin in databases without
transaction support can put you in trouble. That is, there is no provision for
automatic rollbacks, so if something were to fail midway through,
you will end up with a half-updated database. Nevertheless, if you found a
problem when merging two users A and B, do not panic. Merging will be successfully
completed when a solution for your problem is included into this plugin, and
you rerun merging users A and B.


# Development

## Developing and testing phase

We recommend to use the [moodlehq/moodle-docker](https://github.com/moodlehq/moodle-docker)
project to run your own Moodle instances for developing and testing.


## PHPUnit testing

To quickly set up your own development, we suggest to run the command:

```
php admin/tool/phpunit/cli/util.php --buildcomponentconfigs
```

as documented at https://docs.moodle.org/dev/PHPUnit to have the `phpunit.xml`
file under `admin/tool/mergeusers/phpunit.xml`.

Then, you can run all plugin's tests as follows:

```
vendor/bin/phpunit -c admin/tool/mergeusers
```

or also like this, without the need of running the `buildcomponentconfigs`:

```
vendor/bin/phpunit --group tool_mergeusers
```

There are other PHPUnit groups created to help testing only the part
of the plugin of your choice. Take a look at the tests code for other group names.

Tip: If you want to see what tests are being processed in a human-readable way,
use the option `--testdox` like:

```
vendor/bin/phpunit -c admin/tool/mergeusers --testdox
```


# License

GNU GPL v3 or later. http://www.gnu.org/copyleft/gpl.html


# Contributors

Maintained by:

* [Jordi Pujol-Ahulló](https://www.urv.cat).
* [Nicolas Dunand](https://moodle.org/plugins/browse.php?list=contributor&id=141933).

[See all Github contributors](https://github.com/jpahullo/moodle-tool_mergeusers/graphs/contributors)
