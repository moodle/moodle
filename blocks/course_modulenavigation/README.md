# moodle-block_course_modulenavigation

[![Build Status](https://travis-ci.org/DigiDago/moodle-block_course_modulenavigation/.svg?branch=master)](https://travis-ci.org/DigiDago/moodle-block_course_modulenavigation)

Introduction
============
Course module navigation is a block that show the users a summary (like a table of content) of a course with sections name and a list of all resources and activties (except URL). One objective of this block is to replace classical block navigation in a way to present only the course contents and sections title.
If you click on resources and activites of the menu, you display the page of the resource or activity.

This block use automaticaly names of sections and names of resources and activities. When you use course module navigation, we recommand to use pages to add videos or contents in order to be able to view all resources in the list of the block.
If you want to display course module navigation on all pages of the course (main, activities, resources), make sure to check permission of the block and display it on "every page".

== We add some options. Now you can : ==

**Section Names**
- Option A : Clicking at section name will point to section area or section page (for example if you use a course format as one section by page).
- Option B : Clicking at section name will display the list of resources and activities

**Labels**
You can choose if labels are displayed or not in the menu with the option "toggleshowlabels".

**Expand/open menu**
You can chose if menu is always open with the option "togglecollapse"


== About activity completion ==
If activity completion is used in the course, course navigation block display a circle empty or green to show state of completion. 

== Display Type of the menu ==
This block has an option to display at choice :
- Only the active section
- or all the sections 

Maintainer
============
Course module navigation was initialy developed by Bas Brand and was based on an request of Digidago (Clément PRUDHOMME). It is currently maintained by DigiDago.


Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  
 2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 3. Copy 'course_modulenavigation' to '/blocks/' if you have not already done so.
 4. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 5.  Put Moodle out of Maintenance Mode.

Upgrade Instructions
====================
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.
 2. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 3. In '/blocks/' move old 'course_modulenavigation' directory to a backup folder outside of Moodle.
 4. Follow installation instructions above.
 5. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
    under 'Home -> Site administration -> Development -> Purge all caches'.
 6. Put Moodle out of Maintenance Mode.

Uninstallation
==============
 1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 2. In '/blocks/' remove the folder 'course_modulenavigation'.
 4. In the database, remove the for 'course_modulenavigation' ('plugin' attribute) in the table 'config_plugins'.  If
    using the default prefix this will be 'mdl_config_plugins'.
 5. Put Moodle out of Maintenance Mode.

Version Information
===================
See Changes.md.


Any Problems, questions, suggestions
===================
If you have a problem with this block, suggestions for improvement, drop an email at :
- Clément PRUDHOMME :  contact@Pimenko.com
- Github : https://github.com/DigiDago/moodle-course_modulenavigation


Languages and translation
===================
English and french versions included / versions anglaise et française incluses.


Customization
===================
You can easily use .css to customize style of the bloc course module navigation.



U
About Pimenko
===================


Pimenko is a leading company specializing in Moodle development and e-learning solutions. With a team of experienced developers and e-learning experts, we are committed to creating innovative and high-quality plugins that enhance the Moodle experience for educators and learners alike.

## Our Expertise

- Custom Moodle development
- Moodle plugin creation and maintenance
- E-learning platform optimization
- Moodle theme development
- Learning analytics solutions
- Audit
- Course creation (elearning, MOOC)

### Connect with Us

- **Website**: [https://www.pimenko.com](https://www.pimenko.com)
- **Email**: contact@pimenko.com
- **GitHub**: [https://github.com/DigiDago](https://github.com/DigiDago)
- **LinkedIn**: [Pimenko LinkedIn Profile](https://www.linkedin.com/company/pimenko/)

We are passionate about improving e-learning experiences through technology. If you have any questions about our services or need custom Moodle solutions, don't hesitate to reach out!

@copyright Pimenko https://www.pimenko.com