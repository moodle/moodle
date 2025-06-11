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
use moodle_page;

/**
 * This is the default renderer factory for Moodle.
 *
 * It simply returns an instance of the appropriate standard renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class standard_renderer_factory extends renderer_factory_base {
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
        $classname = '';

        [$target, $suffix] = $this->get_target_suffix($target);
        // First look for a version with a suffix.
        foreach ($classnames as $classnamedetails) {
            if ($classnamedetails['validwithoutprefix']) {
                $newclassname = $classnamedetails['classname'] . $suffix;
                if (class_exists($newclassname)) {
                    $classname = $newclassname;
                    break;
                } else {
                    $newclassname = $classnamedetails['classname'];
                    if (class_exists($newclassname)) {
                        $classname = $newclassname;
                        break;
                    }
                }
            }
        }
        // Now look for a non-suffixed version.
        if (empty($classname)) {
            foreach ($classnames as $classnamedetails) {
                if ($classnamedetails['validwithoutprefix']) {
                    $newclassname = $classnamedetails['classname'];
                    if (class_exists($newclassname)) {
                        $classname = $newclassname;
                        break;
                    }
                }
            }
        }

        if (empty($classname)) {
            // Standard renderer must always exist.
            throw new coding_exception('Request for an unknown renderer class. Searched for: ' . var_export($classnames, true));
        }

        return new $classname($page, $target);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(standard_renderer_factory::class, \standard_renderer_factory::class);
