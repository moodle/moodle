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

use core\exception\coding_exception;
use core\output\renderer_base;
use core\output\theme_config;
use moodle_page;

/**
 * This is renderer factory allows themes to override the standard renderers using php code.
 *
 * It will load any code from theme/mytheme/renderers.php and
 * theme/parenttheme/renderers.php, if then exist. Then whenever you ask for
 * a renderer for 'component', it will create a mytheme_component_renderer or a
 * parenttheme_component_renderer, instead of a component_renderer,
 * if either of those classes exist.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class theme_overridden_renderer_factory extends renderer_factory_base {
    /**
     * @var array An array of renderer prefixes
     */
    protected $prefixes = [];

    /**
     * Constructor.
     * @param theme_config $theme the theme we are rendering for.
     */
    public function __construct(theme_config $theme) {
        parent::__construct($theme);
        // Initialise $this->prefixes.
        $this->prefixes = $theme->renderer_prefixes();
    }

    /**
     * Implement the subclass method
     *
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return renderer_base an object implementing the requested renderer interface.
     */
    public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null) {
        $classnames = $this->standard_renderer_classnames($component, $subtype);

        [$target, $suffix] = $this->get_target_suffix($target);

        // Theme lib.php and renderers.php files are loaded automatically
        // when loading the theme configs.

        // First try the renderers with correct suffix.
        foreach ($this->prefixes as $prefix) {
            foreach ($classnames as $classnamedetails) {
                if ($classnamedetails['validwithprefix']) {
                    if ($classnamedetails['autoloaded']) {
                        $newclassname = $prefix . $classnamedetails['classname'] . $suffix;
                    } else {
                        $newclassname = $prefix . '_' . $classnamedetails['classname'] . $suffix;
                    }
                    if (class_exists($newclassname)) {
                        return new $newclassname($page, $target);
                    }
                }
            }
        }
        foreach ($classnames as $classnamedetails) {
            if ($classnamedetails['validwithoutprefix']) {
                $newclassname = $classnamedetails['classname'] . $suffix;
                if (class_exists($newclassname)) {
                    // Use the specialised renderer for given target, default renderer might also decide
                    // to implement support for more targets.
                    return new $newclassname($page, $target);
                }
            }
        }

        // Then try general renderer.
        foreach ($this->prefixes as $prefix) {
            foreach ($classnames as $classnamedetails) {
                if ($classnamedetails['validwithprefix']) {
                    if ($classnamedetails['autoloaded']) {
                        $newclassname = $prefix . $classnamedetails['classname'];
                    } else {
                        $newclassname = $prefix . '_' . $classnamedetails['classname'];
                    }
                    if (class_exists($newclassname)) {
                        return new $newclassname($page, $target);
                    }
                }
            }
        }

        // Final attempt - no prefix or suffix.
        foreach ($classnames as $classnamedetails) {
            if ($classnamedetails['validwithoutprefix']) {
                $newclassname = $classnamedetails['classname'];
                if (class_exists($newclassname)) {
                    return new $newclassname($page, $target);
                }
            }
        }
        throw new coding_exception('Request for an unknown renderer ' . $component . ', ' . $subtype . ', ' . $target);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(theme_overridden_renderer_factory::class, \theme_overridden_renderer_factory::class);
