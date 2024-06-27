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

use moodle_page;
use core\output\renderer_base;

/**
 * A renderer factory is just responsible for creating an appropriate renderer
 * for any given part of Moodle.
 *
 * Which renderer factory to use is chose by the current theme, and an instance
 * if created automatically when the theme is set up.
 *
 * A renderer factory must also have a constructor that takes a theme_config object.
 * (See {@see renderer_factory_base::__construct} for an example.)
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
interface renderer_factory_interface {
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
     * @return renderer_base an object implementing the requested renderer interface.
     */
    public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null);
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(renderer_factory_interface::class, \renderer_factory::class);
