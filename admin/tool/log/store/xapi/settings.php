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
 * Settings file.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/src/autoload.php');
require_once($CFG->dirroot . '/admin/tool/log/store/xapi/lib.php');

if ($hassiteconfig) {
    // Endpoint.
    $settings->add(new admin_setting_configtext('logstore_xapi/endpoint',
        get_string('endpoint', 'logstore_xapi'), '',
        'http://example.com/endpoint/location/', PARAM_URL));
    // Username.
    $settings->add(new admin_setting_configtext('logstore_xapi/username',
        get_string('username', 'logstore_xapi'), '', 'username', PARAM_TEXT));
    // Key or password.
    $settings->add(new admin_setting_configtext('logstore_xapi/password',
        get_string('password', 'logstore_xapi'), '', 'password', PARAM_TEXT));

    // Switch background batch mode on.
    $settings->add(new admin_setting_configcheckbox('logstore_xapi/backgroundmode',
        get_string('backgroundmode', 'logstore_xapi'),
        get_string('backgroundmode_desc', 'logstore_xapi'), 1));

    $settings->add(new admin_setting_configtext('logstore_xapi/maxbatchsize',
        get_string('maxbatchsize', 'logstore_xapi'),
        get_string('maxbatchsize_desc', 'logstore_xapi'), 30, PARAM_INT));

    // Maximum batch size for failed records being replayed.
    $settings->add(new admin_setting_configtext('logstore_xapi/maxbatchsizeforfailed',
        get_string('maxbatchsizeforfailed', 'logstore_xapi'),
        get_string('maxbatchsizeforfailed_desc', 'logstore_xapi'), 15, PARAM_INT));

    // Maximum batch size for historical records.
    $settings->add(new admin_setting_configtext('logstore_xapi/maxbatchsizeforhistorical',
        get_string('maxbatchsizeforhistorical', 'logstore_xapi'),
        get_string('maxbatchsizeforhistorical_desc', 'logstore_xapi'), 30, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/resendfailedbatches',
        get_string('resendfailedbatches', 'logstore_xapi'),
        get_string('resendfailedbatches_desc', 'logstore_xapi'), 0));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/mbox',
        get_string('mbox', 'logstore_xapi'),
        get_string('mbox_desc', 'logstore_xapi'), 0));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/shortcourseid',
        get_string('shortcourseid', 'logstore_xapi'),
        get_string('shortcourseid_desc', 'logstore_xapi'), 0));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/sendidnumber',
        get_string('sendidnumber', 'logstore_xapi'),
        get_string('sendidnumber_desc', 'logstore_xapi'), 0));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/send_username',
        get_string('send_username', 'logstore_xapi'),
        get_string('send_username_desc', 'logstore_xapi'), 0));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/send_jisc_data',
        get_string('send_jisc_data', 'logstore_xapi'),
        get_string('send_jisc_data_desc', 'logstore_xapi'), 0));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/sendresponsechoices',
       get_string('send_response_choices', 'logstore_xapi'),
       get_string('send_response_choices_desc', 'logstore_xapi'), 0));

    // Control sending notifications.
    $settings->add(new admin_setting_configcheckbox('logstore_xapi/enablesendingnotifications',
        get_string('enablesendingnotifications', 'logstore_xapi'),
        get_string('enablesendingnotifications_desc', 'logstore_xapi'), 1));

    // Set threshold for sending notifications.
    $settings->add(new admin_setting_configtext('logstore_xapi/errornotificationtrigger',
        get_string('errornotificationtrigger', 'logstore_xapi'),
        get_string('errornotificationtrigger_desc', 'logstore_xapi'), 10, PARAM_INT));

    // Cohorts.
    $settings->add(new admin_setting_heading('cohorts',
        get_string('cohorts', 'logstore_xapi'),
        get_string('cohorts_help', 'logstore_xapi')));

    $cohorts = logstore_xapi_get_cohorts();
    $arrcohorts = array();
    foreach ($cohorts as $cohort) {
        $arrcohorts[$cohort->id] = $cohort->name;
    }

    // If there are no cohorts then do not display this option,
    // especially when displaying the settings page for the first time after an upgrade.
    if (count($arrcohorts) != 0) {
        $settings->add(new admin_setting_configmulticheckbox('logstore_xapi/cohorts',
            get_string('includecohorts', 'logstore_xapi'), '', '', $arrcohorts));
    }

    // Additional email addresses.
    $settings->add(new admin_setting_configtext('logstore_xapi/send_additional_email_addresses',
        get_string('send_additional_email_addresses', 'logstore_xapi'),
        get_string('send_additional_email_addresses_desc', 'logstore_xapi'), '', PARAM_TEXT));

    // Filters.
    $settings->add(new admin_setting_heading('filters',
        get_string('filters', 'logstore_xapi'),
        get_string('filters_help', 'logstore_xapi')));

    $settings->add(new admin_setting_configcheckbox('logstore_xapi/logguests',
        get_string('logguests', 'logstore_xapi'), '', '0'));

    $menuroutes = [];
    $eventfunctionmap = \src\transformer\get_event_function_map();
    foreach (array_keys($eventfunctionmap) as $eventname) {
        $menuroutes[$eventname] = $eventname;
    }

    $settings->add(new admin_setting_configmulticheckbox('logstore_xapi/routes',
        get_string('routes', 'logstore_xapi'), '', $menuroutes, $menuroutes));

    // The xAPI Error Log page.
    $errorreport = new admin_externalpage(
        'logstorexapierrorlog',
        get_string('logstorexapierrorlog', 'logstore_xapi'),
        new moodle_url('/admin/tool/log/store/xapi/report.php', array('id' => XAPI_REPORT_ID_ERROR)),
        array('logstore/xapi:viewerrorlog')
    );
    $ADMIN->add('logging', $errorreport);

    // The xAPI Historic Log page.
    $historicreport = new admin_externalpage(
        'logstorexapihistoriclog',
        get_string('logstorexapihistoriclog', 'logstore_xapi'),
        new moodle_url('/admin/tool/log/store/xapi/report.php', array('id' => XAPI_REPORT_ID_HISTORIC)),
        array('logstore/xapi:managehistoric')
    );
    $ADMIN->add('logging', $historicreport);
}
