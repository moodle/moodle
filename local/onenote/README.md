# Moodle Plugins for Microsoft Services
*including* **Microsoft 365** *and other Microsoft services*

## Microsoft OneNote Online API Local Plugin

This plugin provides a common client API for various other Moodle plugins that allow Moodle users to take advantage of Microsoft OneNote Online. This includes  operations such as browsing your notebooks, sections, and pages; students doing assignments in OneNote and teachers providing feedback on those assignments in OneNote. It uses the Microsoft Account local plugin for authentication and using the OneNote Oneline REST API.

## Usage

Instantiation:
        $onenoteapi = \local_onenote\api\base::getinstance();

Logging the user in:
        $onenoteapi->is_logged_in();

Making a REST API call:
        $notebooks = $onenoteapi->get_items_list('');


## Design details

There are several parts that make up the Microsoft OneNote Online API Local plugin.

### Configuration
None. This plugin depends upon the Microsoft Account local plugin to be configured for accessing the appropriate Microsoft Live application.

### \local_onenote\api\base class
This is a singleton class that provides simple wrappers for various authentication and the OneNote online REST API. Some of the functionality provided includes:
- render_signin_widget: Returns the HTML for displaying the signin widget for Microsoft OneNote.
- get_items_list: Used for drilling down the hierarchy of OneNote notebooks, sections, and pages.
- download_page: Downloads the contents of a OneNote page, including the HTML and any images; saves them in a folder and creates a .zip file out of it.
- render_action_button: Returns the HTML for displaying action buttons that allow students to view or work on OneNote assignments and teachers to provide feedback on them.
- get_page: Gets (or creates) the student submission page or teacher feedback page in OneNote for the given student assignment. Performs all actions such as determining if a page already exists and returning that; or determining if this is the first time a student is accessing the assignment and creating the page from the assignment title / prompt etc; or determining if a downloaded zipped page already exists and thawing the OneNote page from that.

### onenote_actions.php file
This file processes form submissions from all the OneNote-related submit buttons in the Moodle API that help students work on their assignments and help teachers provide feedback on those assignments in OneNote.


### Additional notes about the OneNote integration

When a user (student or teacher) clicks on the "Work on this" or "View Submission" or "View Feedback" buttons, the following actions may happen if needed:
- A notebook is created called "Moodle Notebook" in the OneNote account of the user
- Sections are created in that notebook with the names of all the courses the user is currently enrolled into.
- A page is created inside that section corresponding to the submission or feedback as necessary.
- The title of the submission / feedback page is the name of the assignment, prefixed by "Submission: " / "Feedback: ", and postfixed by "[firstname lastname]" of the student.
- Note that these actions take place in a lazy manner and only when necessary i.e. when the corresponding notebook, section, or page does not exist.
- These actions will also occur if the user subsequently goes into OneNote and deletes the notebook, section, or page.
- The connection between Moodle and the OneNote section or page is via the unique id of the section or page. This connection is loose i.e. if the user deletes the section or page, a new one will be created in its place and the appropriate ID maintained in the related Moodle database is updated.

Each submission for each student will have a Submission page and a Feedback page in the student's OneNote account. Also, for each assignment, there is a submission and feedback page for each student in the teacher's account. This is also done lazily i.e. only when teacher clicks on the above buttons. Correspondingly, each such page, when it gets saved inside Moodle, has a copy of the HTML and any associates images, all zipped up in a zip file.

If any of these submission or feedback pages get deleted in OneNote for some reason, they will get recreated when needed from their saved copy in Moodle. So the "master" is always with Moodle and the OneNote pages associated with the master are loosely connected to it.

This is part of the suite of Microsoft Services plugins for Moodle.

This repository is updated with stable releases. To follow active development, see: https://github.com/Microsoft/o365-moodle

## Installation

1. Unpack the plugin into /local/onenote within your Moodle install.
2. From the Moodle Administration block, expand Site Administration and click "Notifications".
3. Follow the on-screen instructions to install the plugin.

For more documentation, visit https://docs.moodle.org/34/en/Office365

For more information including support and instructions on how to contribute, please see: https://github.com/Microsoft/o365-moodle/blob/master/README.md

## Copyright

&copy; Microsoft, Inc.  Code for this plugin is licensed under the GPLv3 license.

Any Microsoft trademarks and logos included in these plugins are property of Microsoft and should not be reused, redistributed, modified, repurposed, or otherwise altered or used outside of this plugin.
