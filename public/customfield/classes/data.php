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

namespace core_customfield;

use core\persistent;

/**
 * Data persistent class
 *
 * @package   core_customfield
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data extends persistent {

    /**
     * Database data.
     */
    const TABLE = 'customfield_data';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return array(
                'fieldid'        => [
                        'type' => PARAM_INT,
                        'optional' => false,
                        'null'     => NULL_NOT_ALLOWED
                ],
                'instanceid'       => [
                        'type' => PARAM_INT,
                        'optional' => false,
                        'null'     => NULL_NOT_ALLOWED
                ],
                'intvalue'       => [
                        'type'     => PARAM_INT,
                        'optional' => true,
                        'default'  => null,
                        'null'     => NULL_ALLOWED
                ],
                'decvalue'       => [
                        'type'     => PARAM_FLOAT,
                        'optional' => true,
                        'default'  => null,
                        'null'     => NULL_ALLOWED
                ],
                'charvalue'      => [
                        'type'     => PARAM_TEXT,
                        'optional' => true,
                        'default'  => null,
                        'null'     => NULL_ALLOWED
                ],
                'shortcharvalue' => [
                        'type'     => PARAM_TEXT,
                        'optional' => true,
                        'default'  => null,
                        'null'     => NULL_ALLOWED
                ],
                // Mandatory field.
                'value'          => [
                        'type'    => PARAM_RAW,
                        'null'    => NULL_NOT_ALLOWED,
                        'default' => ''
                ],
                // Mandatory field.
                'valueformat'    => [
                        'type'    => PARAM_INT,
                        'null'    => NULL_NOT_ALLOWED,
                        'default' => FORMAT_MOODLE,
                        'optional' => true
                ],
                'valuetrust' => [
                    'type' => PARAM_BOOL,
                    'null' => NULL_NOT_ALLOWED,
                    'default' => false,
                    'optional' => true,
                ],
                'contextid'      => [
                        'type'     => PARAM_INT,
                        'optional' => false,
                        'null'     => NULL_NOT_ALLOWED
                ],
                'component' => [
                        'type' => PARAM_COMPONENT,
                ],
                'area' => [
                        'type' => PARAM_COMPONENT,
                ],
                'itemid' => [
                        'type' => PARAM_INT,
                        'optional' => true,
                        'default' => 0,
                ],
        );
    }

    /**
     * For integer data field, persistent won't allow empty string, swap for null
     *
     * @param string|null $value
     * @return self
     */
    protected function set_intvalue(?string $value): self {
        $value = (string) $value === '' ? null : (int) $value;
        return $this->raw_set('intvalue', $value);
    }

    /**
     * For decimal data field, persistent won't allow empty string, swap for null
     *
     * @param string|null $value
     * @return self
     */
    protected function set_decvalue(?string $value): self {
        $value = (string) $value === '' ? null : (float) $value;
        return $this->raw_set('decvalue', $value);
    }

    /**
     * Ensure value field observes non-nullability
     *
     * @param string|null $value
     * @return self
     */
    protected function set_value(?string $value): self {
        return $this->raw_set('value', (string) $value);
    }
}
