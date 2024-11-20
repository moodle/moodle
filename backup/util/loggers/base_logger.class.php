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
 * @package    moodlecore
 * @subpackage backup-logger
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base abstract class for all the loggers to be used in backup/restore
 *
 * Any message passed will be processed by all the loggers in the defined chain
 * (note some implementations may be not strictly "loggers" but classes performing
 * other sort of tasks (avoiding browser/php timeouts, painters...). One simple 1-way
 * basic chain of commands/responsibility pattern.
 *
 * TODO: Finish phpdocs
 */
abstract class base_logger implements checksumable {

    protected $level;     // minimum level of logging this logger must handle (valid level from @backup class)
    protected $showdate;  // flag to decide if the logger must output the date (true) or no (false)
    protected $showlevel; // flag to decide if the logger must output the level (true) or no (false)
    protected $next; // next logger in the chain

    public function __construct($level, $showdate = false, $showlevel = false) {
        // TODO: check level is correct
        $this->level = $level;
        $this->showdate = $showdate;
        $this->showlevel = $showlevel;
        $this->next = null;
    }

    final public function set_next($next) {
        // TODO: Check is a base logger

        // TODO: Check next hasn't been set already

        // TODO: Avoid circular dependencies
        if ($this->is_circular_reference($next)) {
            $a = new stdclass();
            $a->alreadyinchain = get_class($this);
            $a->main = get_class($next);
            throw new base_logger_exception('logger_circular_reference', $a);
        }

        $this->next = $next;
    }

    public function get_next() {
        return $this->next;
    }

    public function get_level() {
        return $this->level;
    }

    /**
     * Destroy (nullify) the chain of loggers references, also closing resources when needed.
     *
     * @since Moodle 3.1
     */
    final public function destroy() {
        // Recursively destroy the chain.
        if ($this->next !== null) {
            $this->next->destroy();
            $this->next = null;
        }
        // And close every logger.
        $this->close();
    }

    /**
     * Close any resource the logger may have open.
     *
     * @since Moodle 3.1
     */
    public function close() {
        // Nothing to do by default. Only loggers using resources (files, own connections...) need to override this.
    }

// checksumable interface methods

    public function calculate_checksum() {
        // Checksum is a simple md5 hash of classname, level and
        // on each specialised logger, its own atrributes
        // Not following the chain at all.
        return md5(get_class($this) . '-' . $this->level);
    }

    public function is_checksum_correct($checksum) {
        return $this->calculate_checksum() === $checksum;
    }

// Protected API starts here

    abstract protected function action($message, $level, $options = null); // To implement

    final public function process($message, $level, $options = null) {
        $result = true;
        if ($this->level != backup::LOG_NONE && $this->level >= $level
            && !(defined('BEHAT_TEST') && BEHAT_TEST)) { // Perform action conditionally.
            $result = $this->action($message, $level, $options);
        }
        if ($result === false) { // Something was wrong, stop the chain
            return $result;
        }
        if ($this->next !== null) { // The chain continues being processed
            $result = $this->next->process($message, $level, $options);
        }
        return $result;
    }

    protected function is_circular_reference($obj) {
        // Get object all nexts recursively and check if $this is already there
        $nexts = $obj->get_nexts();
        if (array_key_exists($this->calculate_checksum(), $nexts) || $obj == $this) {
            return true;
        }
        return false;
    }

    protected function get_nexts() {
        $nexts = array();
        if ($this->next !== null) {
            $nexts[$this->next->calculate_checksum()] = $this->next->calculate_checksum();
            $nexts = array_merge($nexts, $this->next->get_nexts());
        }
        return $nexts;
    }

    protected function get_datestr() {
        return userdate(time(), '%c');
    }

    protected function get_levelstr($level) {
        $result = 'undefined';
        switch ($level) {
            case backup::LOG_ERROR:
                $result = 'error';
                break;
            case backup::LOG_WARNING:
                $result = 'warn';
                break;
            case backup::LOG_INFO:
                $result = 'info';
                break;
            case backup::LOG_DEBUG:
                $result = 'debug';
                break;
        }
        return $result;
    }

    protected function get_prefix($level, $options) {
        $prefix = '';
        if ($this->showdate) {
            $prefix .= '[' . $this->get_datestr() . '] ';
        }
        if ($this->showlevel) {
            $prefix .= '[' . $this->get_levelstr($level) . '] ';
        }
        return $prefix;
    }
}

/*
 * Exception class used by all the @base_logger stuff
 */
class base_logger_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
