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
 * Contains class core_tag_collections_table
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Table with the list of tag collections for "Manage tags" page.
 *
 * @package   core_tag
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_collections_table extends html_table {

    /**
     * Constructor
     * @param string|moodle_url $pageurl
     */
    public function __construct($pageurl) {
        global $OUTPUT;
        parent::__construct();

        $this->attributes['class'] = 'generaltable tag-collections-table';

        $this->head = array(
            get_string('name'),
            get_string('component', 'tag'),
            get_string('tagareas', 'tag'),
            get_string('searchable', 'tag') . $OUTPUT->help_icon('searchable', 'tag'),
            ''
        );

        $this->data = array();

        $tagcolls = core_tag_collection::get_collections();
        $idx = 0;
        foreach ($tagcolls as $tagcoll) {
            $actions = '';
            $name = core_tag_collection::display_name($tagcoll);
            $url = new moodle_url($pageurl, array('sesskey' => sesskey(), 'tc' => $tagcoll->id));
            if (!$tagcoll->isdefault) {
                // Move up.
                if ($idx > 1) {
                    $url->param('action', 'collmoveup');
                    $actions .= $OUTPUT->action_icon($url, new pix_icon('t/up', get_string('moveup')), null,
                        array('class' => 'action-icon action_moveup'));
                }
                // Move down.
                if ($idx < count($tagcolls) - 1) {
                    $url->param('action', 'collmovedown');
                    $actions .= $OUTPUT->action_icon($url, new pix_icon('t/down', get_string('movedown')), null,
                        array('class' => 'action-icon action_movedown'));
                }
            }
            if (!$tagcoll->isdefault && empty($tagcoll->component)) {
                // Delete.
                $url->param('action', 'colldelete');
                $actions .= $OUTPUT->action_icon('#', new pix_icon('t/delete', get_string('delete')), null,
                        array('data-url' => $url->out(false), 'data-collname' => $name,
                            'class' => 'action-icon action_delete'));
            }
            $component = '';
            if ($tagcoll->component) {
                $component = ($tagcoll->component === 'core' || preg_match('/^core_/', $tagcoll->component)) ?
                    get_string('coresystem') : get_string('pluginname', $tagcoll->component);
            }
            $allareas = core_tag_collection::get_areas_names(null, false);
            $validareas = core_tag_collection::get_areas_names($tagcoll->id);
            $areaslist = array_map(function($key) use ($allareas, $validareas) {
                return "<li data-areaid=\"{$key}\" " .
                        (array_key_exists($key, $validareas) ? "" : "style=\"display:none;\"") .
                        ">{$allareas[$key]}</li>";
            }, array_keys($allareas));
            $displayname = new \core_tag\output\tagcollname($tagcoll);
            $searchable = new \core_tag\output\tagcollsearchable($tagcoll);
            $this->data[] = array(
                $displayname->render($OUTPUT),
                $component,
                "<ul data-collectionid=\"{$tagcoll->id}\">" . join('', $areaslist) . '</ul>',
                $searchable->render($OUTPUT),
                $actions);
            $idx++;
        }

    }
}
