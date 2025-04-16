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
 * The user screen.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\screen;

use context_course;
use grade_seq;
use gradereport_singleview;
use moodle_url;
use pix_icon;
use html_writer;
use gradereport_singleview\local\ui\range;
use gradereport_singleview\local\ui\bulk_insert;
use grade_item;
use grade_grade;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * The user screen.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends tablelike implements selectable_items {

    /** @var array $categories A cache for grade_item categories */
    private $categories = [];

    /** @var int $requirespaging Do we have more items than the paging limit? */
    private $requirespaging = true;

    /** @var array get a valid user.  */
    public $item = [];

    /**
     * Get the label for the select box that chooses items for this page.
     * @return string
     */
    public function select_label(): string {
        return get_string('selectgrade', 'gradereport_singleview');
    }

    /**
     * Get the description for the screen.
     *
     * @return string
     */
    public function description(): string {
        return get_string('gradeitems', 'grades');
    }

    /**
     * Convert the list of items to a list of options.
     *
     * @return array
     */
    public function options(): array {
        $result = [];
        foreach ($this->items as $itemid => $item) {
            $result[$itemid] = $item->get_name();
        }
        return $result;
    }

    /**
     * Get the type of items on this screen.
     *
     * @return string
     */
    public function item_type(): string {
        return 'grade';
    }

    /**
     * Init the screen
     *
     * @param bool $selfitemisempty Have we selected an item yet?
     */
    public function init($selfitemisempty = false) {

        if (!$selfitemisempty) {
            $validusers = \grade_report::get_gradable_users($this->courseid, $this->groupid);
            if (!isset($validusers[$this->itemid])) {
                // If the passed user id is not valid, show the first user from the list instead.
                $this->item = reset($validusers);
                $this->itemid = $this->item->id;
            } else {
                $this->item = $validusers[$this->itemid];
            }
        }

        $seq = new grade_seq($this->courseid, true);

        $this->items = [];
        foreach ($seq->items as $itemid => $item) {
            if (grade::filter($item)) {
                $this->items[$itemid] = $item;
            }
        }

        // If we change perpage on pagination we might end up with a page that doesn't exist.
        if ($this->perpage) {
            $numpages = intval(count($this->items) / $this->perpage) + 1;
            if ($numpages <= $this->page) {
                $this->page = 0;
            }
        } else {
            $this->page = 0;
        }

        $this->requirespaging = count($this->items) > $this->perpage;

        $this->setup_structure();

        $this->definition = [
            'finalgrade', 'feedback', 'override', 'exclude'
        ];
        $this->set_headers($this->original_headers());
    }

    /**
     * Get the list of headers for the table.
     *
     * @return array List of headers
     */
    public function original_headers(): array {
        return [
            get_string('assessmentname', 'gradereport_singleview'),
            '', // For filter icon.
            get_string('gradecategory', 'grades'),
            get_string('gradenoun'),
            get_string('range', 'grades'),
            get_string('feedback', 'grades'),
            get_string('override', 'gradereport_singleview'),
            get_string('exclude', 'gradereport_singleview'),
        ];
    }

    /**
     * Format each row of the table.
     *
     * @param grade_item $item
     * @return array
     */
    public function format_line($item): array {
        global $OUTPUT;

        $grade = $this->fetch_grade_or_default($item, $this->item->id);
        $gradestatus = '';

        // Show hidden icon if the grade is hidden and the user has permission to view hidden grades.
        $showhiddenicon = ($grade->is_hidden() || $item->is_hidden()) &&
            has_capability('moodle/grade:viewhidden', context_course::instance($item->courseid));

        $context = [
            'hidden' => $showhiddenicon,
            'locked' => $grade->is_locked(),
        ];

        if (in_array(true, $context)) {
            $context['classes'] = 'gradestatus';
            $gradestatus = $OUTPUT->render_from_template('core_grades/status_icons', $context);
        }

        // Create a fake gradetreeitem so we can call get_element_header().
        // The type logic below is from grade_category->_get_children_recursion().
        $gradetreeitem = [];

        $type = in_array($item->itemtype, ['course', 'category']) ? "{$item->itemtype}item" : 'item';
        $gradetreeitem['type'] = $type;
        $gradetreeitem['object'] = $item;
        $gradetreeitem['userid'] = $this->item->id;

        $itemname = \grade_helper::get_element_header($gradetreeitem, true, false, false, false, true);
        $grade->label = $item->get_name();

        $formatteddefinition = $this->format_definition($grade);

        $itemicon = html_writer::div($this->format_icon($item), 'me-1');
        $itemtype = \html_writer::span(\grade_helper::get_element_type_string($gradetreeitem),
            'd-block text-uppercase small dimmed_text');

        $itemtitle = html_writer::div($itemname, 'rowtitle');
        $itemcontent = html_writer::div($itemtype . $itemtitle);

        $line = [
            html_writer::div($itemicon . $itemcontent, "{$type} d-flex align-items-center"),
            $this->get_item_action_menu($item),
            $this->category($item),
            $formatteddefinition['finalgrade'] . $gradestatus,
            new range($item),
            $formatteddefinition['feedback'],
            $formatteddefinition['override'],
            $formatteddefinition['exclude'],
        ];
        $lineclasses = [
            'gradeitem',
            'action',
            'category',
            'grade',
            'range',
        ];

        $outputline = [];
        $i = 0;
        foreach ($line as $key => $value) {
            $cell = new \html_table_cell($value);
            if ($isheader = $i == 0) {
                $cell->header = $isheader;
                $cell->scope = "row";
            }
            if (array_key_exists($key, $lineclasses)) {
                $cell->attributes['class'] = $lineclasses[$key];
            }
            $outputline[] = $cell;
            $i++;
        }

        return $outputline;
    }

    /**
     * Helper to get the icon for an item.
     *
     * @param grade_item $item
     * @return string
     */
    private function format_icon($item): string {
        $element = ['type' => 'item', 'object' => $item];
        return \grade_helper::get_element_icon($element);
    }

    /**
     * Return the action menu HTML for the grade item.
     *
     * @param grade_item $item
     * @return mixed
     */
    private function get_item_action_menu(grade_item $item) {
        global $OUTPUT;

        $menuitems = [];
        $url = new moodle_url($this->format_link('grade', $item->id));
        $title = get_string('showallgrades', 'core_grades');
        $menuitems[] = new \action_menu_link_secondary($url, null, $title);
        $menu = new \action_menu($menuitems);
        $icon = $OUTPUT->pix_icon('i/moremenu', get_string('actions'));
        $extraclasses = 'btn btn-icon d-flex';
        $menu->set_menu_trigger($icon, $extraclasses);
        $menu->set_menu_left();
        $menu->set_boundary('window');

        return $OUTPUT->render($menu);
    }

    /**
     * Helper to get the category for an item.
     *
     * @param grade_item $item
     * @return string
     */
    private function category(grade_item $item): string {
        global $DB;

        if (empty($item->categoryid)) {

            if ($item->itemtype == 'course') {
                return $this->course->fullname;
            }

            $params = ['id' => $item->iteminstance];
            $elem = $DB->get_record('grade_categories', $params);

            return $elem->fullname;
        }

        if (!isset($this->categories[$item->categoryid])) {
            $category = $item->get_parent_category();

            $this->categories[$category->id] = $category;
        }

        return $this->categories[$item->categoryid]->get_name();
    }

    /**
     * Get the heading for the page.
     *
     * @return string
     */
    public function heading(): string {
        global $PAGE;
        $headinglangstring = $PAGE->user_is_editing() ? 'gradeuseredit' : 'gradeuser';
        return get_string($headinglangstring, 'gradereport_singleview', fullname($this->item));
    }

    /**
     * Get the summary for this table.
     *
     * @return string
     */
    public function summary(): string {
        return get_string('summaryuser', 'gradereport_singleview');
    }

    /**
     * Default pager
     *
     * @return string
     */
    public function pager(): string {
        global $OUTPUT;

        if (!$this->supports_paging()) {
            return '';
        }

        return $OUTPUT->paging_bar(
            count($this->items), $this->page, $this->perpage,
            new moodle_url('/grade/report/singleview/index.php', [
                'perpage' => $this->perpage,
                'id' => $this->courseid,
                'group' => $this->groupid,
                'itemid' => $this->itemid,
                'item' => 'user'
            ])
        );
    }

    /**
     * Does this page require paging?
     *
     * @return bool
     */
    public function supports_paging(): bool {
        return $this->requirespaging;
    }


    /**
     * Process the data from the form.
     *
     * @param array $data
     * @return stdClass of warnings
     */
    public function process($data): stdClass {
        $bulk = new bulk_insert($this->item);
        // Bulk insert messages the data to be passed in
        // ie: for all grades of empty grades apply the specified value.
        if ($bulk->is_applied($data)) {
            $filter = $bulk->get_type($data);
            $insertvalue = $bulk->get_insert_value($data);

            $userid = $this->item->id;
            foreach ($this->items as $gradeitemid => $gradeitem) {
                $null = $gradeitem->gradetype == GRADE_TYPE_SCALE ? -1 : '';
                $field = "finalgrade_{$gradeitem->id}_{$this->itemid}";
                if (isset($data->$field)) {
                    continue;
                }

                $oldfinalgradefield = "oldfinalgrade_{$gradeitem->id}_{$this->itemid}";
                // Bulk grade changes for all grades need to be processed and shouldn't be skipped if they had a previous grade.
                if ($gradeitem->is_course_item() || ($filter != 'all' && !empty($data->$oldfinalgradefield))) {
                    if ($gradeitem->is_course_item()) {
                        // The course total should not be overridden.
                        unset($data->$field);
                        unset($data->oldfinalgradefield);
                        $oldoverride = "oldoverride_{$gradeitem->id}_{$this->itemid}";
                        unset($data->$oldoverride);
                        $oldfeedback = "oldfeedback_{$gradeitem->id}_{$this->itemid}";
                        unset($data->$oldfeedback);
                    }
                    continue;
                }
                $grade = grade_grade::fetch([
                    'itemid' => $gradeitemid,
                    'userid' => $userid
                ]);

                $data->$field = empty($grade) ? $null : $grade->finalgrade;
                $data->{"old$field"} = $data->$field;
            }

            foreach ($data as $varname => $value) {
                if (preg_match('/^oldoverride_(\d+)_(\d+)/', $varname, $matches)) {
                    // If we've selected overriding all grades.
                    if ($filter == 'all') {
                        $override = "override_{$matches[1]}_{$matches[2]}";
                        $data->$override = '1';
                    }
                }
                if (!preg_match('/^finalgrade_(\d+)_(\d+)/', $varname, $matches)) {
                    continue;
                }

                $gradeitem = grade_item::fetch([
                    'courseid' => $this->courseid,
                    'id' => $matches[1],
                ]);

                $isscale = ($gradeitem->gradetype == GRADE_TYPE_SCALE);

                $empties = (trim($value ?? '') === '' || ($isscale && $value == -1));

                if ($filter == 'all' || $empties) {
                    $data->$varname = ($isscale && empty($insertvalue)) ? -1 : $insertvalue;
                }
            }
        }
        return parent::process($data);
    }
}
