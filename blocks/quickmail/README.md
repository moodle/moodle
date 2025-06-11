# Quickmail (v2)

Quickmail is a Moodle block plugin that provides selective, bulk messaging for Moodle courses and sites. Developed by Louisiana State University :tiger:, Geaux Tigers!!!

## Overview

Quickmail is a convenient way to select enrolled users in your Moodle course and message them. You can select (or exclude) recipients individually, by role, or even by group. Additionally, you can use Quickmail at the administrative level to select and message users within the entire site.

**Please note that these are the docs for Quickmail v2**. This second version is much different than the original version in the way it operates and saves data. If you are wanting to install the original version, please see the [Quickmail v1 branch](https://github.com/lsuits/block_quickmail/tree/dev-30)  of this plugin.

It is highly recommended that you review the features below and make sure this plugin will work for you, and that you backup any existing data before installing!!

## Installation

**Note:** In order for this block to send messages you need to have CRON working and running properly on your server. If you don't have CRON running regularly, **do not install** (or upgrade) this block!

Quickmail should be installed like any other block. See the [Moodle Docs page on plugin installation](https://docs.moodle.org/34/en/Installing_plugins#Installing_a_plugin) for more info. You can also find Quickmail on the official [Moodle Plugin Directory](https://moodle.org/plugins/block_quickmail).

If you are upgrading from v1 to v2, a data migration script is included in this plugin which will format your existing data to the new structure. **Note:** Signatures and Alternate Emails are not carried over, however, and will have to be re-created manually.

## Features

* **Multiple Message Types**

  Send messages via email OR as [Moodle Messages](http://docs.moodle.org/en/Messaging).

* **Task-based Messaging**

  All messages are sent behind the scenes as "ad-hoc tasks" so you don't have to wait around for your message to be sent!

* **Scheduled Sending**
  
  Want your message to go out first thing tomorrow morning? No problem! You can choose the date and time your message will be sent!

* **Custom User Data Injection**
  
  Want to add a personal touch easily? No problem! Using a handy little helper when composing your message you can have Quickmail automatically inject personal data like first name, last name, email, etc.

* **Admin-level Messaging**

  Want to use Quickmail to address your entire site, not just those in a specific course? No problem! Add the Quickmail block to your Dashboard page.

* **Drafts**
  
  Still working on that email? Save it as a draft to come back to later! You can even duplicate your favorite drafts so you don't have to type them all over again!

* **Signatures**
  
  Add a personal touch by creating your own signatures and applying them to your messages!

* **Mentor Copy**
  
  Send a copy of your message to a recipient's mentor with one click!

* **File Attachments**
  
  Attach files to your messages easily! The recipient will see a list of all files and even a zipped version to download. Bonus: you can use nested directories in your attachments as well.

* **Allow Students To Send**
  
  Want to allow students of a particular course to message other students? Configure Quickmail to act like you want, FERPA guidelines can even be taken into account. You make the call!

* **Additional Emails**
  
  Want to message someone outside of your Moodle through Quickmail? No problem! Just type in their email address and they will receive a copy!

## Contributions

Contributions of any form are welcome. GitHub pull requests are preferred. Report any bugs or requests through our GitHub [issue tracker](https://github.com/lsuits/quickmail/issues).

## Tests

To run PHPUnit tests: `vendor/bin/phpunit -c blocks/quickmail/phpunit.xml`

## License

Quickmail adopts the same license that Moodle does.