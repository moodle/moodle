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
 * Quick search form renderable.
 *
 * @package    mod_forum
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\output;
defined('MOODLE_INTERNAL') || die();

use help_icon;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Quick search form renderable class.
 *
 * @package    mod_forum
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quick_search_form implements renderable, templatable {

    /** @var int The course ID. */
    protected $courseid;
    /** @var string Current query. */
    protected $query;
    /** @var moodle_url The form action URL. */
    protected $actionurl;
    /** @var help_icon The help icon. */
    protected $helpicon;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     * @param string $query The current query.
     */
    public function __construct($courseid, $query = '') {
        $this->courseid = $courseid;
        $this->query = $query;
        $this->actionurl = new moodle_url('/mod/forum/search.php');
        $this->helpicon = new help_icon('search', 'core');
    }

    public function export_for_template(renderer_base $output) {
        $hiddenfields = [
            (object) ['name' => 'id', 'value' => $this->courseid],
        ];
        $data = [
            'action' => $this->actionurl->out(false),
            'hiddenfields' => $hiddenfields,
            'query' => $this->query,
            'helpicon' => $this->helpicon->export_for_template($output),
            'inputname' => 'search',
            'searchstring' => get_string('searchforums', 'mod_forum')
        ];
        return $data;
    }

}
