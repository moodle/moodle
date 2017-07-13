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
require_once 'cc_weblink.php';

class cc_converter_url extends cc_converter {

    public function __construct(cc_i_item &$item, cc_i_manifest &$manifest, $rootpath, $path){
        $this->cc_type     = cc_version11::weblink;
        $this->defaultfile = 'url.xml';
        $this->defaultname = 'weblink.xml';
        parent::__construct($item, $manifest, $rootpath, $path);
    }

    public function convert($outdir) {
        $rt = new url11_resurce_file();
        $title = $this->doc->nodeValue('/activity/url/name');
        $rt->set_title($title);
        $url = $this->doc->nodeValue('/activity/url/externalurl');
        if (!empty($url)) {
            /**
             *
             * Display value choices
             * 0 - automatic (system chooses what to do) (usualy defaults to the open)
             * 1 - embed - display within a frame
             * 5 - open - just open it full in the same frame
             * 6 - in popup - popup - new frame
             */
            $display = intval($this->doc->nodeValue('/activity/forum/display'));
            $target = ($display == 6) ? '_blank' : '_self';
            //TODO: Moodle also supports custom parameters
            //this should be transformed somehow into url where possible
            $rt->set_url($url, $target);
        }

        $this->store($rt, $outdir, $title);
        return true;
    }

}

