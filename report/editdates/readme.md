# Edit dates report

This 'report' is actually a tool that lets you edit all the dates for all
the activities in your course on a single page.


## Acknowledgements

This question type was created by the Open University (http://www.open.ac.uk/).


## Installation and set-up

Once the plugin is installed, you can access the functionality by going to
Reports -> Dates in the Course administration block.

### Install from the plugins database

Install from the Moodle plugins database
* https://moodle.org/plugins/report_editdates

### Install using git

Or you can install using git. Type this commands in the root of your Moodle install

    git clone https://github.com/moodleou/moodle-report_editdates.git report/editdates
    echo '/report/editdates/' >> .git/info/exclude

Then run the moodle update process
Site administration > Notifications


## Supporting other activities

This plugin needs to know about what dates are contained in each activity or block.
This is done by code in separate classes. For some things, these classes are in
this plugin: https://github.com/moodleou/moodle-report_editdates/tree/master/mod,
https://github.com/moodleou/moodle-report_editdates/tree/master/blocks.

For other plugins, there is the option to put the class in the other plugin.
You need to make a class called `mod_`_mymodname_`_report_editdates_integration`,
which therefore goes in mod/_mymodname_/classes/report_editdates_integration.php.
For blocks, the equivalent class is `block_`_myblock_`_report_editdates_integration`.
