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

    /** @var int Maximum number of students that can be shown on one page */
    protected static $maxperpage = 5000;

    /**
     * List of allowed values for 'perpage' setting
     * @var array $validperpage
     */
    protected static $validperpage = [20, 100];

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
        if (!in_array($perpage, self::$validperpage) && ($perpage !== 0)) {
            // Get from cache.
            $perpage = $cache->get(get_class($this));
        } else {
            // Save to cache.
            $cache->set(get_class($this), $perpage);
        }
        if (isset($perpage) && $perpage) {
            $this->perpage = $perpage;
        } else {
            // Get from cache.
            $perpage = $cache->get(get_class($this));
            $this->perpage = ($perpage === 0) ? $perpage : min(self::$validperpage);
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
    public function format_link(string $screen, int $itemid, ?bool $display = null): string {
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
     *
     * @deprecated since Moodle 4.3
     * @return array A list of enroled users.
     */
    protected function load_users(): array {
        debugging('The function ' . __FUNCTION__ . '() is deprecated. Please use grade_report::get_gradable_users() instead.',
            DEBUG_DEVELOPER);

        return grade_report::get_gradable_users($this->courseid, $this->groupid);
    }

    /**
     * Allow selection of number of items to display per page.
     * @return string
     */
    public function perpage_select(): string {
        global $PAGE, $OUTPUT;

        $url = new moodle_url($PAGE->url);
        $numusers = count($this->items);
        // Print per-page dropdown.
        $pagingoptions = self::$validperpage;
        if ($this->perpage) {
            $pagingoptions[] = $this->perpage; // To make sure the current preference is within the options.
        }
        $pagingoptions = array_unique($pagingoptions);
        sort($pagingoptions);
        $pagingoptions = array_combine($pagingoptions, $pagingoptions);
        if ($numusers > self::$maxperpage) {
            $pagingoptions['0'] = self::$maxperpage;
        } else {
            $pagingoptions['0'] = get_string('all');
        }

        $perpagedata = [
            'baseurl' => $url->out(false),
            'options' => []
        ];
        foreach ($pagingoptions as $key => $name) {
            $perpagedata['options'][] = [
                'name' => $name,
                'value' => $key,
                'selected' => $key == $this->perpage,
            ];
        }

        // The number of students per page is always limited even if it is claimed to be unlimited.
        $this->perpage = $this->perpage ?: self::$maxperpage;
        $perpagedata['pagingbar'] = $this->pager();
        return $OUTPUT->render_from_template('gradereport_singleview/perpage', $perpagedata);;
    }
}
