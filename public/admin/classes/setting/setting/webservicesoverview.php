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
 * Special class for overview of external services
 *
 * @author Jerome Mouneyrac
 */
namespace core_admin\setting\setting;

class webservicesoverview extends \admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicesoverviewui',
                        get_string('webservicesoverview', 'webservice'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // do not write any setting
        return '';
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $return = "";
        $brtag = \html_writer::empty_tag('br');

        /// One system controlling Moodle with Token
        $return .= $OUTPUT->heading(get_string('onesystemcontrolling', 'webservice'), 3, 'main');
        $table = new \html_table();
        $table->head = array(get_string('step', 'webservice'), get_string('status'),
            get_string('description'));
        $table->colclasses = array('leftalign step', 'leftalign status', 'leftalign description');
        $table->id = 'onesystemcontrol';
        $table->attributes['class'] = 'admintable wsoverview table generaltable table-hover';
        $table->data = array();

        $return .= $brtag . get_string('onesystemcontrollingdescription', 'webservice')
                . $brtag . $brtag;

        /// 1. Enable Web Services
        $row = array();
        $url = new \moodle_url("/admin/search.php?query=enablewebservices");
        $row[0] = "1. " . \html_writer::tag('a', get_string('enablews', 'webservice'),
                        array('href' => $url));
        $status = \html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        if ($CFG->enablewebservices) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enablewsdescription', 'webservice');
        $table->data[] = $row;

        /// 2. Enable protocols
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=webserviceprotocols");
        $row[0] = "2. " . \html_writer::tag('a', get_string('enableprotocols', 'webservice'),
                        array('href' => $url));
        $status = \html_writer::tag('span', get_string('none'), array('class' => 'badge bg-danger text-white'));
        //retrieve activated protocol
        $active_protocols = empty($CFG->webserviceprotocols) ?
                array() : \explode(',', $CFG->webserviceprotocols);
        if (!empty($active_protocols)) {
            $status = "";
            foreach ($active_protocols as $protocol) {
                $status .= $protocol . $brtag;
            }
        }
        $row[1] = $status;
        $row[2] = get_string('enableprotocolsdescription', 'webservice');
        $table->data[] = $row;

        /// 3. Create user account
        $row = array();
        $url = new \moodle_url("/user/editadvanced.php?id=-1");
        $row[0] = "3. " . \html_writer::tag('a', get_string('createuser', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createuserdescription', 'webservice');
        $table->data[] = $row;

        /// 4. Add capability to users
        $row = array();
        $url = new \moodle_url("/admin/roles/check.php?contextid=1");
        $row[0] = "4. " . \html_writer::tag('a', get_string('checkusercapability', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('checkusercapabilitydescription', 'webservice');
        $table->data[] = $row;

        /// 5. Select a web service
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "5. " . \html_writer::tag('a', get_string('selectservice', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createservicedescription', 'webservice');
        $table->data[] = $row;

        /// 6. Add functions
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "6. " . \html_writer::tag('a', get_string('addfunctions', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('addfunctionsdescription', 'webservice');
        $table->data[] = $row;

        /// 7. Add the specific user
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "7. " . \html_writer::tag('a', get_string('selectspecificuser', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('selectspecificuserdescription', 'webservice');
        $table->data[] = $row;

        /// 8. Create token for the specific user
        $row = array();
        $url = new \moodle_url('/admin/webservice/tokens.php', ['action' => 'create']);
        $row[0] = "8. " . \html_writer::tag('a', get_string('createtokenforuser', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createtokenforuserdescription', 'webservice');
        $table->data[] = $row;

        /// 9. Enable the documentation
        $row = array();
        $url = new \moodle_url("/admin/search.php?query=enablewsdocumentation");
        $row[0] = "9. " . \html_writer::tag('a', get_string('enabledocumentation', 'webservice'),
                        array('href' => $url));
        $status = '<span class="warning">' . get_string('no') . '</span>';
        if ($CFG->enablewsdocumentation) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enabledocumentationdescription', 'webservice');
        $table->data[] = $row;

        /// 10. Test the service
        $row = array();
        $url = new \moodle_url("/admin/webservice/testclient.php");
        $row[0] = "10. " . \html_writer::tag('a', get_string('testwithtestclient', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('testwithtestclientdescription', 'webservice');
        $table->data[] = $row;

        $return .= \html_writer::table($table);

        /// Users as clients with token
        $return .= $brtag . $brtag . $brtag;
        $return .= $OUTPUT->heading(get_string('userasclients', 'webservice'), 3, 'main');
        $table = new \html_table();
        $table->head = array(get_string('step', 'webservice'), get_string('status'),
            get_string('description'));
        $table->colclasses = array('leftalign step', 'leftalign status', 'leftalign description');
        $table->id = 'userasclients';
        $table->attributes['class'] = 'admintable wsoverview table generaltable table-hover';
        $table->data = array();

        $return .= $brtag . get_string('userasclientsdescription', 'webservice') .
                $brtag . $brtag;

        /// 1. Enable Web Services
        $row = array();
        $url = new \moodle_url("/admin/search.php?query=enablewebservices");
        $row[0] = "1. " . \html_writer::tag('a', get_string('enablews', 'webservice'),
                        array('href' => $url));
        $status = \html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        if ($CFG->enablewebservices) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enablewsdescription', 'webservice');
        $table->data[] = $row;

        /// 2. Enable protocols
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=webserviceprotocols");
        $row[0] = "2. " . \html_writer::tag('a', get_string('enableprotocols', 'webservice'),
                        array('href' => $url));
        $status = \html_writer::tag('span', get_string('none'), array('class' => 'badge bg-danger text-white'));
        //retrieve activated protocol
        $active_protocols = empty($CFG->webserviceprotocols) ?
                array() : \explode(',', $CFG->webserviceprotocols);
        if (!empty($active_protocols)) {
            $status = "";
            foreach ($active_protocols as $protocol) {
                $status .= $protocol . $brtag;
            }
        }
        $row[1] = $status;
        $row[2] = get_string('enableprotocolsdescription', 'webservice');
        $table->data[] = $row;


        /// 3. Select a web service
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "3. " . \html_writer::tag('a', get_string('selectservice', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('createserviceforusersdescription', 'webservice');
        $table->data[] = $row;

        /// 4. Add functions
        $row = array();
        $url = new \moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "4. " . \html_writer::tag('a', get_string('addfunctions', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('addfunctionsdescription', 'webservice');
        $table->data[] = $row;

        /// 5. Add capability to users
        $row = array();
        $url = new \moodle_url("/admin/roles/check.php?contextid=1");
        $row[0] = "5. " . \html_writer::tag('a', get_string('addcapabilitytousers', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('addcapabilitytousersdescription', 'webservice');
        $table->data[] = $row;

        /// 6. Test the service
        $row = array();
        $url = new \moodle_url("/admin/webservice/testclient.php");
        $row[0] = "6. " . \html_writer::tag('a', get_string('testwithtestclient', 'webservice'),
                        array('href' => $url));
        $row[1] = "";
        $row[2] = get_string('testauserwithtestclientdescription', 'webservice');
        $table->data[] = $row;

        $return .= \html_writer::table($table);

        return highlight($query, $return);
    }

}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(webservicesoverview::class, \admin_setting_webservicesoverview::class);
