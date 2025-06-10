# AWS SDK Local plugin
This plugin was contributed by the Open LMS Product Development team. Open LMS is an education technology company
dedicated to bringing excellent online teaching to institutions across the globe.  We serve colleges and universities,
schools and organizations by supporting the software that educators use to manage and deliver instructional content to
learners in virtual classrooms.

This plugin only serves as a way to distribute the AWS SDK for PHP.

## Installation
Extract the contents of the plugin into _/wwwroot/local_ then visit `admin/upgrade.php` or use the CLI script to upgrade your site.

## Flags

### The `proxyhost` flag.
### The `proxyuser` flag.
### The `proxypassword` flag.
### The `proxytype` flag.
### The `phpunit_local_aws_sdk_test` flag.
### The `proxyport` flag.


## How to update the SDK
Make any necessary updates to the `composer.json` file.  Usually nothing needs to change unless upgrading to the next
major version.  Then, from within this project, run this command:

    composer update --prefer-dist --optimize-autoloader

Then stage changes:

    git add -A vendor composer.*

If everything looks good, then commit the changes.  Please include SDK version in the commit message.  Lastly,
update the `thirdpartylibs.xml` with new versions and any new libraries.

## License
Copyright (c) 2021 Open LMS (https://www.openlms.net)

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
