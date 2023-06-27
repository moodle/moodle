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
 * The form for configuring which services are subscribed and published on a host
 *
 * @package    core
 * @subpackage mnet
 * @copyright  2010 Penny Leach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class mnet_services_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $mnet_peer =& $this->_customdata['peer'];
        $myservices = mnet_get_service_info($mnet_peer);

        $mform->addElement('hidden', 'hostid', $mnet_peer->id);
        $mform->setType('hostid', PARAM_INT);

        $count = 0;
        foreach ($myservices as $name => $versions) {
            $version = current($versions);
            $langmodule =
                ($version['plugintype'] == 'mod'
                    ? ''
                    : ($version['plugintype'] . '_'))
                . $version['pluginname']; // TODO there should be a moodle-wide way to do this

            if ($count > 0) {
                $mform->addElement('html', '<hr />');
            }
            $mform->addElement('html', '<h3>' .  get_string($name.'_name', $langmodule , $mnet_peer->name) . '</h3>' . get_string($name.'_description', $langmodule, $mnet_peer->name));

            $mform->addElement('hidden', 'exists[' . $version['serviceid'] . ']', 1);
            // Temporary fix until MDL-38885 gets integrated.
            $mform->setType('exists', PARAM_BOOL);

            $pubstr = get_string('publish','mnet');
            if (!empty($version['hostsubscribes'])) {
                $pubstr .= ' <a class="notifysuccess" title="'.s(get_string('issubscribed','mnet', $mnet_peer->name)).'">&radic;</a> ';
            }
            $mform->addElement('advcheckbox', 'publish[' . $version['serviceid'] . ']', $pubstr);

            $substr = get_string('subscribe','mnet');
            if (!empty($version['hostpublishes'])) {
                $substr .= ' <a class="notifysuccess" title="'.s(get_string('ispublished','mnet', $mnet_peer->name)).'">&radic;</a> ';
            }
            $mform->addElement('advcheckbox', 'subscribe[' . $version['serviceid']. ']', $substr);
            $count++;
        }
        $this->add_action_buttons();
    }
}
