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
 * Time Series Database log store.
 *
 * @package    logstore_tsdb
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_tsdb\log;

defined('MOODLE_INTERNAL') || die();

/**
 * TSDB log store class.
 *
 * Implements writer interface to store Moodle events in a Time Series Database.
 *
 * @package    logstore_tsdb
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store implements \tool_log\log\writer {

    /** @var \tool_log\log\manager Log manager instance */
    protected $manager;

    /** @var array Plugin configuration */
    protected $config;

    /** @var mixed TSDB client connection */
    protected $client;

    /** @var array Event buffer for async writes */
    protected $buffer = [];

    /** @var int Last buffer flush timestamp */
    protected $lastflush = 0;

    /**
     * Constructor.
     *
     * @param \tool_log\log\manager $manager Log manager instance
     */
    public function __construct(\tool_log\log\manager $manager) {
        $this->manager = $manager;
        $this->load_config();
        $this->init_client();
    }

    /**
     * Load plugin configuration.
     */
    protected function load_config() {
        $this->config = [
            'tsdb_type' => $this->get_config('tsdb_type', 'timescaledb'),
            'host' => $this->get_config('host', 'localhost'),
            'port' => $this->get_config('port', '5433'),
            'database' => $this->get_config('database', 'moodle_logs_tsdb'),
            'username' => $this->get_config('username', 'moodleuser'),
            'password' => $this->get_config('password', ''),
            'writemode' => $this->get_config('writemode', 'async'),
            'buffersize' => $this->get_config('buffersize', 1000),
            'flushinterval' => $this->get_config('flushinterval', 60),
        ];
    }

    /**
     * Initialize TSDB client connection.
     */
    protected function init_client() {
        try {
            if ($this->config['tsdb_type'] === 'timescaledb') {
                // Initialize TimescaleDB client.
                require_once(__DIR__ . '/../client/timescaledb_client.php');
                $this->client = new \logstore_tsdb\client\timescaledb_client($this->config);

                // Verify connection.
                if ($this->client->is_connected()) {
                    debugging('TimescaleDB client initialized successfully', DEBUG_DEVELOPER);
                } else {
                    debugging('TimescaleDB client connected but health check failed', DEBUG_NORMAL);
                }
            } else {
                // Future support for other TSDBs (InfluxDB, etc.)
                debugging("TSDB type '{$this->config['tsdb_type']}' not yet supported", DEBUG_NORMAL);
            }
        } catch (\Exception $e) {
            // Log error but don't fail - plugin can still work in degraded mode.
            debugging('Error initializing TSDB client: ' . $e->getMessage(), DEBUG_NORMAL);
            $this->client = null;
        }
    }

    /**
     * Write event to TSDB.
     *
     * @param \core\event\base $event Event to store
     * @param \tool_log\log\manager $manager Log manager instance
     */
    public function write(\core\event\base $event, \tool_log\log\manager $manager) {
        // Ignore anonymous events.
        if ($event->anonymous) {
            return;
        }

        // Transform event to TSDB format.
        $datapoint = $this->transform_event($event);

        if ($this->config['writemode'] === 'sync') {
            // Synchronous write - immediate.
            $this->write_datapoint($datapoint);
        } else {
            // Asynchronous write - buffer.
            $this->buffer_event($datapoint);
        }
    }

    /**
     * Transform Moodle event to TSDB datapoint format.
     *
     * @param \core\event\base $event Moodle event
     * @return array Formatted datapoint
     */
    protected function transform_event(\core\event\base $event) {
        global $USER;

        return [
            'measurement' => 'moodle_events',
            'tags' => [
                'eventname' => $event->eventname,
                'component' => $event->component,
                'action' => $event->action,
                'target' => $event->target,
                'crud' => $event->crud,
                'edulevel' => (string)$event->edulevel,
                'courseid' => (string)$event->courseid,
            ],
            'fields' => [
                'userid' => $event->userid,
                'contextid' => $event->contextid,
                'contextlevel' => $event->contextlevel,
                'contextinstanceid' => $event->contextinstanceid,
                'objectid' => $event->objectid ?? null,
                'objecttable' => $event->objecttable ?? null,
                'relateduserid' => $event->relateduserid ?? null,
                'realuserid' => !empty($event->realuserid) ? $event->realuserid : null,
                'anonymous' => $event->anonymous ? 1 : 0,
                'ip' => getremoteaddr(),
                'origin' => $event->origin ?? 'web',
                'other' => !empty($event->other) ? json_encode($event->other) : null,
            ],
            'timestamp' => $event->timecreated,
        ];
    }

    /**
     * Write single datapoint to TSDB.
     *
     * @param array $datapoint Formatted datapoint
     * @return bool Success status
     */
    protected function write_datapoint($datapoint) {
        if (!$this->client) {
            debugging('TSDB client not initialized, cannot write event', DEBUG_DEVELOPER);
            return false;
        }

        try {
            $success = $this->client->write_point($datapoint);

            if (!$success) {
                debugging('Failed to write event to TSDB', DEBUG_DEVELOPER);
            }

            return $success;

        } catch (\Exception $e) {
            debugging('Error writing to TSDB: ' . $e->getMessage(), DEBUG_NORMAL);
            return false;
        }
    }

    /**
     * Add event to buffer for asynchronous writing.
     *
     * @param array $datapoint Formatted datapoint
     */
    protected function buffer_event($datapoint) {
        $this->buffer[] = $datapoint;

        // Check if buffer should be flushed.
        if (count($this->buffer) >= $this->config['buffersize'] ||
            (time() - $this->lastflush) >= $this->config['flushinterval']) {
            $this->flush_buffer();
        }
    }

    /**
     * Flush buffered events to TSDB.
     *
     * @return bool Success status
     */
    protected function flush_buffer() {
        if (empty($this->buffer)) {
            return true;
        }

        if (!$this->client) {
            debugging('TSDB client not initialized, discarding ' . count($this->buffer) . ' buffered events', DEBUG_NORMAL);
            $this->buffer = [];
            return false;
        }

        try {
            $count = count($this->buffer);
            debugging("Flushing $count buffered events to TSDB", DEBUG_DEVELOPER);

            // Batch write all buffered events.
            $success = $this->client->write_points($this->buffer);

            if ($success) {
                debugging("Successfully flushed $count events to TSDB", DEBUG_DEVELOPER);
                $this->buffer = [];
                $this->lastflush = time();
                return true;
            } else {
                debugging("Failed to flush $count events to TSDB", DEBUG_NORMAL);
                // Keep buffer for retry on next attempt.
                return false;
            }

        } catch (\Exception $e) {
            debugging('Error flushing buffer to TSDB: ' . $e->getMessage(), DEBUG_NORMAL);
            // On critical error, discard buffer to prevent memory issues.
            $this->buffer = [];
            return false;
        }
    }

    /**
     * Cleanup - flush any remaining buffered events.
     */
    public function dispose() {
        // Flush any remaining buffered events before closing.
        $this->flush_buffer();

        // Close TSDB connection.
        if ($this->client && method_exists($this->client, 'close')) {
            $this->client->close();
            debugging('TSDB connection closed on dispose', DEBUG_DEVELOPER);
        }
    }

    /**
     * Helper to get plugin configuration.
     *
     * @param string $name Config name
     * @param mixed $default Default value
     * @return mixed Config value
     */
    protected function get_config($name, $default = null) {
        $value = get_config('logstore_tsdb', $name);
        return ($value !== false) ? $value : $default;
    }
}
