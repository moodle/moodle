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

require_once 'cc_general.php';

class url1_resurce_file extends general_cc_file {
    const deafultname = 'weblink.xml';

    protected $rootns = 'wl';
    protected $rootname = 'wl:webLink';
    protected $ccnamespaces = array('wl'  => 'http://www.imsglobal.org/xsd/imswl_v1p0',
                                    'xsi' => 'http://www.w3.org/2001/XMLSchema-instance');
    protected $ccnsnames = array('wl'   => 'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_5/imswl_v1p0_localised.xsd');

    protected $url = null;
    protected $title = null;
    protected $href = null;
    protected $target = '_self';
    protected $window_features = null;

    /**
     *
     * Set the url title
     * @param string $title
     */
    public function set_title($title) {
        $this->title = self::safexml($title);
    }

    /**
     *
     * Set the url specifics
     * @param string $url
     * @param string $target
     * @param string $window_features
     */
    public function set_url($url, $target='_self', $window_features=null) {
        $this->url = $url;
        $this->target = $target;
        $this->window_features = $window_features;
    }

    protected function on_save() {
        $this->append_new_element($this->root, 'title', $this->title);
        $url = $this->append_new_element($this->root, 'url');
        $this->append_new_attribute($url, 'href', $this->url);
        if (!empty($this->target)) {
            $this->append_new_attribute($url, 'target', $this->target);
        }
        if (!empty($this->window_features)) {
            $this->append_new_attribute($url, 'windowFeatures', $this->window_features);
        }
        return true;
    }

}

class url11_resurce_file extends url1_resurce_file {
    protected $rootname = 'webLink';

    protected $ccnamespaces = array('wl'  => 'http://www.imsglobal.org/xsd/imsccv1p1/imswl_v1p1',
                                    'xsi' => 'http://www.w3.org/2001/XMLSchema-instance');
    protected $ccnsnames = array('wl' => 'http://www.imsglobal.org/profile/cc/ccv1p1/ccv1p1_imswl_v1p1.xsd');

    protected function on_save() {
        $rns = $this->ccnamespaces[$this->rootns];
        $this->append_new_element_ns($this->root, $rns, 'title', $this->title);
        $url = $this->append_new_element_ns($this->root, $rns, 'url');
        $this->append_new_attribute_ns($url, $rns, 'href', $this->url);
        if (!empty($this->target)) {
            $this->append_new_attribute_ns($url, $rns, 'target', $this->target);
        }
        if (!empty($this->window_features)) {
            $this->append_new_attribute_ns($url, $rns, 'windowFeatures', $this->window_features);
        }
        return true;
    }
}

