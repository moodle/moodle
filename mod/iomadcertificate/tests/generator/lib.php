<?php

// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Certificate module data generator.
 *
 * @package    mod_iomadcertificate
 * @category   test
 * @author     Russell England <russell.england@catalyst-eu.net>
 * @copyright  Catalyst IT Ltd 2013 <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

class mod_iomadcertificate_generator extends testing_module_generator {

    /**
     * Create new iomadcertificate module instance
     * @param array|stdClass $record data for module being generated. Requires 'course' key
     *     (an id or the full object). Also can have any fields from add module form.
     * @param null|array $options general options for course module. Since 2.6 it is
     *     possible to omit this argument by merging options into $record
     * @return stdClass record from module-defined table with additional field
     *     cmid (corresponding id in course_modules table)
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;
        require_once("$CFG->dirroot/mod/iomadcertificate/lib.php");

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object)(array)$record;
        $options = (array)$options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }

        $defaults = array();
        $defaults['name'] = get_string('pluginname', 'iomadcertificate').' '.$i;
        $defaults['intro'] = 'Test iomadcertificate '.$i;
        $defaults['introformat'] = FORMAT_MOODLE;
        $defaults['emailteachers'] = 0;
        $defaults['savecert'] = 0;
        $defaults['reportcert'] = 0;
        $defaults['delivery'] = 0;
        $defaults['iomadcertificatetype'] = 'A4_non_embedded';
        $defaults['orientation'] = 'L';
        $defaults['borderstyle'] = '0';
        $defaults['bordercolor'] = '0';
        $defaults['printwmark'] = '0';
        $defaults['printdate'] = 0;
        $defaults['datefmt'] = 0;
        $defaults['printnumber'] = 0;
        $defaults['printgrade'] = 0;
        $defaults['gradefmt'] = 0;
        $defaults['printoutcome'] = 0;
        $defaults['printhours'] = '';
        $defaults['printteacher'] = 0;
        $defaults['printsignature'] = '0';
        $defaults['printseal'] = '0';
        foreach ($defaults as $field => $value) {
            if (!isset($record->$field)) {
                $record->$field = $value;
            }
        }

        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }

        // Do work to actually add the instance.
        return parent::create_instance($record, (array)$options);
    }
}
