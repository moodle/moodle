Grid Course Format
============================
A topics based format that uses a grid of user selectable images to pop up a light box of the section.

Required version of Moodle
==========================
This version works with Moodle version 2013051400.00 release 2.5 (Build: 20130514) and above until the next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/25/en/Installing_Moodle' / 'docs.moodle.org/dev/Moodle_2.5_release_notes'.

Free Software
=============
The Grid format is 'free' software under the terms of the GNU GPLv3 License, please see 'COPYING.txt'.

It can be obtained for free from:
https://moodle.org/plugins/view.php?plugin=format_grid
and
https://github.com/gjb2048/moodle-courseformat_grid/releases

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

If you reuse any of the code then I kindly ask that you make reference to the format.

If you make improvements or bug fixes then I would appreciate if you would send them back to me by forking from
https://github.com/gjb2048/moodle-courseformat_grid and doing a 'Pull Request' so that the rest of the
Moodle community benefits.

Installation
============
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no
   users using it bar you as the administrator - if you have not already done so.
3. Copy 'grid' to '/course/format/' if you have not already done so.
4. Go back in as an administrator and follow standard the 'plugin' update notification.  If needed, go to
   'Site administration' -> 'Notifications' if this does not happen.
5. Put Moodle out of Maintenance Mode.
6. You may need to check that the permissions within the 'grid' folder are 755 for folders and 644 for files.

Uninstallation
==============
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
   not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
   course the first format in the list.  You can then set the desired format.
3. In '/course/format/' remove the folder 'grid'.
4. In the database, remove the row with the 'plugin' of 'format_grid' and 'name' of 'version' in the 'config_plugins' table
   and drop the 'format_grid_icon' and 'format_grid_summary' tables.
5. Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
2. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
3. In '/course/format/' move old 'grid' directory to a backup folder outside of Moodle.
4. Copy new 'grid' to '/course/format/'.
5. Go back in as an administrator and follow standard the 'plugin' update notification.  If needed, go to
   'Site administration' -> 'Notifications' if this does not happen.
6. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
   under 'Home -> Site administration -> Development -> Purge all caches'.
7. Put Moodle out of Maintenance Mode.

Downgrading
===========
If for any reason you need to downgrade to a previous version of the format then the procedure will inform you how to
do so:
1.  Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2.  In '/course/format/' remove the folder 'grid' i.e. ALL it's contents - this is VITAL.
3.  Put in the replacement 'grid' folder into '/course/format/'.
4.  This step depends on if you are downgrading to a version prior to 15th July 2012, this should therefore only be for
    Moodle 2.3.x and below versions.  If you are, perform step 4.1 otherwise, perform step 4.2.
4.1 In the database, remove the row with the 'plugin' of 'format_grid' and 'name' of 'version' in the 'config_plugins' table
    and drop the 'format_grid_icon' and 'format_grid_summary' tables.  If automatic 'Purge all caches' appears not to work by
    lack of display etc. then perform a manual 'Purge all caches' under 'Home -> Site administration -> Development ->
    Purge all caches'.
4.2 In the database, change the row with the 'plugin' of 'format_grid' and 'name' of 'version' in the 'config_plugins' table
    to have the same 'value' as '$plugin->version' in the 'grid/version.php' file i.e. like '2013083000'.  Then perform a manual
    'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.
5.  Go back in as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
6.  Put Moodle out of Maintenance Mode.

Reporting Issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  Major version numbers
are always the same, so for Moodle 2.5.x there will be a Grid format 2.5.x.  The primary release area is located on
https://moodle.org/plugins/view.php?plugin=format_grid.  It is also essential that you are operating the required version of Moodle
as stated at the top - this is because the format relies on core functionality that is out of its control.

All 'Grid format' does is integrate with the course page and control it's layout, therefore what may appear to be an issue
with the format is in fact to do with a theme or core component.  Please be confident that it is an issue with 'Grid format'
but if in doubt, ask.

