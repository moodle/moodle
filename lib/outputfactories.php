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
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($component, $page, $subtype=null);
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
    protected function standard_renderer_class_for_plugin($component, $subtype=null) {
        global $CFG; // needed in incldued files

        if ($component !== 'core') {
            // renderers are stored in lib.php files like the rest of standard functions and classes
            $libfile = get_component_directory($component) . '/renderer.php';
            if (file_exists($libfile)) {
                include_once($libfile);
            }
        }

        if (strpos($component, 'mod_') === 0) {
            $component = substr($component, 4);
        }
        if (is_null($subtype)) {
            $class = $component . '_renderer';
        } else {
            $class = $component . '_' . $subtype . '_renderer';
        }
        if (!class_exists($class)) {
            throw new coding_exception('Request for an unknown renderer class ' . $class);
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
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($component, $page, $subtype=null) {
        if ($component === 'core') {
            return new core_renderer($page);
        } else {
            $class = $this->standard_renderer_class_for_plugin($component, $subtype);
            return new $class($page, $this->get_renderer('core', $page));
        }
    }
}


/**
 * This is a slight variation on the standard_renderer_factory used by CLI scripts.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class cli_renderer_factory extends standard_renderer_factory {
    /**
     * Implement the subclass method
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($component, $page, $subtype=null) {
        if ($component === 'core') {
            return new cli_core_renderer($page);
        } else {
            parent::get_renderer($component, $page, $subtype);
        }
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
class theme_overridden_renderer_factory extends standard_renderer_factory {
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
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($component, $page, $subtype=null) {
        if (strpos($component, 'mod_') === 0) {
            $component = substr($component, 4);
        }

        foreach ($this->prefixes as $prefix) {
            // theme lib.php files are loaded automatically
            if (is_null($subtype)) {
                $classname = $prefix . '_' . $component . '_renderer';
            } else {
                $classname = $prefix . '_' . $component . '_' . $subtype . '_renderer';
            }
            if (class_exists($classname)) {
                if ($component === 'core') {
                    return new $classname($page);
                } else {
                    return new $classname($page, $this->get_renderer('core', $page));
                }
            }
        }
        // use standard renderes if themes do not contain overridden renderer
        return parent::get_renderer($component, $page, $subtype);
    }
}
