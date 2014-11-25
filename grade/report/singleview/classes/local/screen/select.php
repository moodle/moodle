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
 * The gradebook simple view - initial view to select your search options
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\screen;

use gradereport_singleview;
use moodle_url;

defined('MOODLE_INTERNAL') || die;

/**
 * The gradebook simple view - initial view to select your search options
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class select extends screen {

    /**
     * Initialise this screen
     *
     * @param bool $selfitemisempty Has an item been selected (will be false)
     */
    public function init($selfitemisempty = false) {
        global $DB;

        $roleids = explode(',', get_config('moodle', 'gradebookroles'));

        $this->items = array();
        foreach ($roleids as $roleid) {
            // Keeping the first user appearance.
            $this->items = $this->items + get_role_users(
                $roleid, $this->context, false, '',
                'u.id, u.lastname, u.firstname', null, $this->groupid,
                $this->perpage * $this->page, $this->perpage
            );
        }
        $this->item = $DB->get_record('course', array('id' => $this->courseid));
    }

    /**
     * Get the type of items on this screen, not valid so return false.
     *
     * @return bool
     */
    public function item_type() {
        return false;
    }

    /**
     * Return the HTML for the page.
     *
     * @return string
     */
    public function html() {
        global $OUTPUT;

        $html = '';

        $types = gradereport_singleview::valid_screens();

        foreach ($types as $type) {
            $classname = "gradereport_singleview\\local\\screen\\${type}";

            $screen = new $classname($this->courseid, null, $this->groupid);

            if (!$screen instanceof selectable_items) {
                continue;
            }

            $options = $screen->options();

            if (empty($options)) {
                continue;
            }

            $params = array(
                'id' => $this->courseid,
                'item' => $screen->item_type(),
                'group' => $this->groupid
            );

            $url = new moodle_url('/grade/report/singleview/index.php', $params);

            $select = new \single_select($url, 'itemid', $options);
            $select->set_label($screen->description());
            $html .= $OUTPUT->render($select);
        }

        if (empty($html)) {
            $OUTPUT->notification(get_string('noscreens', 'gradereport_singleview'));
        }

        return $html;
    }

    /**
     * Should we show the next prev selector?
     * @return bool
     */
    public function supports_next_prev() {
        return false;
    }
}
