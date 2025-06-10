# Ally admin tool

The Ally admin tool (tool_ally) provides a web service facilitating communication between Moodle and Ally. It also
contains all the main settings required for Ally to function. Ally is an accessibility tool providing analysis / remedy
of accessibility issues for files hosted within moodle. It can provide a detailed course level report illustrating the
level of accessibility for files throughtout the course.

## Installation

You can download the admin tool plugin from:

https://github.com/anthology-ally/moodle-tool_ally

This plugin should be located and named as:
 [yourmoodledir]/admin/tool/ally

## Configuring the Ally admin tool

Open the settings for the Ally admin tool:

Site Administration > Plugins > Admin Tools > Ally

The Secret, Key, Launch URL, File updates URL and Client id fields should be populated with values provided to you by an
Ally representative.

You will also need to make the Moodle Ally web service available to Ally. The easiest way to do this is to click the
link entitled "Auto configure Ally web service" at the bottom of the settings page and then by clicking continue. You
will be able to test that the Ally web service is working by following the link at the bottom of the page. Please note,
this URL must be accessible from the internet for Ally to function. Following this link should open a new page starting
with the following text:
{"tool_ally":{"version":...
If successful, you will need to copy this link and provide it to your Ally representative. They will contact you once
everything is set up at their end.

## Additional plugins

The Ally admin tool isn't useful on its own. The main functionality of Ally is provided by two other plugins which you
should download and install:

### Ally filter

The Ally filter can be downloaded from:

https://github.com/anthology-ally/moodle-filter_ally

The filter should be located and named as:
 [yourmoodledir]/filter/ally
 
### Ally accessibility report

The Ally accessibility report can be downloaded from:
 
https://github.com/anthology-ally/moodle-report_allylti
 
The report should be located and named as:
 [yourmoodledir]/report/allylti

## Uninstall
1. Remove the `tool_ally`, `filter_ally` and `report_allylti` plugins from the Moodle folder:
   * [yourmoodledir]/admin/tool/ally
   * [yourmoodledir]/filter/ally
   * [yourmoodledir]/report/allylti
2. Access the plugin uninstall page: Site Administration > Plugins > Plugins overview
3. Look for the removed plugins and click on uninstall for each plugin. 

## License for Ally admin tool

© 2017 Open LMS / 2023 Anthology Inc. and its affiliates

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
