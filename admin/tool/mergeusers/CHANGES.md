Release notes
=============

2025020505

1. 2025-02-05 - bump version to make a new plugin version available for M4.5.

2025020503

1. 2025-02-05 - fix: URL on old and new user profile fields definition.
2. 2025-02-05 - improvement: CLI script now shows better log of merging operations.

2025020502

1. 2025-02-05 #295 - fix: remove deprecation warning on CLIGathering, related to Iterator. Thanks to @CatSema. 

2025020501

1. 2025-02-05 - bump version, update CHANGES.md, and kept support only for M4.1.

2025020500

1. 2025-02-05 - bump version and update CHANGES.md
2. 2025-02-05 #283 - new feature: use custom profile fields to identify merged old and new users (to both users). Partly contributed thanks to @sampraxis and @ClausSchmidtPraxis on 2024-11-14. PHPUnit test ensures the behaviour is the expected.
3. 2025-02-05 #294 - fix: pass tests on mod_assign again. Thanks to @leonstr.
4. 2025-02-04 #304 - fix: set up again suspended image to merged source user.
5. 2025-02-04 #253 - fix: CRLF codification passed to LF.
6. 2025-01-23 #299 - fix: fix file content to get them properly uploaded into AMOS.

2025012300

1. 2025-01-23 #299 - make lang file compatible with AMOS to be translatable.
2. 2025-01-23 #299 - add $plugin->release property as releasing new plugin version warns it.

2025012200

1. 2025-01-22 #295 - CliGathering: remove deprecation warnings from Iterator implementation.
2. 2025-01-22 #292 - Bump plugin version and requires Moodle 4.5 onwards.
   1. CI passes on green, as before, for PHP 8.1, 8.2 and 8.3 only for core MOODLE_405_STABLE.
   2. version.php updated.
   3. Improve type detection on IDE. 
   4. Uses new trait on the assign_test class.
3. 2025-01-17 #291 - Make web administration work on merge users.
   1. Removed lines requiring "lib/outputcomponents.php" from two files. 

2024060300

1. 2024-06-03 #268 - CI: verify it is working from M4.1 to M4.4 and master. Solves #263 too.
2. 2024-06-03 #268 - Fixed PHPDoc issues and revisited output from merger when running from CLI.

2023061900

1. 2023-06-19 #245 - Add list of incremental changes on file CHANGES.md.
2. 2023-06-19 #247 - Fix proper support for Moodle 4.2, thanks to Matthias Opitz.

2023040402

1. 2023-04-13 #243 - remove unused class with API inconsistence.

2023040401

1. 2023-04-04 #211 - add CSV export for merged user logs; added mergedbyuserid field, thanks to Mark Johnson.

2023040400

1. 2023-04-04 Fix CI to run only for supported versions, supporting Moodle 3.11 and up.
2. 2022-12-15 #228 - Add compound indexes for customcert_issues table, thanks to Leon Stringer.
3. 2021-08-01 #197 - Use Github Actions, remove Travis CI usage.

2021072200

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

For a more extense list of changes, [see git logs for changes before April 2019](https://github.com/jpahullo/moodle-tool_mergeusers/commits/master).

Contributors
============

Maintained by:

* [Jordi Pujol-Ahulló](https://www.urv.cat).
* [Nicolas Dunand](https://moodle.org/plugins/browse.php?list=contributor&id=141933).

[See all Github contributors](https://github.com/jpahullo/moodle-tool_mergeusers/graphs/contributors)
