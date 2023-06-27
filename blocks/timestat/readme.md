# Timestat block for Moodle

This block measure time of real activity done by Moodle users. Measured activity time is incremented only when Moodle tab in web browser is active (it is done via Javascript).

## Installation

Install block in standard way (copy it to '/moodle/blocks' folder and click 'Notifications' in admin panel)

IMPORTANT FOR USERS OF PREVIOUS VERSIONS (less than 2014090400):
Previous versions of this app used column named 'timespent' in 'log' table. Now time is stored in table 'block_timestat'.
It is recommended to uninstall previous version first and install this new version next.

BACKUP YOUR DATABASE BEFORE EXECUTING FOLLOWING QUERIES:

Old data can be copied to new table with query:
INSERT INTO mdl_block_timestat (log_id, timespent) SELECT id, timespent FROM mdl_log;

After this the field 'timestpent' can be removed from table 'mdl_log':
ALTER TABLE mdl_log DROP timespent;

## Usage

The block only counts the time on the pages to which it has been added, so you need to add the block on the pages where you want to count the time. If you want to add the block on the course page and on all activity pages at once, please refer to the following documentation:
https://docs.moodle.org/400/en/Block_settings#Making_a_block_sticky_throughout_a_course

The content of the block displays a link to a time spent report. This report allows filtering by course, activity and user. By default this report is only visible by the following roles: editingteacher, teacher, coursecreator, manager and admin. The 'block/timestat:view' capability allows to extend this permission to other roles.

## More information

This application was developed in cooperation by team composed of:
Barbara Dębska
Łukasz Musiał
Łukasz Sanokowski

Upgrade from 1.9 to 2.5 version was made thanks to contribution of:
Classroom Revolution
Lib Ertea
Mart van der Niet
Joseph Thibault

## Contact

mostly prefered by forum discussion:
http://moodle.org/mod/forum/discuss.php?d=167732

## License

Licensed under the [GNU GPL License](http://www.gnu.org/copyleft/gpl.html).