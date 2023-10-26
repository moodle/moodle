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
 * Abstract class used as a base for the 3 screens.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\screen;

use context_course;
use grade_report;
use moodle_url;
use html_writer;
use grade_structure;
use grade_grade;
use grade_item;
use stdClass;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/grade/report/lib.php');

/**
 * Abstract class used as a base for the 3 screens.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class screen {

    /**
     * The id of the course
     * @var int $courseid
     */
    protected $courseid;

    /**
     * Either a user id or a grade_item id
     * @var int|null $itemid
     */
    protected $itemid;

    /**
     * The currently set groupid (if set)
     * @var int $groupid
     */
    protected $groupid;

    /**
     * The course context
     * @var context_course $context
     */
    protected $context;

    /**
     * The page number
     * @var int $page
     */
    protected $page;

    /**
     * Results per page
     * @var int $perpage
     */
    protected $perpage;

    /**
     * List of items on the page, they could be users or grade_items
     * @var array $items
     */
    protected $items;

    /**
     * List of allowed values for 'perpage' setting
     * @var array $validperpage
     */
    protected static $validperpage = [20, 50, 100, 200, 400, 1000, 5000];

    /**
     * To store course data
     * @var stdClass
     */
    protected $course;

    /**
     * General structure representing grade items in course
     * @var grade_structure
     */
    protected $structure;

    /**
     * Constructor
     *
     * @param int $courseid The course id
     * @param int|null $itemid The item id
     * @param int|null $groupid The group id
     */
    public function __construct(int $courseid, ?int $itemid, ?int $groupid = null) {
        global $DB;

        $this->courseid = $courseid;
        $this->itemid = $itemid;
        $this->groupid = $groupid;

        $this->context = context_course::instance($this->courseid);
        $this->course = $DB->get_record('course', ['id' => $courseid]);

        $this->page = optional_param('page', 0, PARAM_INT);

        $cache = \cache::make_from_params(\cache_store::MODE_SESSION, 'gradereport_singleview', 'perpage');
        $perpage = optional_param('perpage', null, PARAM_INT);
        if (!in_array($perpage, self::$validperpage)) {
            // Get from cache.
            $perpage = $cache->get(get_class($this));
        } else {
            // Save to cache.
            $cache->set(get_class($this), $perpage);
        }
        if ($perpage) {
            $this->perpage = $perpage;
        } else {
            $this->perpage = 100;
        }

        $this->init(empty($itemid));
    }

    /**
     * Cache the grade_structure class
     */
    public function setup_structure() {
        $this->structure = new grade_structure();
        $this->structure->modinfo = get_fast_modinfo($this->course);
    }

    /**
     * Create a nice link from a thing (user or grade_item).
     *
     * @param string $screen
     * @param int $itemid
     * @param bool|null $display Should we wrap this in an anchor ?
     * @return string The link
     */
    public function format_link(string $screen, int $itemid, bool $display = null): string {
        $url = new moodle_url('/grade/report/singleview/index.php', [
            'id' => $this->courseid,
            'item' => $screen,
            'itemid' => $itemid,
            'group' => $this->groupid,
        ]);

        if ($display) {
            return html_writer::link($url, $display);
        } else {
            return $url;
        }
    }

    /**
     * Get the grade_grade
     *
     * @param grade_item $item The grade_item
     * @param int $userid The user id
     * @return grade_grade
     */
    public function fetch_grade_or_default(grade_item $item, int $userid): grade_grade {
        $grade = grade_grade::fetch([
            'itemid' => $item->id, 'userid' => $userid
        ]);

        if (!$grade) {
            $default = new stdClass;

            $default->userid = $userid;
            $default->itemid = $item->id;
            $default->feedback = '';

            $grade = new grade_grade($default, false);
        }

        $grade->grade_item = $item;

        return $grade;
    }

    /**
     * Get the default heading for the screen.
     *
     * @return string
     */
    public function heading(): string {
        return get_string('entrypage', 'gradereport_singleview');
    }

    /**
     * Override this to init the screen.
     *
     * @param boolean $selfitemisempty True if no item has been selected yet.
     */
    abstract public function init(bool $selfitemisempty = false);

    /**
     * Get the type of items in the list.
     *
     * @return null|string
     */
    abstract public function item_type(): ?string;

    /**
     * Get the entire screen as a string.
     *
     * @return string
     */
    abstract public function html(): string;

    /**
     * Does this screen support paging?
     *
     * @return bool
     */
    public function supports_paging(): bool {
        return true;
    }

    /**
     * Default pager
     *
     * @return string
     */
    public function pager(): string {
        return '';
    }

    /**
     * Initialise the js for this screen.
     */
    public function js() {
        global $PAGE;

        $module = [
            'name' => 'gradereport_singleview',
            'fullpath' => '/grade/report/singleview/js/singleview.js',
            'requires' => ['base', 'dom', 'event', 'event-simulate', 'io-base']
        ];

        $PAGE->requires->strings_for_js(['overridenoneconfirm', 'removeoverride', 'removeoverridesave'],
            'gradereport_singleview');
        $PAGE->requires->js_init_call('M.gradereport_singleview.init', [], false, $module);
    }

    /**
     * Process the data from a form submission.
     *
     * @param array|object $data
     * @return stdClass of warnings
     */
    public function process($data): stdClass {
        $warnings = [];

        $fields = $this->definition();

        // Avoiding execution timeouts when updating
        // a large amount of grades.
        $progress = 0;
        $progressbar = new \core\progress\display_if_slow();
        $progressbar->start_html();
        $progressbar->start_progress(get_string('savegrades', 'gradereport_singleview'), count((array) $data) - 1);
        $changecount = [];
        // This array is used to determine if the override should be excluded from being counted as a change.
        $ignorevalues = [];

        foreach ($data as $varname => $throw) {
            $progressbar->progress($progress);
            $progress++;
            if (preg_match("/(\w+)_(\d+)_(\d+)/", $varname, $matches)) {
                $itemid = $matches[2];
                $userid = $matches[3];
            } else {
                continue;
            }

            $gradeitem = grade_item::fetch([
                'id' => $itemid, 'courseid' => $this->courseid
            ]);

            if (preg_match('/^old[oe]{1}/', $varname)) {
                $elementname = preg_replace('/^old/', '', $varname);
                if (!isset($data->$elementname)) {
                    // Decrease the progress because we've increased the
                    // size of the array we are iterating through.
                    $progress--;
                    $data->$elementname = false;
                }
            }

            if (!in_array($matches[1], $fields)) {
                continue;
            }

            if (!$gradeitem) {
                continue;
            }

            $grade = $this->fetch_grade_or_default($gradeitem, $userid);

            $classname = '\\gradereport_singleview\\local\\ui\\' . $matches[1];
            $element = new $classname($grade);

            $name = $element->get_name();
            $oldname = "old$name";

            $posted = $data->$name;

            $format = $element->determine_format();

            if ($format->is_textbox() and trim($data->$name) === '') {
                $data->$name = null;
            }

            // Same value; skip.
            if (isset($data->$oldname) && $data->$oldname == $posted) {
                continue;
            }

            // If the user submits Exclude grade elements without the proper.
            // permissions then we should refuse to update.
            if ($matches[1] === 'exclude' && !has_capability('moodle/grade:manage', $this->context)){
                $warnings[] = get_string('nopermissions', 'error', get_string('grade:manage', 'role'));
                continue;
            }

            $msg = $element->set($posted);
            // Value to check against our list of matchelements to ignore.
            $check = explode('_', $varname, 2);

            // Optional type.
            if (!empty($msg)) {
                $warnings[] = $msg;
                if ($element instanceof \gradereport_singleview\local\ui\finalgrade) {
                    // Add this value to this list so that the override object that is coming next will also be skipped.
                    $ignorevalues[$check[1]] = $check[1];
                    // This item wasn't changed so don't add to the changecount.
                    continue;
                }
            }
            // Check to see if this value has already been skipped.
            if (array_key_exists($check[1], $ignorevalues)) {
                continue;
            }
            if (preg_match('/_(\d+)_(\d+)/', $varname, $matchelement)) {
                $changecount[$matchelement[0]] = 1;
            }
        }

        // Some post-processing.
        $eventdata = new stdClass;
        $eventdata->warnings = $warnings;
        $eventdata->post_data = $data;
        $eventdata->instance = $this;
        $eventdata->changecount = $changecount;

        $progressbar->end_html();

        return $eventdata;
    }

    /**
     * By default, there are no options.
     * @return array
     */
    public function options(): array {
        return [];
    }

    /**
     * Should we show the group selector?
     * @return bool
     */
    public function display_group_selector(): bool {
        return true;
    }

    /**
     * Should we show the next prev selector?
     * @return bool
     */
    public function supports_next_prev(): bool {
        return true;
    }

    /**
     * Load a valid list of users for this gradebook as the screen "items".
     * @return array $users A list of enroled users.
     */
    protected function load_users(): array {
        global $CFG;

        // Create a graded_users_iterator because it will properly check the groups etc.
        $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
        $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
        $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $this->context);

        require_once($CFG->dirroot.'/grade/lib.php');
        $gui = new \graded_users_iterator($this->course, null, $this->groupid);
        $gui->require_active_enrolment($showonlyactiveenrol);
        $gui->init();

        // Flatten the users.
        $users = [];
        while ($user = $gui->next_user()) {
            $users[$user->user->id] = $user->user;
        }
        $gui->close();
        return $users;
    }

    /**
     * Allow selection of number of items to display per page.
     * @return string
     */
    public function perpage_select(): string {
        global $PAGE, $OUTPUT;

        $options = array_combine(self::$validperpage, self::$validperpage);

        $url = new moodle_url($PAGE->url);
        $url->remove_params(['page', 'perpage']);

        $out = '';
        $select = new \single_select($url, 'perpage', $options, $this->perpage, null, 'perpagechanger');
        $select->label = get_string('itemsperpage', 'gradereport_singleview');
        $out .= $OUTPUT->render($select);

        return $out;
    }
}
