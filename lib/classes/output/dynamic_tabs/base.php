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

declare(strict_types=1);

namespace core\output\dynamic_tabs;

use core\exception\moodle_exception;
use core\output\templatable;

/**
 * Class tab_base
 *
 * @package     core
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base implements templatable {
    /** @var array */
    protected $data;

    /**
     * tab constructor.
     *
     * @param array $data
     */
    final public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * HTML "id" attribute that should be used for this tab, by default the last part of class name
     *
     * @return string
     */
    public function get_tab_id(): string {
        $parts = preg_split('/\\\\/', static::class);
        return array_pop($parts);
    }

    /**
     * The label to be displayed on the tab
     *
     * @return string
     */
    abstract public function get_tab_label(): string;

    /**
     * Check permission of the current user to access this tab
     *
     * @return bool
     */
    abstract public function is_available(): bool;

    /**
     * Check that tab is accessible, throw exception otherwise - used from WS requesting tab contents
     *
     * @throws moodle_exception
     */
    final public function require_access() {
        if (!$this->is_available()) {
            throw new moodle_exception('nopermissiontoaccesspage', 'error');
        }
    }

    /**
     * Template to use to display tab contents
     *
     * @return string
     */
    abstract public function get_template(): string;

    /**
     * Return tab data attributes
     *
     * @return array
     */
    public function get_data(): array {
        return $this->data;
    }

    /**
     * Add custom data to the tab data attributes
     *
     * @param array $data
     */
    public function add_data(array $data): void {
        $this->data = array_merge($this->data, $data);
    }
}
