# Release notes

## 2025101701

1. 2025-10-17: task: #383: Moodle 5.1 compatible.

## 2025101700
1. 2025-10-17: fix: #381: add all user-related compound indexes into default plugin settings.
   1. default_db_config.php updated manually with structured section about compound indexes.
   2. listuserfields.php CLI script improved to list all user-related compound indexes. This script must help administrators to identify other compound indexes that affect their Moodle instances.

## 2025101400
1. 2025-10-14: fix: #382: ensure grade_grades table is merged properly. Thanks Daniel Tomé.
   1. Added tests for the new grade_grades table merger.
   2. Improved some existing tests.
   3. Improve Makefile to let run phpcs/phpcbf more easily.


## 2025092100

1. 2025-09-21: improvement: #372: add output from last steps of regrading and reaggregation of course completions.
   Also, reaggregation of course completion now happens inside the time of the merge process, and not after as before.
2. 2025-09-21: task: #362: remove YUI code. Simplified javascript code to the maximum.


## 2025091800

1. 2025-09-18: fix: #371: listuserfields.php CLI scripts supports tables that does not exist on the XML database schema.


## 2025090401

1. 2025-09-04: fix: #367: database settings tab did no show properly
   the default nor calculated settings.
2. 2025-09-04: fix: #369: add PHP attributes to hook.


## 2025082301

1. 2025-08-23: improvement: #360: new class added to manage session-based
   users selection when merging users from web administration.
2. 2025-08-23: improvement: #358: add "suspended" tag besides the
   user detail on all pages (including user search, user review and logs).
   Single log page now shows the full user detail, as in the rest of pages.
3. 2025-08-23: fix: #358: users selection table showed always the user
   detail of the user to remove. Now, it shows properly both users.
   It was detected while working on the improvement of #358.


## 2025082200

1. 2025-08-22: improvement: #356: code reorganization on the whole plugin.
   1. Placed all suitable file under `/classes/` for autoloading.
   2. Revisited all files (except `/tests/`) with phpcs (using `local_codechecker`)
   3. Removed content from `lib.php` in favour of `settingslib.php` and
      a new class `database_transactions::are_supported()`.
   4. Added a `Makefile` with some targets for helping while developing.
   5. All tests passes again.
   6. All clicks on the web work again.
   7. Moved the link to see merge logs into the `Reports` administration menu.

### UPGRADING

When upgrading to this version, you have to choose one of these paths:

1. **In case you have local plugin customizations:** you must check twice
   the new plugin structure since there has been a major refactor
   of the whole plugin. The `lib/` directory was removed, and
   most of the plugin classes were moved inside the `classes/` directory
   for a better code organization and with the benefit of autoloading.
   Also, we removed the vast majority of functions from the `lib.php`,
   leaving there only the necessary Moodle callbacks.
   Update your local customizations appropriately according to new
   classes and file structure.
2. **In case you DO NOT have local plugin customizations:** you can freely
   upgrade the plugin without worrying.


## 2025082000

1. 2025-08-20: fix: #354: ensure that setting `tool_mergeusers/uniquekeynewidtomaintain`
   applies.


## 2025081900

1. 2025-08-19: improvement: #350: added hook to proceed with operations after
   all tables have been merged, and before registering the end of the merge.
   1. If these hook's callbacks are invoked, it means that all went ok
      and table mergers processed the merge with success till now.
   2. The callbacks for this hook are meant to process any kind of operations
      from Moodle internals or plugin specific tasks, that are transversal,
      (operations not specific for a single table) or any kind of
      aggregation operation, not updated by the table mergers.
   3. To provide you an example, we have moved the regrading of the users and
      the course recompletions into callbacks for this hook.
   4. We think this hook will help Moodle and plugin developers to adjust the
      merge users tool to better fit any Moodle instance (with a variable
      number of custom Moodle changes and plugins).

### NOTE
Actually, with callbacks for both hooks, Moodle core and plugins
can make work this plugin as they need to merge users properly. Why?
This plugin provides a generic way to merge users, but internals from
Moodle core (subsystems, and so) and plugins really know how user's
information is managed. So, their maintainers have the full knowledge
and they can provide callbacks for both hooks:
1. Callbacks for `add_settings_before_merging` hook may help providing specific
 database-related settings: mainly table mergers (setting `tablemergers`),
 compound indexes (setting `compoundindexes`) or user-related table columns
 (setting `userfieldnames`).
2. Callbacks for `after_merged_all_tables` hook may help providing specific
 post-processes.

### UPGRADING
**Just as a clarification:** The inclusion of the hooks does not alter the
way of using this plugin at all. It will behave as it did.

However, these two hooks provide you as a developer and maintainer of your
plugin or Moodle instance powerful tools to customize the behaviour of the merge,
just placing the necessary callbacks and related stuff in your own
code, to ensure merge users is processed properly.

## 2025081800