We operate a policy that we will fix all genuine issues for free (this only applies to the code as supplied from the sources listed
in 'Free Software' above.  Any changes / improvements you make are not covered and invalidate this policy for all of the code).
Improvements are at our discretion.  We are happy to make bespoke customisations / improvements for a negotiated fee.  We will
endeavour to respond to all requests for support as quickly as possible, if you require a faster service then offering payment for
the service will expedite the response.

It takes time and effort to maintain the format, therefore donations are appreciated.

When reporting an issue you can post in the course format's forum on Moodle.org (currently 'moodle.org/mod/forum/view.php?id=47'), 
on Moodle tracker 'tracker.moodle.org' ensuring that you chose the 'Non-core contributed modules' and 'Course Format: Grid'
for the component or contact us direct (details at the bottom).

It is essential that you provide as much information as possible, the critical information being the contents of the format's 
version.php file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

Usage
=====

Viewing
-------
Click on a grid icon or use 'esc' to open the current selected icon which will then display the shade box containing the section
content.  Click on the 'X' or use 'esc' to close.

Use the 'left' / 'right' cursor keys to select the previous / next section when the shade box is and is not displayed.

Use the 'left' / 'right' arrows that appear when hovering over the middle of the border when the shade box is shown to navigate to
the previous / next section.

When the 'Course layout' course setting are set to 'Show all sections on one page' the shade box will operate.  When set to
'Show one section per page' the shade box will not show but instead the icons will act like links as they do with the
'Topics' format and take you to a single section page.

Editing
-------
Use the 'Change image' link underneath each icon to change the icon's image.

Edit the sections underneath the icons in the normal way.  Note: Some things like current section colour will not update until page
refresh.

The shade box is not shown in this mode.

Making Changes
==============

Changing the keyboard control code
----------------------------------
To change the 'gridkeys.js' code then you'll firstly need to read: http://docs.moodle.org/dev/YUI/Shifter
it is used to build the source in '/yui/src/gridkeys/js/gridkeys.js' and bring in the 'gallery-event-nav-keys' to build
the YUI module into 'yui/build/moodle-format_grid-gridkeys' and place a back port minified version in '/yui/gridkeys' for
use in Moodle 2.3 and 2.4 versions - so even if you have those versions you will need this Moodle 2.5 version to
make changes.  The compiled YUI module is then loaded in all versions (2.3, 2.4 and 2.5) in 'renderer.php' by the line:
$PAGE->requires->yui_module('moodle-format_grid-gridkeys', 'M.format_grid.gridkeys.init', null, null, true);
So even though the location is different for M2.3 / M2.4 than M2.5 it's the same - that's a M2.5+ thing.  There is no
rocket science to using / learning Shifter, I did so late on a Saturday night whilst half asleep - admittedly with Andrew's
on-line assistance.

Current selected colour
-----------------------
Edit 'styles.css', change the value in the '.course-content ul.gridicons li.currentselected' selector and perform a 'Purge all caches'
or override in your theme.

Current section
---------------
Edit 'styles.css', change the value in the '.course-content ul.gridicons li.current' selector and perform a 'Purge all caches' or
override in your theme.

File information
================

Languages
---------
The grid/lang folder contains the language files for the format, such as:

* grid/lang/en/format_grid.php
* grid/lang/ru/format_grid.php
* grid/lang/es/format_grid.php
* grid/lang/fr/format_grid.php

Note that existing formats store their language strings in the main
moodle.php, which you can also do, but this separate file is recommended
for contributed formats.

Of course you can have other folders as well as English etc. if you want to
provide multiple languages.

Styles
------
The file grid/styles.css contains the CSS styles for the format which can
be overridden by the theme.

Backup
------
The files:

grid/backup/moodle2/backup_format_grid_plugin.class.php
grid/backup/moodle2/restore_format_grid_plugin.class.php

are responsible for backup and restore.

Backup and restore run automatically when backing up the course.
You can't back up the course format data independently.

