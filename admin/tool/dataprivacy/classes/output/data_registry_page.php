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
 * Data registry renderable.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use tool_dataprivacy\data_registry;

require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');
require_once($CFG->libdir . '/blocklib.php');

/**
 * Class containing the data registry renderable
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_registry_page implements renderable, templatable {

    /**
     * @var int
     */
    private $defaultcontextlevel;

    /**
     * @var int
     */
    private $defaultcontextid;

    /**
     * Constructor.
     *
     * @param int $defaultcontextlevel
     * @param int $defaultcontextid
     * @return null
     */
    public function __construct($defaultcontextlevel = false, $defaultcontextid = false) {
        $this->defaultcontextlevel = $defaultcontextlevel;
        $this->defaultcontextid = $defaultcontextid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;

        $params = [\context_system::instance()->id, $this->defaultcontextlevel, $this->defaultcontextid];
        $PAGE->requires->js_call_amd('tool_dataprivacy/data_registry', 'init', $params);

        $data = new stdClass();
        $defaultsbutton = new \action_link(
            new \moodle_url('/admin/tool/dataprivacy/defaults.php'),
            get_string('setdefaults', 'tool_dataprivacy'),
            null,
            ['class' => 'btn btn-default']
        );
        $data->defaultsbutton = $defaultsbutton->export_for_template($output);

        $actionmenu = new \action_menu();
        $actionmenu->set_menu_trigger(get_string('edit'), 'btn btn-default');
        $actionmenu->set_owner_selector('dataregistry-actions');
        $actionmenu->set_alignment(\action_menu::TL, \action_menu::BL);

        $url = new \moodle_url('/admin/tool/dataprivacy/categories.php');
        $categories = new \action_menu_link_secondary($url, null, get_string('categories', 'tool_dataprivacy'));
        $actionmenu->add($categories);

        $url = new \moodle_url('/admin/tool/dataprivacy/purposes.php');
        $purposes = new \action_menu_link_secondary($url, null, get_string('purposes', 'tool_dataprivacy'));
        $actionmenu->add($purposes);

        $data->actions = $actionmenu->export_for_template($output);

        if (!data_registry::defaults_set()) {
            $data->info = (object)[
                    'message' => get_string('dataregistryinfo', 'tool_dataprivacy'),
                    'announce' => 1
            ];
            $data->nosystemdefaults = (object)[
                'message' => get_string('nosystemdefaults', 'tool_dataprivacy'),
                'announce' => 1
            ];
        }

        $data->tree = $this->get_default_tree_structure();

        return $data;
    }

    /**
     * Returns the tree default structure.
     *
     * @return array
     */
    private function get_default_tree_structure() {

        $frontpage = \context_course::instance(SITEID);

        $categorybranches = $this->get_all_category_branches();

        $elements = [
            'text' => get_string('contextlevelname' . CONTEXT_SYSTEM, 'tool_dataprivacy'),
            'contextlevel' => CONTEXT_SYSTEM,
            'branches' => [
                [
                    'text' => get_string('user'),
                    'contextlevel' => CONTEXT_USER,
                ], [
                    'text' => get_string('categories'),
                    'branches' => $categorybranches,
                    'expandelement' => 'category',
                ], [
                    'text' => get_string('frontpagecourse', 'tool_dataprivacy'),
                    'contextid' => $frontpage->id,
                    'branches' => [
                        [
                            'text' => get_string('activitiesandresources', 'tool_dataprivacy'),
                            'expandcontextid' => $frontpage->id,
                            'expandelement' => 'module',
                            'expanded' => 0,
                        ], [
                            'text' => get_string('blocks'),
                            'expandcontextid' => $frontpage->id,
                            'expandelement' => 'block',
                            'expanded' => 0,
                        ],
                    ]
                ]
            ]
        ];

        // Returned as an array to follow a common array format.
        return [self::complete($elements, $this->defaultcontextlevel, $this->defaultcontextid)];
    }

    /**
     * Returns the hierarchy of system course categories.
     *
     * @return array
     */
    private function get_all_category_branches() {

        $categories = data_registry::get_site_categories();

        $categoriesbranch = [];
        while (count($categories) > 0) {
            foreach ($categories as $key => $category) {

                $context = \context_coursecat::instance($category->id);
                $newnode = [
                    'text' => shorten_text(format_string($category->name, true, ['context' => $context])),
                    'categoryid' => $category->id,
                    'contextid' => $context->id,
                ];
                if ($category->coursecount > 0) {
                    $newnode['branches'] = [
                        [
                            'text' => get_string('courses'),
                            'expandcontextid' => $context->id,
                            'expandelement' => 'course',
                            'expanded' => 0,
                        ]
                    ];
                }

                $added = false;
                if ($category->parent == 0) {
                    // New categories root-level node.
                    $categoriesbranch[] = $newnode;
                    $added = true;

                } else {
                    // Add the new node under the appropriate parent.
                    if ($this->add_to_parent_category_branch($category, $newnode, $categoriesbranch)) {
                        $added = true;
                    }
                }

                if ($added) {
                    unset($categories[$key]);
                }
            }
        }

        return $categoriesbranch;
    }

    /**
     * Gets the courses branch for the provided category.
     *
     * @param \context $catcontext
     * @return array
     */
    public static function get_courses_branch(\context $catcontext) {

        if ($catcontext->contextlevel !== CONTEXT_COURSECAT) {
            throw new \coding_exception('A course category context should be provided');
        }

        $coursecat = \coursecat::get($catcontext->instanceid);
        $courses = $coursecat->get_courses();

        $branches = [];

        foreach ($courses as $course) {

            $coursecontext = \context_course::instance($course->id);

            if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                continue;
            }

            $coursenode = [
                'text' => shorten_text(format_string($course->shortname, true, ['context' => $coursecontext])),
                'contextid' => $coursecontext->id,
                'branches' => [
                    [
                        'text' => get_string('activitiesandresources', 'tool_dataprivacy'),
                        'expandcontextid' => $coursecontext->id,
                        'expandelement' => 'module',
                        'expanded' => 0,
                    ], [
                        'text' => get_string('blocks'),
                        'expandcontextid' => $coursecontext->id,
                        'expandelement' => 'block',
                        'expanded' => 0,
                    ],
                ]
            ];
            $branches[] = self::complete($coursenode);
        }

        return $branches;
    }

    /**
     * Gets the modules branch for the provided course.
     *
     * @param \context $coursecontext
     * @return array
     */
    public static function get_modules_branch(\context $coursecontext) {

        if ($coursecontext->contextlevel !== CONTEXT_COURSE) {
            throw new \coding_exception('A course context should be provided');
        }

        $branches = [];

        // Using the current user.
        $modinfo = get_fast_modinfo($coursecontext->instanceid);
        foreach ($modinfo->get_instances() as $moduletype => $instances) {
            foreach ($instances as $cm) {

                if (!$cm->uservisible) {
                    continue;
                }

                $a = (object)[
                    'instancename' => shorten_text($cm->get_formatted_name()),
                    'modulename' => get_string('pluginname', 'mod_' . $moduletype),
                ];

                $text = get_string('moduleinstancename', 'tool_dataprivacy', $a);
                $branches[] = self::complete([
                    'text' => $text,
                    'contextid' => $cm->context->id,
                ]);
            }
        }

        return $branches;
    }

    /**
     * Gets the blocks branch for the provided course.
     *
     * @param \context $coursecontext
     * @return null
     */
    public static function get_blocks_branch(\context $coursecontext) {
        global $DB;

        if ($coursecontext->contextlevel !== CONTEXT_COURSE) {
            throw new \coding_exception('A course context should be provided');
        }

        $branches = [];

        $children = $coursecontext->get_child_contexts();
        foreach ($children as $childcontext) {

            if ($childcontext->contextlevel !== CONTEXT_BLOCK) {
                continue;
            }

            $blockinstance = block_instance_by_id($childcontext->instanceid);
            $displayname = shorten_text(format_string($blockinstance->get_title(), true, ['context' => $childcontext]));
            $branches[] = self::complete([
                'text' => $displayname,
                'contextid' => $childcontext->id,
            ]);

        }

        return $branches;
    }

    /**
     * Adds the provided category to the categories branch.
     *
     * @param stdClass $category
     * @param array $newnode
     * @param array $categoriesbranch
     * @return bool
     */
    private function add_to_parent_category_branch($category, $newnode, &$categoriesbranch) {

        foreach ($categoriesbranch as $key => $branch) {
            if (!empty($branch['categoryid']) && $branch['categoryid'] == $category->parent) {
                // It may be empty (if it does not contain courses and this is the first child cat).
                if (!isset($categoriesbranch[$key]['branches'])) {
                    $categoriesbranch[$key]['branches'] = [];
                }
                $categoriesbranch[$key]['branches'][] = $newnode;
                return true;
            }
            if (!empty($branch['branches'])) {
                $parent = $this->add_to_parent_category_branch($category, $newnode, $categoriesbranch[$key]['branches']);
                if ($parent) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Completes tree nodes with default values.
     *
     * @param array $node
     * @param int|false $currentcontextlevel
     * @param int|false $currentcontextid
     * @return array
     */
    private static function complete($node, $currentcontextlevel = false, $currentcontextid = false) {
        if (!isset($node['active'])) {
            if ($currentcontextlevel && !empty($node['contextlevel']) &&
                    $currentcontextlevel == $node['contextlevel'] &&
                    empty($currentcontextid)) {
                // This is the active context level, we also checked that there
                // is no default contextid set.
                $node['active'] = true;
            } else if ($currentcontextid && !empty($node['contextid']) &&
                    $currentcontextid == $node['contextid']) {
                $node['active'] = true;
            } else {
                $node['active'] = null;
            }
        }

        if (!isset($node['branches'])) {
            $node['branches'] = [];
        } else {
            foreach ($node['branches'] as $key => $childnode) {
                $node['branches'][$key] = self::complete($childnode, $currentcontextlevel, $currentcontextid);
            }
        }

        if (!isset($node['expandelement'])) {
            $node['expandelement'] = null;
        }

        if (!isset($node['expandcontextid'])) {
            $node['expandcontextid'] = null;
        }

        if (!isset($node['contextid'])) {
            $node['contextid'] = null;
        }

        if (!isset($node['contextlevel'])) {
            $node['contextlevel'] = null;
        }

        if (!isset($node['expanded'])) {
            if (!empty($node['branches'])) {
                $node['expanded'] = 1;
            } else {
                $node['expanded'] = 0;
            }
        }
        return $node;
    }

    /**
     * From a list of purpose persistents to a list of id => name purposes.
     *
     * @param \tool_dataprivacy\purpose $purposes
     * @param bool $includenotset
     * @param bool $includeinherit
     * @return string[]
     */
    public static function purpose_options($purposes, $includenotset = true, $includeinherit = true) {
        $options = self::base_options($includenotset, $includeinherit);
        foreach ($purposes as $purpose) {
            $options[$purpose->get('id')] = $purpose->get('name');
        }

        return $options;
    }

    /**
     * From a list of category persistents to a list of id => name categories.
     *
     * @param \tool_dataprivacy\category $categories
     * @param bool $includenotset
     * @param bool $includeinherit
     * @return string[]
     */
    public static function category_options($categories, $includenotset = true, $includeinherit = true) {
        $options = self::base_options($includenotset, $includeinherit);
        foreach ($categories as $category) {
            $options[$category->get('id')] = $category->get('name');
        }

        return $options;
    }

    /**
     * Base not set and inherit options.
     *
     * @param bool $includenotset
     * @param bool $includeinherit
     * @return array
     */
    private static function base_options($includenotset = true, $includeinherit = true) {

        $options = [];

        if ($includenotset) {
            $options[\tool_dataprivacy\context_instance::NOTSET] = get_string('notset', 'tool_dataprivacy');
        }

        if ($includeinherit) {
            $options[\tool_dataprivacy\context_instance::INHERIT] = get_string('inherit', 'tool_dataprivacy');
        }

        return $options;
    }
}
