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

use core\exception\coding_exception;
use moodle_page;

/**
 * Basis for all plugin renderers.
 *
 * @copyright Petr Skoda (skodak)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class plugin_renderer_base extends renderer_base {
    /**
     * @var renderer_base|core_renderer A reference to the current renderer.
     * The renderer provided here will be determined by the page but will in 90%
     * of cases by the {@see core_renderer}
     */
    protected $output;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        if (empty($target) && $page->pagelayout === 'maintenance') {
            // If the page is using the maintenance layout then we're going to force the target to maintenance.
            // This way we'll get a special maintenance renderer that is designed to block access to API's that are likely
            // unavailable for this page layout.
            $target = RENDERER_TARGET_MAINTENANCE;
        }
        $this->output = $page->get_renderer('core', null, $target);
        parent::__construct($page, $target);
    }

    /**
     * Renders the provided widget and returns the HTML to display it.
     *
     * @param renderable $widget instance with renderable interface
     * @return string
     */
    public function render(renderable $widget) {
        $classname = get_class($widget);

        // Strip namespaces.
        $classname = preg_replace('/^.*\\\/', '', $classname);

        // Keep a copy at this point, we may need to look for a deprecated method.
        $deprecatedmethod = "render_{$classname}";

        // Remove _renderable suffixes.
        $classname = preg_replace('/_renderable$/', '', $classname);
        $rendermethod = "render_{$classname}";

        if (method_exists($this, $rendermethod)) {
            // Call the render_[widget_name] function.
            // Note: This has a higher priority than the named_templatable to allow the theme to override the template.
            return $this->$rendermethod($widget);
        }

        if ($widget instanceof named_templatable) {
            // This is a named templatable.
            // Fetch the template name from the get_template_name function instead.
            // Note: This has higher priority than the deprecated method which is not overridable by themes anyway.
            return $this->render_from_template(
                $widget->get_template_name($this),
                $widget->export_for_template($this)
            );
        }

        if ($rendermethod !== $deprecatedmethod && method_exists($this, $deprecatedmethod)) {
            // This is exactly where we don't want to be.
            // If you have arrived here you have a renderable component within your plugin that has the name
            // blah_renderable, and you have a render method render_blah_renderable on your plugin.
            // In 2.8 we revamped output, as part of this change we changed slightly how renderables got rendered
            // and the _renderable suffix now gets removed when looking for a render method.
            // You need to change your renderers render_blah_renderable to render_blah.
            // Until you do this it will not be possible for a theme to override the renderer to override your method.
            // Please do it ASAP.
            static $debugged = [];
            if (!isset($debugged[$deprecatedmethod])) {
                debugging(sprintf(
                    'Deprecated call. Please rename your renderables render method from %s to %s.',
                    $deprecatedmethod,
                    $rendermethod
                ), DEBUG_DEVELOPER);
                $debugged[$deprecatedmethod] = true;
            }
            return $this->$deprecatedmethod($widget);
        }

        // Pass to core renderer if method not found here.
        // Note: this is not a parent. This is _new_ renderer which respects the requested format, and output type.
        return $this->output->render($widget);
    }

    /**
     * Magic method used to pass calls otherwise meant for the standard renderer
     * to it to ensure we don't go causing unnecessary grief.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments) {
        if (method_exists('renderer_base', $method)) {
            throw new coding_exception('Protected method called against ' . get_class($this) . ' :: ' . $method);
        }
        if (method_exists($this->output, $method)) {
            return call_user_func_array([$this->output, $method], $arguments);
        } else {
            throw new coding_exception('Unknown method called against ' . get_class($this) . ' :: ' . $method);
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(plugin_renderer_base::class, \plugin_renderer_base::class);
