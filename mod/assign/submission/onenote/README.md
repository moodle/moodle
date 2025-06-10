# Moodle Plugins for Microsoft Services
*including* **Microsoft 365** *and other Microsoft services*

## Microsoft OneNote Assignment Submission Plugin

This plugin provides the functionality related to students working with an assignment in OneNote. This includes creating a OneNote page associated with an assignment submission, saving student's work from OneNote into Moodle as a zip package containing the HTML and any associated images contained in the submission, and recreating the OneNote page from the zip package saved in Moodle if necessary. It uses the Microsoft OneNote API Local plugin to do some of these things.

## Design details

### Basic design
This plugin follows a design similar to the File submission plugin wherever possible. It uses the API exposed by the local_onenote plugin to perform most of the OneNote-related operations.
Note that the association between an assignment submission in Moodle and the associated OneNote page is loose i.e. the OneNote page may get deleted and it will not affect Moodle since it keeps a copy of the page in a zip package and can always recreate the OneNote page from it.

### Use cases supported
- When a student wants to start working on an assignment which allows OneNote submissions, they click on a button in the plugin UI that creates a OneNote page for their submission from the title and prompt of the assignment.
- When the student wants to save their work back in Moodle, they click on a save button in the plugin UI, which results in this plugin downloading the content of the OneNote page, including the HTML and any associated images and zipping them up as a single file and saving it in the Moodle database.
- If the OneNote page associated with an assignment submission gets deleted, the student can still click one a button in the plugin UI that will recreate the OneNote page from the zip package that was saved in Moodle.

### Plugin dependencies
assignsubmission_onenote => local_onenote => local_o365

### Configuration
This plugin adds a radio button to the assignment creation form that allows a teacher to specify that a student may submit their work as a OneNote page.
This plugin also provides a setting for the maximum size in bytes of the OneNote submission.


This is part of the suite of Microsoft Services plugins for Moodle.

This repository is updated with stable releases. To follow active development, see: https://github.com/Microsoft/o365-moodle

## Installation

1. Unpack the plugin into /mod/assign/submission/onenote within your Moodle install.
2. From the Moodle Administration block, expand Site Administration and click "Notifications".
3. Follow the on-screen instuctions to install the plugin.

For more documentation, visit https://docs.moodle.org/34/en/Office365

For more information including support and instructions on how to contribute, please see: https://github.com/Microsoft/o365-moodle/blob/master/README.md

## Copyright

&copy; Microsoft, Inc.  Code for this plugin is licensed under the GPLv3 license.

Any Microsoft trademarks and logos included in these plugins are property of Microsoft and should not be reused, redistributed, modified, repurposed, or otherwise altered or used outside of this plugin.
