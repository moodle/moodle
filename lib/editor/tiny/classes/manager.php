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

namespace editor_tiny;

use context;

/**
 * Tiny Editor Plugin manager.
 *
 * @package    editor_tiny
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Get the configuration for all plugins.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param  editor $editor The editor instance in which the plugin is initialised
     */
    public function get_plugin_configuration(
        context $context,
        array $options = [],
        array $fpoptions = [],
        ?editor $editor = null
    ): array {
        $disabledplugins = $this->get_disabled_plugins();

        // Get the list of plugins.
        // Note: Disabled plugins are already removed from this list.
        $plugins = $this->get_shipped_plugins();

        // Fetch configuration for Moodle plugins.
        $moodleplugins = \core_component::get_plugin_list_with_class('tiny', 'plugininfo');
        foreach ($moodleplugins as $plugin => $classname) {
            if (in_array($plugin, $disabledplugins) || in_array("{$plugin}/plugin", $disabledplugins)) {
                // Skip getting data for disabled plugins.
                continue;
            }

            if (!is_a($classname, plugin::class, true)) {
                // Skip plugins that do not implement the plugin interface.
                debugging("Plugin {$plugin} does not implement the plugin interface", DEBUG_DEVELOPER);
                continue;
            }

            if (!$classname::is_enabled($context, $options, $fpoptions, $editor)) {
                // This plugin has disabled itself for some reason.
                // This is typical for media plugins where there is no file storage.
                continue;
            }

            // Get the plugin information, which includes the list of buttons, menu items, and configuration.
            $plugininfo = $classname::get_plugin_info(
                $context,
                $options,
                $fpoptions,
                $editor
            );

            if (!empty($config)) {
                $plugininfo['config'] = $config;
            }

            // We suffix the plugin name for Moodle plugins with /plugin to avoid conflicts with Tiny plugins.
            $plugins["{$plugin}/plugin"] = $plugininfo;
        }

        return $plugins;
    }

    /**
     * Get a list of the buttons provided by this plugin.
     *
     * @return string[]
     */
    protected function get_tinymce_buttons(): array {
        // The following list is defined at:
        // https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/#thecoretoolbarbuttons.
        return [
            // These are always available, without requiring additional plugins.
            'aligncenter',
            'alignjustify',
            'alignleft',
            'alignnone',
            'alignright',
            'blockquote',
            'backcolor',
            'bold',
            'copy',
            'cut',
            'fontselect',
            'fontsizeselect',
            'forecolor',
            'formatselect',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'indent',
            'italic',
            'language',
            'lineheight',
            'newdocument',
            'outdent',
            'paste',
            'redo',
            'remove',
            'removeformat',
            'selectall',
            'strikethrough',
            'styleselect',
            'subscript',
            'superscript',
            'underline',
            'undo',
            'visualaid',
        ];
    }

    /**
     * Get a list of the menu items provided by this plugin.
     *
     * @return string[]
     */
    protected function get_tinymce_menuitems(): array {
        // The following list is defined at:
        // https://www.tiny.cloud/docs/advanced/available-menu-items/#thecoremenuitems.
        return [
            'align' => 'format',
            'backcolor' => 'format',
            'blockformats' => 'format',
            'bold' => 'format',
            'codeformat' => 'format',
            'copy' => 'copy',
            'cut' => 'copy',
            'forecolor' => 'format',
            'formats' => 'format',
            'fontformats' => 'format',
            'fontsizes' => 'format',
            'italic' => 'format',
            'language' => 'format',
            'lineheight' => 'format',
            'newdocument' => 'file',
            'paste' => 'copy',
            'redo' => 'copy',
            'removeformat' => 'format',
            'selectall' => 'edit',
            'strikethrough' => 'format',
            'subscript' => 'format',
            'superscript' => 'format',
            'underline' => 'format',
            'undo' => 'copy',
            'visualaid' => 'view',
        ];
    }

    /**
     * Return a list of all available plugins, including both TinyMCE shipped, and Moodle add-onis.
     *
     * Each plugin is returned as an array element containing:
     * - a list of buttons (if applicable); and
     * - a list of menuitems (if applicable).
     *
     * Note: Not all plugins include buttons, and not all plugins include menuitems.
     * These array keys are optional.
     *
     * @return array
     */
    protected function get_available_plugins(): array {
        $plugins = $this->get_shipped_plugins();
        $plugins += $this->get_moodle_plugins();

        $disabledplugins = $this->get_disabled_plugins();
        $plugins = array_filter($plugins, function ($plugin) use ($disabledplugins) {
            return !in_array($plugin, $disabledplugins);
        }, ARRAY_FILTER_USE_KEY);

        return $plugins;
    }

    /**
     * Return a list of all available plugins built into TinyMCE and not shipped as separate Moodle plugins.
     *
     * Each plugin is returned as an array element containing:
     * - a list of buttons (if applicable); and
     * - a list of menuitems (if applicable).
     *
     * Note: Not all plugins include buttons, and not all plugins include menuitems.
     * These array keys are optional.
     *
     * @return array
     */
    protected function get_shipped_plugins(): array {
        $plugins = $this->get_tinymce_plugins();
        if ($this->premium_plugins_enabled()) {
            $plugins += $this->get_premium_plugins();
        }

        $disabledplugins = $this->get_disabled_plugins();
        return array_filter($plugins, function($plugin) use ($disabledplugins) {
            return !in_array($plugin, $disabledplugins);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Get a list of the core plugins with their button, and menuitem, configuration.
     *
     * @return array[]
     */
    protected function get_tinymce_plugins(): array {
        // The following list is defined at:
        // https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/#thecoretoolbarbuttons.
        return [
            'anchor' => [
                'buttons' => [
                    'anchor',
                ],
                'menuitems' => [
                    'anchor' => 'insert',
                ],
            ],
            'autosave' => [
                'buttons' => [
                    'restoredraft',
                ],
                'menuitems' => [
                    'restoredraft' => 'file',
                ],
            ],
            'charmap' => [
                'buttons' => [
                    'charmap',
                ],
                'menuitems' => [
                    'charmap' => 'insert',
                ],
            ],
            'code' => [
                'buttons' => [
                    'code',
                ],
                'menuitems' => [
                    'code' => 'view',
                ],
            ],
            'codesample' => [
                'buttons' => [
                    'codesample',
                ],
                'menutiems' => [
                    'codesample' => 'insert',
                ],
            ],
            'directionality' => [
                'buttons' => [
                    'ltr',
                    'rtl',
                ],
            ],
            'emoticons' => [
                'buttons' => [
                    'emoticons',
                ],
                'menuitems' => [
                    'emoticons' => 'insert',
                ],
            ],
            'fullscreen' => [
                'buttons' => [
                    'fullscreen',
                ],
                'menuitems' => [
                    'fullscreen' => 'view',
                ],
            ],
            'help' => [
                'buttons' => [
                    'help',
                ],
                'menuitems' => [
                    'help' => 'help',
                ],
            ],
            'image' => [
                'buttons' => [
                    'image',
                ],
                'menuitems' => [
                    'image' => 'insert',
                ],
            ],
            'insertdatetime' => [
                'buttons' => [
                    'insertdatetime',
                ],
                'menuitems' => [
                    'insertdatetime' => 'insert',
                ],
            ],
            'link' => [
                'buttons' => [
                    'link',
                    'openlink',
                    'unlink',
                ],
                'menuitems' => [
                    'link' => 'insert',
                ],
            ],
            'lists' => [
                'buttons' => [
                    'bullist',
                    'numlist',
                ],
            ],
            'media' => [
                'buttons' => [
                    'media',
                ],
                'menuitems' => [
                    'media' => 'insert',
                ],
            ],
            'nonbreaking' => [
                'buttons' => [
                    'nonbreaking',
                ],
                'menuitems' => [
                    'nonbreaking' => 'insert',
                ],
            ],
            'pagebreak' => [
                'buttons' => [
                    'pagebreak',
                ],
                'menuitems' => [
                    'pagebreak' => 'insert',
                ],
            ],
            'preview' => [
                'buttons' => [
                    'preview',
                ],
                'menuitems' => [
                    'preview' => 'file',
                ],
            ],
            'quickbars' => [
                'buttons' => [
                    'quickimage',
                    'quicklink',
                    'quicktable',
                ],
            ],
            'save' => [
                'buttons' => [
                    'cancel',
                    'save',
                ],
            ],
            'searchreplace' => [
                'buttons' => [
                    'searchreplace',
                ],
                'menuitems' => [
                    'searchreplace' => 'edit',
                ],
            ],
            'table' => [
                'buttons' => [
                    'table',
                    'tablecellprops',
                    'tablecopyrow',
                    'tablecutrow',
                    'tabledelete',
                    'tabledeletecol',
                    'tabledeleterow',
                    'tableinsertdialog',
                    'tableinsertcolafter',
                    'tableinsertcolbefore',
                    'tableinsertrowafter',
                    'tableinsertrowbefore',
                    'tablemergecells',
                    'tablepasterowafter',
                    'tablepasterowbefore',
                    'tableprops',
                    'tablerowprops',
                    'tablesplitcells',
                    'tableclass',
                    'tablecellclass',
                    'tablecellvalign',
                    'tablecellborderwidth',
                    'tablecellborderstyle',
                    'tablecaption',
                    'tablecellbackgroundcolor',
                    'tablecellbordercolor',
                    'tablerowheader',
                    'tablecolheader',
                ],
                'menuitems' => [
                    'inserttable' => 'table',
                    'tableprops' => 'table',
                    'deletetable' => 'table',
                    'cell' => 'table',
                    'tablemergecells' => 'table',
                    'tablesplitcells' => 'table',
                    'tablecellprops' => 'table',
                    'column' => 'table',
                    'tableinsertcolumnbefore' => 'table',
                    'tableinsertcolumnafter' => 'table',
                    'tablecutcolumn' => 'table',
                    'tablecopycolumn' => 'table',
                    'tablepastecolumnbefore' => 'table',
                    'tablepastecolumnafter' => 'table',
                    'tabledeletecolumn' => 'table',
                    'row' => 'table',
                    'tableinsertrowbefore' => 'table',
                    'tableinsertrowafter' => 'table',
                    'tablecutrow' => 'table',
                    'tablecopyrow' => 'table',
                    'tablepasterowbefore' => 'table',
                    'tablepasterowafter' => 'table',
                    'tablerowprops' => 'table',
                    'tabledeleterow' => 'table',
                ],
            ],
            'template' => [
                'buttons' => [
                    'template',
                ],
                'menuitems' => [
                    'template' => 'insert',
                ],
            ],
            'visualblocks' => [
                'buttons' => [
                    'visualblocks',
                ],
                'menuitems' => [
                    'visualblocks' => 'view',
                ],
            ],
            'visualchars' => [
                'buttons' => [
                    'visualchars',
                ],
                'menuitems' => [
                    'visualchars' => 'view',
                ],
            ],
            'wordcount' => [
                'buttons' => [
                    'wordcount',
                ],
                'menuitems' => [
                    'wordcount' => 'tools',
                ],
            ],
        ];
    }

    /**
     * Get a list of the disabled plugins.
     *
     * @return string[]
     */
    protected function get_disabled_plugins(): array {
        return [
            // Disable the image and media plugins.
            // These are not generally compatible with Moodle.
            'image',
            'media',

            // Use the Moodle autosave plugin instead.
            'autosave',

            // Disable the Template plugin for now.
            'template',

            // Disable the preview plugin as it does not support Moodle filters.
            'preview',

            // Use the Moodle link plugin instead.
            'link',
        ];
    }

    /**
     * Get a list of the Moodle plugins with their button, and menuitem, configuration.
     *
     * @return array[]
     */
    protected function get_moodle_plugins(): array {
        $plugins = \core_component::get_plugin_list_with_class('tiny', 'plugininfo');

        $pluginconfig = [];
        foreach ($plugins as $pluginname => $classname) {
            if (!is_a($classname, plugin::class, true)) {
                continue;
            }
            // Module name => [buttons, menuitems].
            $pluginconfig["{$pluginname}/plugin"] = $classname::get_plugin_info();
        }

        return $pluginconfig;
    }

    /**
     * Check whether premium plugins are configured and enabled.
     *
     * @return bool
     */
    protected function premium_plugins_enabled(): bool {
        return false;
    }

    /**
     * Get a list of the Tiny Premium plugins with their button, and menuitem, configuration.
     *
     * Note: This only includes _compatible_ premium plugins.
     * Some premium plugins *may not* be compatible with Moodle, and some may require additional configuration.
     *
     * @return array[]
     */
    protected function get_premium_plugins(): array {
        return [
            'a11ycheck' => [
                'buttons' => [
                    'a11ycheck',
                ],
                'menuitems' => [
                    'a11ycheck',
                ],
            ],
            'advcode' => [
                'buttons' => [
                    'code',
                ],
                'menuitems' => [
                    'code',
                ],
            ],
            'footnotes' => [
                'buttons' => [
                    'footnotes',
                    'footnotesupdate',
                ],
                'menuitems' => [
                    'footnotes',
                    'footnotesupdate',
                ],
            ],
            'mergetags' => [
                'buttons' => [
                    'mergetags',
                ],
                'menuitems' => [
                    'mergetags',
                ],
            ],
            'autocorrect' => [
                'menuitems' => [
                    'autocorrect',
                    'capitalization',
                ],
            ],
        ];
    }
}
