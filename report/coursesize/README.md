# Course Size Report 

This plugin provides approximate disk usage by Moodle courses. 


## Branches

| Moodle version    | Branch             | PHP  |
| ----------------- | ------------------ | ---- |
| Moodle 3.9+       | `MOODLE_39_STABLE` | 7.2+ |
| Totara 12+        | `totara-master`    | 7.2+ |
| Moodle 3.5 to 3.8 | `MOODLE_35_STABLE` | 7.2+ |

There are 2 known shortcomings with this plugin
* If the same file is used multiple times within a course, the report will report an inflated disk usage figure as the files
  will be counted each time even though Moodle only stores one copy of the file on disk.
* If the same file is used within multiple courses it will be counted in each course and there is no indicator within the
  report to inform the user if they delete the course or files within the course they will not free that amount from disk.

It should be possible to improve the report to address these issues - we'd greatly appreciate any patches to improve the plugin!


Copyright 2014 Catalyst IT http://www.catalyst.net.nz