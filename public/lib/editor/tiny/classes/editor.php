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

/**
 * Tiny Editor.
 *
 * @package    editor_tiny
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor extends \texteditor {

    /** @var manager The Tiny Manager instace */
    protected $manager;

    /** @var \stdClass|null The default configuration to use if none is provided */
    protected static $defaultconfiguration = null;

    /**
     * Instantiate the new editor instance.
     */
    public function __construct() {
        $this->manager = new manager();
    }

    /**
     * Set the default configuration for the editor.
     *
     * @param manager $manager The editor manager
     */
    public static function set_default_configuration(manager $manager): void {
        global $PAGE;

        if (self::is_default_configuration_set()) {
            return;
        }

        $context = $PAGE->context;

        $config = (object) [
            'css' => $PAGE->theme->editor_css_url()->out(false),
            'context' => $context->id,
            'plugins' => $manager->get_plugin_configuration($context, [], []),
        ];

        $config = json_encode($config);
        $inlinejs = <<<EOF
            M.util.js_pending('editor_tiny/editor:defaultConfiguration');
            require(['editor_tiny/editor'], (Tiny) => {
                Tiny.configureDefaultEditor({$config});
                M.util.js_complete('editor_tiny/editor:defaultConfiguration');
            });
        EOF;

        $PAGE->requires->js_amd_inline($inlinejs);

        self::$defaultconfiguration = $config;
    }

    /**
     * Fetch the current defautl configuration.
     *
     * @return \stdClass|null The default configuration or null if not set.
     */
    public static function get_default_configuration(): ?\stdClass {
        return self::$defaultconfiguration;
    }

    /**
     * Reset the default configuration.
     */
    public static function reset_default_configuration(): void {
        self::$defaultconfiguration = null;
    }

    /**
     * Check if the default configuration is set.
     *
     * @return bool True if the default configuration is set.
     */
    public static function is_default_configuration_set(): bool {
        return !empty(self::$defaultconfiguration);
    }

    /**
     * Is the current browser supported by this editor?
     *
     * @return bool
     */
    public function supported_by_browser() {
        return true;
    }

    /**
     * List of supported text field formats.
     *
     * @return array
     */
    public function get_supported_formats() {
        return [
            FORMAT_HTML => FORMAT_HTML,
        ];
    }

    /**
     * Returns text format preferred by this editor.
     *
     * @return int
     */
    public function get_preferred_format() {
        return FORMAT_HTML;
    }

    /**
     * Does this editor support picking from repositories?
     *
     * @return bool
     */
    public function supports_repositories() {
        return true;
    }

    /**
     * Use this editor for given element.
     *
     * @param string $elementid
     * @param array $options
     * @param null $fpoptions
     */
    public function use_editor($elementid, ?array $options = null, $fpoptions = null) {
        global $PAGE;

        // Ensure that the default configuration is set.
        self::set_default_configuration($this->manager);

        if ($fpoptions === null) {
            $fpoptions = [];
        }

        $context = $PAGE->context;

        if (isset($options['context']) && ($options['context'] instanceof \context)) {
            // A different context was provided.
            // Use that instead.
            $context = $options['context'];
        }

        // Generate the configuration for this editor.
        $siteconfig = get_config('editor_tiny');
        $config = (object) [
            // The URL to the CSS file for the editor.
            'css' => $PAGE->theme->editor_css_url()->out(false),

            // The current context for this page or editor.
            'context' => $context->id,

            // File picker options.
            'filepicker' => (object) $fpoptions,

            // Default draft item ID.
            'draftitemid' => 0,

            'currentLanguage' => current_language(),

            'branding' => property_exists($siteconfig, 'branding') ? !empty($siteconfig->branding) : true,
            'extended_valid_elements' => $siteconfig->extended_valid_elements ?? 'script[*],p[*],i[*]',

            // Language options.
            'language' => [
                'currentlang' => current_language(),
                'installed' => get_string_manager()->get_list_of_translations(true),
                'available' => get_string_manager()->get_list_of_languages()
            ],

            // Placeholder selectors.
            // Some contents (Example: placeholder elements) are only shown in the editor, and not to users. It is unrelated to the
            // real display. We created a list of placeholder selectors, so we can decide to or not to apply rules, styles... to
            // these elements.
            // The default of this list will be empty.
            // Other plugins can register their placeholder elements to placeholderSelectors list by calling
            // editor_tiny/options::registerPlaceholderSelectors.
            'placeholderSelectors' => [],

            // Plugin configuration.
            'plugins' => $this->manager->get_plugin_configuration($context, $options, $fpoptions, $this),
        ];

        if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
            // Add sample selectors for Behat test.
            $config->placeholderSelectors = ['.behat-tinymce-placeholder'];
        }

        foreach ($fpoptions as $fp) {
            // Guess the draftitemid for the editor.
            // Note: This is the best we can do at the moment.
            if (!empty($fp->itemid)) {
                $config->draftitemid = $fp->itemid;
                break;
            }
        }

        $configoptions = json_encode(convert_to_array($config));

        // Note: This is not ideal but the editor does not have control over any HTML output.
        // The Editor API only allows you to run JavaScript.
        // In the future we will extend the editor API to allow it to generate the textarea, or attributes to use in the
        // textarea or its wrapper.
        // For now we cannot use the `js_call_amd()` API call because it warns if the parameters passed exceed a
        // relatively low character limit.
        $config = json_encode($config);
        $inlinejs = <<<EOF
            M.util.js_pending('editor_tiny/editor');
            require(['editor_tiny/editor'], (Tiny) => {
                Tiny.setupForElementId({
                    elementId: "{$elementid}",
                    options: {$configoptions},
                });
                M.util.js_complete('editor_tiny/editor');
            });
        EOF;

        $PAGE->requires->js_amd_inline($inlinejs);
    }
}
