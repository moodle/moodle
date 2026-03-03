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

namespace aiprovider_awsbedrock;

use aiprovider_awsbedrock\model_definition;
use core_ai\aimodel\base;

/**
 * Helper class for the AWS Bedrock provider.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Get all model definitions.
     *
     * @return model_definition[] Array of model definitions.
     */
    public static function get_models(): array {
        return model_registry::get_models();
    }

    /**
     * Backward-compatible alias for model definitions.
     *
     * @return model_definition[] Array of model definitions.
     */
    public static function get_model_classes(): array {
        return static::get_models();
    }

    /**
     * Get model definition by name.
     *
     * @param string $modelname Model name.
     * @return base|null
     */
    public static function get_model_class(string $modelname): ?base {
        return model_registry::get_model($modelname);
    }
}
