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
 * Question renderer.
 *
 * @package    theme_boost
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost\output\core_question;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/renderer.php');

/**
 * Question renderer class.
 *
 * @package    theme_boost
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bank_renderer extends \core_question_bank_renderer {

    /**
     * Display additional navigation if needed.
     *
     * @return string
     */
    public function extra_horizontal_navigation() {
        // Horizontal navigation for question bank.
        if ($questionnode = $this->page->settingsnav->find("questionbank", \navigation_node::TYPE_CONTAINER)) {
            if ($children = $questionnode->children) {
                $tabs = [];
                foreach ($children as $key => $node) {
                    $tabs[] = new \tabobject($node->key, $node->action, $node->text);
                }
                $active = $questionnode->find_active_node()->key;
                return \html_writer::div(print_tabs([$tabs], $active, null, null, true), 'questionbank-navigation');
            }
        }
        return '';
    }
}
