# Ally filter

![example workflow](https://github.com/anthology-ally/moodle-filter_ally/actions/workflows/moodle-ci.yml/badge.svg)

The Ally filter provides accessibility scores and tools for files uploaded to Moodle by teachers. It also provides
alternative download types to enhance accessibility - e.g. audio, braille, etc

## Installation

The Ally filter can be downloaded from:

https://github.com/anthology-ally/moodle-filter_ally

The filter should be located and named as:
 [yourmoodledir]/filter/ally
 
## Configuration

For the filter to function it is necessary to download and configure the Ally admin tool:
 
https://github.com/anthology-ally/moodle-tool_ally
 
Instructions on how to do so are available in the README.md file for the admin tool.

## Usage

The filter will be functional after the Ally admin tool has been successfully configured and your Ally
representative has finalised the setup.

### Non image files

The filter should automatically add two new icons to non-image files created by teachers (.pdf, .doc, .docx, .odt, .ppt,
.pptx, .odp, .html).
For teachers, a feedback icon and an alternative downloads icon will display.
For students, only the alternative downloads icon will display.

### Image files

For teachers, a feedback icon will be shown.
For any potentially seizure inducing images, a seizure guard will be displayed over the image.
For students, only the seizure guard will be shown for images capable of inducing seizures.

### Teacher feedback icon

The teacher feedback icon is a little meter indicator with 3 states :
* (red) low, requires improvement
* (amber) medium, has areas for improvement
* (green) high accessibility.
On clicking the feedback icon, the feedback modal is displayed. This modal will offer advice on how to improve the
accessibility of the file. In some cases the feedback modal will also present options to automatically correct or
improve accessibility.

### Download accessible versions icon

The download icon allows students to download accessible versions.
When clicked, a modal appears where a student can select the alternative version they wish to download.

## Uninstall
1. Remove the `tool_ally`, `filter_ally` and `report_allylti` plugins from the Moodle folder:
   * [yourmoodledir]/admin/tool/ally
   * [yourmoodledir]/filter/ally
   * [yourmoodledir]/report/allylti
2. Access the plugin uninstall page: Site Administration > Plugins > Plugins overview
3. Look for the removed plugins and click on uninstall for each plugin. 

## License for Ally filter

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
