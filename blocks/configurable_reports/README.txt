Configurable Reports Block

Installation, Documentation, Tutorials....
See http://docs.moodle.org/en/blocks/configurable_reports/
Also http://moodle.org/mod/data/view.php?d=13&rid=4283

Author: Juan Leyva
<http://moodle.org/user/profile.php?id=49568>
<http://twitter.com/jleyvadelgado>
<http://sites.google.com/site/mooconsole/>
<http://moodle-es.blogspot.com>
<http://openlearningtech.blogspot.com>

Thanks to:
Nadav Kavalerchik for developing amazing new features
Ivan Breziansky for translating the block to slovak language
Iñaki Arenaza for translating the block documentation to spanish
Luis de Vasconcelos for testing the block
Adam Olley and Netspot Moodle Partner for improving some parts of the Moodle2 version

Some functionalities of this plugin uses code from:

Admin Report: Custom SQL queries
http://moodle.org/mod/data/view.php?d=13&rid=2884
By Tim Hunt


VERSIONS HISTORY

4.1.0 (2023120600) for Moodle 4.1
Release date: 06.12.2023
----------------------------------------------------------------------
This release focuses on solving issues in Moodle 4.1 and PHP 7.4 and start with small improvements and bug fixes.
- Reformat code
- Add CI testing based on Github Actions created by Catalyst IT
- Add PHP 7.4 support - minimum PHP version is now 7.4
- Add PHP 8.0 support
- Add PHP 8.1 support
- pChart library updated to version 2.4.0
- JS tablesorter library updated to version 2.31.3
- JS CodeMirror library updated to version 5.65.16
- Move repository to https://github.com/Lesterhuis-Training-en-Consultancy/moodle-block_configurablereports
- Original repository (https://github.com/jleyva/moodle-block_configurablereports) can be used for older release or use the old branches within this repository
- removed cr_add_to_log not used anymore
- Important this branch is not backwards compatible with Moodle 3.11 and lower

Thanks to Lesterhuis Training & Consultancy for the contribution / updated by Ldesign Media

- TODOS for the future:
  * Namespaces for classes & autoloading
  * Move all to `classes` to `classes/` directory
  * Clean up code
  * Add tests
  * AJAX_SCRIPT move to webservice
  * unserialize() to json_decode()
  * Readme to Markdown format
  * validation if a class exists and inherits the correct parent class.
  * Changelog to separate file
  * Move cron to a scheduled task
  * phpcpd and phpdocs checks in CI
  * Make CSS strict should only affect this plugin!

----------------------------------------------------------------------


3.9.0 (2019122000) for Moodle 3.4, 3.5, 3.6, 3.7, 3.8, 3.9
Release date: Tuesday, 3 November 2020
----------------------------------------------------------------------
- Starting this version for allowing SQL queries performing data insertion/creation the following configuration variable set to 1
  is required in your site root /config.php:
  $CFG->block_configurable_reports_enable_sql_execution = 1;
  Otherwise, previous Custom SQL reports performing data insertion/creation will stop working.
- Added matching colors to pie charts
- Added unmapped palette for general colors to pie charts
- Changed fuserfield to allow for multiple instances of the filter
- Added alphabetical sort to courses filter
- Added webservice to get reports data
- Added new filters for competencies
- Other fixes and improvements:
  * Fix offset error for bar graphs when not using SQL reporting
  * Removed default sorting from DataTables JS UI
  * Fixed issue where colors would mismatch if values weren't present
  * Fixed STARTTIME and ENDTIME variable substitution
  * Avoid text filtering when exporting

Thanks Alex Rowe, David Saylor, Michael Gardener, Muhammad Osama Arshad, Daniel Poggenpohl, Daniel Neis, François Parlant and all the contributors who have sent several fixes and improvements.


3.8.0 (2019122000) for Moodle 3.4, 3.5, 3.6, 3.7, 3.8
Release date: Friday, 20 Dec 2019
----------------------------------------------------------------------
- Order the list of users in users filter by fullname considering language - #130
- Other fixes and improvements:
  * Fix template array error - #132
  * Fix DB mismatch between install.xml and upgrade.php - #123
  * Fix LIMIT error for MSSQL - CONTRIB-7891
  * Remove "_" from the table header titles - #137
  * Review supported Moodle versions, because the core_userlist_provider interface is only supported from 3.4 onwards - #125


Thanks Daniel Neis (danielneis), Mike McDougal (mcdoogs), Tuan Ngoc Nguyen (tuanngocnguyen), Mike Henry (mhenry79mnet), safatshahinsd, kristian-94 and all the contributors who have sent several fixes and improvements.
And special thanks also to Carlos, Moodle HQ and Juan for letting Sara work on this again during the project week.


3.3.0 (2019121900) for Moodle 3.0, 3.1, 3.2, 3.3
Release date: Friday, 20 Dec 2019
----------------------------------------------------------------------
- Order the list of users in users filter by fullname considering language - #130
- Add a new calculation type: percentage
- Allow userfields to be used more than once in the permissions
- Move JS to AMD modules
- Upgrade some 3rd party JS libraries
- Fix import from XML
- Review the year filter to take min and max years from the calendar factory
- Other fixes and improvements:
  * Fix template array error - #132
  * Fix DB mismatch between install.xml and upgrade.php - #123
  * Fix LIMIT error for MSSQL - CONTRIB-7891
  * Remove "_" from the table header titles - #137
  * Review supported Moodle versions, because the core_userlist_provider interface is only supported from 3.4 onwards - #125
  * Fix error when using templates
  * Fix error when using the Start/End filter
  * Fix legacy_polyfill error when running unit tests
  * Display custom title for the block
  * Replace deprecated methods (htmleditor, pix_url and coursecat)
  * Remove CSS files not found error in JS console
  * Show breadcrumbs for users without manage report capability
  * Fix DB query function call for the enrolled students filter
  * Raise the memory limit when exporting
  * Clean some request parameters before using them
  * Fix error with user field search box filter
  * Improve compatibility with PostgreSQL when importing


Thanks Daniel Neis (danielneis), Mike McDougal (mcdoogs), Tuan Ngoc Nguyen (tuanngocnguyen), Mike Henry (mhenry79mnet), safatshahinsd, kristian-94, David (davidpesce), Dimitrii (dmitriim), Alex (agrowe), Donald (emyb2), sopnep15, Danniel (dannielarriola) and all the contributors who have sent several fixes and improvements.
And special thanks also to Carlos, Sander, Moodle HQ and Juan for letting Sara work on this again during the project week.


3.7.0 (2019060300) for Moodle 3.4, 3.5, 3.6, 3.7
Release date: Monday, 3 Jun 2019
----------------------------------------------------------------------
- Add a new calculation type: percentage
- Allow userfields to be used more than once in the permissions
- Other fixes and improvements:
  * Fix error when using templates
  * Fix error when using the Start/End filter
  * Fix legacy_polyfill error when running unit tests
  * Display custom title for the block


Thanks David (davidpesce), Dimitrii (dmitriim) and all the contributors who have sent several fixes and improvements.
And special thanks also to Sander, Moodle HQ and Juan for letting Sara work on this again during the project week.


3.6.0 (2019021500) for Moodle 3.4, 3.5, 3.6
Release date: Friday, 15 Feb 2019
----------------------------------------------------------------------
- Implement Privacy API
- Replace deprecated methods:
  * htmleditor
  * pix_url
  * coursecat
- Move JS to AMD modules
- Upgrade some 3rd party JS libraries
- Fix import from XML
- Review the year filter to take min and max years from the calendar factory
- Other fixes and improvements:
  * Remove CSS files not found error in JS console
  * Show breadcrumbs for users without manage report capability
  * Fix DB query function call for the enrolled students filter
  * Raise the memory limit when exporting
  * Clean some request parameters before using them
  * Fix error with user field search box filter
  * Improve compatibility with PostgreSQL when importing

Thanks Alex (agrowe), Donald (emyb2), sopnep15, Danniel (dannielarriola) and all the contributors who have sent several
fixes and improvements.
And special thanks also to Sander, Moodle HQ and Juan for letting Sara work on this during the project week.



3.1.1 (2016020103) for Moodle 3.0, 3.1
Release date: Monday, 10 Oct 2016
----------------------------------------------------------------------
- Fix a critical bug when adding user action/outline statistics, see https://tracker.moodle.org/browse/CONTRIB-4600

Thanks Marina Glancy for the fix


3.1.0 (2016020102) for Moodle 3.0, 3.1
Release date: Wednesday, 25 May 2016
----------------------------------------------------------------------
- Several bug fixes


3.0.1 (2016020101) for Moodle 3.0
2.3.9 (2011040121) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9
Release date: Wednesday, 17 Feb 2016
----------------------------------------------------------------------
- Fix bug when creating the category list


3.0.0 (2016020100) for Moodle 3.0
Release date: Tuesday, 02 Feb 2016
----------------------------------------------------------------------
- Compatible version with Moodle 3.0


2.3.8 (2011040120) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9
Release date: Tuesday, 02 Feb 2016
----------------------------------------------------------------------
- Release including bug fixes


2.3.7 (2011040119) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9
Release date: Monday, 03 Aug 2015
----------------------------------------------------------------------
- Several bug fixes
- Fixed installation for old moodle versions
- Improved pie chats

Thanks CV&A and Carlos Escobedo for the contribution


2.3.6 (2011040118) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9
Release date: Wednesday, 27 May 2015
----------------------------------------------------------------------
- Minor release fixing a regression

Thanks to Dan Marsden for the fix


2.3.5 (2011040116) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9
Release date: Wednesday, 06 May 2015
----------------------------------------------------------------------
- Added support for Moodle 2.8 and 2.9
- Added support to the new logging system
- Improved the color palette for graphics with lots of elements
- Allow translations of DataTable strings
- Restored the fsearchuserfield plugin
- Minor issues fixes

Thanks albergasset and jpeak5 for their fixes


2.3.4 (2011040114) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7
Release date: Thursday, 26 June 2014, 18:16
----------------------------------------------------------------------
- New fullname user field column
- Several bug fixes


2.3.3 (2011040113) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6, 2.7
Release date: Friday, 13 June 2014, 18:16
----------------------------------------------------------------------
- Fixed layout and notice/warnings problems


2.3.2 (2011040110) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6
Release date: Friday, 14 February 2014, 11:14 AM
----------------------------------------------------------------------
- Several bug fixes


2.3.2 (2011040109) for Moodle 2.2, 2.3, 2.4, 2.5, 2.6
Release date: Thursday, 30 January 2014, 10:33 AM
----------------------------------------------------------------------
- Fixed invalid table reference in cron


2.3.2 (2011040108) for Moodle  2.2, 2.3, 2.4, 2.5m 2.6
Release date Thursday, 16 January 2014, 15:25 AM
----------------------------------------------------------------------
- Some bug fixes
- New CSV export


2.3.1 (2011040107) for Moodle 2.0, 2.1, 2.2, 2.3, 2.4, 2.5m 2.6
Release date Monday, 16 December 2013, 10:25 AM
----------------------------------------------------------------------
- Some minor bug fixes
- SQL syntax by default is disabled
- New block instance settings (change name and also show/hide global reports)
- New users cohorts condition


2.3 (2011040106) for Moodle 2.0, 2.1, 2.2, 2.3, 2.4, 2.5m 2.6
Release date Friday, 13 December 2013, 4:35 PM
----------------------------------------------------------------------
- Support for Moodle 2.6
- Multiple bugs fixed
- Global report that can be shared in all courses
- Public reports repository with multiple sample reports available
- Public SQL queries repository
- Reports can run on a different DB that the current (production) DB
- Reports can run on a CRON scheduler
- Several filter plugins added
- Integrated DataTables.js for the report table
- Integrated CodeMirror.js for highlighting SQL query code, while editing.
- New security settings

Thanks to Nadav Kavalerchik for providing most of the new features


2.2 (2011040105) for Moodle 2.0, 2.1, 2.2, 2.3, 2.4, 2.5
Release date Wednesday, 27 February 2013, 9:35 AM
----------------------------------------------------------------------
- Support for Moodle 2.4 and 2.5
- Bugs fixed


2.1 (2011040103) for Moodle 2.0, 2.1, 2.2, 2.3
Release date Friday, 6 July 2012, 1:29 PM
----------------------------------------------------------------------
- Support for Moodle 2.3
- Bugs fixed


2.0.2 (2011040102) for Moodle 2.0, 2.1, 2.2
Release date Monday, 9 January 2012, 11:41 AM
----------------------------------------------------------------------
- Support for Moodle 2.2
- Bugs fixed


2.0.1 (2011040101) for Moodle 2.0, 2.1
Release date Thursday, 13 October 2011, 12:55 AM
----------------------------------------------------------------------


2.0 (2011040100) for Moodle 2.0, 2.1
Release date Thursday, 29 September 2011, 10:47 AM
----------------------------------------------------------------------


1.0 (2010090100) for Moodle 1.9
Release date Thursday, 29 September 2011, 10:38 AM
----------------------------------------------------------------------
