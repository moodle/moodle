Merge users plugin for Moodle
===============================

This admin tool will merge two Moodle user accounts, "user A" and "user B".
The intent of the plugin is to assign all activity & records from user A to
user B. This will give the effect of user B seeming to have done everything
both users have ever done in Moodle. 

Moodle requirements and release notes
=====================================

Check the CHANGES.md file for Moodle version requirements, system requirements
and any news related to the current release of this plugin.

How merge users plugin works
============================

The basic function of the plugin is to
loop through the tables and update the userid of every record from user A to
user B. This works well for most tables. We do however, have a few special
cases:

* Special Case #1: The grade_grades table has a compound unique key on userid
    and itemid. This prevents a simple update statement if the two users have
    done the same activity. What this script does is determine which activities
    have been completed by both users and delete the entry for the old user
    from this table. Data is not lost because a duplicate entry can be found in
    the grade_grades_history table, which is correctly updated by the regular
    processing of the script.
* Special Case #2: The user_enrolments table controls which user is enrolled
    in which course. Rather than unenroll the old user from the course, this
    script simply updates their access to the course to "2" which makes them
    completely unable to access the course. To remove these records all
    together I recomend disabling or deleting the entire old user account once
    the migration has been successful.
* Special Case #3: There are 4 logging/preference tables
    (user_lastaccess, user_preferences, user_private_key, my_pages) which exist in
    Moodle 2.x. This script is simply skipping these tables since there's no
    legitimate purpose to updating the userid value here. This would lead to
    duplicate rows for the new user which is silly. Again, if you want to
    remove these records I would recommend deleting the old user after this
    script runs sucessfully. my_pages' records will not be deleted, but
    this is something you find in Moodle, that not all records related to a
    specific entity are clened up. We need to skip my_pages table too, since
    that MyMoodle page of the old user have a relation of blocks appearing on it.
    If we proceed with a normal merging action, resulting with two records
    with the same userid, the user will not see correctly his/her MyMoodle page.
    Due to a community request, all these tables except my_pages can be omitted
    from this exclusion and so, if set in settings, they can be merged as usual.
* Special Case #4: mod/journal plugin has a record per user and journal on
    journal_entries table. In case there is a record for both users, we delete
    the record related to the old user. For the rest of cases, this operates as usual.
* Special Case #5: groups_members table has a record per user and group.
    Updating always the old user id for the new one is incorrect if both users
    appear in that group. In that case, this plugin deletes the record related
    to the old user. For the rest of cases, this plugin operates as usual.
* Special Case #6: course_completions table has a record per user and course.
    Updating always the old user id for the new one is incorrect if both users
    appear in that group. In that case, this plugin deletes the record related
    to the old user. For the rest of cases, this plugin operates as usual.
* Special case #7: message_contacts table has a record per user and contact id,
    which is again a user.id. If replacing the old id by the new one means
    index conflict, this means actually that the resulting record already exists,
    so we can securely remove the old record. In addition, this checking is performed
    for both column names (userid and contactid) looking for matching on both
    in the same way.
* Special case #8: role_assignments table has a three-field unique index,
    including context, role and userid. As before, it always updates records to
    be the new one. If only old id exists, it is updated; if only new id exists,
    it does nothing; if both ids exist, the record with the old id is removed.
* Special case #9: user_lastaccess table has a two-field unique index, including
    userid and courseid. In case both users has a record for the same
    courseid, this plugin removes the record for the old user and keep that one
    for the new user. For the rest of cases, this plugin operates as usual.
* Special case #10: quiz_attempts table has a three-field unique index, including
    userid, quiz and attempt. This table and related quiz_grades and quiz_grades_history
    are processed as specified in the plugin settings. This plugin provies you
    several options when merging quiz attempts from two users:
 1. Merge attempts from both users and renumber. Attempts from the old user are
    merged with the ones of the new user and renumbered by the time they were
    started.
 2. Keep attempts from the new user. Attempts from the old user are removed.
    Attempts from the new user are kept, since this option considers them as the
    most important.
 3. Keep attempts from the old user. Attempts from the new user are removed.
    Attempts from the old user are kept, since this option considers them as the
    most important.
 4. Do nothing: do not merge nor delete (by default). Attempts are not merged nor
    deleted, remaining related to the user who made them. This is the most secure
    action, but merging users from user A to user B or B to A may produce different
    quiz grades.
