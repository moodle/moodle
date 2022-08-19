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

require_once(__DIR__  . '/behat_form_field.php');

/**
 * Custom interaction with select_menu elements
 *
 * @package   core_form
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_select_menu extends behat_form_field {
    public function set_value($value) {
        self::require_javascript();

        $rootnode = $this->field->getParent();
        $options = $rootnode->findAll('css', '[role=option]');
        $this->field->click();
        foreach ($options as $option) {
            if (trim($option->getHtml()) == $value) {
                $option->click();
                break;
            }
        }
    }

    public function get_value() {
        $rootnode = $this->field->getParent();
        $input = $rootnode->find('css', 'input');
        return $input->getValue();
    }
}
