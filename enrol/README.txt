ENROLMENT MODULES
-----------------

(Yes, that's the correct English spelling  ;-) )

enrol.class.php contains a simple 'factory' method that
will instantiate your class when called. For an example
of a complete class, take a look at the 'manual' class.

Each plugin is in a subfolder here.

Except for the configuration methods, most methods
defined in the API are optional -- callers will use
method_exists() to determine whether your plugin offers
the functionality they are after.


Mandatory methods
=================

  config_form()
  process_config()


Login-time methods
==================

  Before Moodle 1.7
  -----------------

      get_student_courses()
      get_teacher_courses()

  You probably will want to offer at least get_student_courses().

  These methods are triggered when a user logs in successfully,
  and they are expected to populate $USER->student and
  $USER->teacher arrays and maintain (add/delete) entries from
  user_students and user_teachers.

  These methods are relevant for most plugins, and are the main
  interest for plugins that work with a read-only backend such
  as LDAP or a database.

  Note that with the multi-enrol infrastructure two things have
  changed. We now have an 'enrol' field in those tables, and
  each plugin must maintain only its own enrolment records.
  Conversely, the $USER->student and ->teacher arrays have the
  enrolment type as value, like

     $USER->student = array ( $courseid => $plugintype );


  Moodle 1.7 and later
  --------------------

      setup_enrolments()

  With the advent of roles, there could well not be students and
  teachers any more, so enrolment plugins have to be more flexible
  about how they map outside data to the internal roles.

  This one method should do everything, calling functions from
  lib/accesslib.php as necessary to set up relationships.


Interactive enrolment methods
=============================

  print_entry()
  check_entry()
  check_group_entry()
  get_access_icons()

These methods are for enrolment plugins that allow for user
driven enrolment. These methods are relevant for plugins
that implement payment gateways (credit card, paypal),
as well as "magic password" schemes.

Only one interactive enrolment method can be active for
a given course. The site default can be set from
Admin->Enrolment, and then individual courses can be
set to specific interactive enrolment methods.


Cron
====

If your class offers a cron() method, it will be invoked by
the standard Moodle cron every time it is called. Note that if the
tasks are not lightweight you must control how frequently they
execute, perhaps offering a config option.

For really heavy cron processing, an alternative is to have
a separate script to be called separately. Currently the LDAP
and DB plugins have external scripts.


Guilty Parties
--------------

Martin Dougiamas and Shane Elliott, Moodle.com
Martin Langhoff and Patrick Li, Catalyst IT

