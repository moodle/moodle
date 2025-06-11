# Migrate mod_hvp to mod_h5pactivity

[![Moodle Plugin CI](https://github.com/moodlehq/moodle-tool_migratehvp2h5p/actions/workflows/ci.yml/badge.svg)](https://github.com/moodlehq/moodle-tool_migratehvp2h5p/actions/workflows/ci.yml)

Moodle plugin allowing to migrate activities created with the mod_hvp plugin created by Joubel to the new mod_h5pactivity created by Moodle HQ since Moodle 3.9.

Some limitations to consider before using this plugin:

* Currently it's still not possible to save the current status with the mod_h5pactivity. The mod_hvp supports it (although it's disabled by default) so, before migrating the activities, consider students might loose these unfinished attempts.
* The new mod_h5pactivity hasn't any global settings to define the default behaviour so general settings defined in mod_hvp, such as the default display options or whether to use or not the hub, are not migrated.

## How to use this tool

There are two ways to execute the activity migration:

* Web interface: site administration -> Migrate content from mod_hvp to mod_h5pactivity
* CLI via terminal: php admin/tool/migratehvp2h5p/cli/migrate.php --execute

Migrations tool will scan for non migrated hvp activities and will create as many H5P activities as needed.

By default, the CLI method will only migrate up to 100 hvp activities per execution, will keep the originals hvp in the courses and will add a link to the content bank. Use the option "--help" to know the CLI params to change this behavior to increase the migration limit, delete/hide the originals hvp, decide whether to link/copy to the content bank or filter by content type.

The tool will only migrate each hvp once. In case you need to re-migrate an hvp, just remove or rename the migrated h5p activity, this way the tool won't detect the hvp as migrated.

## Tool dependencies

This tools requires both core H5P and the third party plugin (mod_hvp) installed in the system. The minimum requirements are:

* Moodle core 3.9: otherwise the H5P activity is not present
* HVP activity 2020020500 or more: in previous versions this migration tool won't work
* H5P core activity enabled

Apart from that, for adding contents to the content bank too (so it's not strictly required if contents won't be migrated to the content bank):

* H5P content type of the content bank enabled
* Content bank repository enabled

### License

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
