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
 * @package mod_dataform
 * @category preset
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->libdir/formslib.php");

/**
 *
 */
class dataformpresetform extends \moodleform {

    public function definition() {
        global $COURSE;

        $mform = &$this->_form;
        $indataform = !empty($this->_customdata['dataformid']);

        $mform->addElement('header', 'presetshdr', get_string('presetadd', 'dataform'));

        // Preset current Dataform.
        if ($indataform) {
            $grp = array();
            $grp[] = &$mform->createElement('radio', 'preset_source', null, get_string('presetfromdataform', 'dataform'), 'current');

            $packdata = array(
                'nodata' => get_string('presetnodata', 'dataform'),
                'data' => get_string('presetdata', 'dataform'),
                'dataanon' => get_string('presetdataanon', 'dataform'),
            );
            $grp[] = &$mform->createElement('select', 'preset_data', null, $packdata);
            $grp[] = &$mform->createElement('radio', 'preset_source', null, get_string('presetfromfile', 'dataform'), 'file');
            $mform->addGroup($grp, 'psourcegrp', null, array('  ', '<br />'), false);
            $mform->setDefault('preset_source', 'current');
        }

        // Upload preset.
        $options = array('subdirs' => 0,
                            'maxbytes' => $COURSE->maxbytes,
                            'maxfiles' => 1,
                            'accepted_types' => array('*.zip', '*.mbz'));
        $mform->addElement('filepicker', 'uploadfile', '<span class="hide">'. get_string('upload'). '</span>', null, $options);
        $mform->disabledIf('uploadfile', 'preset_source', 'neq', 'file');

        $mform->addElement('submit', 'add', get_string('add'));
    }

}
