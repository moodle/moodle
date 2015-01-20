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
 * Moodle implementation of process manager, to execute external commands.
 *
 * @package    core
 * @copyright  2015 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Moodle implementation of process manager, to execute external commands.
 *
 * @package    core
 * @copyright  2015 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_manager implements Countable {

    /**
     * Standard in.
     */
    const STDIN  = 0;

    /**
     * Standard out.
     */
    const STDOUT = 1;

    /**
     * Standard error.
     */
    const STDERR = 2;

    /**
     * Non blocking mode.
     */
    const NON_BLOCKING = 0;

    /**
     * Blocking mode.
     */
    const BLOCKING     = 1;

    /** @var array Descriptor used for process. */
    private static $DESCRIPTORSPEC = array(
        self::STDIN  => array('pipe', 'r'),
        self::STDOUT => array('pipe', 'w'),
        self::STDERR => array('pipe', 'w'),
    );

    /** @var array list of processes. */
    private $processes = array();

    /** @var array list of stdin */
    private $stdins    = array();

    /** @var array list of stdout */
    private $stdouts   = array();

    /** @var array list of stderr. */
    private $stderrs   = array();

    /**
     * Create new process and keep track of it.
     *
     * @param string $name name of the process, has to be unique for process identification.
     * @param string $cmd command to execute.
     * @param string $cwd absolute path of working directory for command to execute.
     * @return false if failed to create process.
     */
    public function create($name, $cmd, $cwd = NULL) {
        $process = proc_open($cmd, self::$DESCRIPTORSPEC, $pipes, $cwd);

        if (false === is_resource($process)) {
            throw new Exception('Error starting worker');
        }

        stream_set_blocking($pipes[self::STDOUT], self::NON_BLOCKING);
        stream_set_blocking($pipes[self::STDERR], self::NON_BLOCKING);

        $this->processes[$name] = $process;
        $this->stdins[$name]    = $pipes[self::STDIN];
        $this->stdouts[$name]   = $pipes[self::STDOUT];
        $this->stderrs[$name]   = $pipes[self::STDERR];

        return true;
    }

    /**
     * Keep listing to process and return status of it.
     *
     * @retrun array status, stdout and stderr.
     */
    public function listen() {
        $read = array();

        foreach ($this->processes as $i => $p) {
            // Update process info.
            if (!($s = @proc_get_status($p)) || !$s['running']) {
                $status[$i] = $this->detach($p);
            } else {
                $status[$i] = 0;
                $read[] = $this->stdouts[$i];
                $read[] = $this->stderrs[$i];
            }
        }

        if ($read) {
            $changednum = stream_select($read, $write, $expect, 0);
        } else {
            return;
        }

        if (false === $changednum) {
            throw new \RuntimeException();
        }

        if (0 === $changednum) {
            return;
        }

        foreach ($read as $stream) {
            $i = array_search($stream, $this->stdouts, true);
            if (false === $i) {
                $i = array_search($stream, $this->stderrs, true);
                if (false === $i) {
                    continue;
                }
            }

            $stdout[$i] = stream_get_contents($this->stdouts[$i]);
            $stderr[$i] = stream_get_contents($this->stderrs[$i]);
        }
        return (array($status, $stdout, $stderr));
    }

    /**
     * Detach process.
     *
     * @param $process process to detatch.
     * @return int status of process.
     */
    public function detach($process) {
        $i = array_search($process, $this->processes, true);

        if (false === $i) {
            throw new \RuntimeException();
        }

        fclose($this->stdins[$i]);
        fclose($this->stdouts[$i]);
        fclose($this->stderrs[$i]);
        $status = proc_close($this->processes[$i]);

        unset($this->processes[$i]);
        unset($this->stdins[$i]);
        unset($this->stdouts[$i]);
        unset($this->stderrs[$i]);

        return $status;
    }

    /**
     * Detach all processes.
     */
    public function detachall() {
        foreach ($this->stdins as $stdin) {
            fclose($stdin);
        }
        foreach ($this->stdouts as $stdout) {
            fclose($stdout);
        }
        foreach ($this->stderrs as $stderr) {
            fclose($stderr);
        }
        foreach ($this->processes as $processs) {
            proc_close($processs);
        }
    }

    /**
     * Return count of active processes.
     *
     * @return int count of active processes.
     */
    public function count() {
        return count($this->processes);
    }

    /**
     * Destructor.
     */
    public function __destruct() {
        $this->detachall();
    }
}
