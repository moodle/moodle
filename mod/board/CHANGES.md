Release Notes

_NOTE - This release will work on Moodle 4.04, & 4.05_

##### 1.404.01 (Build 2024102001)

New features / improvements:

* Adding support for 404, and 405.
* Adding improved activity icon for 404, and 405.

Bug fixes:

* Fix deprecated JS calls for 404.

_NOTE - This release will work on Moodle 4.00, 4.01, 4.02 & 4.03_

##### 1.401.03 (Build 2022040112)

New features / improvements:

* Adding 4.03 support.

Bug fixes:

* Fix 4.03 default activity completion.
* Fix 4.03 embed display.

_NOTE - This release will work on Moodle 4.00, 4.01 & 4.02. (4.3 has not been released yet)_

##### 1.401.02 (Build 2022040111)

New features / improvements:

* Adding 4.01, 4.02, and PHP8.1 support.
* New board icon.
* Allow undo rate post option.
* Support svg image format.
* New Admin setting to control usage of singleuser board types.
* Keep board comments in the database for auditing / reporting.

Bug fixes:

* Fix invalid external service parameter type.
* Backup / restore info for images and link replacements.
* Fix board error on single activity course format.
* Set correct aria label to column lock icon.
* Fix board error on single activity course format.
* Get board configuration using webservice.
* Show board title and link to board in page when embedded.
* Fix board column sorting in non-single user mode and prevent text overflowing the post container.
* Fix / reverse button order for post cancel in add post modal.
* Fix delete/post comment duplication.
* Remove the requirement to set grouping.

_NOTE - This release will work on Moodle 4.00._

##### 1.400.01 (Build 2022101201)'

* Added a single user mode.
* In single user mode a teacher can post on your board.
* Teachers can move columns.
* Per board control of opening links in a new window.
* Added activity completion settings.
* CSV export includes number of "likes".
* Added setting to allow each student to have their own board.
* Added a setting to allow/prevent Youtube videos.
* Better UI for moving and editing.
* Allow columns to be locked.
* Enable commenting functionality on each post when opened in overlay.
* Change loglevel for actions to be 'participating'.
* Add ability to embed board on the course page.
* Add ability to embed board on the course page.
* Ensure move icon has proper aria labelling.
* Ensure all keyboard controls work in the View Post popup.

##### 1.39.06 (Build 2021101501)
New features / improvements:

* Added privacy API.
* Orphaned ratings are removed.

Big fixes:

* Fixed error with group context.
* Fixed manager role default access to view and manage board instances.

***Please note, if upgrading, that site administrators will need to RESET or update their manager role capabilities, ensuring that all Board plugin related capabilities are now allowed.***

The [Moodle Docs page for resetting roles is here](http://docs.moodle.org/en/Manage_roles#Reset_role_to_defaults).

##### 1.39.05 (Build 2021092101)
New features / improvements:

* New look and layout for the board.
* Allow users to move their own note placements.
* Sorting options now include "none".

##### 1.39.04 (Build 2021090801)
New features / improvements:

* Create and edit note now in a modal.
* Tooltips added

Bug fixes:

* Ensure HTML/scripts cannot be added to media additions.
