moodle-availability_password
============================

[![Moodle Plugin CI](https://github.com/moodle-an-hochschulen/moodle-availability_password/workflows/Moodle%20Plugin%20CI/badge.svg?branch=main)](https://github.com/moodle-an-hochschulen/moodle-availability_password/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3Amain)

Moodle availability plugin which lets users restrict resources and activities with password access


Requirements
------------

This plugin requires Moodle 4.5+


Motivation for this plugin
--------------------------

There may come the day in a Moodle admin's life when a teacher requests to protect his course resources from unauthorized access even beyond the mechanisms which Moodle already provides and when this teacher comes up with the idea of adding a "password protection" to uploaded resources. If you had a discussion like this, this plugin is for you.


Installation
------------

Install the plugin like any other plugin to folder
/availability/condition/password

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it is ready to use without the need for any configuration.

Teachers (and other users with editing rights) can add the "Password" availability condition to activities / resources in their courses. While adding the condition, they have to define the password which will be requested from students before they can access the activity / resource for the first time. For subsequent access, the availability plugin remembers that the student has already given the correct password once and does not bug him anymore.

If you want to learn more about using availability plugins in Moodle, please see https://docs.moodle.org/en/Restrict_access.


Protection course sections
--------------------------

At the moment, availability_password can't be used to protect course sections, it can only be applied to single activities / resources in a course.

For those who are curious: There is a technical reason for this restriction. Most availability plugins use some external data to decide on the availability. This plugin uses data which is internal to the plugin itself. As availability data is stored in a serialised object in the course_modules table, it has no unique identifying ID of its own. Instead we use the course module id to be the link between the passwords already entered, the particular availability condition and the password being attempted (as appropriate).
It would be possible to rework the plugin to work with sections as well by processing the availability information in the course_sections table as well, but, as that was not our goal, we did not attempt the (slightly tricky) reworking to allow this yet.

If you really want availability_password to protect course sections, please let us know on https://github.com/moodle-an-hochschulen/moodle-availability_password/issues or, ideally, submit a pull request on https://github.com/moodle-an-hochschulen/moodle-availability_password/pull.


Granting only temporary access
------------------------------

At the moment, if a student has input the correct password for an activity / resource, availability_password remembers this fact and grants access for this particular student until forever (Special case: Or until, for whatever reason, a teacher changes the password for the activity / resource).

However, there might be scenarios with even higher security demands which make it necessary that a student inputs the password every time he wants to re-access the activity / resource after a login and to have Moodle forget the fact that the user has input the correct password after he has logged out or his Moodle session has expired.

availability_password also supports this use case. Go to Site administration -> Plugins -> Availability restrictions -> Restriction by password and change the "Remember password entered" setting to "Until the user logs out".

Note:
Moodle core did not support settings pages for availability conditions until 2.9.5 and 3.0.3 (see https://tracker.moodle.org/browse/MDL-49620).

So, if your are running a legacy version of Moodle and you really need to limit the memory of availability_password to a user's session, you have to set the plugin's configuration directly in the DB. This can be done with this SQL command:

INSERT INTO mdl_config_plugins ("plugin", "name", "value") VALUES ('availability_password', 'remember', 'session');

Please only run this SQL command if you really know what you are doing. After running the SQL command, you might have to clear your Moodle cache for the change to take effect.


Restricting usage
-----------------

The feature which availability_password provides is necessary / useful in specialized scenarios / environments. However, you might not want every teacher in your Moodle instance to be able to add passwords to their activities / resources because this will put additional barriers between your students and your content and might even harm the acceptance of Moodle among students in your institution.

Because of that and in contrast to other availability plugins, availability_password supports a capability availability/password:addinstance which lets you control who is able to add this condition to activities / resources and who is not. By default, the capability is granted to managers, coursecreators and editing teachers at plugin installation time, but feel free to change this setup within your Moodle role configuration.

By the way, if teacher A who has this capability adds the condition to an activity / resource and teacher B who has not the capability edits this activity / resource, B is able to see, edit and delete the condition of this particular activity / resource, but is still not allowed to add the condition to another activity / resource in the course.


Capabilities
------------

This plugin also introduces these additional capabilities:

### availability/password:addinstance

This capability controls who is able to add password conditions to activities.


Scheduled Tasks
---------------

This plugin does not add any additional scheduled tasks.


How this plugin works
---------------------

Normally, authentication plugins just use existing information from a course to decide about the access to an activity / resource. This plugin goes beyond this approach and inteligently adds a popup window between the course overview page view and the activity / resource view asking the user for a password.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/availability_password

The latest development version can be found on Github:
https://github.com/moodle-an-hochschulen/moodle-availability_password


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodle-an-hochschulen/moodle-availability_password/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodle-an-hochschulen/moodle-availability_password/issues

Please create pull requests on Github:
https://github.com/moodle-an-hochschulen/moodle-availability_password/pulls

We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature _proposals_ and not as feature _requests_.


Moodle release support
----------------------

Due to limited resources, this plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is an undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


Maintainers
-----------

The plugin is maintained by\
Moodle an Hochschulen e.V.


Copyright
---------

The copyright of this plugin is held by\
Moodle an Hochschulen e.V.

Individual copyrights of individual developers are tracked in PHPDoc comments and Git commits.


Initial copyright
-----------------

Initial copyright
-----------------

This plugin was initially built by\
Davo Smith\
Synergy Learning UK\
www.synergy-learning.com

on behalf of\
Ulm University\
Communication and Information Centre (kiz)

and maintained and published by\
Ulm University\
Communication and Information Centre (kiz)\
Alexander Bias

It was contributed to the Moodle an Hochschulen e.V. plugin catalogue in 2022.
