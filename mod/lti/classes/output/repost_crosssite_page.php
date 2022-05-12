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
 * Render a page containing a simple form which reposts to self via JS.
 *
 * The purpose of this form is to resend a cross-site request to self, which allows the browsers to include the Moodle
 * session cookie alongside the original POST data, allowing LTI flows to function despite browsers blocking
 * cross-site cookies.
 *
 * @copyright  2021 Cengage
 * @package    mod_lti
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\output;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/lti/locallib.php');

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Render a page containing a simple form which reposts to self via JS.
 *
 * The purpose of this form is to resend a cross-site request to self, which allows the browsers to include the Moodle
 * session cookie alongside the original POST data, allowing LTI flows to function despite browsers blocking
 * cross-site cookies.
 *
 * @copyright  2021 Cengage
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repost_crosssite_page implements renderable, templatable {

    /**
     * Constructor
     *
     * @param string $url moodle URL to repost to
     * @param array $post the POST params to be re-posted
     */
    public function __construct(string $url, array $post) {
        $this->params = array_map(function($k) use ($post) {
            return ["key" => $k, "value" => $post[$k]];
        }, array_keys($post));
        $this->url = $url;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer
     * @return stdClass Data to be used by the template
     */
    public function export_for_template(renderer_base $output) {
        $renderdata = new stdClass();
        $renderdata->url = $this->url;
        $renderdata->params = $this->params;
        return $renderdata;
    }
}
