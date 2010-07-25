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
 * Interface and classes for creating appropriate renderers for various
 * parts of Moodle.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package    core
 * @subpackage lib
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** General rendering target, usually normal browser page */
define('RENDERER_TARGET_GENERAL', 'general');

/** Plain text rendering for CLI scripts and cron */
define('RENDERER_TARGET_CLI', 'cli');

/** Plain text rendering for Ajax scripts*/
define('RENDERER_TARGET_AJAX', 'ajax');

/** Plain text rendering intended for sending via email */
define('RENDERER_TARGET_TEXTEMAIL', 'textemail');

/** Rich text html rendering intended for sending via email */
define('RENDERER_TARGET_HTMLEMAIL', 'htmlemail');

/* note: maybe we could define portfolio export target too */


/**
 * A renderer factory is just responsible for creating an appropriate renderer
 * for any given part of Moodle.
 *
 * Which renderer factory to use is chose by the current theme, and an instance
 * if created automatically when the theme is set up.
 *
 * A renderer factory must also have a constructor that takes a theme_config object.
 * (See {@link renderer_factory_base::__construct} for an example.)
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
interface renderer_factory {
    /**
     * Return the renderer for a particular part of Moodle.
     *
     * The renderer interfaces are defined by classes called {plugin}_renderer
     * where {plugin} is the name of the component. The renderers for core Moodle are
     * defined in lib/renderer.php. For plugins, they will be defined in a file
     * called renderer.php inside the plugin.
     *
     * Renderers will normally want to subclass the renderer_base class.
     * (However, if you really know what you are doing, you don't have to do that.)
     *
     * There is no separate interface definition for renderers. The default
     * {plugin}_renderer implementation also serves to define the API for
     * other implementations of the interface, whether or not they subclass it.
     *
     * A particular plugin can define multiple renderers if it wishes, using the
     * $subtype parameter. For example workshop_renderer,
     * workshop_allocation_manual_renderer etc.
     *
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer(moodle_page $page, $component, $subtype=null, $target=null);
}


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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
abstract class renderer_factory_base implements renderer_factory {
    /** @var theme_config the theme we belong to. */
    protected $theme;

    /**
     * Constructor.
     * @param theme_config $theme the theme we belong to.
     */
    public function __construct(theme_config $theme) {
        $this->theme = $theme;
    }

    /**
     * Returns suffix of renderer class expected for given target.
     * @param string $target one of the renderer target constants, target is guessed if null used
     * @return array two element array, first element is target, second the target suffix string
     */
    protected function get_target_suffix($target) {
        if (empty($target)) {
            // automatically guessed defaults
            if (CLI_SCRIPT) {
                $target = RENDERER_TARGET_CLI;
            } else if (AJAX_SCRIPT) {
                $target = RENDERER_TARGET_AJAX;
            }
        }

        switch ($target) {
            case RENDERER_TARGET_CLI: $suffix = '_cli'; break;
            case RENDERER_TARGET_AJAX: $suffix = '_ajax'; break;
            case RENDERER_TARGET_TEXTEMAIL: $suffix = '_textemail'; break;
            case RENDERER_TARGET_HTMLEMAIL: $suffix = '_htmlemail'; break;
            default: $target = RENDERER_TARGET_GENERAL; $suffix = '';
        }

        return array($target, $suffix);
    }

    /**
     * For a given module name, return the name of the standard renderer class
     * that defines the renderer interface for that module.
     *
     * Also, if it exists, include the renderer.php file for that module, so
     * the class definition of the default renderer has been loaded.
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return string the name of the standard renderer class for that module.
     */
    protected function standard_renderer_classname($component, $subtype = null) {
        global $CFG; // needed in included files

        // standardize component name ala frankenstyle
        list($plugin, $type) = normalize_component($component);
        if ($type === null) {
            $component = $plugin;
        } else {
            $component = $plugin.'_'.$type;
        }

        if ($component !== 'core') {
            // renderers are stored in renderer.php files
            if (!$compdirectory = get_component_directory($component)) {
                throw new coding_exception('Invalid component specified in renderer request');
            }
            $rendererfile = $compdirectory . '/renderer.php';
            if (file_exists($rendererfile)) {
                include_once($rendererfile);
            }

        } else if (!empty($subtype)) {
            $coresubsystems = get_core_subsystems();
            if (!isset($coresubsystems[$subtype])) {
                throw new coding_exception('Invalid core subtype "' . $subtype . '" in renderer request');
            }
            $rendererfile = $CFG->dirroot . '/' . $coresubsystems[$subtype] . '/renderer.php';
            if (file_exists($rendererfile)) {
                include_once($rendererfile);
            }
        }

        if (empty($subtype)) {
            $class = $component . '_renderer';
        } else {
            $class = $component . '_' . $subtype . '_renderer';
        }
        return $class;
    }
}


/**
 * This is the default renderer factory for Moodle. It simply returns an instance
 * of the appropriate standard renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class standard_renderer_factory extends renderer_factory_base {
    /**
     * Implement the subclass method
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null) {
        $classname = $this->standard_renderer_classname($component, $subtype);
        if (!class_exists($classname)) {
            throw new coding_exception('Request for an unknown renderer class ' . $classname);
        }

        list($target, $suffix) = $this->get_target_suffix($target);
        if (class_exists($classname . $suffix)) {
            // use the specialised renderer for given target, default renderer might also decide
            // to implement support for more targets
            $classname = $classname . $suffix;
        }

        return new $classname($page, $target);
    }
}


/**
 * This is renderer factory allows themes to override the standard renderers using
 * php code.
 *
 * It will load any code from theme/mytheme/renderers.php and
 * theme/parenttheme/renderers.php, if then exist. Then whenever you ask for
 * a renderer for 'component', it will create a mytheme_component_renderer or a
 * parenttheme_component_renderer, instead of a component_renderer,
 * if either of those classes exist.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class theme_overridden_renderer_factory extends renderer_factory_base {

    protected $prefixes = array();

    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     */
    public function __construct(theme_config $theme) {
        parent::__construct($theme);
        // Initialise $this->prefixes.
        $this->prefixes = $theme->renderer_prefixes();
    }

    /**
     * Implement the subclass method
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null) {
        $classname = $this->standard_renderer_classname($component, $subtype);
        if (!class_exists($classname)) {
            // standard renderer must always exist
            throw new coding_exception('Request for an unknown renderer class ' . $classname);
        }

        list($target, $suffix) = $this->get_target_suffix($target);

        // theme lib.php and renderers.php files are loaded automatically
        // when loading the theme configs

        // first try the renderers with correct suffix
        foreach ($this->prefixes as $prefix) {
            if (class_exists($prefix . '_' . $classname . $suffix)) {
                $classname = $prefix . '_' . $classname . $suffix;
                return new $classname($page, $target);
            }
        }
        if (class_exists($classname . $suffix)) {
            // use the specialised renderer for given target, default renderer might also decide
            // to implement support for more targets
            $classname = $classname . $suffix;
            return new $classname($page, $target);
        }

        // then try general renderer
        foreach ($this->prefixes as $prefix) {
            if (class_exists($prefix . '_' . $classname)) {
                $classname = $prefix . '_' . $classname;
                return new $classname($page, $target);
            }
        }

        return new $classname($page, $target);
    }
}
