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

namespace core\output\renderer_factory;

use core_component;
use core\exception\coding_exception;
use core\output\theme_config;

/**
 * This is a base class to help you implement the renderer_factory interface.
 *
 * It keeps a cache of renderers that have been constructed, so you only need
 * to construct each one once in you subclass.
 *
 * It also has a method to get the name of, and include the renderer.php with
 * the definition of, the standard renderer class for a given module.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
abstract class renderer_factory_base implements renderer_factory_interface {
    /**
     * Constructor.
     *
     * @param theme_config $theme the theme we belong to.
     */
    public function __construct(
        /** @var theme_config The theme we belong to */
        protected theme_config $theme,
    ) {
    }

    /**
     * Returns suffix of renderer class expected for given target.
     *
     * @param string $target one of the renderer target constants, target is guessed if null used
     * @return array two element array, first element is target, second the target suffix string
     */
    protected function get_target_suffix($target) {
        if (empty($target) || $target === RENDERER_TARGET_MAINTENANCE) {
            // If the target hasn't been specified we need to guess the defaults.
            // We also override the target with the default if the maintenance target has been provided.
            // This ensures we don't use the maintenance renderer if we are processing a special target.
            if (defined('PREFERRED_RENDERER_TARGET')) {
                $target = PREFERRED_RENDERER_TARGET;
            } else if (CLI_SCRIPT) {
                $target = RENDERER_TARGET_CLI;
            } else if (AJAX_SCRIPT) {
                $target = RENDERER_TARGET_AJAX;
            }
        }

        switch ($target) {
            case RENDERER_TARGET_CLI:
                $suffix = '_cli';
                break;
            case RENDERER_TARGET_AJAX:
                $suffix = '_ajax';
                break;
            case RENDERER_TARGET_TEXTEMAIL:
                $suffix = '_textemail';
                break;
            case RENDERER_TARGET_HTMLEMAIL:
                $suffix = '_htmlemail';
                break;
            case RENDERER_TARGET_MAINTENANCE:
                $suffix = '_maintenance';
                break;
            default:
                $target = RENDERER_TARGET_GENERAL;
                $suffix = '';
        }

        return [$target, $suffix];
    }

    /**
     * For a given module name, return the possible class names
     * that defines the renderer interface for that module.
     *
     * Newer auto-loaded class names are returned as well as the old style _renderable classnames.
     *
     * Also, if it exists, include the renderer.php file for that module, so
     * the class definition of the default renderer has been loaded.
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to:
     *              '\mod_forum\output\news_renderer'
     *              or '\mod_forum\output\news\renderer'
     *              or non-autoloaded 'mod_forum_news'
     * @return array[] Each element of the array is an array with keys:
     *                 classname - The class name to search
     *                 autoloaded - Does this classname assume autoloading?
     *                 validwithprefix - Is this class name valid when a prefix is added to it?
     *                 validwithoutprefix - Is this class name valid when no prefix is added to it?
     * @throws coding_exception
     */
    protected function standard_renderer_classnames($component, $subtype = null) {
        global $CFG; // Needed in included files.
        $classnames = [];

        // Standardize component name ala frankenstyle.
        [$plugin, $type] = core_component::normalize_component($component);
        if ($type === null) {
            $component = $plugin;
        } else {
            $component = $plugin . '_' . $type;
        }

        if ($component !== 'core') {
            // Renderers are stored in renderer.php files.
            if (!$compdirectory = core_component::get_component_directory($component)) {
                throw new coding_exception('Invalid component specified in renderer request', $component);
            }
            $rendererfile = $compdirectory . '/renderer.php';
            if (file_exists($rendererfile)) {
                include_once($rendererfile);
            }
        } else if (!empty($subtype)) {
            $coresubsystems = core_component::get_core_subsystems();
            if (!array_key_exists($subtype, $coresubsystems)) { // There may be nulls.
                throw new coding_exception('Invalid core subtype "' . $subtype . '" in renderer request', $subtype);
            }
            if ($coresubsystems[$subtype]) {
                $rendererfile = $coresubsystems[$subtype] . '/renderer.php';
                if (file_exists($rendererfile)) {
                    include_once($rendererfile);
                }
            }
        }

        if (empty($subtype)) {
            // Theme specific auto-loaded name (only valid when prefixed with the theme name).
            $classnames[] = [
                'validwithprefix' => true,
                'validwithoutprefix' => false,
                'autoloaded' => true,
                'classname' => '\\output\\' . $component . '_renderer',
            ];

            // Standard autoloaded plugin name (not valid with a prefix).
            $classnames[] = [
                'validwithprefix' => false,
                'validwithoutprefix' => true,
                'autoloaded' => true,
                'classname' => '\\' . $component . '\\output\\renderer',
            ];
            // Legacy class name - (valid with or without a prefix).
            $classnames[] = [
                'validwithprefix' => true,
                'validwithoutprefix' => true,
                'autoloaded' => false,
                'classname' => $component . '_renderer',
            ];
        } else {
            // Theme specific auto-loaded name (only valid when prefixed with the theme name).
            $classnames[] = [
                'validwithprefix' => true,
                'validwithoutprefix' => false,
                'autoloaded' => true,
                'classname' => '\\output\\' . $component . '\\' . $subtype . '_renderer',
            ];
            // Version of the above with subtype being a namespace level on it's own.
            $classnames[] = [
                'validwithprefix' => true,
                'validwithoutprefix' => false,
                'autoloaded' => true,
                'classname' => '\\output\\' . $component . '\\' . $subtype . '\\renderer',
            ];
            // Standard autoloaded plugin name (not valid with a prefix).
            $classnames[] = [
                'validwithprefix' => false,
                'validwithoutprefix' => true,
                'autoloaded' => true,
                'classname' => '\\' . $component . '\\output\\' . $subtype . '_renderer',
            ];
            // Version of the above with subtype being a namespace level on it's own.
            $classnames[] = [
                'validwithprefix' => false,
                'validwithoutprefix' => true,
                'autoloaded' => true,
                'classname' => '\\' . $component . '\\output\\' . $subtype . '\\renderer',
            ];
            // Legacy class name - (valid with or without a prefix).
            $classnames[] = [
                'validwithprefix' => true,
                'validwithoutprefix' => true,
                'autoloaded' => false,
                'classname' => $component . '_' . $subtype . '_renderer',
            ];
        }
        return $classnames;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(renderer_factory_base::class, \renderer_factory_base::class);
