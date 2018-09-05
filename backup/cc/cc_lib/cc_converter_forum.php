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

require_once 'cc_converters.php';
require_once 'cc_general.php';
require_once 'cc_forum.php';

class cc_converter_forum extends cc_converter {

    public function __construct(cc_i_item &$item, cc_i_manifest &$manifest, $rootpath, $path){
        $this->cc_type     = cc_version11::discussiontopic;
        $this->defaultfile = 'forum.xml';
        $this->defaultname = 'discussion.xml';
        parent::__construct($item, $manifest, $rootpath, $path);
    }

    public function convert($outdir) {
        $rt = new forum11_resurce_file();
        $title = $this->doc->nodeValue('/activity/forum/name');
        $rt->set_title($title);
        $text = $this->doc->nodeValue('/activity/forum/intro');
        $deps = null;
        if (!empty($text)) {
            $textformat = intval($this->doc->nodeValue('/activity/forum/introformat'));
            $contextid = $this->doc->nodeValue('/activity/@contextid');
            $result = cc_helpers::process_linked_files($text,
                                                       $this->manifest,
                                                       $this->rootpath,
                                                       $contextid,
                                                       $outdir);

            $textformat = ($textformat == 1) ? 'text/html' : 'text/plain';
            $rt->set_text($result[0], $textformat);
            $deps = $result[1];
        }

        $this->store($rt, $outdir, $title, $deps);
        return true;
    }

}

