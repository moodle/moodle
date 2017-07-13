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

class forum1_resurce_file extends general_cc_file {
    const deafultname = 'discussion.xml';

    protected $rootns = 'dt';
    protected $rootname = 'dt:topic';
    protected $ccnamespaces = array('dt'  => 'http://www.imsglobal.org/xsd/imsdt_v1p0',
                                    'xsi' => 'http://www.w3.org/2001/XMLSchema-instance');
    protected $ccnsnames = array('dt' => 'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_6/imsdt_v1p0_localised.xsd');

    protected $title = null;
    protected $text_type = 'text/plain';
    protected $text = null;
    protected $attachments = array();

    public function set_title($title) {
        $this->title = self::safexml($title);
    }

    public function set_text($text, $type='text/plain') {
        $this->text = self::safexml($text);
        $this->text_type = $type;
    }

    public function set_attachments(array $attachments) {
        $this->attachments = $attachments;
    }

    protected function on_save() {
        $this->append_new_element($this->root, 'title', $this->title);
        $text = $this->append_new_element($this->root, 'text', $this->text);
        $this->append_new_attribute($text, 'texttype', $this->text_type);
        if (!empty($this->attachments)) {
            $attachments = $this->append_new_element($this->root, 'attachments');
            foreach ($this->attachments as $value) {
                $att = $this->append_new_element($attachments, 'attachment');
                $this->append_new_attribute($att, 'href', $value);
            }
        }
        return true;
    }

}

class forum11_resurce_file extends forum1_resurce_file {
    protected $rootns = 'dt';
    protected $rootname = 'topic';
    protected $ccnamespaces = array('dt'  => 'http://www.imsglobal.org/xsd/imsccv1p1/imsdt_v1p1',
                                    'xsi' => 'http://www.w3.org/2001/XMLSchema-instance');
    protected $ccnsnames = array('dt' => 'http://www.imsglobal.org/profile/cc/ccv1p1/ccv1p1_imsdt_v1p1.xsd');

    protected function on_save() {
        $rns = $this->ccnamespaces[$this->rootns];
        $this->append_new_element_ns($this->root, $rns, 'title', $this->title);
        $text = $this->append_new_element_ns($this->root, $rns, 'text', $this->text);
        $this->append_new_attribute_ns($text, $rns, 'texttype', $this->text_type);
        if (!empty($this->attachments)) {
            $attachments = $this->append_new_element_ns($this->root, $rns, 'attachments');
            foreach ($this->attachments as $value) {
                $att = $this->append_new_element_ns($attachments, $rns, 'attachment');
                $this->append_new_attribute_ns($att, $rns, 'href', $value);
            }
        }
        return true;
    }

}