Roadmap
=============
1. Improved instructions including Moodle docs.
2. User definable grid row icon numbers - https://moodle.org/mod/forum/discuss.php?d=196716
3. CONTRIB-3240 - Gridview course format more accessible.
4. CONTRIB-4099 - Grid format does not allow the user to set the size of the image / box.
5. Use of crowd funding facility to support development.
6. Continued maintenance of issues: https://tracker.moodle.org/browse/CONTRIB/component/11231.
7. Add in grid format specific capabilities to change things.

Known Issues
=============
1. All listed on https://tracker.moodle.org/browse/CONTRIB/component/11231.
2. Unable to delete a grid icon image.

History
=============
3rd October 2013 Version 2.5.4.4 - Stable.
Change by G J Barnard
  1.  Fix broken call to '_is_empty_text' as reported on CONTRIB-4589.

2nd October 2013 Version 2.5.4.3 - Stable.
Change by G J Barnard
  1.  Fixed sections not being shown when in 'Show one section per page' mode and editing.  Thanks to
      Zdravko Stoimenov for reporting this.
  2.  Changed 'editimage.php' to ensure that only the icon is removed when changing it.  No specific
      issue just refactoring the code as a preventative measure.
  3.  Fixed section 0 content displaying when it's in the grid, you first load a page and click on another
      section.  Thanks to Llywelyn Morgan for reporting this.

12th September 2013 Version 2.5.4.2 - Stable.
Change by G J Barnard
  1.  Changed 'JSON' code in 'module.js' to use the YUI library for JSON to support situations where the 'JSON'
      library is not built into the browser.  Thanks to Colin Taylor for providing information of a situation I
      could not have possibly tested.
Note: If you have already installed V2.5.4 or V2.5.4.1 then this is not an essential upgrade.

12th September 2013 Version 2.5.4.1 - Stable.
Change by G J Barnard
  1.  Commented out 'console.log' code in 'module.js'.
  2.  Removed old 'gridkeys.js' from 'javascript' folder.
Note: If you have already installed V2.5.4 then this is not an essential upgrade.

10th September 2013 Version 2.5.4 - Stable.
Change by G J Barnard
  1.  Partial implementation of CONTRIB-3240.  Thanks to Andrew Nicols for helping with the YUI module code
      on: https://moodle.org/mod/forum/discuss.php?d=237275.
      This means that it is now possible to navigate using the keyboard with the 'left' / 'right' cursor keys
      being used to perform previous section / next section respectively and the 'esc' key to toggle open / closed
      the shade box.  As a bonus of this change I've added in navigation arrows to the shade box which appear when
      you hover over the middle of the sides - cool eh?
      Initially I also added Shift-TAB (previous section) / TAB (next section) / Enter (open shade box) /
      Shift-Enter (close shade box) keys to but after much deliberation (and logic issues) I have decided that until
      WIA-ARIA is fully understood I'll leave them out.  Once much more information is known I'll put them back in.
      Also thanks to Enrico Canale and Darren Britten of La Trobe University for their support and information.

      Note:  If you're wondering where the M2.3 and M2.4 versions are, well I intend to release them a few days after
             this M2.5 version so that any bugs that have not been found can be fixed once without having to re-release
             three versions.
  2.  'module.js' has been completely reworked so that it is efficient and documented.
  3.  Added Pirate language.

30th August 2013 Version 2.5.3.3 - Stable
Change by G J Barnard
  1.  Implemented CONTRIB-4580 - Highlight current section.
  2.  Implemented CONTRIB-4579, thanks to all who helped on https://moodle.org/mod/forum/discuss.php?d=236075.
  3.  At the request of Tim St.Clair I've changed the code such that the sections underneath the icons are hidden
      by CSS when JavaScript is enabled so that there is no 'flash' as previously JS would perform the hiding.
  4.  Added 'Downgrading' instructions above.
  5.  Added 'Upgrading' instructions above.
  6.  Added 'Known Issues' above.

