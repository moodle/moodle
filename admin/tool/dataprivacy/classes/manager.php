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
 * Class \tool_dataprivacy\manager
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy;
defined('MOODLE_INTERNAL') || die();

/**
 * Wrapper for \core_privacy\manager that sends notifications about exceptions to DPO
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager extends \core_privacy\manager {

    /**
     * Call the named method with the specified params on the supplied component if it implements the relevant interface on its provider.
     *
     * @param   string  $component The component to call
     * @param   string  $interface The interface to implement
     * @param   string  $methodname The method to call
     * @param   array   $params The params to call
     * @return  mixed
     */
    public static function component_class_callback(string $component, string $interface, string $methodname, array $params) {
        try {
            return parent::component_class_callback($component, $interface, $methodname, $params);
        } catch (\Throwable $e) {
            debugging($e->getMessage(), DEBUG_DEVELOPER, $e->getTrace());
            self::notify_dpo($e, $component, $interface, $methodname, $params);
        }
        return null;
    }

    /**
     * Notifies all DPOs about exception occurred
     *
     * @param \Throwable $e
     * @param string $component
     * @param string $interface
     * @param string $methodname
     * @param array $params
     * @return mixed
     */
    protected static function notify_dpo(\Throwable $e, string $component, string $interface, string $methodname, array $params) {

        // Get the list of the site Data Protection Officers.
        $dpos = api::get_site_dpos();

        $messagesubject = get_string('exceptionnotificationsubject', 'tool_dataprivacy');
        $a = (object)[
            'fullmethodname' => static::get_provider_classname_for_component($component) . '::' . $methodname,
            'component' => $component,
            'message' => $e->getMessage(),
            'backtrace' => $e->getTraceAsString()
        ];
        $messagebody = get_string('exceptionnotificationbody', 'tool_dataprivacy', $a);

        // Email the data request to the Data Protection Officer(s)/Admin(s).
        foreach ($dpos as $dpo) {
            $message = new \core\message\message();
            $message->courseid          = SITEID;
            $message->component         = 'tool_dataprivacy';
            $message->name              = 'notifyexceptions';
            $message->userfrom          = \core_user::get_noreply_user();
            $message->subject           = $messagesubject;
            $message->fullmessageformat = FORMAT_HTML;
            $message->notification      = 1;
            $message->userto            = $dpo;
            $message->fullmessagehtml   = $messagebody;
            $message->fullmessage       = html_to_text($messagebody);

            // Send message.
            return message_send($message);
        }
    }
}