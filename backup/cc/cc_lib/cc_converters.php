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

require_once('cc_interfaces.php');

abstract class cc_converter {
    /**
     *
     * Enter description here ...
     * @var cc_item
     */
    protected $item     = null;
    /**
     *
     * Enter description here ...
     * @var cc_manifest
     */
    protected $manifest = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $rootpath = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $path     = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $defaultfile = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $defaultname = null;
    /**
     *
     * Enter description here ...
     * @var string
     */
    protected $cc_type = null;
    /**
     *
     * Document
     * @var XMLGenericDocument
     */
    protected $doc = null;

    /**
     *
     * ctor
     * @param  cc_i_item $item
     * @param  cc_i_manifest $manifest
     * @param  string $rootpath
     * @param  string $path
     * @throws InvalidArgumentException
     */
    public function __construct(cc_i_item &$item, cc_i_manifest &$manifest, $rootpath, $path) {
        $rpath = realpath($rootpath);
        if (empty($rpath)) {
            throw new InvalidArgumentException('Invalid path!');
        }
        $rpath2 = realpath($path);
        if (empty($rpath)) {
            throw new InvalidArgumentException('Invalid path!');
        }
        $doc = new XMLGenericDocument();
        if (!$doc->load($path . DIRECTORY_SEPARATOR . $this->defaultfile)) {
            throw new RuntimeException('File does not exist!');
        }

        $this->doc      = $doc;
        $this->item     = $item;
        $this->manifest = $manifest;
        $this->rootpath = $rpath;
        $this->path     = $rpath2;
    }

    /**
     *
     * performs conversion
     * @param string $outdir - root directory of common cartridge
     * @return boolean
     */
    abstract public function convert($outdir);

    /**
     *
     * Is the element visible in the course?
     * @throws RuntimeException
     * @return bool
     */
    protected function is_visible() {
        $tdoc = new XMLGenericDocument();
        if (!$tdoc->load($this->path . DIRECTORY_SEPARATOR . 'module.xml')) {
            throw new RuntimeException('File does not exist!');
        }
        $visible = (int)$tdoc->nodeValue('/module/visible');
        return ($visible > 0);
    }

    /**
     *
     * Stores any files that need to be stored
     */
    protected function store(general_cc_file $doc, $outdir, $title, $deps = null) {
        $rdir = new cc_resource_location($outdir);
        $rtp = $rdir->fullpath(true).$this->defaultname;
        if ( $doc->saveTo($rtp) ) {
            $resource = new cc_resource($rdir->rootdir(), $this->defaultname, $rdir->dirname(true));
            $resource->dependency = empty($deps) ? array() : $deps;
            $resource->instructoronly = !$this->is_visible();
            $res = $this->manifest->add_resource($resource, null, $this->cc_type);
            $resitem = new cc_item();
            $resitem->attach_resource($res[0]);
            $resitem->title = $title;
            $this->item->add_child_item($resitem);
        } else {
            throw new RuntimeException("Unable to save file {$rtp}!");
        }
    }
}
