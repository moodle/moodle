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

class assesment1_resurce_file extends general_cc_file {
    const deafultname = 'assesment.xml';

    protected $rootns   = 'xmlns';
    protected $rootname = 'questestinterop';
    protected $ccnamespaces = array('xmlns' => 'http://www.imsglobal.org/xsd/ims_qtiasiv1p2',
                                    'xsi'   => 'http://www.w3.org/2001/XMLSchema-instance');
    protected $ccnsnames = array('xmlns' => 'http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_4/ims_qtiasiv1p2_localised.xsd');

    protected $assesment_title = null;
    protected $assesment_ident = null;
    protected $qtimetadata = array();

    protected function on_save() {
        $rns = $this->ccnamespaces[$this->rootns];
        //add some root stuff
        $assesment = $this->append_new_element_ns($this->root, $rns, 'assesment');
        $this->append_new_attribute_ns($assesment, $rns, 'ident', $this->assesment_ident);
        $this->append_new_attribute_ns($assesment, $rns, 'title', $this->assesment_title);

        if (!empty($this->qtimetadata)) {
            $qtimetadata = $this->append_new_element_ns($assesment, $rns, 'qtimetadata');
            foreach ($this->qtimetadata as $label => $entry) {
                $this->append_new_element_ns($qtimetadata, $rns, 'fieldlabel', $label);
                $this->append_new_element_ns($qtimetadata, $rns, 'fieldentry', $entry);
            }
        }

        //TODO: implement section processing (looks quite complicated for implementing)

        //TODO: finish this!
        return false;
    }
}


class assesment11_resurce_file extends assesment1_resurce_file {
    protected $ccnsnames = array('xmlns' => 'http://www.imsglobal.org/profile/cc/ccv1p1/ccv1p1_qtiasiv1p2p1_v1p0.xsd');
}
