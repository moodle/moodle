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
 * Levels info writer.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use block_xp\external\external_api;
use block_xp\external\external_multiple_structure;
use block_xp\external\external_single_structure;
use block_xp\external\external_value;
use block_xp\local\backup\restore_context;
use block_xp\local\config\config;
use block_xp\local\course_world;
use block_xp\local\world;
use core_collator;
use core_text;
use invalid_parameter_exception;

/**
 * Levels info writer.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class levels_info_writer {

    /** @var config The admin config. */
    protected $config;

    /**
     * Constructor.
     *
     * @param config $config The admin config.
     */
    public function __construct(config $config) {
        $this->config = $config;
    }

    /**
     * Save defaults.
     *
     * @param array $rawdata The raw data.
     */
    public function save_defaults($rawdata) {
        $data = $this->validate_raw_data($rawdata);

        $finalpoints = $this->process_points($data);
        $finalmetadata = $this->process_metadata($data);
        $finalalgo = $this->process_algo($data);

        $finaldata = $this->construct_finaldata([
            'points' => $finalpoints,
            'metadata' => $finalmetadata,
            'algo' => $finalalgo,
        ]);

        $this->config->set('levelsdata', json_encode($finaldata));
    }

    /**
     * Save for a world.
     *
     * @param world $world The world.
     * @param array $rawdata The raw data.
     */
    public function save_for_world(world $world, $rawdata) {
        if (!$world instanceof course_world) {
            throw new \coding_exception('Type of world not handled.');
        }

        $data = $this->validate_raw_data($rawdata);

        $finalpoints = $this->process_points($data);
        $finalmetadata = $this->process_metadata($data, $world);
        $finalalgo = $this->process_algo($data);

        $finaldata = $this->construct_finaldata([
            'points' => $finalpoints,
            'metadata' => $finalmetadata,
            'algo' => $finalalgo,
        ]);

        $world->get_config()->set('levelsdata', json_encode($finaldata));
    }

    /**
     * Update world after restore.
     *
     * @param restore_context $restore The context.
     * @param world $world The world.
     */
    public function update_world_after_restore(restore_context $restore, world $world) {

        // We don't have levels data, or it's invalid, do nothing.
        $levelsdata = json_decode($world->get_config()->get('levelsdata'), true);
        if (!$levelsdata) {
            return;
        }

        $parts = $this->deconstruct_finaldata($levelsdata);
        $parts['metadata'] = $this->process_metadata_after_restore($restore, $parts['metadata'], $world);
        $finaldata = $this->construct_finaldata($parts);

        $world->get_config()->set('levelsdata', json_encode($finaldata));
    }

    /**
     * Constructor the final data.
     *
     * @param array $parts Data parts.
     * @return array
     */
    protected function construct_finaldata($parts) {
        $finaldata = [
            'v' => 2,
            'xp' => $parts['points'],
        ];

        // Add the algo.
        $algo = $parts['algo'] ?? null;
        if (!empty($algo)) {
            $finaldata['algo'] = $algo;
        }

        // Reorganise metadata per key.
        $metadata = $parts['metadata'] ?? [];
        foreach ($metadata as $level => $levelmetadata) {
            if (empty($levelmetadata)) {
                continue;
            }
            foreach ($levelmetadata as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                if (!isset($finaldata[$key])) {
                    $finaldata[$key] = [];
                }
                $finaldata[$key][$level] = $value;
            }
        }

        return $finaldata;
    }

    /**
     * Deconstruct the final data.
     *
     * @param array $data The constructed data.
     * @return array The parts.
     */
    protected function deconstruct_finaldata($data) {
        $version = (int) ($data['v'] ?? 1);

        $nonmetakeys = ['v', 'xp', 'algo'];
        if ($version < 2) {
            $nonmetakeys = array_merge($nonmetakeys, ['base', 'coef', 'usealgo']);
        }

        $metadata = [];
        $metadatakeys = array_diff(array_keys($data), $nonmetakeys);
        foreach ($metadatakeys as $metakey) {
            foreach (($data[$metakey] ?? []) as $level => $value) {
                if (!isset($metadata[$level])) {
                    $metadata[$level] = [];
                }
                $metadata[$level][$metakey] = $value;
            }
        }

        // Extract the algorithm. Before the version 2, the parameters of the algorithm were defined
        // differently. You may want to check algo_levels_info for more defaults. Because we're expecting
        // the final data here, we must convert that to the new format.
        $algo = $data['algo'] ?? null;
        if (!$algo && $version < 2) {
            $algo = [
                'base' => $data['base'] ?? algo_levels_info::DEFAULT_BASE,
                'coef' => $data['coef'] ?? algo_levels_info::DEFAULT_COEF,
                'incr' => algo_levels_info::DEFAULT_INCR,
                'method' => 'relative',
            ];
        }

        return [
            'points' => array_values($data['xp']), // Forced indexation at zero as it wasn't the case previously.
            'algo' => $algo,
            'metadata' => $metadata,
        ];
    }

    /**
     * Get the metadata for the level.
     *
     * @param int $level The level number.
     * @param array $metadata The metadata before processing.
     * @param world|null $world The world, if any.
     */
    protected function get_metadata_for_level($level, $metadata, world $world = null) {

        // We can only deal with this type of world at the moment.
        $world = $world instanceof course_world ? $world : null;

        $finaldata = [];
        if (!empty($metadata['name'])) {
            $finaldata['name'] = core_text::substr($metadata['name'], 0, 40);
        }

        if (!empty($metadata['description'])) {
            $finaldata['desc'] = core_text::substr($metadata['description'], 0, 280);
        }

        return $finaldata;
    }

    /**
     * Get the metadata for the level after restore.
     *
     * @param restore_context $restore The context.
     * @param int $level The level number.
     * @param array $metadata The metadata before processing.
     * @param world|null $world The world, if any.
     */
    protected function get_metadata_for_level_after_restore(restore_context $restore, $level, $metadata, world $world = null) {
        return $metadata;
    }

    /**
     * Process the algo.
     *
     * @param array $data The data.
     * @return array|null
     */
    protected function process_algo($data) {
        if (empty($data['algo'])) {
            return null;
        }
        $algo = $data['algo'];
        return [
            'base' => min(max(1, $algo['base'] ?? 120), PHP_INT_MAX),
            'coef' => min(max(1, $algo['coef'] ?? 1.3), PHP_INT_MAX),
            'incr' => min(max(0, $algo['incr'] ?? 40), PHP_INT_MAX),
            'method' => !in_array($algo['method'] ?? '', ['flat', 'linear', 'relative']) ? 'relative' : $algo['method'],
        ];
    }

    /**
     * Process the metadata.
     *
     * @param array $data All the data.
     * @param world|null $world The world, if any.
     * @return array Indexed by level.
     */
    protected function process_metadata($data, world $world = null) {

        // Reorganise metadata per level.
        $rawmetadata = array_reduce($data['levels'], function($carry, $level) {
            $carry[$level['level']] = array_reduce($level['metadata'], function($carry, $item) {
                $carry[$item['name']] = $item['value'];
                return $carry;
            }, []);
            return $carry;
        }, []);

        // Construct all the metadata.
        $finalmetadata = [];
        foreach ($rawmetadata as $level => $metadata) {
            if ($level < 1  || $level > 99) {
                continue;
            }
            $tmp = $this->get_metadata_for_level($level, $metadata, $world);
            $finalmetadata[$level] = array_filter($tmp);
        }

        return $finalmetadata;
    }

    /**
     * Process the metadata after restore.
     *
     * @param restore_context $restore The context.
     * @param array $metadata Indexed by level.
     * @param world|null $world The world, if any.
     * @return array Indexed by level.
     */
    protected function process_metadata_after_restore(restore_context $restore, $metadata, world $world = null) {
        $finalmetadata = [];
        foreach ($metadata as $level => $levelmetadata) {
            $tmp = $this->get_metadata_for_level_after_restore($restore, $level, $levelmetadata, $world);
            $finalmetadata[$level] = array_filter($tmp);
        }
        return $finalmetadata;
    }

    /**
     * Get and normalise the level points.
     *
     * @param array $data The data.
     * @return int[] The points, indexed at 0.
     */
    protected function process_points($data) {

        // Get the levels, and ensure order ascending.
        $levels = $data['levels'];
        core_collator::asort_array_of_arrays_by_key($levels, 'level', core_collator::SORT_NUMERIC);
        $points = array_values(array_map(function($level) {
            return $level['xprequired'];
        }, $levels));

        // Ensure the points are increasing.
        return array_reduce(array_keys($points), function($carry, $idx) use ($points) {
            $xp = $points[$idx];
            $prevxp = array_key_exists($idx - 1, $carry) ? $carry[$idx - 1] : null;

            if ($prevxp === null) {
                $xp = 0;
            } else {
                $xp = min(max($prevxp + 1, $xp), PHP_INT_MAX);
            }

            $carry[$idx] = $xp;
            return $carry;
        }, []);
    }

    /**
     * Validate and return the metadata from raw data.
     *
     * @param array $rawdata The raw data.
     * @return array
     */
    protected function validate_raw_data($rawdata) {
        $structure = new external_single_structure([
            'levels' => new external_multiple_structure(new external_single_structure([
                'level' => new external_value(PARAM_INT),
                'xprequired' => new external_value(PARAM_INT),
                'metadata' => new external_multiple_structure(new external_single_structure([
                    'name' => new external_value(PARAM_ALPHAEXT),
                    'value' => new external_value(PARAM_RAW, '', VALUE_OPTIONAL, null),
                ]), '', VALUE_DEFAULT, []),

                // Kept for backwards compatibility, but no longer used.
                'name' => new external_value(PARAM_NOTAGS, '', VALUE_DEFAULT, ''),
                'description' => new external_value(PARAM_NOTAGS, '', VALUE_DEFAULT, ''),
            ])),
            'algo' => new external_single_structure([
                'method' => new external_value(PARAM_ALPHANUMEXT),
                'base' => new external_value(PARAM_INT),
                'incr' => new external_value(PARAM_INT),
                'coef' => new external_value(PARAM_FLOAT),
            ], '', VALUE_OPTIONAL, null),
        ]);

        $data = external_api::validate_parameters($structure, $rawdata);
        if (count($data['levels']) < 2 || count($data['levels']) > 99) {
            throw new invalid_parameter_exception('Invalid number of levels');
        }

        return $data;
    }

}
