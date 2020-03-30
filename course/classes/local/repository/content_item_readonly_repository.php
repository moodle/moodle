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
                $arialabel = get_string('morehelpaboutmodule', '', get_string('modulename', $modname));
                $doclink = $OUTPUT->doc_link($link, $linktext, true, ['aria-label' => $arialabel]);
                $help .= \html_writer::tag('div', $doclink, ['class' => 'helpdoclink']);
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
        global $OUTPUT;

        // Make sure the legacy data results in a content_item with id = 0.
        // Even with an id, we can't uniquely identify the item, because we can't guarantee what component it came from.
        // An id of -1, signifies this.
        $item->id = -1;

        // If the module provides the helplink property, append it to the help text to match the look and feel
        // of the default course modules.
        if (isset($item->help) && isset($item->helplink)) {
            $linktext = get_string('morehelp');
            $item->help .= \html_writer::tag('div',
                $OUTPUT->doc_link($item->helplink, $linktext, true), ['class' => 'helpdoclink']);
        }

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
     * Helper to get the contentitems from all subplugin hooks for a given module plugin.
     *
     * @param string $parentpluginname the name of the module plugin to check subplugins for.
     * @param content_item $modulecontentitem the content item of the module plugin, to pass to the hooks.
     * @param \stdClass $user the user object to pass to subplugins.
     * @return array the array of content items.
     */
    private function get_subplugin_course_content_items(string $parentpluginname, content_item $modulecontentitem,
            \stdClass $user): array {

        $contentitems = [];
        $pluginmanager = \core_plugin_manager::instance();
        foreach ($pluginmanager->get_subplugins_of_plugin($parentpluginname) as $subpluginname => $subplugin) {
            // Call the hook, but with a copy of the module content item data.
            $spcontentitems = component_callback($subpluginname, 'get_course_content_items', [$modulecontentitem, $user], null);
            if (!is_null($spcontentitems)) {
                foreach ($spcontentitems as $spcontentitem) {
                    $contentitems[] = $spcontentitem;
                }
            }
        }
        return $contentitems;
    }

    /**
     * Get all the content items for a subplugin.
     *
     * @param string $parentpluginname
     * @param content_item $modulecontentitem
     * @return array
     */
    private function get_subplugin_all_content_items(string $parentpluginname, content_item $modulecontentitem): array {
        $contentitems = [];
        $pluginmanager = \core_plugin_manager::instance();
        foreach ($pluginmanager->get_subplugins_of_plugin($parentpluginname) as $subpluginname => $subplugin) {
            // Call the hook, but with a copy of the module content item data.
            $spcontentitems = component_callback($subpluginname, 'get_all_content_items', [$modulecontentitem], null);
            if (!is_null($spcontentitems)) {
                foreach ($spcontentitems as $spcontentitem) {
                    $contentitems[] = $spcontentitem;
                }
            }
        }
        return $contentitems;
    }

    /**
     * Helper to make sure any legacy items have certain properties, which, if missing are inherited from the parent module item.
     *
     * @param \stdClass $legacyitem the legacy information, a stdClass coming from get_shortcuts() hook.
     * @param content_item $modulecontentitem The module's content item information, to inherit if needed.
     * @return \stdClass the updated legacy item stdClass
     */
    private function legacy_item_inherit_missing(\stdClass $legacyitem, content_item $modulecontentitem): \stdClass {
        // Fall back to the plugin parent value if the subtype didn't provide anything.
        $legacyitem->archetype = $legacyitem->archetype ?? $modulecontentitem->get_archetype();
        $legacyitem->icon = $legacyitem->icon ?? $modulecontentitem->get_icon();
        return $legacyitem;
    }

    /**
     * Find all the available content items, not restricted to course or user.
     *
     * @return array the array of content items.
     */
    public function find_all(): array {
        global $OUTPUT, $DB, $CFG;

        // Get all modules so we know which plugins are enabled and able to add content.
        // Only module plugins may add content items.
        $modules = $DB->get_records('modules', ['visible' => 1]);
        $return = [];

        // Now, generate the content_items.
        foreach ($modules as $modid => $mod) {
            // Exclude modules if the code doesn't exist.
            if (!file_exists("$CFG->dirroot/mod/$mod->name/lib.php")) {
                continue;
            }
            // Create the content item for the module itself.
            // If the module chooses to implement the hook, this may be thrown away.
            $help = $this->get_core_module_help_string($mod->name);
            $archetype = plugin_supports('mod', $mod->name, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);

            $contentitem = new content_item(
                $mod->id,
                $mod->name,
                new lang_string_title("modulename", $mod->name),
                new \moodle_url(''), // No course scope, so just an empty link.
                $OUTPUT->pix_icon('icon', '', $mod->name, ['class' => 'icon']),
                $help,
                $archetype,
                'mod_' . $mod->name
            );

            $modcontentitemreference = clone($contentitem);

            if (component_callback_exists('mod_' . $mod->name, 'get_all_content_items')) {
                // Call the module hooks for this module.
                $plugincontentitems = component_callback('mod_' . $mod->name, 'get_all_content_items',
                    [$modcontentitemreference], []);
                if (!empty($plugincontentitems)) {
                    array_push($return, ...$plugincontentitems);
                }

                // Now, get those for subplugins of the module.
                $subplugincontentitems = $this->get_subplugin_all_content_items('mod_' . $mod->name, $modcontentitemreference);
                if (!empty($subplugincontentitems)) {
                    array_push($return, ...$subplugincontentitems);
                }
            } else {
                // Neither callback was found, so just use the default module content item.
                $return[] = $contentitem;
            }
        }
        return $return;
    }

    /**
     * Get the list of potential content items for the given course.
     *
     * @param \stdClass $course the course
     * @param \stdClass $user the user, to pass to plugins implementing callbacks.
     * @return array the array of content_item objects
     */
    public function find_all_for_course(\stdClass $course, \stdClass $user): array {
        global $OUTPUT, $DB, $CFG;

        // Get all modules so we know which plugins are enabled and able to add content.
        // Only module plugins may add content items.
        $modules = $DB->get_records('modules', ['visible' => 1]);
        $return = [];

        // A moodle_url is expected and required by modules in their implementation of the hook 'get_shortcuts'.
        $urlbase = new \moodle_url('/course/mod.php', ['id' => $course->id]);

        // Now, generate the content_items.
        foreach ($modules as $modid => $mod) {
            // Exclude modules if the code doesn't exist.
            if (!file_exists("$CFG->dirroot/mod/$mod->name/lib.php")) {
                continue;
            }
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

            // Legacy vs new hooks.
            // If the new hook is found for a module plugin, use that path (calling mod plugins and their subplugins directly)
            // If not, check the legacy hook. This won't provide us with enough information to identify items uniquely within their
            // component (lti + lti source being an example), but we can still list these items.
            $modcontentitemreference = clone($contentitem);

            if (component_callback_exists('mod_' . $mod->name, 'get_course_content_items')) {
                // Call the module hooks for this module.
                $plugincontentitems = component_callback('mod_' . $mod->name, 'get_course_content_items',
                    [$modcontentitemreference, $user, $course], []);
                if (!empty($plugincontentitems)) {
                    array_push($return, ...$plugincontentitems);
                }

                // Now, get those for subplugins of the module.
                $subpluginitems = $this->get_subplugin_course_content_items('mod_' . $mod->name, $modcontentitemreference, $user);
                if (!empty($subpluginitems)) {
                    array_push($return, ...$subpluginitems);
                }

            } else if (component_callback_exists('mod_' . $mod->name, 'get_shortcuts')) {
                // TODO: MDL-68011 this block needs to be removed in 4.3.
                debugging('The callback get_shortcuts has been deprecated. Please use get_course_content_items and
                    get_all_content_items instead. Some features of the activity chooser, such as favourites and recommendations
                    are not supported when providing content items via the deprecated callback.');

                // If get_shortcuts() callback is defined, the default module action is not added.
                // It is a responsibility of the callback to add it to the return value unless it is not needed.
                // The legacy hook, get_shortcuts, expects a stdClass representation of the core module content_item entry.
                $modcontentitemreference = $this->content_item_to_legacy_data($contentitem);

                $legacyitems = component_callback($mod->name, 'get_shortcuts', [$modcontentitemreference], null);
                if (!is_null($legacyitems)) {
                    foreach ($legacyitems as $legacyitem) {

                        $legacyitem = $this->legacy_item_inherit_missing($legacyitem, $contentitem);

                        // All items must have different links, use them as a key in the return array.
                        // If plugin returned the only one item with the same link as default item - keep $modname,
                        // otherwise append the link url to the module name.
                        $legacyitem->name = (count($legacyitems) == 1 &&
                            $legacyitem->link->out() === $contentitem->get_link()->out()) ? $mod->name : $mod->name . ':' .
                                $legacyitem->link;

                        $plugincontentitem = $this->content_item_from_legacy_data($legacyitem);

                        $return[] = $plugincontentitem;
                    }
                }
            } else {
                // Neither callback was found, so just use the default module content item.
                $return[] = $contentitem;
            }
        }

        return $return;
    }
}