22nd August 2013 Version 2.5.3.2 - Stable
Change by G J Barnard
  1.  Fixed icon container size relative to icon size.
  2.  Added 'alt' image attribute information being that of the section name.
  3.  Tidied up more styles such that to pre-empt conflicts.

10th August 2013 Version 2.5.3.1 - Stable
Change by G J Barnard
  1.  Fixed CONTRIB-4216 - Error importing quizzes with grid course format.
  2.  Fixed CONTRIB-4253 - mdl_log queried too often to generate New Activity tag.  This has been fixed by using the 'course_sections'
      table instead to spot when a new activity / resource has been added since last login.

4th August 2013 Version 2.5.3 - Stable
Change by G J Barnard
  1.  Fixed scroll to top when clicking on an icon.  Thanks to Javier Dorfsman for reporting this.
  2.  Added in code developed by Nadav Kavalerchik to facilitate multi-lingual support for the 'new activity' icon.  Thank
      you Nadav :).
  3.  Adapted the width of the shade box such that it is dynamic against the size of the window.

5th July 2013 Version 2.5.2 - Stable
Change by G J Barnard
  1.  Code refactoring to reduce and separate the format as a separate entity.
  2.  Corrected as much as possible as detected by 'Code Checker' version 2013060600 release 2.2.7.
  3.  Once the first box is shown then the 'Enter' key will toggle the 'current' box hidden and shown.
  4.  Changed the order of the history so that the latest change is at the top.

14th May 2013 Version 2.5.1 - Stable
Change by G J Barnard
  1.  First stable version for Moodle 2.5 stable.

12th May 2013 - Version 2.5.0.2 - Beta
Change by G J Barnard
  1. Removed '.jumpmenu' from styles.css because of MDL-38907.
  2. Added automatic 'Purge all caches' when upgrading.  If this appears not to work by lack of display etc. then perform a
     manual 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.
  3. Changes for MDL-39542.

13th April 2013 - Version 2.5.0.1 - Beta version.
Change by G J Barnard
  1. First 'Beta' release for Moodle 2.5 Beta.

24th February 2013 - Version 2.4.1 - Stable version.
Change by G J Barnard
  1. Changes because of MDL-37901.
  2. Invisible section fix for Tim Wilde - https://moodle.org/mod/forum/discuss.php?d=218505#p959249.
  3. This version considered 'Stable' from feedback of Theo Konings on CONTRIB-3534.

21st January 2013 - Version 2.4.0.2 - Alpha version, not for production servers.
Change by G J Barnard
  1. Changes to 'renderer.php' because of MDL-36095 hence requiring Moodle version 2012120301.02 release 2.4.1+ (Build: 20130118) and above.

12th January 2013 - Version 2.5.0.1 - Alpha version, not for production servers.
1. Migrated code to Moodle 2.5 development version.

9th January 2013 - Version 2.4.0.5 - Beta version, not for production servers.
Change by G J Barnard
  1. Fixed issue in editimage.php where the GD library needs to be used for image conversion for transparent PNG's.
  2. Perform a 'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches' after this is installed.

3rd January 2013 - Version 2.4.0.4 - Beta version, not for production servers.
Change by G J Barnard
  1. Fixed issue where the grid did not function in 'One section per page mode' on the course settings.

21st December 2012 - Version 2.4.0.3 - Beta version, not for production servers.
Change by G J Barnard
  1. Hopefully eliminated BOM issue (http://docs.moodle.org/24/en/UTF-8_and_BOM) that was causing the failure of the images to display.

18th December 2012 - Version 2.4.0.2 - Alpha version, not for production servers.
Change by G J Barnard
  1. Second alpha release for Moodle 2.4

18th December 2012 - Version 2.4.0.1 - Alpha version, not for production servers.
Change by G J Barnard
  1. First alpha release for Moodle 2.4

Authors
-------
J Ridden - Moodle profile: https://moodle.org/user/profile.php?id=39680 - Web: http://www.moodleman.net
G J Barnard - Moodle profile: moodle.org/user/profile.php?id=442195 - Web profile: about.me/gjbarnard