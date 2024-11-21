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
 * Ladder controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use block_xp\di;
use block_xp\local\division\division;
use block_xp\local\division\group_division;
use moodle_exception;

/**
 * Ladder controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ladder_controller extends page_controller {

    /** Page size flag. */
    const PAGE_SIZE_FLAG = 'ladder-pagesize';

    /** @var bool */
    protected $requiremanage = false;
    /** @var bool */
    protected $supportsgroups = true;
    /** @var string */
    protected $routename = 'ladder';

    protected function page_setup() {
        global $PAGE;
        parent::page_setup();
        $PAGE->add_body_class('block_xp-ladder');
    }

    /**
     * The optional params expected.
     *
     * @return array
     */
    protected function define_optional_params() {
        return [
            ['pagesize', 0, PARAM_INT, false],
        ];
    }

    /**
     * Is visible to viewers?
     *
     * @return bool
     */
    protected function is_visible_to_viewers() {
        return (bool) $this->world->get_config()->get('enableladder');
    }

    /**
     * Get the division.
     *
     * @return division|null
     */
    protected function get_division(): ?division {
        if ($this->get_groupid()) {
            return new group_division($this->get_groupid());
        }
        return null;
    }

    /**
     * Get the leadeboard.
     *
     * @return \block_xp\local\leaderboard\leaderboard
     */
    protected function get_leaderboard() {
        $division = $this->get_division();
        $lbf = \block_xp\di::get('leaderboard_factory_maker')->get_leaderboard_factory($this->world);
        if ($division) {
            return $lbf->get_leaderboard_for_division($division);
        }
        return $lbf->get_leaderboard();
    }

    /**
     * Get the table.
     *
     * @return \flexible_table
     */
    protected function get_table() {
        global $USER;
        $table = new \block_xp\output\leaderboard_table(
            $this->get_leaderboard(),
            $this->get_renderer(),
            [
                'context' => $this->world->get_context(),
                'identitymode' => $this->world->get_config()->get('identitymode'),
                'rankmode' => $this->world->get_config()->get('rankmode'),
            ],
            $USER->id
        );
        $table->show_pagesize_selector(true);
        $table->define_baseurl($this->pageurl);
        return $table;
    }

    protected function get_page_html_head_title() {
        return get_string('ladder', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('ladder', 'block_xp');
    }

    /**
     * Get the page size.
     *
     * @return int
     */
    protected function get_page_size() {
        global $USER;

        $indicator = \block_xp\di::get('user_generic_indicator');
        $pagesizepref = $indicator->get_user_flag($USER->id, self::PAGE_SIZE_FLAG);
        $defaultpagesize = 20;

        // Get page size from URL argument.
        $pagesize = $this->get_param('pagesize');

        // Fallback on preference.
        if (empty($pagesize)) {
            $pagesize = $pagesizepref;
        }

        // Check that it is the right value.
        if (!in_array($pagesize, [20, 50, 100])) {
            $pagesize = $defaultpagesize;
        }

        if ($pagesize == $defaultpagesize) {
            // When the default, and we've got a saved flag, unset it.
            if (!empty($pagesizepref)) {
                $indicator->unset_user_flag($USER->id, self::PAGE_SIZE_FLAG);
            }

        } else if ($pagesize != $pagesizepref) {
            // It's not the default, and it's not our flag, save the flag.
            $indicator->set_user_flag($USER->id, self::PAGE_SIZE_FLAG, $pagesize);
        }

        return (int) $pagesize;
    }

    protected function page_content() {
        global $PAGE;
        $output = $this->get_renderer();

        $canmanage = $this->world->get_access_permissions()->can_manage();
        if ($canmanage) {
            echo $output->advanced_heading(get_string('ladder', 'block_xp'), [
                'intro' => new \lang_string('ladderintro', 'block_xp'),
                'help' => new \help_icon('ladder', 'block_xp'),
                'visible' => $this->is_visible_to_viewers(),
                'menu' => $this->get_page_menu_items(),
            ]);
            $PAGE->requires->js_call_amd('block_xp/modal-form', 'registerOpen', ['[data-action="open-form"]']);
        }

        $this->print_group_menu();
        echo $this->get_table()->out($this->get_page_size(), false);
    }

    /**
     * Get the menu items.
     *
     * @return array
     */
    protected function get_page_menu_items() {
        $config = di::get('config');
        $hasaddon = di::get('addon')->is_activated();
        return array_filter([
            [
                'label' => get_string('pagesettings', 'block_xp'),
                'data-action' => 'open-form',
                'data-form-class' => di::get('leaderboard_form_class'),
                'data-form-args__contextid' => $this->world->get_context()->id,
                'href' => '#',
            ],
            $config->get('enablepromoincourses') && !$hasaddon ? [
                'label' => get_string('export', 'block_xp'),
                'href' => '#',
                'disabled' => true,
                'addonrequired' => true,
            ] : null,
        ]);
    }

}
