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
 * Certificate template created event.
 *
 * @package   mod_customcert
 * @copyright 2023 Leon Stringer <leon.stringer@ntlworld.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert\event;

use mod_customcert\template;

/**
 * Certificate template created event class.
 *
 * @package   mod_customcert
 * @copyright 2023 Leon Stringer <leon.stringer@ntlworld.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_created extends \core\event\base {

    /**
     * Initialises the event.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'customcert_templates';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        if ($this->contextlevel == \context_system::instance()->contextlevel) {
            // If CONTEXT_SYSTEM assume it's a template.
            return "The user with id '$this->userid' created the certificate template with id '$this->objectid'.";
        } else {
            // Else assume it's a module instance in a course.
            return "The user with id '$this->userid' created the certificate template in course module "
                . "'$this->contextinstanceid'.";
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventtemplatecreated', 'customcert');
    }

    /**
     * Create instance of event.
     *
     * @param template $template
     * @return template_created
     */
    public static function create_from_template(template $template): template_created {
        $data = [
            'context' => $template->get_context(),
            'objectid' => $template->get_id(),
        ];

        return self::create($data);
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        if ($this->contextlevel == \context_system::instance()->contextlevel) {
            return new \moodle_url('/mod/customcert/manage_templates.php');
        } else {
            return new \moodle_url('/mod/customcert/view.php',
                    ['id' => $this->contextinstanceid]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public static function get_objectid_mapping() {
        return ['db' => 'customcert_templates', 'restore' => 'customcert_templates'];
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public static function get_other_mapping() {
        // No need to map.
        return false;
    }
}
