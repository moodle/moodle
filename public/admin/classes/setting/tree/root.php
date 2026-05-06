<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\tree;

/**
 * Root of admin settings tree, does not have any parent.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class root extends \core_admin\setting\tree\category {
    /** @var array List of errors */
    public $errors;
    /** @var string search query */
    public $search;
    /** @var bool full tree flag - true means all settings required, false only pages required */
    public $fulltree;
    /** @var bool flag indicating loaded tree */
    public $loaded;
    /** @var mixed site custom defaults overriding defaults in settings files*/
    public $custom_defaults; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /**
     * Constructor for the root of admin settings tree.
     *
     * @param bool $fulltree Whether all settings are required.
     *      If false, only pages are required.
     */
    public function __construct($fulltree) {
        global $CFG;

        parent::__construct('root', get_string('administration'), false);
        $this->errors   = [];
        $this->search   = '';
        $this->fulltree = $fulltree;
        $this->loaded   = false;

        $this->category_cache = [];

        // Load custom defaults if found.
        $this->custom_defaults = null;
        $defaultsfile = "$CFG->dirroot/local/defaults.php";
        if (is_readable($defaultsfile)) {
            $defaults = [];
            include($defaultsfile);
            if (is_array($defaults) && count($defaults)) {
                $this->custom_defaults = $defaults;
            }
        }
    }

    /**
     * Empties children array, and sets loaded to false
     *
     * @param bool $requirefulltree
     */
    public function purge_children($requirefulltree) {
        $this->children = [];
        $this->fulltree = ($requirefulltree || $this->fulltree);
        $this->loaded   = false;
        // Break circular dependencies - this helps PHP 5.2.
        while ($this->category_cache) {
            array_pop($this->category_cache);
        }
        $this->category_cache = [];
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(root::class, \admin_root::class);
