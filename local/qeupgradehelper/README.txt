This plugin can help upgrade site with a large number of question attempts from
Moodle 2.0 to 2.1.

With a lot of question attempts, doing the whole conversion on upgrade is very
slow. The plugin can help with that in various ways.


To install using git, type this command in the root of your Moodle install
    git clone git://github.com/timhunt/moodle-local_qeupgradehelper.git local/qeupgradehelper
Then add /local/qeupgradehelper to your git ignore.

Alternatively, download the zip from
    https://github.com/timhunt/moodle-local_qeupgradehelper/zipball/master
unzip it into the local folder, and then rename the new folder to qeupgradehelper.


When installed in a Moodle 2.0 site:

1. It provies a report of how much data there is to upgrade.

2. It can extract test-cases from the database. This can help you report bugs
in the upgrade process to the developers.

3. This plugin can also do a dry-run of the upgrade. (It loads the old data from
the database, transform it to the new form, but not write the transformed data
to the database.)


If this plugin is present during upgrade:

4. then only a subset of attempts are upgraded. (You can edit a function in
this plugin to control which attempts are upgraded immediately.)


If this plugin is present in a Moodle 2.0 site after upgrade:


5. If not all attempts have been upgraded in a 2.1 site, then this plugin
displays a list of how many quizzes still need to be upgraded

6. ... and can be used to complete the upgrade manually ...

7. or this plugin has a cron script that can be used to finish the upgrade
automatically after the main upgrade has finished.


(Note that none of the above acutally works yet. It is just a statement of
intent. Lots of the code here is a partial implementation of the concepts.)