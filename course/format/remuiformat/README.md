Edwiser Course Formats block plugin for Moodle
==============================================

# Table of Contents

- [Description](#description)
- [Features](#features)
- [Plugin Version](#plugin-version)
- [Required version of Moodle](#required-version-of-moodle)
- [Free Software](#free-software)
- [Support](#support)
- [Installation](#installation)
- [Uninstallation](#uninstallation)
- [Files Information](#files-information)
- [Roadmap](#roadmap)
- [History](#history)
- [Author](#author)
- [Provided by](#provided-by)

# Description

Let your students focus on what matters - learning, with the all-new and intuitive Edwiser Course Formats plugin! Choose from unique course formats like Card and List.

Edwiser Course Formats plugin comes with two different layouts for your courses.

The List Format is one of the simplest course formats you can come across. Using this format, you can list your course activities in the form of lists. This format is minimalistic in design, with an option to collapse/expand course sections. The collapsible nature of List format completely removes any scope of endless scrolling across courses.

Card Format is another standout course format in the offering. Edwiser Card Format displays your courses neatly stacked as cards on your course page. This format is pleasing to the eye and also highly functional.

Note: Please use image of 34:35 size in card layout to apply proper background to cards.

[(Back to top)](#table-of-contents)

# Features

* With List Format, you can choose between showing all sections on a single page and showing one section per page.
* Card Format also renders all the activities from the section in the card format.
* Compatible with Boost Theme.
* Compatible with Fordson Theme.
* Compatible with Moove Theme.
* Compatible with Handlebar Theme.
* Responsive.


[(Back to top)](#table-of-contents)

# Plugin Version

v1.0.7 - Plugin Released

[(Back to top)](#table-of-contents)

# Required version of Moodle

This version works with Moodle 3.4+ version 2017111300.00 (Build: 20171113) and above until the next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'https://docs.moodle.org/39/en/Step-by-step_Installation_Guide_for_Ubuntu'.

[(Back to top)](#table-of-contents)

# Free Software

The Edwiser Course Format is 'free' software under the terms of the GNU GPLv3 License, please see 'LICENSE.md'.

The primary source is on https://github.com/WisdmLabs/moodle-format_remuiformat

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

If you reuse any of the code then we kindly ask that you make reference to the format.

[(Back to top)](#table-of-contents)

# Support

For all support queries related to Edwiser Site Monitor plugin you could email us at edwiser@wisdmlabs.com
Apart from that you could raise your support queries in this forum too - https://forums.edwiser.org/category/41/edwiser-course-formats

And if you wish to see any new features as part of the product then you could share your feature requests here
forum https://forums.edwiser.org/category/42/request-a-feature for support.
Together we could make this solution better for your Moodle.

[(Back to top)](#table-of-contents)

# Installation

1. Download remuiformat.zip file from the purchase receipt.
2. Open your Moodle website and go to Site administration > Plugins > Install plugins.
3. Choose the remuiformat.zip’ file. Then click on Install plugin from zip file.
4. Refresh your Moodle site or go to Site administration > Plugins > Plugin overview and click on Check for available updates.
5. In the list of plugins appearing, scroll down and click Upgrade Moodle database.
6. The Plugins will get updated and installed in your Moodle database.

* Instructions to install using FTP-

1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
2. Put Moodle in 'Maintenance Mode' (https://docs.moodle.org/39/en/Maintenance_mode) so that there are no
   users using it bar you as the administrator - if you have not already done so.
3. Copy 'remuiformat' to '/course/format/' if you have not already done so.
4. Go back in as an administrator and follow standard the 'plugin' update notification.  If needed, go to
   'Site administration' -> 'Notifications' if this does not happen.
5. Put Moodle out of Maintenance Mode.
6. You may need to check that the permissions within the 'remuiformat' folder are 755 for folders and 644 for files.

[(Back to top)](#table-of-contents)

# Uninstallation

1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
   not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
   course the first format in the list.  You can then set the desired format.
3. Go to Site administration > Plugins > Plugin overview and go to Course Formats section, click on uninstall link for 'Edwiser Course Formats'.
4. In '/course/format/' remove the folder 'remuiformat'.
5. Put Moodle out of Maintenance Mode.

[(Back to top)](#table-of-contents)

# Files Information
Languages
---------
The remuiformat/lang folder contains the language files for the format.

Note that existing formats store their language strings in the main
moodle.php, which you can also do, but this separate file is recommended
for contributed formats.

Of course you can have other folders as well as English etc. if you want to
provide multiple languages.

Styles
------
The file remuiformat/styles.css contains the CSS styles for the format.

[(Back to top)](#table-of-contents)

# Roadmap

1. Global Course Announcement Feature.

[(Back to top)](#table-of-contents)

# History
See changes.txt

[(Back to top)](#table-of-contents)

# Author

Wisdmlabs

[(Back to top)](#table-of-contents)

# Provided by

[![alt text](https://git.wisdmlabs.net/edwiser/remuiformat/raw/dev/images/readme-img.png)](https://edwiser.org)

[(Back to top)](#table-of-contents)
