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
 * Admin route controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;
defined('MOODLE_INTERNAL') || die();

use block_xp\di;
use coding_exception;

require_once($CFG->libdir . '/adminlib.php');

/**
 * Admin route controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class admin_route_controller extends route_controller {

    /** @var string The section name. */
    protected $sectionname;

    /**
     * Authentication.
     *
     * @return void
     */
    protected function require_login() {
        global $CFG, $PAGE, $USER, $SITE, $OUTPUT;
        if (!$this->sectionname) {
            throw new coding_exception('The property $sectionname was not defined.');
        }
        // We must pass the compatible URL, or the navigation does not match the right page.
        admin_externalpage_setup($this->sectionname, '', null, $this->pageurl->get_compatible_url());
    }

    /**
     * Post authentication.
     *
     * Use this to initialise objects which you'll need throughout the request.
     *
     * @return void
     */
    protected function post_login() {
        $this->urlresolver = \block_xp\di::get('url_resolver');
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
    }

    /**
     * Output editing defaults warning, if needed.
     *
     * @param string $routename The corresponding route name outside admin.
     */
    protected function page_warning_editing_defaults($routename = '') {
        if (di::get('config')->get('context') != CONTEXT_SYSTEM) {
            return;
        }
        $url = $this->urlresolver->reverse($routename, ['courseid' => SITEID]);
        echo di::get('renderer')->notification_without_close(strip_tags(
            markdown_to_html(get_string('editingdefaultsettingsinwholesitemodenotice', 'block_xp', [
                'url' => $url->out(false),
            ])),
            '<a><em><strong>'
        ), \core\output\notification::NOTIFY_WARNING);
    }
}
