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
 * Abstract renderer for independent renderable elements.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use tool_dataprivacy\external\purpose_exporter;
use tool_dataprivacy\external\category_exporter;

/**
 * Abstract renderer for independent renderable elements.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class crud_element {

    /**
     * Returns the top navigation buttons.
     *
     * @return \action_link[]
     */
    protected final function get_navigation() {
        $back = new \action_link(
            new \moodle_url('/admin/tool/dataprivacy/dataregistry.php'),
            get_string('back'),
            null,
            ['class' => 'btn btn-primary']
        );
        return [$back];
    }

    /**
     * Adds an action menu for the provided element
     *
     * @param string $elementname 'purpose' or 'category'.
     * @param \stdClass $exported
     * @param \core\persistent $persistent
     * @return \action_menu
     */
    protected final function action_menu($elementname, $exported, $persistent) {

        // Just in case, we are doing funny stuff below.
        $elementname = clean_param($elementname, PARAM_ALPHA);

        // Actions.
        $actionmenu = new \action_menu();
        $actionmenu->set_menu_trigger(get_string('actions'));
        $actionmenu->set_owner_selector($elementname . '-' . $exported->id . '-actions');

        $url = new \moodle_url('/admin/tool/dataprivacy/edit' . $elementname . '.php',
            ['id' => $exported->id]);
        $link = new \action_menu_link_secondary($url, new \pix_icon('t/edit',
            get_string('edit')), get_string('edit'));
        $actionmenu->add($link);

        if (!$persistent->is_used()) {
            $url = new \moodle_url('#');
            $attrs = ['data-id' => $exported->id, 'data-action' => 'delete' . $elementname, 'data-name' => $exported->name];
            $link = new \action_menu_link_secondary($url, new \pix_icon('t/delete',
                get_string('delete')), get_string('delete'), $attrs);
            $actionmenu->add($link);
        }

        return $actionmenu;
    }
}
