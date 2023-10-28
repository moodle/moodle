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

    /**
     * Used for paging
     * @var int $totalitemcount
     */
    private $totalitemcount = 0;

    /**
     * True if this is a manual grade item
     * @var bool $requiresextra
     */
    private $requiresextra = false;

    /**
     *  True if there are more users than our limit.
     * @var bool $requirepaging
     */
    private $requirespaging = true;

    /**
     * True if $CFG->grade_overridecat is true
     *
     * @return bool
     */
    public static function allowcategories(): bool {
        return get_config('moodle', 'grade_overridecat');
    }

    /**
     * Filter the list excluding category items (if required)?
     * @param grade_item $item The grade item.
     * @return bool
     */
    public static function filter($item): bool {
        return get_config('moodle', 'grade_overridecat') ||
                !($item->is_course_item() || $item->is_category_item());
    }

    /**
     * Get the label for the select box that chooses items for this page.
     * @return string
     */
    public function select_label(): string {
        return get_string('selectuser', 'gradereport_singleview');
    }

    /**
     * Get the description of this page
     * @return string
     */
    public function description(): string {
        return get_string('users');
    }

    /**
     * Convert this list of items into an options list
     *
     * @return array
     */
    public function options(): array {
        $options = [];
        foreach ($this->items as $userid => $user) {
            $options[$userid] = fullname($user);
        }

        return $options;
    }

    /**
     * Return the type of the things in this list.
     * @return string
     */
    public function item_type(): string {
        return 'user';
    }

    /**
     * Get the original settings for this item
     * @return array
     */
    public function original_definition(): array {
        return [
            'finalgrade',
            'feedback',
            'override',
            'exclude'
        ];
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

        $params = [
            'id' => $this->itemid,
            'courseid' => $this->courseid
        ];

        $this->item = grade_item::fetch($params);
        if (!self::filter($this->item)) {
            $this->items = [];
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
        return [
            get_string('fullnameuser', 'core'),
            '', // For filter icon.
            get_string('grade', 'grades'),
            get_string('range', 'grades'),
            get_string('feedback', 'grades'),
            get_string('override', 'gradereport_singleview'),
            get_string('exclude', 'gradereport_singleview'),
        ];
    }

    /**
     * Format a row in the table
     *
     * @param user $item
     * @return array
     */
    public function format_line($item): array {
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

        if (has_capability('moodle/site:viewfullnames', \context_course::instance($this->courseid))) {
            $fullname = $lockicon . fullname($item, true);
        } else {
            $fullname = $lockicon . fullname($item);
        }

        $item->imagealt = $fullname;
        $url = new moodle_url("/user/view.php", ['id' => $item->id, 'course' => $this->courseid]);
        $grade->label = $fullname;
        $userpic = $OUTPUT->user_picture($item, ['link' => false, 'visibletoscreenreaders' => false]);

        $formatteddefinition = $this->format_definition($grade);

        $line = [
            html_writer::link($url, $userpic . $fullname),
            $this->get_user_action_menu($item),
            $formatteddefinition['finalgrade'],
            $this->item_range(),
            $formatteddefinition['feedback'],
            $formatteddefinition['override'],
            $formatteddefinition['exclude'],
        ];
        $lineclasses = [
            'user',
            'action',
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
    public function supports_paging(): bool {
        return $this->requirespaging;
    }

    /**
     * Get the pager for this page.
     *
     * @return string
     */
    public function pager(): string {
        global $OUTPUT;

        return $OUTPUT->paging_bar(
            $this->totalitemcount, $this->page, $this->perpage,
            new moodle_url('/grade/report/singleview/index.php', [
                'perpage' => $this->perpage,
                'id' => $this->courseid,
                'group' => $this->groupid,
                'itemid' => $this->itemid,
                'item' => 'grade'
            ])
        );
    }

    /**
     * Get the heading for this page.
     *
     * @return string
     */
    public function heading(): string {
        return get_string('gradeitem', 'gradereport_singleview', $this->item->get_name());
    }

    /**
     * Get the summary for this table.
     *
     * @return string
     */
    public function summary(): string {
        return get_string('summarygrade', 'gradereport_singleview');
    }

    /**
     * Process the data from the form.
     *
     * @param array $data
     * @return \stdClass of warnings
     */
    public function process($data): \stdClass {
        $bulk = new bulk_insert($this->item);
        // Bulk insert messages the data to be passed in
        // ie: for all grades of empty grades apply the specified value.
        if ($bulk->is_applied($data)) {
            $filter = $bulk->get_type($data);
            $insertvalue = $bulk->get_insert_value($data);
            // Appropriately massage data that may not exist.
            if ($this->supports_paging()) {
                $gradeitem = grade_item::fetch([
                    'courseid' => $this->courseid,
                    'id' => $this->item->id
                ]);

                $null = $gradeitem->gradetype == GRADE_TYPE_SCALE ? -1 : '';

                foreach ($this->items as $itemid => $item) {
                    $field = "finalgrade_{$gradeitem->id}_{$itemid}";
                    if (isset($data->$field)) {
                        continue;
                    }

                    $grade = grade_grade::fetch([
                        'itemid' => $gradeitem->id,
                        'userid' => $itemid
                    ]);

                    $data->$field = empty($grade) ? $null : $grade->finalgrade;
                    $data->{"old$field"} = $data->$field;
                }
            }

            foreach ($data as $varname => $value) {
                if (preg_match('/^oldoverride_(\d+)_(\d+)/', $varname, $matches)) {
                    // If we've selected overriding all grades.
                    if ($filter == 'all') {
                        $override = "override_{$matches[1]}_{$matches[2]}";
                        $data->$override = '1';
                    }
                }
                if (!preg_match('/^finalgrade_(\d+)_/', $varname, $matches)) {
                    continue;
                }

                $gradeitem = grade_item::fetch([
                    'courseid' => $this->courseid,
                    'id' => $matches[1]
                ]);

                $isscale = ($gradeitem->gradetype == GRADE_TYPE_SCALE);

                $empties = (trim($value) === '' || ($isscale && $value == -1));

                if ($filter == 'all' || $empties) {
                    $data->$varname = ($isscale && empty($insertvalue)) ?
                        -1 : $insertvalue;
                }
            }
        }
        return parent::process($data);
    }

    /**
     * Return the action menu HTML for the user item.
     *
     * @param \stdClass $user
     * @return mixed
     */
    private function get_user_action_menu(\stdClass $user) {
        global $OUTPUT;

        $menuitems = [];
        $url = new moodle_url($this->format_link('user', $user->id));
        $title = get_string('showallgrades', 'core_grades');
        $menuitems[] = new \action_menu_link_secondary($url, null, $title);
        $menu = new \action_menu($menuitems);
        $icon = $OUTPUT->pix_icon('i/moremenu', get_string('actions'));
        $extraclasses = 'btn btn-link btn-icon icon-size-3 d-flex align-items-center justify-content-center';
        $menu->set_menu_trigger($icon, $extraclasses);
        $menu->set_menu_left();
        $menu->set_boundary('window');

        return $OUTPUT->render($menu);
    }
}
