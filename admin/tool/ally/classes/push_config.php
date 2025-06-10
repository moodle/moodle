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
 * Push file updates config.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Push file updates config.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class push_config {

    /**
     * Timeout to be used if config for tasks.
     */
    const TASKTIMEOUT = 60;

    /**
     * Timeout to be used if config is for live push.
     */
    const LIVETIMEOUT = 10;

    /**
     * Push file updates to this URL.
     *
     * @var string
     */
    private $url;

    /**
     * LTI consumer key.
     *
     * @var string
     */
    private $key;

    /**
     * LTI secret.
     *
     * @var string
     */
    private $secret;

    /**
     * @var int
     */
    private $batch;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $connecttimeout = 10;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var bool
     */
    private $clionly = false;

    /**
     * @var int
     */
    private $maxpushattempts = 3;

    /**
     * @param string|null $url
     * @param string|null $key
     * @param string|null $secret
     * @param int|null $batch
     */
    public function __construct($url = null, $key = null, $secret = null, $batch = null) {
        $this->url    = $url;
        $this->key    = $key;
        $this->secret = $secret;
        $this->batch  = $batch;

        $this->apply_default_configs();
    }

    private function apply_default_configs() {
        $config = get_config('tool_ally');
        if (empty($config)) {
            return;
        }
        if (is_null($this->url) && !empty($config->pushurl)) {
            $this->url = $config->pushurl;
        }
        if (is_null($this->key) && !empty($config->key)) {
            $this->key = $config->key;
        }
        if (is_null($this->secret) && !empty($config->secret)) {
            $this->secret = $config->secret;
        }
        if (is_null($this->batch)) {
            if (!empty($config->push_batch_size) && is_numeric($config->push_batch_size)) {
                $this->batch = (int) $config->push_batch_size;
            } else {
                $this->batch = 500;
            }
        }
        if (CLI_SCRIPT) {
            $this->timeout = isset($config->push_timeout_cli) ? $config->push_timeout_cli : self::TASKTIMEOUT;
        } else {
            $this->timeout = isset($config->push_timeout) ? $config->push_timeout : self::LIVETIMEOUT;
        }
        if (isset($config->push_connect_timeout)) {
            $this->connecttimeout = (int) $config->push_connect_timeout;
        }
        if (isset($config->push_debug)) {
            $this->debug = (bool) $config->push_debug;
        }
        if (isset($config->push_cli_only)) {
            $this->clionly = (bool) $config->push_cli_only;
        }
        if (isset($config->max_push_attempts)) {
            $this->maxpushattempts = (int) $config->max_push_attempts;
        }
    }

    /**
     * Are we properly configured?
     *
     * @return bool
     */
    public function is_valid() {
        return $this->url !== null && $this->key !== null && $this->secret !== null;
    }

    /**
     * @return string
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * @return string
     */
    public function get_key() {
        return $this->key;
    }

    /**
     * @return string
     */
    public function get_secret() {
        return $this->secret;
    }

    /**
     * @return int
     */
    public function get_batch_size() {
        return $this->batch;
    }

    /**
     * @return int
     */
    public function get_timeout() {
        return $this->timeout;
    }

    /**
     * @return int
     */
    public function get_connect_timeout() {
        return $this->connecttimeout;
    }

    /**
     * @return bool
     */
    public function get_debug() {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function is_cli_only() {
        return $this->clionly;
    }

    /**
     * @return int
     */
    public function get_max_push_attempts() {
        return $this->maxpushattempts;
    }
}