1. 2025-08-18: improvement: #348: added hook to load database-related settings.
   1. This is though to help Moodle and plugin developers to adjust their code to help
      this plugin merge users properly.
   2. The settings that are loaded by this hook are those populated on the old
      `config/config.php` and `config/config.local.php` files. These files are not supported any more.
   3. The content of the old `config/config.php` is now placed on `classes/local/default_db_config.php`.
      This must help this plugin maintainers to keep in a single place the default behaviour.
   4. Added tests to ensure the database-related settings are kept properly.
   5. Priority of the settings (more priority settings are kept, in front of subsequent settings):
      1. Custom admin setting: the set of settings with the highest priority.
         This must let administrators adjusting plugin behaviour at any time.
      2. Hook settings: settings populated from this hook's callbacks are the second set of
         settings in priority.
      3. Default settings: the plugin's default settings are kept as with the lowest priority.
         Any existing setting from hooks and custom settings will replace default ones.

## 2025081700

1. 2025-08-17: fix: #308: reportedly, extension `pcntl` seems to be loaded sometimes but its `pcntl_*` functions
 are not available. We have removed support for aborting for `Ctrl+C` (`SIGINT`) using `pcntl` extension.
 No panic: in several instances we have, we can cancel the CLI script execution with `Ctrl+C` without
 the `pcntl` extension loaded. It was necessary on initial version of this plugin. Nowadays it seems unnecessary.
2. 2025-08-17: improvement: #308: updated the CLI script help to show that when using `--alwaysrollback` option
 there is no loop for merging pairs of users.

## 2025081603

1. 2025-0816: improvement: #244: allow resetting web user selection. Unified search and review tables.
 Added extra column on search table to show whether a user is already suspended (probably already merged).
2. 2025-08-16: improvement: #345: move config.local.php into a new admin setting, in JSON format, for being human-readable.
   1. Also, consider that setting with name `alwaysRollback` was renamed to `alwaysrollback` to unify the case insensitiveness
   of the rest of the configuration settings. It applies within the code and also on the CLI script parameters.

### UPGRADING

**Recommendation when upgrading:** Keep your `config/config.local.php` in place. It will help
updating the value of the new admin setting  `tool_mergeusers/customdbsettings` **automatically**,
without the need to convert your old `config/config.local.php` to JSON.
But, it is only a recommendation.

Otherwise, you will have to update that admin setting manually with the content of your previous
`config/config.local.php` on the new admin setting. To help you, you can use the
`tool_mergeusers\local\jsonizer::to_json($customsettings)` with an array with all your
`$customsettings`, and it will provide you the JSON content to place.

If you did not have any customization or file on `config/config.local.php`, you have to do nothing
with this upgrading step.

## 2025081500

1. 2025-08-15: improvement: #328: added support for Moodle 5.0.

## 2025081402

1. 2025-08-14: fix: #336: deleted users are excluded from everywhere when searching by and merging users.
2. 2025-08-14: improvement: #281: add debug info into log when no course module is found when regrading. Thanks to @Richardvi.
3. 2025-08-14: fix: #277: revisit all Moodle tables and user-related fields to
   update config.php settings. Provide a CLI script to help.

## 2025081300

1. 2025-08-13 - fix: #275: make web user search work on any database engine. Thanks to @leonstr.
2. 2025-08-13 - fix: #273: exclude deleted users from the web search. Thanks to @leonstr.

## 2025081205

1. 2025-08-12 - fix: #329: set renumber quiz attempts as the default setting.
2. 2025-08-12 - fix: #331: prevents web browser alert from leaving form page on the summary page.
3. 2025-08-12 - improvement: #311: use Catalyst CI. Thanks to @matthewhilton.
4. 2025-08-12 - improvement: #306: remove user profile fields; use profile page hook to show merging detail. Thanks to @matthewhilton.
5. 2025-08-12 - fix: #68: move logger to proper namespace. Thanks to @matthewhilton.
6. 2025-08-12 - improvement: #306: define new capability to see logs, either as admin or from user profile. Thanks to @matthewhilton.
7. 2025-08-12 - improvement: #306: add information about whether a user is deletable from this plugin viewpoint.
8. 2025-08-12 - improvement: #306: add settings item to inform that prior custom user profile fields added by this plugin still exist and should be deleted.
 There are no longer used nor updated.
9. 2025-08-12 - improvement: #306: add PHP API to list deletable users.

### UPGRADING
Before upgrading to this version, please check your own automatization processes in case they use the plugin profile fields.
You should adapt them to use the logs from the plugin table `tool_mergeusers`. In this case,
we provide a PHP API on `tool_mergeusers\local\last_merge::list_all_deletable_users()`
to list all candidate users to be deletable. You can adapt your scripts starting from
this new API.

## 2025081100

