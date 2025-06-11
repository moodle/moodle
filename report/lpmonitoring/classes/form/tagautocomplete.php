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
 * Tag autocomplete field for both standard and non standard tags.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $CFG;
require_once($CFG->libdir . '/form/tags.php');

use report_lpmonitoring\api;

/**
 * Tag autocomplete field for both standard and non standard tags.
 *
 * @package    report_lpmonitoring
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagautocomplete extends MoodleQuickForm_tags {

    /**
     * Constructor.
     *
     * @param string $elementname Element name
     * @param mixed $elementlabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementname = null, $elementlabel = null, $options = [], $attributes = null) {
        // The id MUST be unique or the javascript won't work correctly.
        // The element won't be shown the second time a modal is rendered.
        $attributes['id'] = str_replace('.', '', 'tags' . microtime(true));
        parent::__construct($elementname, $elementlabel, $options, $attributes);
    }

    /**
     * Internal function to load standard tags, overriden to load both standard and non standard tags.
     * Only the tags associated to at least one plan that the user can manage are returned.
     */
    protected function load_standard_tags() {
        if (!$this->is_tagging_enabled()) {
            return [];
        }

        // The array must contain the tag as keys AND as values.
        $tags = array_values( api::search_tags_for_accessible_plans() );
        return array_combine($tags, $tags);
    }
}
