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

        $this->items = [];
        foreach ($roleids as $roleid) {
            // Keeping the first user appearance.
            $this->items = $this->items + get_role_users(
                $roleid, $this->context, false, '',
                'u.id, u.lastname, u.firstname', null, $this->groupid,
                $this->perpage * $this->page, $this->perpage
            );
        }
        $this->item = $DB->get_record('course', ['id' => $this->courseid]);
    }

    /**
     * Get the type of items on this screen, not valid so return false.
     *
     * @return string|null
     */
    public function item_type(): ?string {
        return false;
    }

    /**
     * Return the HTML for the page.
     *
     * @return string
     */
    public function html(): string {
        global $OUTPUT, $COURSE;

        if ($this->itemid === null) {
            $userlink = new \moodle_url('/grade/report/singleview/index.php', ['id' => $COURSE->id, 'item' => 'user_select']);
            $gradelink = new \moodle_url('/grade/report/singleview/index.php', ['id' => $COURSE->id, 'item' => 'grade_select']);
            $context = [
                'courseid' => $COURSE->id,
                'imglink' => $OUTPUT->image_url('zero_state', 'gradereport_singleview'),
                'userzerolink' => $userlink->out(false),
                'userselectactive' => false,
                'gradezerolink' => $gradelink->out(false),
                'gradeselectactive' => false,
                'displaylabel' => false
            ];
            return $OUTPUT->render_from_template('gradereport_singleview/zero_state', $context);
        }

        $html = '';

        $types = gradereport_singleview\report\singleview::valid_screens();

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

            $params = [
                'id' => $this->courseid,
                'item' => $screen->item_type(),
                'group' => $this->groupid
            ];

            $url = new moodle_url('/grade/report/singleview/index.php', $params);

            $select = new \single_select($url, 'itemid', $options, '', ['' => $screen->select_label()]);
            $select->set_label($screen->select_label(), ['class' => 'accesshide']);
            $html .= $OUTPUT->render($select);
        }
        $html = $OUTPUT->container($html, 'selectitems');

        if (empty($html)) {
            $OUTPUT->notification(get_string('noscreens', 'gradereport_singleview'));
        }

        return $html;
    }

    /**
     * Should we show the next prev selector?
     * @return bool
     */
    public function supports_next_prev(): bool {
        return false;
    }

    /**
     * Should we show the base singlereport group selector?
     * @return bool
     */
    public function display_group_selector(): bool {
        if ($this->itemid === null) {
            return false;
        }
        return true;
    }

    /**
     * Get the heading for the screen.
     *
     * @return string
     */
    public function heading(): string {
        return ' ';
    }

    /**
     * Does this screen support paging?
     *
     * @return bool
     */
    public function supports_paging(): bool {
        return false;
    }
}
