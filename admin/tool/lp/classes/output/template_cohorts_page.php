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
 * Template cohorts page renderable.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\output;
defined('MOODLE_INTERNAL') || die();

/**
 * Template cohorts renderable.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_cohorts_page implements \renderable {

    /** @var \core_competency\template|null */
    protected $template = null;

    /** @var \moodle_url|null */
    protected $url = null;

    /** @var template_cohorts_table|null */
    public $table = null;

    /**
     * Constructor.
     * @param \core_competency\template $template
     * @param \moodle_url $url
     */
    public function __construct(\core_competency\template $template, \moodle_url $url) {
        $this->template = $template;
        $this->url = $url;
        $this->table = new template_cohorts_table('tplcohorts', $template);
        $this->table->define_baseurl($url);
    }

}
