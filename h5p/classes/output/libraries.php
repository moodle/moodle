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
 * Contains class core_h5p\output\libraries
 *
 * @package   core_h5p
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use action_menu;
use action_menu_link;
use pix_icon;

/**
 * Class to help display H5P library management table.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class libraries implements renderable, templatable {

    /** @var H5P factory */
    protected $factory;

    /** @var H5P library list */
    protected $libraries;

    /**
     * Constructor.
     *
     * @param factory $factory The H5P factory
     * @param array $libraries array of h5p libraries records
     */
    public function __construct(\core_h5p\factory $factory, array $libraries) {
        $this->factory = $factory;
        $this->libraries = $libraries;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $installed = [];
        $filestorage = $this->factory->get_core()->fs;
        foreach ($this->libraries as $libraryname => $versions) {
            foreach ($versions as $version) {
                // Get the icon URL.
                $version->icon = $filestorage->get_icon_url(
                    $version->id,
                    $version->machine_name,
                    $version->major_version,
                    $version->minor_version
                );
                // Get the action menu options.
                $actionmenu = new action_menu();
                $actionmenu->set_menu_trigger(get_string('actions', 'core_h5p'));
                $actionmenu->set_alignment(action_menu::TL, action_menu::BL);
                $actionmenu->prioritise = true;
                $actionmenu->add_primary_action(new action_menu_link(
                    new moodle_url('/h5p/libraries.php', ['deletelibrary' => $version->id]),
                    new pix_icon('t/delete', get_string('deletelibraryversion', 'core_h5p')),
                    get_string('deletelibraryversion', 'core_h5p')
                ));
                $version->actionmenu = $actionmenu->export_for_template($output);
                $installed[] = $version;
            }
        }
        $r = new stdClass();
        $r->contenttypes = $installed;
        return $r;
    }
}
