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

namespace aiprovider_ollama;

use core_ai\aimodel\base;

/**
 * Helper class for the Ollama provider.
 *
 * @package    aiprovider_ollama
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get all model classes.
     *
     * @return array Array of model classes.
     */
    public static function get_model_classes(): array {
        $models = [];
        $modelclasses = \core_component::get_component_classes_in_namespace('aiprovider_ollama', 'aimodel');
        foreach ($modelclasses as $class => $path) {
            if (!class_exists($class) || !is_a($class, base::class, true)) {
                throw new \coding_exception("Model class not valid: {$class}");
            }
            $models[] = $class;
        }
        return $models;
    }

    /**
     * Get model class by name.
     *
     * @param string $modelname Model name.
     * @return base|null
     */
    public static function get_model_class(string $modelname): ?base {
        foreach (static::get_model_classes() as $classname) {
            $model = new $classname();
            if ($model->get_model_name() === $modelname) {
                return $model;
            }
        }
        return null;
    }
}
