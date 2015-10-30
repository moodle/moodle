Course formats
==============

To create a new course format, make another folder in here.

If you want a basic format, you only need to write the 'standard files' listed
below.

If you want to store information in the database for your format, or control
access to features of your format, you need some of the optional files too.

All names below assume that your format is called 'yourformat'.


Standard files
--------------

* yourformat/format.php

  Code that actually displays the course view page. See existing formats for
  examples.

* yourformat/config.php

  Configuration file, mainly controlling default blocks for the format.
  See existing formats for examples.

* yourformat/lang/en/format_yourformat.php

  Language file containing basic language strings for your format. Here
  is a minimal language file:

<?php
$string['formatyourformat']='Your format'; // Name to display for format
$string['nameyourformat']='section'; // Name of a section within your format
?>

  The first string is used in the dropdown menu of course settings. The second
  is used when editing an activity within a course of your format.

  Note that existing formats store their language strings in the main
  moodle.php, which you can also do, but this separate file is recommended
  for contributed formats.

  You can also store other strings in this file if you wish. They can be
  accessed as follows, for example to get the section name:

  get_string('nameyourformat','format_yourformat');

  Of course you can have other folders as well as just English if you want
  to provide multiple languages.


Optional files (database access)
--------------------------------

If these files exist, Moodle will use them to set up database tables when you
visit the admin page.

* yourformat/db/install.xml

  Database table definitions. Use your format name at the start of the table
  names to increase the chance that they are unique.

* yourformat/db/upgrade.php

  Database upgrade instructions. Similar to other upgrade.php files, so look
  at those for modules etc. if you want to see.

  The function must look like:

  function xmldb_format_yourformat_upgrade($oldversion) {
  ...

* yourformat/version.php

  Required if you use database tables.

  <?php
  $plugin->version  = 2006120100; // Plugin version (update when tables change)
  $plugin->requires = 2006092801; // Required Moodle version
  ?>


Optional files (backup)
-----------------------

If these files exist, backup and restore run automatically when backing up
the course. You can't back up the course format data independently.

* yourformat/backuplib.php

  Similar to backup code for other plugins. Must have a function:

  function yourformat_backup_format_data($bf,$preferences) {
  ...

* yourformat/restorelib.php

  Similar to restore code for other plugins. Must have a function:

  function yourformat_restore_format_data($restore,$data) {
  ...

  ($data is the xmlized data underneath FORMATDATA in the backup XML file.
  Do print_object($data); while testing to see how it looks.)


Optional file (capabilities)
----------------------------

If this file exists, Moodle refreshes your format's capabilities
(checks that they are all included in the database) whenever you increase
the version in yourformat/version.php.

* yourformat/db/access.php

  Contains capability entries similar to other access.php files.

  The array definition must look like:

  $format_yourformat_capabilities = array(
  ...

  Format names must look like:

  format/yourformat:specialpower

  Capability definitions in your language file must look like:

  $string['yourformat:specialpower']='Revolutionise the world';



Optional file (styles)
----------------------

* yourformat/styles.php

  If this file exists it will be included in the CSS Moodle generates.


Optional delete course hook
---------------------------

* in your yourformat/lib.php add function format_yourformat_delete_course($courseid)