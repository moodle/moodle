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

1. It provides a report of how much data there is to upgrade.

2. It can extract test-cases from the database. This can help you report bugs
in the upgrade process to the developers.

3. You can set up cron to complete the conversion of quiz attempts, if you have
configured a partial upgrade.


If this plugin is present during upgrade:

4. then only a subset of attempts are upgraded. Read the instructions in the
partialupgrade-example.php script.


If this plugin is present in a Moodle 2.1 site after upgrade:

5. If not all attempts have been upgraded in a 2.1 site, then this plugin
displays a list of how many quizzes still need to be upgraded

6. ... and can be used to complete the upgrade manually ...

7. or this plugin has a cron script that can be used to finish the upgrade
automatically after the main upgrade has finished.

8. It can also reset any attempts that were upgraded (provided they have not
subsequently been modified) so you can re-upgrade them. This may allow you to
recover from a buggy upgrade.

9. Finally, you can still use the extract test-cases script to help report bugs.


Manual upgrades can be processed via the web interface or the command line tool
cliupgrade.php. To run cliupgrade.php, use a command similar to:
sudo -u www-data /usr/bin/php local/qeupgradehelper/cli/convert.php -h
The -h flag will show the options for running the tool.