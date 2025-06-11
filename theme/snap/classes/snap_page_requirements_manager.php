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
 * Snap page requirements manager.
 * Required for blacklisting core javascript / css.
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap;

defined('MOODLE_INTERNAL') || die();

class snap_page_requirements_manager extends \page_requirements_manager {
    /**
     * Ensure that the specified JavaScript function is called from an inline script
     * from page footer.
     *
     * @param string $function the name of the JavaScritp function to with init code,
     *      usually something like 'M.mod_mymodule.init'
     * @param array $extraarguments and array of arguments to be passed to the function.
     *      The first argument is always the YUI3 Y instance with all required dependencies
     *      already loaded.
     * @param bool $ondomready wait for dom ready (helps with some IE problems when modifying DOM)
     * @param array $module JS module specification array
     */
    public function js_init_call($function, array $extraarguments = null, $ondomready = false, array $module = null) {
        $blacklist = [
            'M.core_completion.init',
        ];
        if (in_array($function, $blacklist)) {
            return;
        }
        parent::js_init_call($function, $extraarguments, $ondomready, $module);
    }

    /**
     * If the $PAGE requirement manager has already been utilised we need to copy those requirements into
     * the snap_page_requirements_manager.
     */
    public function copy_page_requirements() {
        global $PAGE;

        // Modify $PAGE to use snap requirements manager.
        $requires = new \ReflectionProperty($PAGE, '_requires');
        $requires->setAccessible(true);

        $pmanreflect = new \ReflectionClass($PAGE->requires);
        $props = $pmanreflect->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $pname = $prop->getName();
            $pval = $prop->getValue($PAGE->requires);
            if ($pval === null) {
                continue;
            }

            $snapmanprop = new \ReflectionProperty($this, $pname);
            // If the property is private or protected  set accessible, after the copy reset to not accessible.
            $isprotected = $snapmanprop->isPrivate() || $snapmanprop->isProtected();
            if ($isprotected) {
                $snapmanprop->setAccessible(true);
            }
            $snapmanprop->setValue($this, $pval);
            $snapmanprop->setAccessible(!$isprotected);
        }
        $requires->setValue($PAGE, $this);
        $requires->setAccessible(false);
    }
}
