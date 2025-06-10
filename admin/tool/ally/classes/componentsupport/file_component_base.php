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
 * Base class for processing module html.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

/**
 * Base class for processing module html.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class file_component_base extends component_base {

    /**
     * @var string
     */
    protected $oldfilename;

    /**
     * @var \stored_file
     */
    protected $file;

    /**
     * @param \stored_file $file
     * @throws \coding_exception
     */
    private function validate_file_component(\stored_file $file) {
        $class = get_class($this);
        $namespacedel = strrpos($class, '\\');
        if ($namespacedel !== false ) {
            $class = substr($class, $namespacedel + 1);
        }
        if ($this->component_type() === self::TYPE_MOD) {
            $modcheck = 'mod_';
        } else {
            $modcheck = '';
        }
        $modcheck .= substr($class, 0, strrpos($class, '_'));
        if ($modcheck !== $file->get_component()) {
            throw new \coding_exception('Using incorrect module support class ('.$class.') for file with component '.
                $file->get_component());
        }
    }

    /**
     * @param string $oldfilename
     * @param \stored_file $file
     * @return void
     */
    public function setup_file_and_validate($oldfilename, \stored_file $file) {
        $this->oldfilename = $oldfilename;
        $this->file = $file;
        $this->validate_file_component($file);
    }

    /**
     * Replace file links.
     */
    abstract public function replace_file_links();
}
