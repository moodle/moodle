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
 * Controller.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use block_xp\di;
use html_writer;

/**
 * Controller class.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drops_controller extends page_controller {

    /** @var string The nav name. */
    protected $navname = 'rules';
    /** @var string The route name. */
    protected $routename = 'drops';

    protected function pre_content() {
        if (!di::get('config')->get('enablepromoincourses')) {
            return redirect($this->urlresolver->reverse('rules', ['courseid' => $this->courseid]));
        }
    }

    protected function get_page_html_head_title() {
        return get_string('drops', 'block_xp');
    }

    protected function get_page_heading() {
        return get_string('drops', 'block_xp');
    }

    protected function page_content() {
        $renderer = $this->get_renderer();
        $promourl = $this->urlresolver->reverse('promo', ['courseid' => $this->courseid])->out(false);

        echo $renderer->advanced_heading(get_string('drops', 'block_xp'), [
            'intro' => new \lang_string('dropsintro', 'block_xp'),
            'help' => new \help_icon('drops', 'block_xp'),
        ]);

        echo html_writer::start_div('xp-mt-4');
        echo $renderer->notification_without_close(get_string('unlockfeaturewithxpplus', 'block_xp', $promourl), 'info');
        echo html_writer::end_div();
    }

}
