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

namespace logstore_xapi;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/admin/tool/log/store/xapi/tests/enchancement_jisc_skeleton.php');
require_once($CFG->dirroot . '/admin/tool/log/store/xapi/classes/form/reportfilter_form.php');

/**
 * Test case for failed events in a report.
 *
 * @package    logstore_xapi
 * @author     László Záborski <laszlo.zaborski@learningpool.com>
 * @copyright  2020 Learning Pool Ltd (http://learningpool.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class failed_report_test extends enchancement_jisc_skeleton {

    /**
     * @var array Simulated submitted reportfilter_form data for failed report.
     */
    protected $simulatedsubmitteddata = [
        'id' => XAPI_REPORT_ID_ERROR,
        'resend' => XAPI_REPORT_RESEND_FALSE,
        'errortype' => XAPI_REPORT_ERROTYPE_DEFAULT,
        'eventnames' => ['\core\event\course_viewed'],
        'response' => XAPI_REPORT_RESPONSE_DEFAULT,
        'datefrom' => 0,
        'dateto' => 0,
        'submitbutton' => 'Search'
    ];

    /**
     * Get submitted and validated form.
     *
     * @return tool_logstore_xapi_reportfilter_form
     */
    protected function get_validated_form() {
        $filterparams = [
            'defaults' => $this->formdefaults,
            'reportid' => XAPI_REPORT_ID_ERROR,
            'eventnames' => logstore_xapi_get_event_names_array(),
            'errortypes' => logstore_xapi_get_distinct_options_from_failed_table('errortype'),
            'responses' => logstore_xapi_get_distinct_options_from_failed_table('response')
        ];

        $form = new form\tool_logstore_xapi_reportfilter_form('', $filterparams);
        $this->assertTrue($form->is_validated());
        $this->assertTrue($form->is_submitted());

        return $form;
    }

    /**
     * Creating minimum a single course view event to xapi logstore.
     * Submit form and validate form data.
     *
     * @covers \form\tool_logstore_xapi_reportfilter_form
     */
    public function test_single_element() {
        global $DB;

        parent::test_single_element();

        $records = $DB->get_records('logstore_xapi_failed_log');
        $this->assertCount($this->generatedxapilog, $records);

        form\tool_logstore_xapi_reportfilter_form::mock_submit($this->simulatedsubmitteddata);

        $form = $this->get_validated_form();
        $this->validate_submitted_data($form->get_data());
    }

    /**
     * Creating multiple course view events to xapi logstore.
     * Record number depends on $multipletestnumber.
     * Submit form and validate form data.
     *
     * @covers \form\tool_logstore_xapi_reportfilter_form
     */
    public function test_multiple_elements() {
        global $DB;

        parent::test_multiple_elements();

        $records = $DB->get_records('logstore_xapi_failed_log');
        $this->assertCount($this->multipletestnumber * $this->generatedxapilog, $records);

        form\tool_logstore_xapi_reportfilter_form::mock_submit($this->simulatedsubmitteddata);

        $form = $this->get_validated_form();
        $this->validate_submitted_data($form->get_data());
    }
}
