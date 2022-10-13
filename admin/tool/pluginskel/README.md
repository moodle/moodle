# Moodle plugin skeleton generator

This tool allows developers to quickly generate code skeleton for Moodle
plugins. Relevant code is generated according to defined list of requested
plugin features.


## Usage ##

Plugin features and other properties are described in so called recipe files. Recipes
use [YAML](https://yaml.org/) syntax.

    name: Moodle demo plugin
    component: tool_demo
    release: "0.1.0"
    requires: "3.9"
    maturity: MATURITY_ALPHA
    copyright: 2021 David Mudrák <david@moodle.com>

    features:
      readme: true
      license: true
      settings: true

    privacy:
      haspersonaldata: false

For full list of supported options, see [cli/example.yaml](cli/example.yaml) file.

To generate skeleton of the plugin described in `./myplugin.yaml`:

    $ php cli/generate.php myplugin.yaml

Run `generate.php --help` for more options and features.


## Documentation ##

See the page [Plugin skeleton
generator](https://docs.moodle.org/en/admin/tool/pluginskel/index) at moodle
docs site.


## Installing from Git ##

1. Go to the folder with your Moodle development installation:

       $ cd {your/moodle/dirroot}

2. Clone this repository to the correct location folder:

       $ git clone https://github.com/mudrd8mz/moodle-tool_pluginskel.git admin/tool/pluginskel

3. Complete the installation:

	   $ sudo -u www-data php admin/cli/upgrade.php

or just log in to your Moodle development site as an admin.


## License ##

Copyright (C) 2016 Alexandru Elisei, David Mudrák

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