* Special case #11: there are cases where third party plugins build unique indexes
    applied onto a single column related to user.id. In such cases, we have added
    a new setting "uniquekeynewidtomaintain" that helps handles this conflict.
    If you mark this option (by default), data related to the new user is kept.
    Otherwise, if you unmark this setting, this plugin will keep the data from
    the old user.


Command-line script
===================

A cli/climerger.php script is added. You can now perform user mergings by command line having
their user ids.

You can go further and develop your own CLI script by extending the Gathering interface
(see lib/cligathering.php for an example). Ok, but let us explain how to do it step by step:

1. Develop a class, namely MyGathering, in lib/mygathering.php, implementing the interface Gathering.
Be sure the class name and the filename are the same, but filename all in lowercase ending with ".php".
See lib/cligathering for an example.
2. Create or edit the file config/config.local.php with at least the following content:

```php
<?php

return array(

    // gathering tool
    'gathering' => 'MyGathering',
);
```
3. Run as a command line in a form like this: *$ time php cli/climerger.php*.


Events and event management
===========================

Once the merging action is completed, an event 'merging_success' is triggered if it was ok,
or an event 'merging_failed' otherwise. The available data on the event are as follows:

* oldid: the user.id of the "user A" to be removed from all his/her activity.
* newid: the user.id of the "user B" which will gather the activity of both users.
* log: string with the list of actions performed.
* timemodified: time in which the event is generated, after the merging action.

The goal of this event triggering is the ability to be detected by other parts of Moodle.

This plugin also manages the 'merging_success' event is trigered, what includes:

1. Suspending the user (user.suspended = 1). This prevents the person to log in with the old account.
2. Changing the old user's profile picture by the given on pix/suspended.jpg. It is a simple
   white image with the text "suspended user", which could help to teachers and
   managers to rapidly detect them.


Correct way of testing this plugin
==================================

First of all, check `admin/settings.php?section=iomadmerge_settings` for the
description of the setting `tool_iomadmerge | transactions_only`
**if your database type and version supports transcations**. If so,
**no action will actually be committed if something goes wrong**.

Mainly, these are the main steps to test this plugin:

1. You should have a replica of your Moodle instance, with a full replica of your Moodle database where you run this plugin.
2. Run a sufficient amount of user merging to check if anything goes wrong.
3. What if...?
    1. ... all was ok? You are almost confident that all will be ok also in your production instance of Moodle.
    2. ... something went wrong? There are several reasons for that:
        1. Non-core plugins installed on your Moodle and not assumed in this plugin.
        2. Local database changes on Moodle that may affect to the normal execution of this plugin.
        3. Some compound index not detected yet.

If in your tests or already in production something went wrong, please, report the error log on the
official plugin website on moodle.org. And if you have some PHP skill, you can try to solve it
and share both the error and the patch to solve it ;-)


Common sense
============

Before running this plugin, it is highly recommended to back up your database.
That will help you to restore the state before any merging action was done.

This plugin stores a log for any user merging, with the list of actions done or
errors produced. If your database supports transactions (see above section),
automatic rollbacks are done at database level, so that your database state
remains consistent.

However, running this plugin in databases without
transaction support can put you in trouble. That is, there is no provision for
automatic rollbacks, so if something were to fail midway through,
you will end up with a half-updated database. Nevertheless, if you found a
problem when merging users A and B, do not panic. Merging will be successfully
completed when a solution for your problem is included into this plugin, and
you rerun merging users A and B.

Development
===========

Developing and testing phase
============================

We recommend to use the [moodlehq/moodle-docker](https://github.com/moodlehq/moodle-docker)
project to run your own Moodle instances for developing and testing.

PHPUnit testing
===============

To quickly setup your own development, we suggest to run the command:

```
php admin/tool/phpunit/cli/util.php --buildcomponentconfigs
```

as documented at https://docs.moodle.org/dev/PHPUnit to have the `phpunit.xml`
file under `admin/tool/iomadmerge/phpunit.xml`.

Then, you can run all plugin's tests as follows:

```
vendor/bin/phpunit -c admin/tool/iomadmerge
```

or also like this, without the need of running the `buildcomponentconfigs`:

```
vendor/bin/phpunit --group tool_iomadmerge
```

There are also other PHPUnit groups created to help testing only the part
of the plugin of your choice. Take a look at the tests code for other group names.

License
=======

GNU GPL v3 or later. http://www.gnu.org/copyleft/gpl.html

Contributors
============

Maintained by:
* Nicolas Dunand.
* Jordi Pujol-Ahulló (at SREd, Universitat Rovira i Virgili).

[See all Github contributors](https://github.com/ndunand/moodle-tool_iomadmerge/graphs/contributors)
