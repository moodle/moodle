Changelog
=========

v3.17.1
-------

Quality of life

- Suspended users are marked as such in the report and logs

Bug fixes

- Deleted and suspended users are excluded from leaderboards
- Deleted users are now excluded from the report and logs

v3.17.0
-------

New features

- Participants can be searched for by name in the reports and logs
- Admins will receive important notices about compatibility issues
- Admin compatibility check now validates if a development version is used

Quality of life

- Managers can view the info and leaderboard pages even when they are not enabled
- Settings for most features have been moved to their respective pages
- Most action and setting forms open without leaving the page
- Other UX and quality of life improvements

Bug fixes

- Delete modules could be displayed in some rule selection screens
- The navbar display was showing in courses where XP is not enabled
- The block rendering could cause unexpected issues in the mobile app
- Fixed rendering issues of some shortcodes in the mobile app
- Other minor fixes and improvements

Technical changes

- Compatibility with Moodle 4.5
- Raised minimum required version to Moodle 4.1

Read our [release blog post](https://www.levelup.plus/blog/xp-quest-release-oct-2024/) to learn more.

v3.16.0
-------

New features

- Admins can reset the levels of all courses to the default values
- Admins can reset the appearance of the levels in all courses to the defaults
- Massive invisible underground work to support future major improvements

Quality of life

- Renamed "For the whole site" to "Sitewide"
- Level up notification popup can be previewed from the levels page
- Dangerous actions are coloured accordingly

Bug fixes

- Mitigated rare issue with tooltips on some themes
- Other minor fixes and improvements

Technical changes

- Compatibility with Moodle 4.4
- Raised minimum required version to Moodle 3.11

Read our [release blog post](https://www.levelup.plus/blog/xp-release-3-16/) to learn more.

v3.15.2
-------

Technical changes

- Improved compatibility with PHP 8.2
- Addressing false positive in automated tests

v3.15.1
-------

Bug fixes

- Levels incorrectly started at 2 when restored from pre 3.15.0 backup

v3.15.0
-------

New features

- The user level badge can be displayed in the top navigation bar
- The level up notification animated the transition to the new level
- Redesign the levels customisation page to simplify setting their points
- Points shortcode can display the number of points of the current user
- Admin setting allow admin users to collect points like other users

Quality of life

- Warn admins when they are editing the default settings in site-wide mode
- The levels can be reset to the admin defaults from the levels page
- The levels appearance can be reset to the admin defaults from the appearance page
- Improved the design of several pages
- Display compatibility checks in the admin general settings page
- Renamed "Visuals" to "Appearance"

Bug fixes

- Fixed JavaScript error when trying to dismiss notice to managers
- The level up event could incorrectly trigger when editing user points
- Other minor fixes and improvements

Technical changes

- Compatibility with Moodle 4.3
- Plugin declare which Moodle versions are explicitly supported

Read our [release blog post](https://www.levelup.plus/blog/xp-release-3-15/) to learn more.

v3.14.1
-------

Bug fixes

- Fix bug occurring with PHP 7.0
- Address compatibility issues with Moodle 3.4
- Prevent notice when `debugusers` setting is undefined

Technical changes

- Address false positives in automated tests

v3.14.0
-------

Quality of life

- Adjusted styles and rules for better flow and readability

Bug fixes

- Prevent narrow page layout from extending out of bounds
- Restoring backup into existing course honours delete contents option
- Remove notices about deprecated events for debug users
- Other minor fixes and improvements

Technical changes

- Raised minimum required version to Moodle 3.4
- Compatibility with Moodle 4.2
- Compatibility with PHP 8.1

Notes

- We are proud to have released [Quest](https://www.levelup.plus/quest?ref=release-notes), another gamification plugin. Read our [launch post here](https://www.levelup.plus/blog/quest-moodle-gamification-plugin?ref=release-notes).

v3.13.3
-------

Bug fixes

- Suppress notice about undefined return type with PHP 8.1
- Fixed failing report task due to incorrect reference to XP+

v3.13.2
-------

Bug fixes

- Some JS files were not loaded in development mode

v3.13.1
-------

Bug fixes

- The block's ranking snapshot of a neighboured leaderboard was not always correct

Technical changes

- Neighboured leaderboard get_ranking, get_count and get_position behaviours changed
- More suppression of developer notices about early renderer instantiation

v3.13.0
-------

New features

- Display a snapshot of the leaderboard on the block
- Showcase the next level a user is progressing towards on the block
- Logs can be filtered for an individual user

Quality of life

- The block appearance has been updated to look more modern
- Navigate to a user's logs directly from the report
- Actions against users in report moved to a dropdown menu
- Renamed "Ladder" to "Leaderboard"

Bug fixes

- Deleting a user removes its associated logs allowing them to re-earn points
- Block appearance settings were not included in backups

Technical changes

- Compatibility with Moodle 4.1
- Remove usage of deprecated YUI module formchangechecker

v3.12.2
-------

Technical changes

- Addressed false negatives in unit tests

v3.12.1
-------

Bug fixes

- Default rules could not be saved in the admin settings
- Usage sharing incorrectly counted number of rules

v3.12.0
-------

New features

- Display student's rank on the block
- New shortcode `[xppoints]` to display number of points
- Additional capability to control access to the report was added
- Optional usage sharing functionality

Quality of life

- Block title can be left empty
- Block appearance settings always set from the plugin's settings
- Navigation improvements by introducing sub-navigation in some pages

Bug fixes

- Shortcode `[xpladder]` with `top` did not display top for neighboured leaderboards
- Leaderboard only links to profile when course profiles are enabled
- Event `user_leveledup` could be skipped when leveling up multiple levels at once
- Other minor bug fixes and improvements

Technical changes

- Raised minimum required version to Moodle 3.3
- Compatibility with Moodle 4.0
- Compatibility with PHP 8
- Support for optional activation of Level Up XP+ for shared hosting (beta)

Additional notes

- The migration of the block appearance settings will happen automatically in most cases. Those settings include the block title, the description shown to students, and whether or not to displays rewards. If those did not migrate accurately, their values can be set from the "Block appearance" section at the bottom of the plugin's settings page.
- The "Log" page is now found under "Report", and the "Visuals" page under "Levels".
- Level Up is renamed Level Up XP, and Level Up Plus becomes Level Up XP+.


v3.11.4
-------

Bug fixes

- Class block_edit_form not found in rare occasions

v3.11.3
-------

Quality of life

- Changed `addinstance` capability to support block_context

Bug fixes

- Fixed invalid reference to language string in levels page

v3.11.2
-------

Bug fixes

- Some CSS styles were not applied for some older Moodle versions

v3.11.1
-------

Quality of life

- Update information in promotional page

v3.11.0
-------

New features

- Complete revamp of the user interface to setup the levels
- Rule picker redesigned to include a description of each rule

Quality of life

- Confirmation asked when deleting a rule or condition with children
- A help message is displayed when other plugins cause the rules screen to crash

Bug fixes

- The leaderboard was not accessible when using Oracle
- The filters on the log table could not be removed when empty

Technical changes

- Compatibility with Moodle 3.11

v3.10.2
-------

Bug fixes

- The filtered_config class did not work with excluded keys

v3.10.1
-------

Technical changes

- Rules base class includes the get_renderer method for children classes to use
- Switched to using numday, numweek, etc. language strings for durations in log settings
- Travis configuration changes

v3.10.0
-------

New features

- Administrators can enforce the anonymity of the leaderboard
- An additional capability to control access to the logs was added

Bug fixes

- The report could be stuck in an empty state after using some filters

Quality of life

- Display a warning when leaving the rules page without saving
- Inform users when the cheat guard settings may become ineffective
- Keep promo page visible when the add-on has been installed

_Level Up XP+_ users may be required to upgrade the add-on for some of these changes to take effect.

v3.9.0
------

New features

- Support additional Privacy API requirement (core_userlist_provider)

Bug fixes

- Fixed vertical alignment of content in report table

Quality of life

- Report and logs display a nicer notice when page is empty

Technical changes

- Level change is identified from within the state store
- Report controller to support additional actions from add-on

v3.8.1
------

Bug fixes

- The cheat guard miscounted the max of actions in time frame
- Sorting the logs by points raised a database error
- The manage permission is now required to search courses in the course rule
- Missing bind when handling exception in module resource selector

Quality of life

- Display a warning when the plugin configuration and URLs mismatch

Technical changes

- Changes to filters and rules to support grade-based rewards in _Level Up XP+_

v3.8.0
------

New features

- Include support for the shortcode `xplevelname`
- Compatibility with Moodle 3.8

Bug fixes

- Restored rules are now updating their internal configuration
- Shortcode `xpladder` would display even when ladder is disabled
- Minor fixes to notification behat test
- Other minor bug fixes and improvements

Quality of life

- Increased the size of the description field in levels form
- Prevent the notices from being announced to screen readers

v3.7.0
------

- Levels can be given a custom name
- Added ability to completely remove the points of a user
- Added filter support (e.g. multi lang) to block description and title - David Bogner
- The report displays a hyphen as level for users that do not have any points
- Minor bug fixes and improvements

Some of these changes were sponsored by Xi'an Jiaotong-Liverpool University.

v3.6.1
------

- New permission `block/xp:manage` for more granular management control
- Include instructions from information page in backups
- Include ladder columns setting in backups

v3.6.0
------

- Option to add arbitrary information to the information
- The information page has been restyled
- Added an option to change the pagination size of the ladder
- The shortcode `xpladder` supports displaying the top users
- Fix minor bug causing issues with some themes

Some of these changes were sponsored by Xi'an Jiaotong-Liverpool University.

v3.5.1
------

- Minor changes

v3.5.0
------

- Allow selection of activity from any course
- Admins can reset the rules of all courses to the defaults
- Teachers can reset their course's rules to the defaults
- Increase minimum required version to Moodle 3.1
- Fixed a rare bug in drag and drop of rules
- Remove entry files for legacy URLs
- Minor styles fixes in rules screen

v3.4.0
------

- Support GIFs and SVGs for level badges
- Maintenance for Level Up XP+ group leaderboards

v3.3.1
------

- Maintenance for Level Up XP+

v3.3.0
------

- New shortcode `xpladder` for displaying the ladder

v3.2.1
------

- Add support for shortcodes using [filter_shortcodes](https://github.com/branchup/moodle-filter_shortcodes)
- Minor fixes and improvements

v3.2.0
------

- Implement privacy API (GDPR compliance)
- Some preferences were not deleted during course deletion
- Prevent rare exception during event collection

v3.1.1
------

- Blocks could not be added or removed from the plugin's page
- The ordering within a level is no longer random in the report
- The promotional notice disappears when dismissed

v3.1.0
------

- Changes to support Moodle Mobile in _Level Up XP+_
- Colours of default badges were revised
- Event 'course_viewed' added to the list of events
- Permit capability overrides at category, course and module levels
- Stronger checks for disabling points gain when the block is removed
- Fix routing issue with nginx
- Minor other bug fixes and improvements

v3.0.2
------

- Fixed a bug causing major disruptions in the official mobile app
- Allow the level algorithm coefficient to be 1, instead of 1.001 previously
- Recent rewards default now correctly refers to the admin setting value
- Stylistic changes to improve design with Boost theme

v3.0.1
------

- Improved compatibility with older plugins depending on Level Up XP

v3.0.0
------

- Administrators can set the defaults for all settings
- The block optionally displays their recent rewards to students
- The default rules are no longer locked and can be changed by administrators and teachers
- The visual appearance of the block has been improved
- Made ready for compatibility with the [Level Up XP+ add-on](https://github.com/FMCorz/moodle-local_xp)
- Visuals no longer require an image to be uploaded for each level
- A preview of the badges is displayed in the visuals page
- The levels are displayed as a graphic in pages accessible to students
- Added option to control what additional columns to display in the ladder
- User experience improvement for setting-up the cheat guard options
- The block appearance options can now directly be set from within the settings page
- Logging is now required and always enabled
- Logging options became admin-only settings
- The block's description text can be dismissed by students
- Various bug fixes and improvements

v2.2.0
------

- Option to disable the cheat guard
- Fixed a bug preventing access to rules for site-wide block
- Fixed a bug always showing the tab to access the ladder

v2.1.2
------

- Fixed a bug causing a blank ladder on older PHP versions
- Fixed a bug where some settings where not backed up
- Fixed a bug where some rules filters were not restored
- Other minor bug fixes and improvements

v2.1.1
------

- Fixed a bug where new settings were not saved on new installs

v2.1.0
------

- Level up notification always appears on the right course
- Ladder option to only display neighbours in the ranking
- Ladder option to hide the rank, or display a relative rank
- Ladder option to hide other participants identity
- Ladder opens on the page where the current user is located
- Performance improvements on ladder and report
- Fixed a bug where some students with 0 XP would appear at the top of the report
- Minor usability improvements

v2.0.1
------

- Fix composer installation directory
- Fix MySQL compability when resetting all course data
- Fix bug causing visuals not to be displayed when XP is used site-wide

v2.0.0
------

- Experience points can be used either for the whole site or per course
- Course groups are considered when displaying the report and the ladder
- \mod_book\event\course_module_viewed has been added to the list of redundant events
- Complete new user interface to set up rules (nested conditions, drag and drop, ...)
- Ability to filter events per activity
- Added a user friendly condition to filter per event
- Anonymous events are now always ignored
- Bug fixes

v1.5.2
------

- Plugin can be installed using composer
- Bug fixes

v1.5.1
------

- Fixed a bug causing cron to fail

v1.5
----

- Improving cheat guard
- Exposing some options of the cheat guard
- Report displays all the students, not just the ones with XP
- Introducing backup and restore functionality
- Removing associated data when a course is deleted

v1.4
----

- Ability to customise the appearance of the level badges

v1.3
----

- Notification popup when a student levels up
- Ability to customise the XP gained with rules

v1.2
----

- Events are not captured for guests and non-logged in users
- Added a ladder page to display their rank to the students
- New capability controlling who earns XP
- Admin users do not earn XP any more
- Customising the levels and the required XP
- New info page displaying the levels, a description and the XP required
- Ability to edit the XP of a user


v1.1
----

- Performance improvements

v1.0
----

- Display current level in block
- Display progress bar in block
- Report page to see the progress of everyone
- Log page to see the events that were captured
- 3 settings to set the number of levels, enable logging and how long to store the logs for
- Capture participating events that match CRU (from CRUD)
- Basic algorithm and xp attribution
- Event fired when someone levels up
