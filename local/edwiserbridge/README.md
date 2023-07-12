Edwiser Bridge local plugin for Moodle
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

Edwiser Bridge - The #1 WordPress and Moodle Integration plugin that provides a robust platform to sell Moodle courses online.

Are you a Moodle user, who creates courses and wants a robust integration with WordPress/WooCommerce to sell them.
Then you are at the right place Edwiser Bridge is the only WordPress plugin that provides stable and robust integration between the two platforms.

Edwiser Bridge provides the necessary platform for you to sell your Moodle courses through default payment gateway PayPal.

In order to extend its functionality and create a complete automated eCommerce solution to sell your Moodle, you got to include following plugins in your WordPress site,

1. <a href="https://wordpress.org/plugins/edwiser-bridge/"> Edwiser Bridge - WordPress Add On </a>
2. <a href="https://edwiser.org/bridge/extensions/woocommerce-integration/?utm_source=WordPress&utm_medium=landingpage&utm_campaign=EBFreePlugin"> Edwiser Bridge - WooCommerce Integration extension </a>
3. <a href="https://edwiser.org/bridge/extensions/bulk-purchase/?utm_source=WordPress&utm_medium=landingpage&utm_campaign=EBFreePlugin"> Edwiser Bridge - Bulk Purchase extension </a>
4. <a href="https://edwiser.org/bridge/extensions/single-sign-on/?utm_source=WordPress&utm_medium=landingpage&utm_campaign=EBFreePlugin"> Edwiser Bridge - Single Sign On extension </a>
5. <a href="https://edwiser.org/bridge/extensions/selective-synchronization/?utm_source=WordPress&utm_medium=landingpage&utm_campaign=EBFreePlugin"> Edwiser Bridge - Selective Synchronization </a>

Please note: Edwiser Bridge WordPress plugin (https://downloads.wordpress.org/plugin/edwiser-bridge.zip) is mandatory for the setup and has to be installed on your WordPress site for WordPress - Moodle integration.
Refer to this documentation for setup: https://edwiser.org/documentation/edwiser-bridge/

[(Back to top)](#table-of-contents)

# Features

= CourseFront - =
* Integration between your WordPress and Moodle sites,
* Import your Moodle courses to WordPress,
* Synchronize Moodle course categories to WordPress,
* Set the Moodle courses as draft,
* Sell courses through WordPress and take its payments through PayPal,
* Synchronize enrolled courses data for users.

= Course Access Control - =
* Automation user registration in Moodle,
* Enable/Disable registration to courses,
* Identical login credentials to access courses in Moodle,
* Set course access time from WordPress,
* Update previously synchronized courses,
* Enrol / Unenrol users from WordPress,
* Provide Refund to your students from WordPress.

= Connect your Moodle with Multiple WordPress Sites - =
* Now connect single Moodle site with multiple WordPress sites,
* Courses from single Moodle site could be sold through multiple WordPress sites,
* Automated 2-way synchronization between each WordPress site and your Moodle site,
* Secured and efficient transfer of information across sites,
* Course Progress of student synced from Moodle to WordPress in real time.

[(Back to top)](#table-of-contents)

# Plugin Version

v2.1.2 - Plugin Released

[(Back to top)](#table-of-contents)

# Required version of Moodle

This version works with Moodle 3.0.3+ version 2016052318.00 (Build: 2016052318) and above until the next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'https://docs.moodle.org/39/en/Step-by-step_Installation_Guide_for_Ubuntu'.

[(Back to top)](#table-of-contents)

# Free Software

The Edwiser Bridge is 'free' software under the terms of the GNU GPLv3 License, please see 'LICENSE.md'.

The primary source is on https://github.com/WisdmLabs/edwiser-bridge

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

[(Back to top)](#table-of-contents)

# Support

For all support queries related to Edwiser Bridge plugin you could email us at edwiser@wisdmlabs.com
Apart from that you could raise your support queries in this forum too - https://forums.edwiser.org/category/27/edwiser-bridge

And if you wish to see any new features as part of the product then you could share your feature requests here
forum https://forums.edwiser.org/category/27/edwiser-bridge for support.
Together we could make this solution better for your Moodle.

[(Back to top)](#table-of-contents)

# Installation

 = Minimum Requirements =
* PHP version 5.6 or greater
* WordPress 4.4 or higher
* Moodle 3.0.3 or higher

 =  Automatic Installation  =
1. Go to the Plugins menu from the dashboard.
2. Click on the 'Add New' button on this page.
3. Search for 'Edwiser Bridge' in the search bar provided.
4. Click on 'Install Now' once you have located the plugin.
5. On successful installation click the 'Activate Plugin' link to activate the plugin.

 =  Manual Installation  =
1. Download the Edwiser Bridge plugin from WordPress.org.
2. Now unzip and upload the folder using the FTP application of your choice.
3. The plugin can then be activated by navigating to the Plugins menu in the admin dashboard.

= Moodle Plugin Automatic Installation =
1. Download the Moodle edwiserbridge plugin from <a href = "https://edwiser.org/wp-content/uploads/edd/2021/09/edwiserbridge.zip">here</a>.
2. Go to the Plugins menu in Moodle.
3. Click on Install plugins.
4. Upload plugins zip file.
5. Then click on Install plugin from the Zip file.

 = Moodle Plugin Manual Installation  =
1. Download the Moodle edwiserbridge plugin from <a href = "https://edwiser.org/wp-content/uploads/edd/2021/09/edwiserbridge.zip">here</a>.
2. Now unzip and upload the folder in local directory of Moodle using the FTP application of your choice.
3. The plugin can then be activated by navigating to the Plugins menu in the dashboard.

 = Moodle Configuration =
Take a look at the link below and follow the steps provided to configure your Moodle website.
<a href = "https://edwiser.org/bridge/documentation/#tab-b540a7a7-e59f-3">Moodle Website Configurations</a>

[(Back to top)](#table-of-contents)

# Uninstallation

1. Go to Site administration > Plugins > Plugin overview and go to Edwiser Bridge section, click on uninstall link for 'Edwiser Bridge'.
2. In '/local/' remove the folder 'edwiserbridge'.

[(Back to top)](#table-of-contents)

# Files Information
Languages
---------
The edwiserbridge/lang folder contains the language files for the format.

Note that existing formats store their language strings in the main
moodle.php, which you can also do, but this separate file is recommended
for contributed formats.

Of course you can have other folders as well as English etc. if you want to
provide multiple languages.

Styles
------
The file edwiserbridge/styles.css contains the CSS styles for the format.

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

[![alt text](https://raw.githubusercontent.com/WisdmLabs/moodle-local_edwiserbridge/master/images/readme-img.png)](https://edwiser.org)

[(Back to top)](#table-of-contents)