1. 2025-08-11 - fix: #315: replaced print_error() functions for moodle_exception. Thanks to @ManaElmountasir and @minhduchoang195.
2. 2025-02-11 - bump version and update CHANGES.md
3. 2025-08-11 - fix: #326: remove deprecation warnings on CLI script and tests. Thanks to @matthewhilton.

## 2025020505

1. 2025-02-05 - bump version to make a new plugin version available for M4.5.

## 2025020503

1. 2025-02-05 - fix: URL on old and new user profile fields definition.
2. 2025-02-05 - improvement: CLI script now shows better log of merging operations.

## 2025020502

1. 2025-02-05 #295 - fix: remove deprecation warning on CLIGathering, related to Iterator. Thanks to @CatSema.

## 2025020501

1. 2025-02-05 - bump version, update CHANGES.md, and kept support only for M4.1.

## 2025020500

1. 2025-02-05 - bump version and update CHANGES.md
2. 2025-02-05 #283 - new feature: use custom profile fields to identify merged old and new users (to both users). Partly contributed thanks to @sampraxis and @ClausSchmidtPraxis on 2024-11-14. PHPUnit test ensures the behaviour is the expected.
3. 2025-02-05 #294 - fix: pass tests on mod_assign again. Thanks to @leonstr.
4. 2025-02-04 #304 - fix: set up again suspended image to merged source user.
5. 2025-02-04 #253 - fix: CRLF codification passed to LF.
6. 2025-01-23 #299 - fix: fix file content to get them properly uploaded into AMOS.

## 2025012300

1. 2025-01-23 #299 - make lang file compatible with AMOS to be translatable.
2. 2025-01-23 #299 - add $plugin->release property as releasing new plugin version warns it.

## 2025012200

1. 2025-01-22 #295 - CliGathering: remove deprecation warnings from Iterator implementation.
2. 2025-01-22 #292 - Bump plugin version and requires Moodle 4.5 onwards.
   1. CI passes on green, as before, for PHP 8.1, 8.2 and 8.3 only for core MOODLE_405_STABLE.
   2. version.php updated.
   3. Improve type detection on IDE.
   4. Uses new trait on the assign_test class.
3. 2025-01-17 #291 - Make web administration work on merge users.
   1. Removed lines requiring "lib/outputcomponents.php" from two files.

## 2024060300

1. 2024-06-03 #268 - CI: verify it is working from M4.1 to M4.4 and master. Solves #263 too.
2. 2024-06-03 #268 - Fixed PHPDoc issues and revisited output from merger when running from CLI.

## 2023061900

1. 2023-06-19 #245 - Add list of incremental changes on file CHANGES.md.
2. 2023-06-19 #247 - Fix proper support for Moodle 4.2, thanks to Matthias Opitz.

## 2023040402

1. 2023-04-13 #243 - remove unused class with API inconsistence.

## 2023040401

1. 2023-04-04 #211 - add CSV export for merged user logs; added mergedbyuserid field, thanks to Mark Johnson.

## 2023040400

1. 2023-04-04 Fix CI to run only for supported versions, supporting Moodle 3.11 and up.
2. 2022-12-15 #228 - Add compound indexes for customcert_issues table, thanks to Leon Stringer.
3. 2021-08-01 #197 - Use Github Actions, remove Travis CI usage.

## 2021072200

1. 2021-07-23 #193 - Allow automatic Moodle Plugins release when defining git tab.
2. 2021-07-14 #175 - Reaggregate completion for target user, thanks to Andrew Hancox.
3. 2021-07-14 #194 - Update unit tests for Moodle 3.10+, thanks to Alistair Spark.
4. 2021-07-02 #177 - Move observer functions into classes to bypass include file error, thanks to Andrew Madden.
5. 2021-06-10 #181 - Guarantee processing any grade item.
6. 2020-02-23 #169 - Fix wrong entries deleted in case of conflict, thanks to Tim Schroeder.
7. 2019-08-18 #166 - Support for duplicated assign submissions and other fixes.
8. 2019-08-16 #67 - Improve and clean up settings.php.
9. 2019-08-16 #163 - Force user to keep not to be suspended.
10. 2019-08-16 #161 - Split in chunks the list of record ids to delete/update to prevent buffer overflow on SQL sentences.
11. 2019-08-15 #147 - Config: Add logstore_standard_log columns related to user.id.
12. 2019-08-15 #151 - Config: Add composed keys for wikis.
13. 2019-08-15 #146 - Fix searching by user.id on pgsql.
14. 2019-08-15 #152 - Support any supported Moodle database.

## Older changes

For a more extense list of changes, [see git logs for changes before April 2019](https://github.com/jpahullo/moodle-tool_mergeusers/commits/master).

# Contributors

Maintained by:

* [Jordi Pujol-Ahulló](https://www.urv.cat).
* [Nicolas Dunand](https://moodle.org/plugins/browse.php?list=contributor&id=141933).

[See all Github contributors](https://github.com/jpahullo/moodle-tool_mergeusers/graphs/contributors)
