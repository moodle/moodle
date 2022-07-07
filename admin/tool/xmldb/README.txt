XMLDB - Base classes and edition interface.

Complete Documentation:

  http://docs.moodle.org/en/XMLDB_Defining_one_XML_structure

Ciao, Eloy Lafuente (stronk7)

========== ========== ========== ========== ==========
========== ==========   HISTORY  ========== ==========
========== ========== ========== ========== ==========

2006-08-07 - Editor working on production

The editor has been used succesfully to build
a bunch of install.xml files and everything
seems to be working properly.

========== ========== ========== ========== ==========

2006-07-11 - PHP4 compatible release

Now everything seems to be working under PHP 4. What
a horrible OOP implementation!

Note that write permissions to */db dirs are required.

Now working in the 3 missing forms, to manually edit
fields, keys and indexes.

Ciao, Eloy Lafuente (stronk7)

========== ========== ========== ========== ==========

2006-07-11 - Important notes

I've just discovered this some seconds ago, in order
to test properly the XMLDB classes and editor:

1.- PHP 5 required for now. Will change this soon.
2.- Perms to "apache" user needed in */db
    dirs in order to allow the XMDBD interface
    to write files.

Ciao, Eloy Lafuente (stronk7)

========== ========== ========== ========== ==========

2006-07-11 - Initial commit

This directory contains the XMLDB classes to be used
under Moodle > 1.7 to store all the DB info in a
neutral form (classes dir). Also it contains one simple
interface to edit all those structures.

To install and test it, simply copy the whole xmldb directory
under your moodle/admin dir and point your browser (as admin)
to http://your.server/moodle/admin/xmldb

The edition interface isn't completed yet (it laks 3 more forms
to edit fields, keys and indexes) and there isn't any lang file
(although I hope everything is really clear).

The edition interface includes one reverse-engineering tool that
provides an easy way to retroffit and to generate any table from
MySQL to the new XMLDB format.

Once the XMLDB format was approved, we'll be able to build all the
"generators" needed in order to use it to create Moodle DB structures
for each RDBMS flavour.

Once the interface was finished (2-3 days from now) related documentation
will be sent to http://docs.moodle.org/en/XML_database_schema in order
to comment/modify/approve the final XML format.

All the code is, obviously GPL, with its copyrights and so on...

Ciao, Eloy Lafuente (stronk7) :-)
