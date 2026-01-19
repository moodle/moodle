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

use aiprovider_awsbedrock\aimodel\ai21;
use aiprovider_awsbedrock\aimodel\amazon;
use aiprovider_awsbedrock\aimodel\anthropic;
use aiprovider_awsbedrock\aimodel\meta;
use aiprovider_awsbedrock\aimodel\mistral;
use aiprovider_awsbedrock\aimodel\stability;
use aiprovider_awsbedrock\model_definition;

/**
 * AWS Bedrock model registry.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class model_registry {
    /** @var array<string, model_definition>|null Cached model map. */
    private static ?array $modelmap = null;

    /**
     * Get all registered models.
     *
     * @return model_definition[]
     */
    public static function get_models(): array {
        return array_values(self::get_model_map());
    }

    /**
     * Get a model by model id.
     *
     * @param string $modelname Model id.
     * @return model_definition|null
     */
    public static function get_model(string $modelname): ?model_definition {
        return self::get_model_map()[$modelname] ?? null;
    }

    /**
     * Get model map keyed by model id.
     *
     * @return array<string, model_definition>
     */
    private static function get_model_map(): array {
        if (self::$modelmap !== null) {
            return self::$modelmap;
        }

        $catalogs = [
            ai21::class,
            amazon::class,
            anthropic::class,
            meta::class,
            mistral::class,
            stability::class,
        ];

        $modelmap = [];
        foreach ($catalogs as $catalog) {
            foreach ($catalog::get_models() as $modelname => $modeldefinition) {
                if (isset($modelmap[$modelname])) {
                    throw new \coding_exception("Duplicate model definition for '{$modelname}'.");
                }
                $modelmap[$modelname] = $modeldefinition;
            }
        }

        self::$modelmap = $modelmap;
        return self::$modelmap;
    }
}
