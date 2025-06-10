moodle-local_bulkenrol
======================

[![Moodle Plugin CI](https://github.com/moodle-an-hochschulen/moodle-local_bulkenrol/workflows/Moodle%20Plugin%20CI/badge.svg?branch=MOODLE_401_STABLE)](https://github.com/moodle-an-hochschulen/moodle-local_bulkenrol/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3AMOODLE_401_STABLE)

Moodle plugin which provides the possibility to bulk enrol a list of users who are identified by their e-mail adresses into a course.


Requirements
------------

This plugin requires Moodle 4.1+


Motivation for this plugin
--------------------------

In some organizations or some teaching scenarios, manually enrolling students into a course may be the preferred way. However, enrolling one user at a time into a course can be daunting for teachers. On the other hand, teachers may not be allowed to use (or be able to understand) the Moodle core upload CSV functionality.

To ease the life of teachers, there is the need for a bulk enrolment tool. There are already plugins out there which provide this functionality, so this is just another one. The goal of this bulk enrolment implementation is not to fulfil everybody's needs, but to do one thing and to do this well.

So, the key features of this plugin are:
1. to let teachers submit a line-separated list of email addresses to enrol them into a course,
2. to let teachers submit this list to a textarea within their course instead of requiring them to create and upload a CSV file first.


Installation
------------

Install the plugin like any other plugin to folder
/local/bulkenrol

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet.

To configure the plugin and its behaviour, please visit:
Site administration -> Plugins -> Enrolments -> User bulk enrolment

There, you find two settings:

### 1. Enrolment plugin
The enrolment method to be used to bulk enrol the users. If the configured enrolment method is not active / added in the course when the users are bulk-enrolled, it is automatically added / activated.

### 2. Role
The role to be used to bulk enrol the users.

### 3. Navigation node placement
The location where the navigation node for this functionality will be added within a course.


Capabilities
------------
This plugin also introduces a new capability:

### local/bulkenrol:enrolusers

By default, Moodle users are _not_ allowed to use the functionality provided by this plugin. As administrator, you can selectively grant users the ability to use this functionality by adding the local/bulkenrol:enrolusers capability to an appropriate Moodle role.


Scheduled Tasks
---------------

This plugin does not add any additional scheduled tasks.


How this plugin works
---------------------

Teachers (rather users who have been granted the capability which is described in the "Capabilities" section above) will find an additional "User bulk enrolment" menu item within the jump menu on the course's participants page.

To enrol existing Moodle users into the course, the teacher will then have to add a list of e-mail adresses to the form on this page, one user / e-mail adress per line.

Example:
```
alice@example.com
bob@example.com
```

Optionally, the teacher will be able to create groups and add the enrolled users to the groups. All he has to do is to add a heading line with a hash sign and the group's name, separating the list of users.

Example:
```
# Group 1
alice@example.com
bob@example.com
# Group 2
carol@example.com
dave@example.com
```


Limitations
-----------

This plugin currently only accepts a list of e-mail adresses to be enrolled into a course. It does especially not accept lists of user names, matriculation IDs or something else.

Additionally, this plugin only enrols users who already exist in Moodle. It won't create Moodle user accounts on-the-fly.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/local_bulkenrol

The latest development version can be found on Github:
https://github.com/moodle-an-hochschulen/moodle-local_bulkenrol


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodle-an-hochschulen/moodle-local_bulkenrol/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodle-an-hochschulen/moodle-local_bulkenrol/issues

Please create pull requests on Github:
https://github.com/moodle-an-hochschulen/moodle-local_bulkenrol/pulls

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

This plugin was initially built by\
Soon Systems GmbH\
www.soon-systems.de

on behalf of\
Ulm University\
Communication and Information Centre (kiz)

and maintained and published by\
Ulm University\
Communication and Information Centre (kiz)\
Alexander Bias

It was contributed to the Moodle an Hochschulen e.V. plugin catalogue in 2022.
