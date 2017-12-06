moodle-availability_role
========================

[![Build Status](https://travis-ci.org/moodleuulm/moodle-availability_role.svg?branch=master)](https://travis-ci.org/moodleuulm/moodle-availability_role)

Moodle availability plugin which lets users restrict resources, activities and sections based on roles


Requirements
------------

This plugin requires Moodle 3.2+


Motivation for this plugin
--------------------------

If your teachers want to restrict activities / resources / sections in their course to a subset of the course participants and these course participants share a common course role, this plugin is for you.

Have a look at an example:

* Tim Teacher is an editing teacher in course A.
* Carl Clueless and Steve Smart are Tim's student assistants.
* As Moodle admin, you have already created a custom course role called "student assistant" in your Moodle installation. Carl and Steve have this role in course A to do their work.
* If Tim wants to provide activities / resources / sections only for Carl and Steve in course A, for example a forum activity where they can discuss internal stuff, he had to do some workarounds in the past. The most popular solution was to put Carl and Steve into a group and restrict the activities / resources / sections to this group, but there were even more complicated workarounds.

With availability_role, Tim does not need any workarounds anymore. He is just able to restrict his activities / resources / sections to a certain course role and all users who have this role in the course context have access.


Installation
------------

Install the plugin like any other plugin to folder
/availability/condition/role

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it is ready to use without the need for any configuration.

Teachers (and other users with editing rights) can add the "Role" availability condition to activities / resources / sections in their courses. While adding the condition, they have to define the role which students have to have in course context to access the activity / resource / section.

If you want to learn more about using availability plugins in Moodle, please see https://docs.moodle.org/en/Restrict_access.


How this plugin works / Pitfalls
--------------------------------

In Moodle, roles normally do not control things directly. Instead, roles contain (multiple) capabilities and these capabilities control things.

We created this availability plugin to ease the use case which is described above. The availability plugin just checks if the user has the given role and, if yes, grants access to the restricted activity.

However, there is the capability moodle/course:viewhiddenactivities (see https://docs.moodle.org/en/Capabilities/moodle/course:viewhiddenactivities) which is contained in the manager, teacher and non-editing teacher roles by default. If a user has a role which contains moodle/course:viewhiddenactivities, he is able to use an activity / resource / section even if the teacher has restricted it with availability_role to some other role.

Because of that, availability_role can't be used to hide activities / resources / sections from users who already are allowed to view hidden activities in the course. Use this availability restriction plugin wisely and explain to your teachers what is possible and what is not.


Theme support
-------------

This plugin should work with all Bootstrap based Moodle themes.
It has been developed on and tested with Moodle Core's Clean and Boost themes.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/availability_role

The latest development version can be found on Github:
https://github.com/moodleuulm/moodle-availability_role


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodleuulm/moodle-availability_role/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodleuulm/moodle-availability_role/issues

Please create pull requests on Github:
https://github.com/moodleuulm/moodle-availability_role/pulls

We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature _proposals_ and not as feature _requests_.


Moodle release support
----------------------

Due to limited resources, this plugin is only maintained for the most recent major release of Moodle. However, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major relase - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is a undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


PHP7 Support
------------

Since Moodle 3.0, Moodle core basically supports PHP7.
Please note that PHP7 support is on our roadmap for this plugin, but it has not yet been thoroughly tested for PHP7 support and we are still running it in production on PHP5.
If you encounter any success or failure with this plugin and PHP7, please let us know.


Copyright
---------

Bence Laky
Synergy Learning UK
www.synergy-learning.com

on behalf of

Ulm University
kiz - Media Department
Team Web & Teaching Support
Alexander Bias
