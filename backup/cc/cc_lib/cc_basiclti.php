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
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once 'cc_general.php';

class basicltil1_resurce_file extends general_cc_file {
    const deafultname = 'basiclti.xml';

    protected $rootns   = 'xmlns';
    protected $rootname = 'cartridge_basiclti_link';
    protected $ccnamespaces = array('xmlns' => 'http://www.imsglobal.org/xsd/imslticc_v1p0',
                                    'blti'  => 'http://www.imsglobal.org/xsd/imsbasiclti_v1p0',
                                    'lticm' => 'http://www.imsglobal.org/xsd/imslticm_v1p0',
                                    'lticp' => 'http://www.imsglobal.org/xsd/imslticp_v1p0',
                                    'xsi'   => 'http://www.w3.org/2001/XMLSchema-instance');
    protected $ccnsnames = array('xmlns' => 'http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticc_v1p0.xsd',
                                 'blti'  => 'http://www.imsglobal.org/xsd/lti/ltiv1p0/imsbasiclti_v1p0p1.xsd',
                                 'lticm' => 'http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticm_v1p0.xsd',
                                 'lticp' => 'http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticp_v1p0.xsd');

    protected $title = 'Untitled';
    protected $description = 'description';
    protected $custom_properties = array();
    protected $extension_properties = array();
    protected $extension_platform = null;
    protected $launch_url = null;
    protected $secure_launch_url = null;
    protected $icon = null;
    protected $secure_icon = null;
    protected $vendor = false;
    protected $vendor_code = 'I';
    protected $vendor_name = null;
    protected $vendor_description = null;
    protected $vendor_url = null;
    protected $vendor_contact = null;
    protected $cartridge_bundle = null;
    protected $cartridge_icon = null;

    public function set_title($title) {
        $this->title = self::safexml($title);
    }
    public function set_description($description) {
        $this->description = self::safexml($description);
    }
    public function set_launch_url($url) {
        $this->launch_url = $url;
    }
    public function set_secure_launch_url($url) {
        $this->secure_launch_url = $url;
    }
    public function set_launch_icon($icon) {
        $this->icon = $icon;
    }
    public function set_secure_launch_icon($icon) {
        $this->secure_icon = $icon;
    }
    public function set_vendor_code($code) {
        $this->vendor_code = $code;
        $this->vendor = true;
    }
    public function set_vendor_name($name) {
        $this->vendor_name = self::safexml($name);
        $this->vendor = true;
    }
    public function set_vendor_description($desc) {
        $this->vendor_description = self::safexml($desc);
        $this->vendor = true;
    }
    public function set_vendor_url($url) {
        $this->vendor_url = $url;
        $this->vendor = true;
    }
    public function set_vendor_contact($email) {
        $this->vendor_contact = array('email' => $email);
        $this->vendor = true;
    }
    public function add_custom_property($property, $value) {
        $this->custom_properties[$property] = $value;
    }
    public function add_extension($extension, $value) {
        $this->extension_properties[$extension] = $value;
    }
    public function set_extension_platform($value) {
        $this->extension_platform = $value;
    }

    public function set_cartridge_bundle($value) {
        $this->cartridge_bundle = $value;
    }

    public function set_cartridge_icon($value) {
        $this->cartridge_icon = $value;
    }

    protected function on_save() {
        //this has to be done like this since order of apearance of the tags is also mandatory
        //and specified in basiclti schema files

        //main items
        $rns = $this->ccnamespaces['blti'];
        $this->append_new_element_ns($this->root, $rns, 'title'      , $this->title      );
        $this->append_new_element_ns($this->root, $rns, 'description', $this->description);

        //custom properties
        if (!empty($this->custom_properties)) {
            $custom = $this->append_new_element_ns($this->root, $rns, 'custom');
            foreach ($this->custom_properties as $property => $value) {
                $node = $this->append_new_element_ns($custom, $this->ccnamespaces['lticm'], 'property' , $value);
                $this->append_new_attribute_ns($node, $this->ccnamespaces['xmlns'],'name', $property);
            }
        }

        //extension properties
        if (!empty($this->extension_properties)) {
            $extension = $this->append_new_element_ns($this->root, $rns, 'extensions');
            if (!empty($this->extension_platform)) {
                $this->append_new_attribute_ns($extension, $this->ccnamespaces['xmlns'], 'platform', $this->extension_platform);
            }
            foreach ($this->extension_properties as $property => $value) {
                $node = $this->append_new_element_ns($extension, $this->ccnamespaces['lticm'], 'property' , $value);
                $this->append_new_attribute_ns($node, $this->ccnamespaces['xmlns'], 'name', $property);
            }
        }

        $this->append_new_element_ns($this->root, $rns, 'launch_url' , $this->launch_url );
        if (!empty($this->secure_launch_url)) {
            $this->append_new_element_ns($this->root, $rns, 'secure_launch_url' , $this->secure_launch_url);
        }
        $this->append_new_element_ns($this->root, $rns, 'icon'       , $this->icon       );
        if (!empty($this->secure_icon)) {
            $this->append_new_element_ns($this->root, $rns, 'secure_icon' , $this->secure_icon);
        }

        //vendor info
        $vendor = $this->append_new_element_ns($this->root, $rns, 'vendor');
        $vcode = empty($this->vendor_code) ? 'I' : $this->vendor_code;
        $this->append_new_element_ns($vendor, $this->ccnamespaces['lticp'], 'code', $vcode);
        $this->append_new_element_ns($vendor, $this->ccnamespaces['lticp'], 'name', $this->vendor_name);
        if (!empty($this->vendor_description)) {
            $this->append_new_element_ns($vendor, $this->ccnamespaces['lticp'], 'description', $this->vendor_description);
        }
        if (!empty($this->vendor_url)) {
            $this->append_new_element_ns($vendor, $this->ccnamespaces['lticp'], 'url', $this->vendor_url);
        }
        if (!empty($this->vendor_contact)) {
            $vcontact = $this->append_new_element_ns($vendor, $this->ccnamespaces['lticp'], 'contact');
            $this->append_new_element_ns($vcontact, $this->ccnamespaces['lticp'], 'email', $this->vendor_contact['email']);
        }

        //cartridge bundle and icon
        if (!empty($this->cartridge_bundle)) {
            $cbundle = $this->append_new_element_ns($this->root, $this->ccnamespaces['xmlns'], 'cartridge_bundle');
            $this->append_new_attribute_ns($cbundle, $this->ccnamespaces['xmlns'], 'identifierref', $this->cartridge_bundle);
        }
        if (!empty($this->cartridge_icon)) {
            $cicon = $this->append_new_element_ns($this->root, $this->ccnamespaces['xmlns'], 'cartridge_icon');
            $this->append_new_attribute_ns($cicon, $this->ccnamespaces['xmlns'], 'identifierref', $this->cartridge_icon);
        }

        return true;
    }

}
