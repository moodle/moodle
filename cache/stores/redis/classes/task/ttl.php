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

namespace cachestore_redis\task;

/**
 * Task deletes old data from Redis caches with TTL set.
 *
 * @package cachestore_redis
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ttl extends \core\task\scheduled_task {
    /** @var int Only display memory savings of at least 100 KB */
    const MIN_MEMORY_SIZE = 100 * 1024;

    /**
     * Gets the name of this task.
     *
     * @return string Task name
     */
    public function get_name(): string {
        return get_string('task_ttl', 'cachestore_redis');
    }

    /**
     * Executes the scheduled task.
     */
    public function execute(): void {
        // Find all Redis cache stores.
        $factory = \cache_factory::instance();
        $config = $factory->create_config_instance();
        $stores = $config->get_all_stores();
        $doneanything = false;
        foreach ($stores as $storename => $storeconfig) {
            if ($storeconfig['plugin'] !== 'redis') {
                continue;
            }

            // For each definition in the cache store, do TTL expiry if needed.
            $definitions = $config->get_definitions_by_store($storename);
            foreach ($definitions as $definition) {
                if (empty($definition['ttl'])) {
                    continue;
                }
                if (!empty($definition['requireidentifiers'])) {
                    // We can't make cache below if it requires identifiers.
                    continue;
                }
                $doneanything = true;
                $definitionname = $definition['component'] . '/' . $definition['area'];
                mtrace($definitionname, ': ');
                \cache::make($definition['component'], $definition['area']);
                $definition = $factory->create_definition($definition['component'], $definition['area']);
                $stores = $factory->get_store_instances_in_use($definition);
                foreach ($stores as $store) {
                    // These were all definitions using a Redis store but one definition may
                    // potentially have multiple stores, we need to process the Redis ones only.
                    if (!($store instanceof \cachestore_redis)) {
                        continue;
                    }
                    $info = $store->expire_ttl();
                    $infotext = 'Deleted ' . $info['keys'] . ' key(s) in ' .
                            sprintf('%0.2f', $info['time'])  . 's';
                    // Only report memory information if available, positive, and reasonably large.
                    // Otherwise the real information is hard to see amongst random variation etc.
                    if (!empty($info['memory']) && $info['memory'] > self::MIN_MEMORY_SIZE) {
                        $infotext .= ' - reported saving ' . display_size($info['memory']);
                    }
                    mtrace($infotext);
                }
            }
        }
        if (!$doneanything) {
            mtrace('No TTL caches assigned to a Redis store; nothing to do.');
        }
    }

    /**
     * Checks if this task is allowed to run - this makes it show the 'Run now' link (or not).
     *
     * @return bool True if task can run
     */
    public function can_run(): bool {
        // The default implementation of this function checks the plugin is enabled, which doesn't
        // seem to work (probably because cachestore plugins can't be enabled).
        // We could check if there is a Redis store configured, but it would have to do the exact
        // same logic as already in the first part of 'execute', so it's probably OK to just return
        // true.
        return true;
    }
}
