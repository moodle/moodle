The questionnaire module allows you to construct questionnaires (surveys) from a
variety of question type. It was originally based on phpESP, and Open Source
survey tool.

--------------------------------------------------------------------------------
To Install:

1. Load the questionnaire module directory into your "mod" subdirectory.
2. Visit your admin page to create all of the necessary data tables.

--------------------------------------------------------------------------------
To Upgrade:

1. Copy all of the files into your 'mod/questionnaire' directory.
2. Visit your admin page. The database will be updated.
3. As part of the update, all existing surveys are assigned as either 'private',
   'public' or 'temmplate'. Surveys assigned to a single questionnaire are set
   to 'private' with the questionnaire's course as the owner. Surveys assigned
   to multiple questionnaires in the same course are set to 'public' with the
   questionnaire's course as the owner. Surveys assigned to multiple
   questionnaires in multiple courses are set to 'public' with the site ID as
   the owner. Surveys that are not deleted but have no associated questionnaires
   are set to 'template' with the site ID as the owner.

*** IMPORTANT ***

IF YOU ARE UPGRADING TO MOODLE 2.3...

Make sure that you upgrade the questionnaire module to the latest 2.2 version in
a Moodle 2.2 install first.

--------------------------------------------------------------------------------
Version 2.4.1 - Release date 20130519

In accordance with current Moodle languages policy, all language folders other than English have been
removed from the lang folder. All translations are now available from AMOS.

--------------------------------------------------------------------------------
Please read the releasenotes.txt file for more info about successive changes
--------------------------------------------------------------------------------

