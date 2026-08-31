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

namespace aiprovider_anthropic;

use aiprovider_anthropic\aimodel\claude_base;
use aiprovider_anthropic\aimodel\custommodel;
use core_ai\aimodel\base;

/**
 * Helper class for the Anthropic Claude provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /** @var string The default Claude model name used when no model is configured. */
    public const DEFAULT_MODEL = 'claude-sonnet-4-5-20250929';

    /**
     * Get the default Claude model name.
     *
     * @return string
     */
    public static function get_default_model(): string {
        return self::DEFAULT_MODEL;
    }

    /**
     * Get all Claude model classes.
     *
     * @return array Array of model class names.
     */
    public static function get_model_classes(): array {
        $models = [];
        $modelclasses = \core_component::get_component_classes_in_namespace('aiprovider_anthropic', 'aimodel');
        foreach ($modelclasses as $class => $path) {
            if (!class_exists($class)) {
                continue;
            }
            $reflection = new \ReflectionClass($class);
            if ($reflection->isAbstract() || $reflection->isInterface()) {
                continue;
            }
            if (!is_a($class, base::class, true)) {
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

    /**
     * Resolve a configured model name to the model that describes its behaviour.
     *
     * A model name that no bundled model class matches is an unlisted model entered by an
     * admin through the "Custom" option, so it falls back to the generic custom model.
     *
     * @param string $modelname Model name.
     * @return base&claude_base
     */
    public static function resolve_model(string $modelname): base&claude_base {
        $model = static::get_model_class($modelname);
        return $model instanceof claude_base ? $model : new custommodel();
    }
}
