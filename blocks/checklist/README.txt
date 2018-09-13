This is a block which works with the checklist module and displays progress bars for a single checklist.

It will not work without the checklist module - which can be downloaded here:
http://moodle.org/plugins/view.php?plugin=mod_checklist

==Changes==

* 2018-04-02 - declare plugin stores no user data (for GDPR), this version only compatible with M3.4+
* 2015-12-23 - hide block from guests / users without 'updateown' capability (and no 'viewreports' capability)
* 2015-01-01 - Display overview of all courses, when added to My home or Front page (thanks to Richard Wallace for this)
* 2014-09-08 - Match up to new checklist activity after backup & restore
* 2013-11-19 - Moodle 2.6 compatibility fixes
* 2012-12-07 - Moodle 2.4 compatibility fixes
* 2012-11-27 - Fixed problems with no students appearing in certain situations
* 2012-09-22 - Fixed bug in group selection when user is not a member of any groups
* 2012-09-19 - Split the 3 plugins (mod / block / grade report) into separate repos for better maintenance
* 2012-08-06 - Now able to change groups via a menu inside the block (the settings page now defines the default group to display)
* 2012-07-07 - Tested against Moodle 2.3
* 2012-01-27 - French translation from Luiggi Sansonetti
* 2012-01-02 - Minor tweaks to improve Moodle 2.2+ compatibility (optional_param_array / context_module::instance )
