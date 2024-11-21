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
 * Notice api controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use coding_exception;
use context_system;

/**
 * Notice api controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ajax_notice_dismiss_controller extends route_controller {

    /**
     * Moodle page specifics.
     *
     * @return void
     */
    protected function page_setup() {
        global $PAGE;
        $PAGE->set_context(context_system::instance());
        $PAGE->set_url($this->pageurl);
    }

    /**
     * Authentication.
     *
     * @return void
     */
    protected function require_login() {
        defined('AJAX_SCRIPT') || die();
        require_login();
    }

    /**
     * Permission checks.
     *
     * None to do, this is handled by admin_externalpage_setup().
     *
     * @throws moodle_exception When the conditions are not met.
     * @return void
     */
    protected function permissions_checks() {
        if ($this->request->get_method() !== 'POST') {
            throw new coding_exception('Invalid method.');
        }
    }
    /**
     * Echo the content.
     *
     * @return void
     */
    protected function content() {
        global $USER;
        $indicator = \block_xp\di::get('user_notice_indicator');
        if ($indicator instanceof \block_xp\local\indicator\user_indicator_with_acceptance) {
            $indicator->set_requires_acceptable_user_flag(true);
        }
        $indicator->set_user_flag($USER->id, $this->get_param('name'), 1);
    }

}
