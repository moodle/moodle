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
 * Contains class content_item_repository, for fetching content_items.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\repository;

defined('MOODLE_INTERNAL') || die();

use core_course\local\entity\content_item;
use core_course\local\entity\lang_string_title;
use core_course\local\entity\string_title;

/**
 * The class content_item_repository, for reading content_items.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_readonly_repository implements content_item_readonly_repository_interface {
    /**
     * Get the help string for content items representing core modules.
     *
     * @param string $modname the module name.
     * @return string the help string, including help link.
     */
    private function get_core_module_help_string(string $modname): string {
        global $OUTPUT;

        $help = '';
        $sm = get_string_manager();
        if ($sm->string_exists('modulename_help', $modname)) {
            $help = get_string('modulename_help', $modname);
            if ($sm->string_exists('modulename_link', $modname)) { // Link to further info in Moodle docs.
                $link = get_string('modulename_link', $modname);
                $linktext = get_string('morehelp');
                $help .= \html_writer::tag('div', $OUTPUT->doc_link($link, $linktext, true), ['class' => 'helpdoclink']);
            }
        }
        return $help;
    }

    /**
     * Create a content_item object based on legacy data returned from the get_shortcuts hook implementations.
     *
     * @param \stdClass $item the stdClass of legacy data.
     * @return content_item a content item object.
     */
    private function content_item_from_legacy_data(\stdClass $item): content_item {
        // Make sure the legacy data results in a content_item with id = 0.
        // Even with an id, we can't uniquely identify the item, because we can't guarantee what component it came from.
        // An id of -1, signifies this.
        $item->id = -1;

        if (is_string($item->title)) {
            $item->title = new string_title($item->title);
        } else if ($item->title instanceof \lang_string) {
            $item->title = new lang_string_title($item->title->get_identifier(), $item->title->get_component());
        }

        // Legacy items had names which are in one of 2 forms:
        // modname, i.e. 'assign' or
        // modname:link, i.e. lti:http://etc...
        // We need to grab the module name out to create the componentname.
        $modname = (strpos($item->name, ':') !== false) ? explode(':', $item->name)[0] : $item->name;

        return new content_item($item->id, $item->name, $item->title, $item->link, $item->icon, $item->help ?? '',
            $item->archetype, 'mod_' . $modname);
    }

    /**
     * Create a stdClass type object based on a content_item instance.
     *
     * @param content_item $contentitem
     * @return \stdClass the legacy data.
     */
    private function content_item_to_legacy_data(content_item $contentitem): \stdClass {
        $item = new \stdClass();
        $item->id = $contentitem->get_id();
        $item->name = $contentitem->get_name();
        $item->title = $contentitem->get_title();
        $item->link = $contentitem->get_link();
        $item->icon = $contentitem->get_icon();
        $item->help = $contentitem->get_help();
        $item->archetype = $contentitem->get_archetype();
        $item->componentname = $contentitem->get_component_name();
        return $item;
    }

    /**
     * Get the list of potential content items for the given course.
     *
     * @param \stdClass $course the course
     * @param \stdClass $user the user, to pass to plugins implementing callbacks.
     * @return array the array of content_item objects
     */
    public function find_all_for_course(\stdClass $course, \stdClass $user): array {
        global $OUTPUT, $DB;

        // Get all modules so we know which plugins are enabled and able to add content.
        // Only module plugins may add content items.
        $modules = $DB->get_records('modules', ['visible' => 1]);
        $return = [];

        // A moodle_url is expected and required by modules in their implementation of the hook 'get_shortcuts'.
        $urlbase = new \moodle_url('/course/mod.php', ['id' => $course->id]);

        // Now, generate the content_items.
        foreach ($modules as $modid => $mod) {

            // Create the content item for the module itself.
            // If the module chooses to implement the hook, this may be thrown away.
            $help = $this->get_core_module_help_string($mod->name);
            $archetype = plugin_supports('mod', $mod->name, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);

            $contentitem = new content_item(
                $mod->id,
                $mod->name,
                new lang_string_title("modulename", $mod->name),
                new \moodle_url($urlbase, ['add' => $mod->name]),
                $OUTPUT->pix_icon('icon', '', $mod->name, ['class' => 'icon']),
                $help,
                $archetype,
                'mod_' . $mod->name
            );

            // Next step is to get the dynamically generated content items for ecah module, if provided.
            // This is achieved by implementation of the hook, 'get_shortcuts'.

            // Give each plugin implementing the hook the main entry for REFERENCE ONLY.
            // The current hook, get_shortcuts, expects a stdClass representation of the core module content_item entry.
            $modcontentitemreference = $this->content_item_to_legacy_data($contentitem);

            // Next, get the content_items from the module callback, if implemented.
            $items = component_callback($mod->name, 'get_shortcuts', [$modcontentitemreference], null);
            if (!is_null($items)) {
                foreach ($items as $item) {
                    // Fall back to the plugin parent value if the subtype didn't provide anything.
                    $item->archetype = $item->archetype ?? $contentitem->get_archetype();
                    $item->icon = $item->icon ?? $contentitem->get_icon();

                    // If the module provides the helplink property, append it to the help text to match the look and feel
                    // of the default course modules.
                    if (isset($item->help) && isset($item->helplink)) {
                        $linktext = get_string('morehelp');
                        $item->help .= \html_writer::tag('div',
                            $OUTPUT->doc_link($item->helplink, $linktext, true), ['class' => 'helpdoclink']);
                    }

                    // Create a content_item instance from the legacy callback data.
                    $plugincontentitem = $this->content_item_from_legacy_data($item);
                    $return[] = $plugincontentitem;
                }

                // If get_shortcuts() callback is defined, the default module action is not added.
                // It is a responsibility of the callback to add it to the return value unless it is not needed.
                continue;
            }

            // The callback get_shortcuts() was not found, use the default item for the activity chooser.
            $return[] = $contentitem;
        }

        return $return;
    }
}
