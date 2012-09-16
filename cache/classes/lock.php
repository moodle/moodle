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
 * Cache lock class. Used for locking when required.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The cache lock class.
 *
 * This class is used for acquiring and releasing locks.
 * We use this rather than flock because we can be sure this is cross-platform compatible and thread/process safe.
 *
 * This class uses the files for locking. It relies on fopens x mode which is documented as follows:
 *
 *    Create and open for writing only; place the file pointer at the beginning of the file. If the file already exists, the
 *    fopen() call will fail by returning FALSE and generating an error of level E_WARNING.
 *    http://www.php.net/manual/en/function.fopen.php
 *
 * Through this we can attempt to call fopen using a lock file name. If the fopen call succeeds we can be sure we have created the
 * file and thus ascertained the lock, otherwise fopen fails and we can look at what to do next.
 *
 * All interaction with this class is handled through its two public static methods, lock and unlock.
 * Internally an instance is generated and used for locking and unlocking. It records the locks used during this session and on
 * destruction cleans up any left over locks.
 * Of course the clean up is just a safe-guard. Really no one should EVER leave a lock and rely on the clean up.
 *
 * Because this lock system uses files for locking really its probably not ideal, but as I could not think of a better cross
 * platform thread safe system it is what we have ended up with.
 *
 * This system also allows us to lock a file before it is created because it doesn't rely on flock.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_lock {

    /**
     * Acquire a lock.
     *
     * If the lock can be acquired:
     *      This function will return true.
     *
     * If the lock cannot be acquired the result of this method is determined by the block param:
     *      $block = true (default)
     *          The function will block any further execution unti the lock can be acquired.
     *          This involves the function attempting to acquire the lock and the sleeping for a period of time. This process
     *          will be repeated until the lock is required or until a limit is hit (100 by default) in which case a cache
     *          exception will be thrown.
     *      $block = false
     *          The function will return false immediately.
     *
     * If a max life has been specified and the lock can not be acquired then the lock file will be checked against this time.
     * In the case that the file exceeds that max time it will be forcefully deleted.
     * Because this can obviously be a dangerous thing it is not used by default. If it is used it should be set high enough that
     * we can be as sure as possible that the executing code has completed.
     *
     * @param string $key The key that we want to lock
     * @param bool $block True if we want the program block further execution until the lock has been acquired.
     * @param int $maxlife A maximum life for the block file if there should be one. Read the note in the function description
     *      before using this param.
     * @return bool
     * @throws cache_exception If block is set to true and more than 100 attempts have been made to acquire a lock.
     */
    public static function lock($key, $block = true, $maxlife = null) {
        $key = md5($key);
        $instance = self::instance();
        return $instance->_lock($key, $block, $maxlife);
    }

    /**
     * Releases a lock that has been acquired.
     *
     * This function can only be used to release locks you have acquired. If you didn't acquire the lock you can't release it.
     *
     * @param string $key
     * @return bool
     */
    public static function unlock($key) {
        $key = md5($key);
        $instance = self::instance();
        return $instance->_unlock($key);
    }

    /**
     * Resets the cache lock class, reinitialising it.
     */
    public static function reset() {
        self::instance(true);
    }

    /**
     * Returns an instance of the cache lock class.
     *
     * @staticvar bool $instance
     * @param bool $forceregeneration
     * @return cache_lock
     */
    protected static function instance($forceregeneration = false) {
        static $instance = false;
        if (!$instance || $forceregeneration) {
            $instance = new cache_lock();
        }
        return $instance;
    }

    /**
     * The directory in which lock files will be created
     * @var string
     */
    protected $cachedir;

    /**
     * An array of lock files currently held by this cache lock instance.
     * @var array
     */
    protected $locks = array();

    /**
     * Constructs this cache lock instance.
     */
    protected function __construct() {
        $this->cachedir = make_cache_directory('cachelock');
    }

    /**
     * Cleans up the instance what it is no longer needed.
     */
    public function __destruct() {
        foreach ($this->locks as $lockfile) {
            // Naught, naughty developers.
            @unlink($lockfile);
        }
    }

    /**
     * Acquires a lock, of dies trying (jokes).
     *
     * Read {@link cache_lock::lock()} for full details.
     *
     * @param string $key
     * @param bool $block
     * @param int|null $maxlife
     * @return bool
     * @throws cache_exception
     */
    protected function _lock($key, $block = true, $maxlife = null) {
        // Get the name of the lock file we want to use.
        $lockfile = $this->get_lock_file($key);

        // Attempt to create a handle to the lock file.
        // Mode xb is the secret to this whole function.
        //   x = Creates the file and opens it for writing. If the file already exists fopen returns false and a warning is thrown.
        //   b = Forces binary mode.
        $result = @fopen($lockfile, 'xb');

        // Check if we could create the file or not.
        if ($result === false) {
            // Lock exists already.
            if ($maxlife !== null) {
                $mtime = filemtime($lockfile);
                if ($mtime < time() - $maxlife) {
                    $this->_unlock($key, true);
                    $result = $this->_lock($key, false);
                    if ($result) {
                        return true;
                    }
                }
            }
            if ($block) {
                // OK we are blocking. We had better sleep and then retry to lock.
                $iterations = 0;
                $maxiterations = 100;
                while (($result = $this->_lock($key, false)) === false) {
                    // usleep causes the application to cleep to x microseconds.
                    // Before anyone asks there are 1'000'000 microseconds to a second.
                    usleep(rand(1000, 50000)); // Sleep between 1 and 50 milliseconds
                    $iterations++;
                    if ($iterations > $maxiterations) {
                        // BOOM! We've exceeded the maximum number of iterations we want to block for.
                        throw new cache_exception('ex_unabletolock');
                    }
                }
            }

            return false;
        } else {
            // We have the lock.
            fclose($result);
            $this->locks[$key] = $lockfile;
            return true;
        }
    }

    /**
     * Releases an acquired lock.
     *
     * For more details see {@link cache_lock::unlock()}
     *
     * @param string $key
     * @param bool $forceunlock If set to true the lock will be removed if it exists regardless of whether or not we own it.
     * @return bool
     */
    protected function _unlock($key, $forceunlock = false) {
        if (array_key_exists($key, $this->locks)) {
            @unlink($this->locks[$key]);
            unset($this->locks[$key]);
            return true;
        } else if ($forceunlock) {
            $lockfile = $this->get_lock_file($key);
            if (file_exists($lockfile)) {
                @unlink($lockfile);
            }
            return true;
        }
        // You cannot unlock a file you didn't lock.
        return false;
    }

    /**
     * Gets the name to use for a lock file.
     *
     * @param string $key
     * @return string
     */
    protected function get_lock_file($key) {
        return $this->cachedir.'/'. $key .'.lock';
    }
}