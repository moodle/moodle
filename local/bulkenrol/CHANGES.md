moodle-local_bulkenrol
======================

Changes
-------

### v4.1-r3

* 2024-08-19 - Fix a behat test which broke recently
* 2024-08-11 - Add section for scheduled tasks to README
* 2024-08-11 - Updated Moodle Plugin CI to latest upstream recommendations

### v4.1-r2

* 2023-10-14 - Add automated release to moodle.org/plugins
* 2023-10-14 - Make codechecker happy again
* 2023-10-10 - Updated Moodle Plugin CI to latest upstream recommendations
* 2023-04-30 - Tests: Updated Moodle Plugin CI to use PHP 8.1 and Postgres 13 from Moodle 4.1 on.

### v4.1-r1

* 2023-01-21 - Prepare compatibility for Moodle 4.1.

### v4.0-r3

* 2023-04-26 - Allow teachers to configure the navigation node placement.
* 2023-04-26 - Add a missing setting to README.md
* 2023-03-12 - Tests: Fix a Behat test which broke after Moodle core upstream changes
* 2023-03-11 - Make codechecker happy again
* 2022-11-28 - Updated Moodle Plugin CI to latest upstream recommendations

### v4.0-r2

* 2022-07-12 - Fix README description how this plugin works in Moodle 4.0

### v4.0-r1

* 2022-07-12 - Fix Behat tests which broke with Moodle 4.0
* 2022-07-12 - Prepare compatibility for Moodle 4.0.

### v3.11-r3

* 2022-07-10 - Add Visual checks section to UPGRADE.md

### v3.11-r2

* 2022-06-26 - Make codechecker happy again
* 2022-06-26 - Updated Moodle Plugin CI to latest upstream recommendations
* 2022-06-26 - Add UPGRADE.md as internal upgrade documentation
* 2022-06-26 - Update maintainers and copyrights in README.md.

### v3.11-r1

* 2021-11-02 - Prepare compatibility for Moodle 3.11.

### v3.10-r2

* 2021-10-20 - Improvement: Grant local/bulkenrol:enrolusers to manager archetype by default - Thanks to Luca Bösch.
* 2021-10-19 - Improvement: Distinguish warnings caused by empty line or by invalid content - Thanks to Luca Bösch.
* 2021-10-19 - Improvement: Use a enrol option as default - Thanks to gustavorivas96.
* 2021-10-19 - Bugfix: Small typo in a language string - Thanks to Luca Bösch.
* 2021-10-15 - Replace the deprecated print_error() function with a Moodle exception
* 2021-02-05 - Move Moodle Plugin CI from Travis CI to Github actions

### v3.10-r1

* 2021-01-09 - Prepare compatibility for Moodle 3.10.
* 2021-01-06 - Change in Moodle release support:
               For the time being, this plugin is maintained for the most recent LTS release of Moodle as well as the most recent major release of Moodle.
               Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.
* 2021-01-06 - Improvement: Declare which major stable version of Moodle this plugin supports (see MDL-59562 for details).

### v3.9-r1

* 2020-08-17 - Prepare compatibility for Moodle 3.9.

### v3.8-r2
* 2020-05-11 - Bugfix: Teachers were not redirected to the participants page on Moodle instances which are installed in subdirectories - Credits to rtschu and MaimaKhan.
* 2020-05-11 - Feature: Add information table about the groups included in the list and to highlight which groups will be created - Credits to Scott Hardwick for proposing the improvement and to Soon Systems for implementing the solution.
* 2020-05-11 - Improvement: Display user enrolment status and group membership status with Bootstrap badges - Credits to Soon Systems.
* 2020-05-11 - Improvement: Don't enrol a user with the given enrolment method additionally if he is already enrolled with something else like meta enrolment. Do only add him to the groups in this case - Credits to Scott Hardwick for reporting the issue and to Soon Systems for implementing the solution.
* 2020-05-11 - Improvement: Don't show the "Enrol users" button if there aren't any valid email addresses given - Credits to Scott Hardwick for reporting the issue and to Soon Systems for implementing the solution.
* 2020-05-11 - Feature: Add admin setting to control the role to be used for bulk enrolment. Up to now, the default role of the configured enrolment method was used - Credits to Soon Systems.

### v3.8-r1

* 2020-02-14 - Prepare compatibility for Moodle 3.8.

### V3.7-r1

* 2019-08-01 - Added behat tests.
* 2019-08-01 - Fixed bug with enrolment method.
* 2019-07-17 - Corrected typos.
* 2019-07-16 - Prepare compatibility for Moodle 3.7.

### v3.6-r1

* 2019-01-21 - Check compatibility for Moodle 3.6, no functionality change.
* 2018-12-05 - Changed travis.yml due to upstream changes.

### v3.5-r2

* 2018-09-20 - Bugfix: Information about group membership when enrolling users was wrong

### v3.5-r1

* 2018-08-22 - Check compatibility for Moodle 3.5, no functionality change.

### v3.4-r4

* 2018-07-11 - Minor cleanups for approval in Moodle plugins repo, no functionality change.

### v3.4-r3

* 2018-05-16 - Implement Privacy API.

### v3.4-r2

* 2018-05-07 - Finish PHPDoc in locallib.php
* 2018-05-07 - Eliminate an unneeded string from the language pack

### v3.4-r1

* 2018-04-30 - Check compatibility for Moodle 3.4, no functionality change
* 2018-04-30 - Improve information about users who are already enrolled and about group memberships
* 2018-04-30 - Process expections which could happen within bulk enrolment
* 2018-04-30 - Change feedback to use a redirect notification
* 2018-04-30 - Show section headings on confirmation page only if needed
* 2018-04-30 - Eliminate an unneeded string from the language pack

### v3.2-r3

* 2018-04-04 - Add a missing string to the language pack.

### v3.2-r2

* 2018-03-19 - Make codechecker happier.

### v3.2-r1

* 2018-03-15 - Initial implementation.

