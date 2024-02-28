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
 * Contains the navigation renderable for user data exports.
 *
 * @package    core_privacy
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Class containing the navigation renderable
 *
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exported_navigation_page implements renderable, templatable {

    /** @var array $tree Full tree in multidimensional form. */
    protected $tree;

    /** @var boolean $firstelement This is used to create unique classes for the first elements in the navigation tree. */
    protected $firstelement = true;

    /**
     * Constructor
     *
     * @param \stdClass $tree Full tree to create navigation out of.
     */
    public function __construct(\stdClass $tree) {
        $this->tree = $tree;
    }

    /**
     * Creates the navigation list html. Why this and not a template? My attempts at trying to get a recursive template
     * working failed.
     *
     * @param  \stdClass $tree Full tree to create navigation out of.
     * @return string navigation html.
     */
    protected function create_navigation(\stdClass $tree) {
        if ($this->firstelement) {
            $html = \html_writer::start_tag('ul', ['class' => 'treeview parent block_tree list', 'id' => 'my-tree']);
            $this->firstelement = false;
        } else {
            $html = \html_writer::start_tag('ul', ['class' => 'parent', 'role' => 'group']);
        }
        foreach ($tree->children as $child) {
            if (isset($child->children)) {
                $html .= \html_writer::start_tag('li', ['class' => 'menu-item', 'role' => 'treeitem', 'aria-expanded' => 'false']);
                $html .= $child->name;
                $html .= $this->create_navigation($child);
            } else {
                $html .= \html_writer::start_tag('li', ['class' => 'item', 'role' => 'treeitem', 'aria-expanded' => 'false']);
                // Normal display.
                if (isset($child->datavar)) {
                    $html .= \html_writer::link('#', $child->name, ['data-var' => $child->datavar]);
                } else {
                    $html .= \html_writer::link($child->url, $child->name, ['target' => '_blank']);
                }
            }
            $html .= \html_writer::end_tag('li');
        }
        $html .= \html_writer::end_tag('ul');
        return $html;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array navigation data for the template.
     */
    public function export_for_template(renderer_base $output): Array {
        $data = $this->create_navigation($this->tree);
        return ['navigation' => $data];
    }
}