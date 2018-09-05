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
 * Contains class core_tag_areas_table
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Table with the list of available tag areas for "Manage tags" page.
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_areas_table extends html_table {

    /**
     * Constructor
     *
     * @param string|moodle_url $pageurl
     */
    public function __construct($pageurl) {
        global $OUTPUT;
        parent::__construct();

        $this->attributes['class'] = 'generaltable tag-areas-table';

        $this->head = array(
            get_string('tagareaname', 'core_tag'),
            get_string('component', 'tag'),
            get_string('tagareaenabled', 'core_tag'),
            get_string('tagcollection', 'tag'),
            get_string('showstandard', 'tag') .
                $OUTPUT->help_icon('showstandard', 'tag')
        );

        $this->data = array();
        $this->rowclasses = array();

        $tagareas = core_tag_area::get_areas();
        $tagcollections = core_tag_collection::get_collections_menu(true);
        $tagcollectionsall = core_tag_collection::get_collections_menu();

        $standardchoices = array(
            core_tag_tag::BOTH_STANDARD_AND_NOT => get_string('standardsuggest', 'tag'),
            core_tag_tag::STANDARD_ONLY => get_string('standardforce', 'tag'),
            core_tag_tag::HIDE_STANDARD => get_string('standardhide', 'tag')
        );

        foreach ($tagareas as $itemtype => $it) {
            foreach ($it as $component => $record) {
                $areaname = core_tag_area::display_name($record->component, $record->itemtype);

                $tmpl = new \core_tag\output\tagareaenabled($record);
                $enabled = $OUTPUT->render_from_template('core/inplace_editable', $tmpl->export_for_template($OUTPUT));

                $tmpl = new \core_tag\output\tagareacollection($record);
                $collectionselect = $OUTPUT->render_from_template('core/inplace_editable', $tmpl->export_for_template($OUTPUT));

                $tmpl = new \core_tag\output\tagareashowstandard($record);
                $showstandardselect = $OUTPUT->render_from_template('core/inplace_editable', $tmpl->export_for_template($OUTPUT));

                $this->data[] = array(
                    $areaname,
                    ($record->component === 'core' || preg_match('/^core_/', $record->component)) ?
                        get_string('coresystem') : get_string('pluginname', $record->component),
                    $enabled,
                    $collectionselect,
                    $showstandardselect
                );
                $this->rowclasses[] = $record->enabled ? '' : 'dimmed_text';
            }
        }

    }

}
