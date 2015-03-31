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
 * The screen with a list of users.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\screen;

use gradereport_singleview\local\ui\range;
use gradereport_singleview\local\ui\bulk_insert;
use grade_grade;
use grade_item;
use moodle_url;
use pix_icon;
use html_writer;
use gradereport_singleview;

defined('MOODLE_INTERNAL') || die;

/**
 * The screen with a list of users.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade extends tablelike implements selectable_items, filterable_items {

    /** @var int $totalitemcount Used for paging */
    private $totalitemcount = 0;

    /** @var bool $requiresextra True if this is a manual grade item */
    private $requiresextra = false;

    /** @var bool $requirepaging True if there are more users than our limit. */
    private $requirespaging = true;

    /**
     * True if $CFG->grade_overridecat is true
     *
     * @return bool
     */
    public static function allowcategories() {
        return get_config('moodle', 'grade_overridecat');
    }

    /**
     * Filter the list excluding category items (if required)?
     * @param grade_item $item The grade item.
     */
    public static function filter($item) {
        return get_config('moodle', 'grade_overridecat') ||
                !($item->is_course_item() || $item->is_category_item());
    }

    /**
     * Get the label for the select box that chooses items for this page.
     * @return string
     */
    public function select_label() {
        return get_string('selectuser', 'gradereport_singleview');
    }

    /**
     * Get the description of this page
     * @return string
     */
    public function description() {
        return get_string('users');
    }

    /**
     * Convert this list of items into an options list
     *
     * @return array
     */
    public function options() {
        $options = array();
        foreach ($this->items as $userid => $user) {
            $options[$userid] = fullname($user);
        }

        return $options;
    }

    /**
     * Return the type of the things in this list.
     * @return string
     */
    public function item_type() {
        return 'user';
    }

    /**
     * Get the original settings for this item
     * @return array
     */
    public function original_definition() {
        $def = array('finalgrade', 'feedback');

        $def[] = 'override';

        $def[] = 'exclude';

        return $def;
    }

    /**
     * Init this page
     *
     * @param bool $selfitemisempty True if we have not selected a user.
     */
    public function init($selfitemisempty = false) {

        $this->items = $this->load_users();
        $this->totalitemcount = count($this->items);

        if ($selfitemisempty) {
            return;
        }

        $params = array(
            'id' => $this->itemid,
            'courseid' => $this->courseid
        );

        $this->item = grade_item::fetch($params);
        if (!self::filter($this->item)) {
            $this->items = array();
            $this->set_init_error(get_string('gradeitemcannotbeoverridden', 'gradereport_singleview'));
        }

        $this->requiresextra = !$this->item->is_manual_item();

        $this->setup_structure();

        $this->set_definition($this->original_definition());
        $this->set_headers($this->original_headers());
    }

    /**
     * Get the table headers
     *
     * @return array
     */
    public function original_headers() {
        return array(
            '', // For filter icon.
            get_string('firstname') . ' (' . get_string('alternatename') . ') ' . get_string('lastname'),
            get_string('range', 'grades'),
            get_string('grade', 'grades'),
            get_string('feedback', 'grades'),
            $this->make_toggle_links('override'),
            $this->make_toggle_links('exclude')
        );
    }

    /**
     * Format a row in the table
     *
     * @param user $item
     * @return string
     */
    public function format_line($item) {
        global $OUTPUT;

        $grade = $this->fetch_grade_or_default($this->item, $item->id);

        $lockicon = '';

        $lockedgrade = $lockedgradeitem = 0;
        if (!empty($grade->locked)) {
            $lockedgrade = 1;
        }
        if (!empty($grade->grade_item->locked)) {
            $lockedgradeitem = 1;
        }
        // Check both grade and grade item.
        if ( $lockedgrade || $lockedgradeitem ) {
            $lockicon = $OUTPUT->pix_icon('t/locked', 'grade is locked') . ' ';
        }

        if (!empty($item->alternatename)) {
            $fullname = $lockicon . $item->alternatename . ' (' . $item->firstname . ') ' . $item->lastname;
        } else {
            $fullname = $lockicon . fullname($item);
        }

        $item->imagealt = $fullname;
        $url = new moodle_url("/user/view.php", array('id' => $item->id, 'course' => $this->courseid));
        $iconstring = get_string('filtergrades', 'gradereport_singleview', $fullname);
        $grade->label = $fullname;

        $line = array(
            $OUTPUT->action_icon($this->format_link('user', $item->id), new pix_icon('t/editstring', $iconstring)),
            $OUTPUT->user_picture($item, array('visibletoscreenreaders' => false)) .
            html_writer::link($url, $fullname),
            $this->item_range()
        );
        $lineclasses = array(
            "action",
            "user",
            "range"
        );
        $outputline = array();
        $i = 0;
        foreach ($line as $key => $value) {
            $cell = new \html_table_cell($value);
            if ($isheader = $i == 1) {
                $cell->header = $isheader;
                $cell->scope = "row";
            }
            if (array_key_exists($key, $lineclasses)) {
                $cell->attributes['class'] = $lineclasses[$key];
            }
            $outputline[] = $cell;
            $i++;
        }

        return $this->format_definition($outputline, $grade);
    }

    /**
     * Get the range ui element for this grade_item
     *
     * @return element;
     */
    public function item_range() {
        if (empty($this->range)) {
            $this->range = new range($this->item);
        }

        return $this->range;
    }

    /**
     * Does this page require paging?
     *
     * @return bool
     */
    public function supports_paging() {
        return $this->requirespaging;
    }

    /**
     * Get the pager for this page.
     *
     * @return string
     */
    public function pager() {
        global $OUTPUT;

        return $OUTPUT->paging_bar(
            $this->totalitemcount, $this->page, $this->perpage,
            new moodle_url('/grade/report/singleview/index.php', array(
                'perpage' => $this->perpage,
                'id' => $this->courseid,
                'group' => $this->groupid,
                'itemid' => $this->itemid,
                'item' => 'grade'
            ))
        );
    }

    /**
     * Get the heading for this page.
     *
     * @return string
     */
    public function heading() {
        return get_string('gradeitem', 'gradereport_singleview', $this->item->get_name());
    }

    /**
     * Get the summary for this table.
     *
     * @return string
     */
    public function summary() {
        return get_string('summarygrade', 'gradereport_singleview');
    }

    /**
     * Process the data from the form.
     *
     * @param array $data
     * @return array of warnings
     */
    public function process($data) {
        $bulk = new bulk_insert($this->item);
        // Bulk insert messages the data to be passed in
        // ie: for all grades of empty grades apply the specified value.
        if ($bulk->is_applied($data)) {
            $filter = $bulk->get_type($data);
            $insertvalue = $bulk->get_insert_value($data);
            // Appropriately massage data that may not exist.
            if ($this->supports_paging()) {
                $gradeitem = grade_item::fetch(array(
                    'courseid' => $this->courseid,
                    'id' => $this->item->id
                ));

                $null = $gradeitem->gradetype == GRADE_TYPE_SCALE ? -1 : '';

                foreach ($this->items as $itemid => $item) {
                    $field = "finalgrade_{$gradeitem->id}_{$itemid}";
                    if (isset($data->$field)) {
                        continue;
                    }

                    $grade = grade_grade::fetch(array(
                        'itemid' => $gradeitem->id,
                        'userid' => $itemid
                    ));

                    $data->$field = empty($grade) ? $null : $grade->finalgrade;
                    $data->{"old$field"} = $data->$field;

                    preg_match('/_(\d+)_(\d+)/', $field, $oldoverride);
                    $oldoverride = 'oldoverride' . $oldoverride[0];
                    if (empty($data->$oldoverride)) {
                        $data->$field = (!isset($grade->rawgrade)) ? $null : $grade->rawgrade;
                    }
                }
            }

            foreach ($data as $varname => $value) {
                if (preg_match('/override_(\d+)_(\d+)/', $varname, $matches)) {
                    $data->$matches[0] = '1';
                }
                if (!preg_match('/^finalgrade_(\d+)_/', $varname, $matches)) {
                    continue;
                }

                $gradeitem = grade_item::fetch(array(
                    'courseid' => $this->courseid,
                    'id' => $matches[1]
                ));

                $isscale = ($gradeitem->gradetype == GRADE_TYPE_SCALE);

                $empties = (trim($value) === '' or ($isscale and $value == -1));

                if ($filter == 'all' or $empties) {
                    $data->$varname = ($isscale and empty($insertvalue)) ?
                        -1 : $insertvalue;
                }
            }
        }

        return parent::process($data);
    }

}
