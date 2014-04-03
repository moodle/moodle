<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * @package    backup-convert
 * @subpackage cc-library
 * @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('cc_converters.php');
require_once('cc_general.php');

class cc_converter_resource extends cc_converter {

    public function __construct(cc_i_item &$item, cc_i_manifest &$manifest, $rootpath, $path) {
        $this->cc_type     = cc_version11::webcontent;
        $this->defaultfile = 'resource.xml';
        parent::__construct($item, $manifest, $rootpath, $path);
    }

    public function convert($outdir) {
        $title = $this->doc->nodeValue('/activity/resource/name');
        $contextid = $this->doc->nodeValue('/activity/@contextid');
        $files = cc_helpers::handle_resource_content($this->manifest,
                                                   $this->rootpath,
                                                   $contextid,
                                                   $outdir);
        $deps = null;
        $resvalue = null;
        foreach ($files as $values) {
            if ($values[2]) {
                $resvalue = $values[0];
                break;
            }
        }

        $resitem = new cc_item();
        $resitem->identifierref = $resvalue;
        $resitem->title = $title;
        $this->item->add_child_item($resitem);

        // Checking the visibility.
        $this->manifest->update_instructoronly($resvalue, !$this->is_visible());

        return true;
    }

}

