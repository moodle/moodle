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

namespace core\navigation;

use core\exception\coding_exception;

/**
 * Subclass of navigation_node allowing different rendering for the breadcrumbs
 * in particular adding extra metadata for search engine robots to leverage.
 *
 * @package   core
 * @category  navigation
 * @copyright 2015 Brendan Heywood
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class breadcrumb_navigation_node extends navigation_node {
    /** @var $last boolean A flag indicating this is the last item in the list of breadcrumbs. */
    private $last = false;

    /**
     * A proxy constructor
     *
     * @param mixed $navnode A navigation_node or an array
     */
    public function __construct($navnode) {
        if (is_array($navnode)) {
            parent::__construct($navnode);
        } else if ($navnode instanceof navigation_node) {
            // Just clone everything.
            $objvalues = get_object_vars($navnode);
            foreach ($objvalues as $key => $value) {
                 $this->$key = $value;
            }
        } else {
            throw new coding_exception('Not a valid breadcrumb_navigation_node');
        }
    }

    /**
     * Getter for "last"
     * @return boolean
     */
    public function is_last() {
        return $this->last;
    }

    /**
     * Setter for "last"
     * @param $val boolean
     */
    public function set_last($val) {
        $this->last = $val;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(breadcrumb_navigation_node::class, \breadcrumb_navigation_node::class);
