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
 * Search renderer.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Search renderer.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * @var int Max number chars to display of a string value
     */
    const SEARCH_RESULT_STRING_SIZE = 100;

    /**
     * @var int Max number chars to display of a text value
     */

    const SEARCH_RESULT_TEXT_SIZE = 500;

    /**
     * Renders search results.
     *
     * @param \core_search\document[] $results
     * @param int $page Zero based page number.
     * @param int $totalcount Total number of results available.
     * @param \moodle_url $url
     * @return string HTML
     */
    public function render_results($results, $page, $totalcount, $url) {

        // Paging bar.
        $perpage = \core_search\manager::DISPLAY_RESULTS_PER_PAGE;
        $content = $this->output->paging_bar($totalcount, $page, $perpage, $url);

        // Results.
        $resultshtml = array();
        foreach ($results as $hit) {
            $resultshtml[] = $this->render_result($hit);
        }
        $content .= \html_writer::tag('div', implode('<hr/>', $resultshtml), array('class' => 'search-results'));

        // Paging bar.
        $content .= $this->output->paging_bar($totalcount, $page, $perpage, $url);

        return $content;
    }

    /**
     * Displaying search results.
     *
     * @param \core_search\document Containing a single search response to be displayed.a
     * @return string HTML
     */
    public function render_result(\core_search\document $doc) {
        $docdata = $doc->export_for_template($this);

        // Limit text fields size.
        $docdata['title'] = shorten_text($docdata['title'], static::SEARCH_RESULT_STRING_SIZE, true);
        $docdata['content'] = $docdata['content'] ? shorten_text($docdata['content'], static::SEARCH_RESULT_TEXT_SIZE, true) : '';
        $docdata['description1'] = $docdata['description1'] ? shorten_text($docdata['description1'], static::SEARCH_RESULT_TEXT_SIZE, true) : '';
        $docdata['description2'] = $docdata['description2'] ? shorten_text($docdata['description2'], static::SEARCH_RESULT_TEXT_SIZE, true) : '';

        return $this->output->render_from_template('core_search/result', $docdata);
    }

    /**
     * Returns a box with a search disabled lang string.
     *
     * @return string HTML
     */
    public function render_search_disabled() {
        $content = $this->output->box_start();
        $content .= $this->output->notification(get_string('globalsearchdisabled', 'search'), 'notifymessage');
        $content .= $this->output->box_end();
        return $content;
    }

    /**
     * Returns information about queued index requests.
     *
     * @param \stdClass $info Info object from get_index_requests_info
     * @return string HTML
     * @throws \moodle_exception Any error with template
     */
    public function render_index_requests_info(\stdClass $info) {
        return $this->output->render_from_template('core_search/index_requests', $info);
    }
}
