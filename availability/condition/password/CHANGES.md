moodle-availability_password
============================

Changes
-------

### v4.1-r4

* 2024-10-27 - Tests: Call parent setup() function when instantiating PHPUnit tests.

### v4.1-r3

* 2024-08-11 - Add section for scheduled tasks to README
* 2024-08-11 - Updated Moodle Plugin CI to latest upstream recommendations

### v4.1-r2

* 2023-10-14 - Add automated release to moodle.org/plugins
* 2023-10-14 - Make codechecker happy again
* 2023-10-10 - Updated Moodle Plugin CI to latest upstream recommendations
* 2023-04-30 - Tests: Updated Moodle Plugin CI to use PHP 8.1 and Postgres 13 from Moodle 4.1 on.

### v4.1-r1

* 2023-01-21 - Prepare compatibility for Moodle 4.1.

### v4.0-r2

* 2023-03-19 - Bugfix: Availability password restrictions could not be edited anymore in lesson and external tool activity instances, solves #22. 
* 2023-03-09 - Make codechecker happy again
* 2022-02-28 - Fix location of Bootstrap in phpunit.xml
* 2022-11-28 - Updated Moodle Plugin CI to latest upstream recommendations

### v4.0-r1

* 2022-07-12 - Fix availability form styling (which didn't use proper Bootstrap styles up to now)
* 2022-07-12 - Fix Behat tests which broke with Moodle 4.0.
* 2022-07-12 - Make codechecker happy again
* 2022-07-12 - Prepare compatibility for Moodle 4.0.

### v3.11-r3

* 2022-07-10 - Add Visual checks section to UPGRADE.md
* 2022-07-10 - Add Capabilities section to README.md

### v3.11-r2

* 2022-06-26 - Make codechecker happy again
* 2022-06-26 - Updated Moodle Plugin CI to latest upstream recommendations
* 2022-06-26 - Add UPGRADE.md as internal upgrade documentation
* 2022-06-26 - Update maintainers and copyrights in README.md.

### v3.11-r1

* 2021-06-13 - Prepare compatibility for Moodle 3.11.
* 2021-06-13 - Added definition for a PHPUnit availability_cohort_testsuite.
* 2021-06-13 - Added type parameter to save().

### v3.10-r2

* 2021-02-05 - Move Moodle Plugin CI from Travis CI to Github actions

### v3.10-r1

* 2021-01-09 - Fix PHPUnit function declaration for Moodle 3.10.
* 2021-01-09 - Prepare compatibility for Moodle 3.10.
* 2021-01-06 - Change in Moodle release support:
               For the time being, this plugin is maintained for the most recent LTS release of Moodle as well as the most recent major release of Moodle.
               Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.
* 2021-01-06 - Improvement: Declare which major stable version of Moodle this plugin supports (see MDL-59562 for details).

### v3.9-r1

* 2020-07-15 - Prepare compatibility for Moodle 3.9.

### v3.8-r1

* 2020-02-12 - Prepare compatibility for Moodle 3.8.
* 2019-07-04 - Optimized behat steps.

### v3.7-r1

* 2019-06-14 - Added more behat tests to cover all options for setting remember.
* 2019-06-14 - Make codechecker happier.
* 2019-06-12 - Prepare compatibility for Moodle 3.7.

### v3.6-r2

* 2019-04-17 - Bugfix: Click handler on activity icon broke YUI script execution at least on dashboard when our plugin block_course_overview_campus was placed there

### v3.6-r1

* 2019-04-08 - Add click handler to the activity icon, not only to the activity title.
* 2019-04-08 - Adopt some bootstrap styles for the password modal.
* 2019-04-08 - Fix layout of condition form in Boost.
* 2019-04-08 - Add new privacy API functions needed for Moodle 3.6 - Credits to Davo Smith.
* 2019-01-11 - Fixed Behat tests.
* 2019-01-11 - Added title to dialogue window.
* 2019-01-10 - Check compatibility for Moodle 3.6, no functionality change.
* 2018-12-05 - Changed travis.yml due to upstream changes.

### v3.5-r1

* 2018-05-24 - Check compatibility for Moodle 3.5, no functionality change.

### v3.4-r2

* 2018-05-16 - Improvement: Remove remembered passwords also when a course or user is deleted.
* 2018-05-16 - Implement Privacy API.

### v3.4-r1

* 2017-12-11 - Check compatibility for Moodle 3.4, no functionality change.

### v3.3-r1

* 2017-12-04 - Fixed compatibility changes for Behat tests.
* 2017-12-04 - Check compatibility for Moodle 3.3, no functionality change.
* 2017-12-04 - Added Workaround to travis.yml for fixing Behat tests with TravisCI.
* 2017-11-08 - Updated travis.yml to use newer node version for fixing TravisCI error.

### v3.2-r4

* 2017-05-30 - Improve Travis CI support

### v3.2-r3

* 2017-05-29 - Add Travis CI support


### v3.2-r2

* 2017-05-05 - Improve README.md

### v3.2-r1

* 2017-01-13 - Check compatibility for Moodle 3.2, no functionality change
* 2017-01-13 - Small change to YUI Code to make the plugin work with theme_boost
* 2017-01-12 - Move Changelog from README.md to CHANGES.md

### v3.1-r1

* 2016-07-19 - Check compatibility for Moodle 3.1, no functionality change

### Changes before v3.1

* 2016-05-26 - Fix javascript error when viewing new assign grading page on Moodle 3.1 - Credits to Davo Smith
* 2016-02-10 - Update README section about availability conditions settings pages
* 2016-02-10 - Change plugin version and release scheme to the scheme promoted by moodle.org, no functionality change
* 2016-01-01 - Initial version
