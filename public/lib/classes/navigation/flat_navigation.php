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

namespace core\navigation;

use core\context\course as context_course;
use core\context_helper;
use core\url;
use moodle_page;

/**
 * Class used to generate a collection of navigation nodes most closely related
 * to the current page.
 *
 * @deprecated since Moodle 4.0 - do not use any more. Leverage secondary/tertiary navigation concepts
 * @package core
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\deprecated(
    since: '4.0',
    reason: 'Do not use this class any more. Leverage secondary/tertiary navigation concepts.'
)]
class flat_navigation extends navigation_node_collection {
    /** @var moodle_page the moodle page that the navigation belongs to */
    protected $page;

    /**
     * Constructor.
     *
     * @param moodle_page $page
     */
    public function __construct(moodle_page &$page) {
        if (during_initial_install()) {
            return;
        }
        debugging("Flat navigation has been deprecated in favour of primary/secondary navigation concepts");
        $this->page = $page;
    }

    /**
     * Build the list of navigation nodes based on the current navigation and settings trees.
     *
     */
    public function initialise() {
        global $PAGE, $USER, $OUTPUT, $CFG;
        if (during_initial_install()) {
            return;
        }

        $current = false;

        $course = $PAGE->course;

        $this->page->navigation->initialise();

        // First walk the nav tree looking for "flat_navigation" nodes.
        if ($course->id > 1) {
            // It's a real course.
            $url = new url('/course/view.php', ['id' => $course->id]);

            $coursecontext = context_course::instance($course->id, MUST_EXIST);
            $displaycontext = context_helper::get_navigation_filter_context($coursecontext);
            // This is the name that will be shown for the course.
            $coursename = empty($CFG->navshowfullcoursenames) ?
                format_string($course->shortname, true, ['context' => $displaycontext]) :
                format_string($course->fullname, true, ['context' => $displaycontext]);

            $flat = new flat_navigation_node(navigation_node::create($coursename, $url), 0);
            $flat->set_collectionlabel($coursename);
            $flat->key = 'coursehome';
            $flat->icon = new pix_icon('i/course', '');

            $courseformat = course_get_format($course);
            $coursenode = $PAGE->navigation->find_active_node();
            $targettype = navigation_node::TYPE_COURSE;

            // Single activity format has no course node - the course node is swapped for the activity node.
            if (!$courseformat->has_view_page()) {
                $targettype = navigation_node::TYPE_ACTIVITY;
            }

            while (!empty($coursenode) && ($coursenode->type != $targettype)) {
                $coursenode = $coursenode->parent;
            }
            // There is one very strange page in mod/feedback/view.php which thinks it is both site and course
            // context at the same time. That page is broken but we need to handle it (hence the SITEID).
            if ($coursenode && $coursenode->key != SITEID) {
                $this->add($flat);
                foreach ($coursenode->children as $child) {
                    if ($child->action) {
                        $flat = new flat_navigation_node($child, 0);
                        $this->add($flat);
                    }
                }
            }

            $this->page->navigation->build_flat_navigation_list($this, true, get_string('site'));
        } else {
            $this->page->navigation->build_flat_navigation_list($this, false, get_string('site'));
        }

        $admin = $PAGE->settingsnav->find('siteadministration', navigation_node::TYPE_SITE_ADMIN);
        if (!$admin) {
            // Try again - crazy nav tree!
            $admin = $PAGE->settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN);
        }
        if ($admin) {
            $flat = new flat_navigation_node($admin, 0);
            $flat->set_showdivider(true, get_string('sitesettings'));
            $flat->key = 'sitesettings';
            $flat->icon = new pix_icon('t/preferences', '');
            $this->add($flat);
        }

        // Add-a-block in editing mode.
        if (
            isset($this->page->theme->addblockposition) &&
                $this->page->theme->addblockposition == BLOCK_ADDBLOCK_POSITION_FLATNAV &&
                $PAGE->user_is_editing() && $PAGE->user_can_edit_blocks()
        ) {
            $url = new url($PAGE->url, ['bui_addblock' => '', 'sesskey' => sesskey()]);
            $addablock = navigation_node::create(get_string('addblock'), $url);
            $flat = new flat_navigation_node($addablock, 0);
            $flat->set_showdivider(true, get_string('blocksaddedit'));
            $flat->key = 'addblock';
            $flat->icon = new pix_icon('i/addblock', '');
            $this->add($flat);

            $addblockurl = "?{$url->get_query_string(false)}";

            $PAGE->requires->js_call_amd(
                'core_block/add_modal',
                'init',
                [$addblockurl, $this->page->get_edited_page_hash()]
            );
        }
    }

    /**
     * Override the parent so we can set a label for this collection if it has not been set yet.
     *
     * @param navigation_node $node Node to add
     * @param string $beforekey If specified, adds before a node with this key,
     *   otherwise adds at end
     * @return navigation_node Added node
     */
    public function add(navigation_node $node, $beforekey = null) {
        $result = parent::add($node, $beforekey);
        // Extend the parent to get a name for the collection of nodes if required.
        if (empty($this->collectionlabel)) {
            if ($node instanceof flat_navigation_node) {
                $this->set_collectionlabel($node->get_collectionlabel());
            }
        }

        return $result;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(flat_navigation::class, \flat_navigation::class);
