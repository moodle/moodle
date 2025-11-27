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

use core_component;
use core_course\local\entity\content_item;
use core_course\local\entity\lang_string_title;

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
            if ($sm->string_exists('modulename_tip', $modname)) {
                $tipcontent = get_string('modulename_tip', $modname);
                $tipicon = $OUTPUT->pix_icon('i/tip', '');
                $tip = $tipicon . get_string('tip', '', $tipcontent);
                $help .= \html_writer::tag('div', $tip, ['class' => 'helpdoctip alert alert-secondary']);
            }
            if ($sm->string_exists('modulename_link', $modname)) { // Link to further info in Moodle docs.
                // The link is stored in a language file but should not be translated, use value for English.
                $link = $sm->get_string('modulename_link', $modname, null, 'en');
                // The text 'More help' and other strings should be in the current language.
                $linktext = get_string('morehelp');
                $arialabel = get_string('morehelpaboutmodule', '', get_string('modulename', $modname));
                $doclink = $OUTPUT->doc_link($link, $linktext, true, ['aria-label' => $arialabel]);
                $help .= \html_writer::tag('div', $doclink, ['class' => 'helpdoclink opacity-75 pt-3']);
            }
        }
        return $help;
    }

    /**
     * Get the summary string for content items representing core modules.
     *
     * @param string $modname the module name.
     * @return string the summary string or empty string if none exists.
     */
    private function get_core_module_summary_string(string $modname): string {
        $sm = get_string_manager();
        if (!$sm->string_exists('modulename_summary', $modname)) {
            return '';
        }

        return get_string('modulename_summary', $modname);
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
     * Filter out any modules that are not to be rendered or managed on the course page.
     *
     * @param content_item[] $contentitems items to be filtered
     * @return content_item[] filtered items
     */
    private static function filter_out_items_not_to_be_displayed(array $contentitems): array {
        return array_filter($contentitems, static function ($module) {
            [, $name] = core_component::normalize_component($module->get_component_name());
            return \course_modinfo::is_mod_type_visible_on_course($name);
        });
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
            $summary = $this->get_core_module_summary_string($mod->name);
            $archetype = plugin_supports('mod', $mod->name, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
            $purpose = plugin_supports('mod', $mod->name, FEATURE_MOD_PURPOSE, MOD_PURPOSE_OTHER);
            $otherpurpose = plugin_supports('mod', $mod->name, FEATURE_MOD_OTHERPURPOSE);
            $isbranded = component_callback('mod_' . $mod->name, 'is_branded', [], false);
            $gradable = plugin_supports('mod', $mod->name, FEATURE_GRADE_HAS_GRADE, false);

            $contentitem = new content_item(
                id: $mod->id,
                name: $mod->name,
                title: new lang_string_title("modulename", $mod->name),
                link: new \moodle_url(''), // No course scope, so just an empty link.
                icon: $OUTPUT->pix_icon('monologo', '', $mod->name, ['class' => 'icon activityicon']),
                help: $help,
                archetype: $archetype,
                componentname: 'mod_' . $mod->name,
                purpose: $purpose,
                branded: $isbranded,
                gradable: $gradable,
                otherpurpose: $otherpurpose,
                summary: $summary,
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

        // These module types are not to be rendered to the course page.
        return self::filter_out_items_not_to_be_displayed($return);
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

        // Now, generate the content_items.
        foreach ($modules as $modid => $mod) {
            // Exclude modules if the code doesn't exist.
            if (!file_exists("$CFG->dirroot/mod/$mod->name/lib.php")) {
                continue;
            }
            // Create the content item for the module itself.
            // If the module chooses to implement the hook, this may be thrown away.
            $help = $this->get_core_module_help_string($mod->name);
            $summary = $this->get_core_module_summary_string($mod->name);
            $archetype = plugin_supports('mod', $mod->name, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
            $purpose = plugin_supports('mod', $mod->name, FEATURE_MOD_PURPOSE, MOD_PURPOSE_OTHER);
            $otherpurpose = plugin_supports('mod', $mod->name, FEATURE_MOD_OTHERPURPOSE);
            $isbranded = component_callback('mod_' . $mod->name, 'is_branded', [], false);
            $gradable = plugin_supports('mod', $mod->name, FEATURE_GRADE_HAS_GRADE, false);

            $icon = 'monologo';
            // Quick check for monologo icons.
            // Plugins that don't have monologo icons will be displayed as is and CSS filter will not be applied.
            $hasmonologoicons = core_component::has_monologo_icon('mod', $mod->name);
            $iconclass = '';
            if (!$hasmonologoicons) {
                $iconclass = 'nofilter';
            }
            $contentitem = new content_item(
                id: $mod->id,
                name: $mod->name,
                title: new lang_string_title("modulename", $mod->name),
                link: new \moodle_url('/course/mod.php', ['id' => $course->id, 'add' => $mod->name]),
                icon: $OUTPUT->pix_icon($icon, '', $mod->name, ['class' => "activityicon $iconclass"]),
                help: $help,
                archetype: $archetype,
                componentname: 'mod_' . $mod->name,
                purpose: $purpose,
                branded: $isbranded,
                gradable: $gradable,
                otherpurpose: $otherpurpose,
                summary: $summary,
            );

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

            } else {
                // Callback was not found, so just use the default module content item.
                $return[] = $contentitem;
            }
        }

        // These module types are not to be rendered to the course page.
        return self::filter_out_items_not_to_be_displayed($return);
    }
}
