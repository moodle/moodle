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

namespace core\output;

use moodle_page;

/**
 * Class renderer helper.
 *
 * This class is created to access output renderers from a
 * dependency injection container. $PAGE and $OUTPUT globals
 * haven't got a constant value, so we can't use them in
 * services or factories.
 *
 * this class wraps all the globals logic and provides a
 * way to access the renderers.
 *
 * @package    core
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer_helper {
    /**
     * Get the renderer for a particular part of Moodle.
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string|null $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return renderer_base
     */
    public function get_renderer(string $component, ?string $subtype = null): renderer_base {
        global $PAGE;
        return $PAGE->get_renderer($component);
    }

    /**
     * Get the core renderer.
     *
     * This method is a shortcut to get the core renderer with the correct
     * return type declaration. This way the IDE can provide better autocompletion.
     *
     * @return core_renderer
     */
    public function get_core_renderer(): core_renderer {
        global $PAGE;
        return $PAGE->get_renderer('core');
    }

    /**
     * Get the current page instance.
     *
     * @return moodle_page
     */
    public function get_page(): moodle_page {
        global $PAGE;
        return $PAGE;
    }
}
