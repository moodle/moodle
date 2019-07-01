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
 * Class containing data for my overview block.
 *
 * @package    block_myoverview
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_myoverview\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

require_once($CFG->dirroot . '/blocks/myoverview/lib.php');

/**
 * Class containing data for my overview block.
 *
 * @copyright  2018 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /**
     * Store the grouping preference.
     *
     * @var string String matching the grouping constants defined in myoverview/lib.php
     */
    private $grouping;

    /**
     * Store the sort preference.
     *
     * @var string String matching the sort constants defined in myoverview/lib.php
     */
    private $sort;

    /**
     * Store the view preference.
     *
     * @var string String matching the view/display constants defined in myoverview/lib.php
     */
    private $view;

    /**
     * Store the paging preference.
     *
     * @var string String matching the paging constants defined in myoverview/lib.php
     */
    private $paging;

    /**
     * Store the display categories config setting.
     *
     * @var boolean
     */
    private $displaycategories;

    /**
     * Store the configuration values for the myoverview block.
     *
     * @var array Array of available layouts matching view/display constants defined in myoverview/lib.php
     */
    private $layouts;

    /**
     * Store a course grouping option setting
     *
     * @var boolean
     */
    private $displaygroupingallincludinghidden;

    /**
     * Store a course grouping option setting.
     *
     * @var boolean
     */
    private $displaygroupingall;

    /**
     * Store a course grouping option setting.
     *
     * @var boolean
     */
    private $displaygroupinginprogress;

    /**
     * Store a course grouping option setting.
     *
     * @var boolean
     */
    private $displaygroupingfuture;

    /**
     * Store a course grouping option setting.
     *
     * @var boolean
     */
    private $displaygroupingpast;

    /**
     * Store a course grouping option setting.
     *
     * @var boolean
     */
    private $displaygroupingstarred;

    /**
     * Store a course grouping option setting.
     *
     * @var boolean
     */
    private $displaygroupinghidden;

    /**
     * main constructor.
     * Initialize the user preferences
     *
     * @param string $grouping Grouping user preference
     * @param string $sort Sort user preference
     * @param string $view Display user preference
     *
     * @throws \dml_exception
     */
    public function __construct($grouping, $sort, $view, $paging) {
        // Get plugin config.
        $config = get_config('block_myoverview');

        // Build the course grouping option name to check if the given grouping is enabled afterwards.
        $groupingconfigname = 'displaygrouping'.$grouping;
        // Check the given grouping and remember it if it is enabled.
        if ($grouping && $config->$groupingconfigname == true) {
            $this->grouping = $grouping;

            // Otherwise fall back to another grouping in a reasonable order.
            // This is done to prevent one-time UI glitches in the case when a user has chosen a grouping option previously which
            // was then disabled by the admin in the meantime.
        } else if ($config->displaygroupingall == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_ALL;
        } else if ($config->displaygroupingallincludinghidden == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_ALLINCLUDINGHIDDEN;
        } else if ($config->displaygroupinginprogress == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_INPROGRESS;
        } else if ($config->displaygroupingfuture == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_FUTURE;
        } else if ($config->displaygroupingpast == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_PAST;
        } else if ($config->displaygroupingstarred == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_FAVOURITES;
        } else if ($config->displaygroupinghidden == true) {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_HIDDEN;

            // In this case, no grouping option is enabled and the grouping is not needed at all.
            // But it's better not to leave $this->grouping unset for any unexpected case.
        } else {
            $this->grouping = BLOCK_MYOVERVIEW_GROUPING_ALLINCLUDINGHIDDEN;
        }
        unset ($groupingconfigname);

        // Check and remember the given sorting.
        $this->sort = $sort ? $sort : BLOCK_MYOVERVIEW_SORTING_TITLE;

        // Check and remember the given view.
        $this->view = $view ? $view : BLOCK_MYOVERVIEW_VIEW_CARD;

        // Check and remember the given page size.
        if ($paging == BLOCK_MYOVERVIEW_PAGING_ALL) {
            $this->paging = BLOCK_MYOVERVIEW_PAGING_ALL;
        } else {
            $this->paging = $paging ? $paging : BLOCK_MYOVERVIEW_PAGING_12;
        }

        // Check and remember if the course categories should be shown or not.
        if (!$config->displaycategories) {
            $this->displaycategories = BLOCK_MYOVERVIEW_DISPLAY_CATEGORIES_OFF;
        } else {
            $this->displaycategories = BLOCK_MYOVERVIEW_DISPLAY_CATEGORIES_ON;
        }

        // Get and remember the available layouts.
        $this->set_available_layouts();
        $this->view = $view ? $view : reset($this->layouts);

        // Check and remember if the particular grouping options should be shown or not.
        $this->displaygroupingallincludinghidden = $config->displaygroupingallincludinghidden;
        $this->displaygroupingall = $config->displaygroupingall;
        $this->displaygroupinginprogress = $config->displaygroupinginprogress;
        $this->displaygroupingfuture = $config->displaygroupingfuture;
        $this->displaygroupingpast = $config->displaygroupingpast;
        $this->displaygroupingstarred = $config->displaygroupingstarred;
        $this->displaygroupinghidden = $config->displaygroupinghidden;

        // Check and remember if the grouping selector should be shown at all or not.
        // It will be shown if more than 1 grouping option is enabled.
        $displaygroupingselectors = array($this->displaygroupingallincludinghidden,
                $this->displaygroupingall,
                $this->displaygroupinginprogress,
                $this->displaygroupingfuture,
                $this->displaygroupingpast,
                $this->displaygroupingstarred,
                $this->displaygroupinghidden);
        $displaygroupingselectorscount = count(array_filter($displaygroupingselectors));
        if ($displaygroupingselectorscount > 1) {
            $this->displaygroupingselector = true;
        } else {
            $this->displaygroupingselector = false;
        }
        unset ($displaygroupingselectors, $displaygroupingselectorscount);
    }


    /**
     * Set the available layouts based on the config table settings,
     * if none are available, defaults to the cards view.
     *
     * @throws \dml_exception
     *
     */
    public function set_available_layouts() {

        if ($config = get_config('block_myoverview', 'layouts')) {
            $this->layouts = explode(',', $config);
        } else {
            $this->layouts = array(BLOCK_MYOVERVIEW_VIEW_CARD);
        }
    }

    /**
     * Get the user preferences as an array to figure out what has been selected.
     *
     * @return array $preferences Array with the pref as key and value set to true
     */
    public function get_preferences_as_booleans() {
        $preferences = [];
        $preferences[$this->sort] = true;
        $preferences[$this->grouping] = true;
        // Only use the user view/display preference if it is in available layouts.
        if (in_array($this->view, $this->layouts)) {
            $preferences[$this->view] = true;
        } else {
            $preferences[reset($this->layouts)] = true;
        }

        return $preferences;
    }

    /**
     * Format a layout into an object for export as a Context variable to template.
     *
     * @param string $layoutname
     *
     * @return \stdClass $layout an object representation of a layout
     * @throws \coding_exception
     */
    public function format_layout_for_export($layoutname) {
        $layout = new stdClass();

        $layout->id = $layoutname;
        $layout->name = get_string($layoutname, 'block_myoverview');
        $layout->active = $this->view == $layoutname ? true : false;
        $layout->arialabel = get_string('aria:' . $layoutname, 'block_myoverview');

        return $layout;
    }

    /**
     * Get the available layouts formatted for export.
     *
     * @return array an array of objects representing available layouts
     */
    public function get_formatted_available_layouts_for_export() {

        return array_map(array($this, 'format_layout_for_export'), $this->layouts);

    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array Context variables for the template
     * @throws \coding_exception
     *
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $nocoursesurl = $output->image_url('courses', 'block_myoverview')->out();

        $preferences = $this->get_preferences_as_booleans();
        $availablelayouts = $this->get_formatted_available_layouts_for_export();

        $defaultvariables = [
            'totalcoursecount' => count(enrol_get_all_users_courses($USER->id, true)),
            'nocoursesimg' => $nocoursesurl,
            'grouping' => $this->grouping,
            'sort' => $this->sort == BLOCK_MYOVERVIEW_SORTING_TITLE ? 'fullname' : 'ul.timeaccess desc',
            // If the user preference display option is not available, default to first available layout.
            'view' => in_array($this->view, $this->layouts) ? $this->view : reset($this->layouts),
            'paging' => $this->paging,
            'layouts' => $availablelayouts,
            'displaycategories' => $this->displaycategories,
            'displaydropdown' => (count($availablelayouts) > 1) ? true : false,
            'displaygroupingallincludinghidden' => $this->displaygroupingallincludinghidden,
            'displaygroupingall' => $this->displaygroupingall,
            'displaygroupinginprogress' => $this->displaygroupinginprogress,
            'displaygroupingfuture' => $this->displaygroupingfuture,
            'displaygroupingpast' => $this->displaygroupingpast,
            'displaygroupingstarred' => $this->displaygroupingstarred,
            'displaygroupinghidden' => $this->displaygroupinghidden,
            'displaygroupingselector' => $this->displaygroupingselector,
        ];
        return array_merge($defaultvariables, $preferences);

    }
}