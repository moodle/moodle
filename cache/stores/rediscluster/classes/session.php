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
 * RedisCluster session handler
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_rediscluster;

defined('MOODLE_INTERNAL') || die();

// We need to explicitly include cachestore_rediscluster here as it isn't able
// to be autoloaded early on during install/upgrade.
require_once(__DIR__ . '/../lib.php');

/**
 * RedisCluster Session handler
 *
 * Forked from the core redis session handler.
 */
class session extends \core\session\handler {

    /**
     * The connection config for RedisCluster.
     *
     * @var string
     */
    protected $config;

    /**
     * How long to wait for a session lock (in seconds).
     *
     * @var int
     */
    protected $acquiretimeout = 120;

    /** @var int $lockretry how long to wait between session lock attempts in ms */
    protected $lockretry = 100;

    /**
     * How long to wait in seconds before expiring the lock automatically so
     * that other requests may continue execution.
     *
     * @var int
     */
    protected $lockexpire;

    /**
     * This key is used to track which locks a given host currently has held.
     *
     * @var string
     */
    protected $lockhostkey;

    /**
     * Should we track lock hosts?
     * @var bool
     */
    protected $tracklockhost = false;

    /** @var string $lasthash hash of the session data content */
    protected $lasthash = null;

    /**
     * The RedisCluster cachestore object.
     *
     * @var cachestore_rediscluster
     */
    protected $connection = null;

    /**
     * List of currently held locks by this page.
     *
     * @var array
     */
    protected $locks = [];

    /**
     * How long sessions live before expiring from inactivity (in seconds).
     *
     * @var int
     */
    protected $timeout;

    /**
     * If enabled, this script won't attempt to get a session lock before
     * running. Useful for endpoints that are known to not affect session data.
     *
     * @var bool
     */
    protected $nolock = false;

    /**
     * The maximum amount of threads a given session can have queued waiting for
     * the session lock.
     *
     * Set this to -1 to allow unlimited waiters. Waiting requests will only
     * stop waiting when they get the lock, or the acquiretimeout has expired
     * for the request.
     *
     * @var int
     */
    protected $maxwaiters = 10;

    /**
     * Flag that indicates if we're currently waiting for a lock or not.
     *
     * @var bool
     */
    protected $waiting = false;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $CFG;

        $this->config = [
            'compression' => \Redis::COMPRESSION_NONE,
            'failover' => \RedisCluster::FAILOVER_NONE,
            'persist' => false,
            'preferrednodes' => null,
            'prefix' => '',
            'readtimeout' => 3.0,
            'serializer' => \Redis::SERIALIZER_PHP,
            'server' => null,
            'serversecondary' => null,
            'session' => true,
            'timeout' => 3.0,
            // How long we try to get a lock for before displaying
            // the waiting room page. 0 = never show the page.
            'waitingroom_start' => 0,
            // Wait for lock only this long per refresh.
            'waitingroom_poll' => 1,
            // Max time between refreshes.
            'waitingroom_backoffmax' => 8,
            // How long till we give up refreshing.
            'waitingroom_maxwait' => 60,
            'waitingroom_statuscode' => '500 Internal Server Error',
        ];

        foreach (array_keys($this->config) as $key) {
            if (!empty($CFG->session_rediscluster[$key])) {
                $this->config[$key] = $CFG->session_rediscluster[$key];
            }
        }

        if (isset($CFG->session_rediscluster['acquire_lock_timeout'])) {
            $this->acquiretimeout = (int)$CFG->session_rediscluster['acquire_lock_timeout'];
        }

        if (isset($CFG->session_redis_acquire_lock_retry)) {
            $this->lockretry = (int)$CFG->session_redis_acquire_lock_retry;
        }

        if (isset($CFG->session_rediscluster['max_waiters'])) {
            $this->maxwaiters = (int)$CFG->session_rediscluster['max_waiters'];
        }

        $this->lockexpire = $CFG->sessiontimeout;
        if (isset($CFG->session_rediscluster['lock_expire'])) {
            $this->lockexpire = (int)$CFG->session_rediscluster['lock_expire'];
        }

        if (isset($CFG->session_rediscluster['tracklockhost'])) {
            $this->tracklockhost = $CFG->session_rediscluster['tracklockhost'];
        }

        // The following configures the session lifetime in redis to allow some
        // wriggle room in the user noticing they've been booted off and
        // letting them log back in before they lose their session entirely.
        $updatefreq = empty($CFG->session_update_timemodified_frequency) ? 20 : $CFG->session_update_timemodified_frequency;
        $this->timeout = $CFG->sessiontimeout + $updatefreq + MINSECS;

        if (!defined('NO_SESSION_LOCK')) {
            define('NO_SESSION_LOCK', false);
        }
        $this->nolock = NO_SESSION_LOCK;

        $this->lockhostkey = "mdl_locklist:".gethostname();
    }

    /**
     * Init session handler.
     */
    public function init() {
        global $CFG;

        require_once("{$CFG->dirroot}/cache/stores/rediscluster/lib.php");

        if (!extension_loaded('redis')) {
            throw new \moodle_exception('sessionhandlerproblem', 'error', '', null, 'redis extension is not loaded');
        }

        if (empty($this->config['server'])) {
            throw new \moodle_exception('sessionhandlerproblem', 'error', '', null,
                    '$CFG->session_redis[\'server\'] must be specified in config.php');
        }

        // The session handler requires a version of Redis extension that supports cluster (>= 2.2.8).
        if (!class_exists('RedisCluster')) {
            throw new \moodle_exception('sessionhandlerproblem', 'error', '', null,
                'redis extension version must be at least 2.2.8');
        }

        $this->connection = new \cachestore_rediscluster(null, $this->config);

        $result = session_set_save_handler([$this, 'handler_open'],
            [$this, 'handler_close'],
            [$this, 'handler_read'],
            [$this, 'handler_write'],
            [$this, 'handler_destroy'],
            [$this, 'handler_gc']);
        if (!$result) {
            throw new \Exception('Session handler is misconfigured');
        }
        return true;
    }

    /**
     * Update our session search path to include session name when opened.
     *
     * @param string $savepath  unused session save path. (ignored)
     * @param string $sessionname Session name for this session. (ignored)
     * @return bool true always as we will succeed.
     */
    public function handler_open($savepath, $sessionname) {
        return true;
    }

    /**
     * Close the session completely. We also remove all locks we may have obtained that aren't expired.
     *
     * @return bool true on success.  false on unable to unlock sessions.
     */
    public function handler_close() {
        $this->lasthash = null;
        try {
            foreach ($this->locks as $id => $expirytime) {
                if ($expirytime > time()) {
                    $this->unlock_session($id);
                }
                unset($this->locks[$id]);
            }
        } catch (\RedisException $e) {
            debugging('Failed talking to redis: '.$e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        return true;
    }
    /**
     * Read the session data from storage
     *
     * @param string $id The session id to read from storage.
     * @return string The session data for PHP to process.
     *
     * @throws RedisException when we are unable to talk to the Redis server.
     */
    public function handler_read($id) {
        try {
            if ($this->requires_write_lock()) {
                $this->lock_session($id);
            }
            $sessiondata = $this->connection->command('get', $id);
            if ($sessiondata === false) {
                if ($this->requires_write_lock()) {
                    $this->unlock_session($id);
                }
                $this->lasthash = sha1('');
                return '';
            }
            $this->connection->command('expire', $id, $this->timeout);
        } catch (\RedisException $e) {
            debugging('Failed talking to redis: '.$e->getMessage(), DEBUG_DEVELOPER);
            throw $e;
        }
        $this->lasthash = sha1(base64_encode($sessiondata));
        return $sessiondata;
    }

    /**
     * Write the serialized session data to our session store.
     *
     * @param string $id session id to write.
     * @param string $data session data
     * @return bool true on write success, false on failure
     */
    public function handler_write($id, $data) {
        if ($this->nolock) {
            return true;
        }
        if (is_null($this->connection)) {
            // The session has already been closed, don't attempt another write.
            debugging('Tried to write session: '.$id.' before open or after close.', DEBUG_DEVELOPER);
            return false;
        }

        $hash = sha1(base64_encode($data));

        // If the content has not changed don't bother writing.
        if ($hash === $this->lasthash) {
            return true;
        }

        // We do not do locking here because memcached doesn't.  Also
        // PHP does open, read, destroy, write, close. When a session doesn't exist.
        // There can be race conditions on new sessions racing each other but we can
        // address that in the future.
        try {
            $this->connection->command('setex', $id, $this->timeout, $data);
        } catch (\RedisException $e) {
            debugging('Failed talking to redis: '.$e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
        return true;
    }

    /**
     * Handle destroying a session.
     *
     * @param string $id the session id to destroy.
     * @return bool true if the session was deleted, false otherwise.
     */
    public function handler_destroy($id) {
        $this->lasthash = null;
        try {
            $this->connection->command('del', $id);
            $this->unlock_session($id);
            $this->connection->command('del', $id.'.lock.waiting');
        } catch (\RedisException $e) {
            debugging('Failed talking to redis: '.$e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        return true;
    }

    /**
     * Garbage collect sessions.  We don't we any as Redis does it for us.
     *
     * @param integer $maxlifetime All sessions older than this should be removed.
     * @return bool true, as Redis handles expiry for us.
     */
    public function handler_gc($maxlifetime) {
        return true;
    }

    /**
     * Unlock a session.
     *
     * @param string $id Session id to be unlocked.
     */
    protected function unlock_session($id) {
        if (isset($this->locks[$id])) {
            $lockkey = "{$id}.lock";
            $this->connection->set_retry_limit(1); // Try extra hard to unlock the session.
            $this->connection->command('del', $lockkey);

            if ($this->tracklockhost) {
                // Remove the lock from our list of held locks for this host.
                $this->connection->command_raw('hdel', $this->lockhostkey, "{$this->config['prefix']}{$lockkey}");
            }

            unset($this->locks[$id]);
        }
    }

    /**
     * Obtain a session lock so we are the only one using it at the moent.
     *
     * @param string $id The session id to lock.
     * @return bool true when session was locked, exception otherwise.
     * @throws exception When we are unable to obtain a session lock.
     */
    protected function lock_session($id) {
        $lockkey = $id.".lock";
        $reqget = $_SERVER['REQUEST_METHOD'] === 'GET';

        if ($this->nolock) {
            return true;
        }

        $haslock = isset($this->locks[$id]) && time() < $this->locks[$id];
        $startlocktime = time();
        $waitkey = "{$lockkey}.waiting";

        // Create the waiting key, or increment it if we end up queued.
        $waitpos = $this->increment($waitkey, $this->lockexpire);
        $this->waiting = true;

        if ($this->maxwaiters >= 0 && $waitpos > $this->maxwaiters) {
            $this->decrement($waitkey);
            $this->waiting = false;
            $this->error('sessionwaiterr');
        }

        // Ensure on timeout or exception that we try to decrement the waiter count.
        \core_shutdown_manager::register_function([$this, 'release_waiter'], [$waitkey]);

        // Waiting room - reduce lock polling time for subsequent refreshes.
        $waitroompoll = optional_param('sst', 0, PARAM_INT) ? $this->config['waitingroom_poll'] :
        $this->config['waitingroom_start'];

        // To be able to ensure sessions don't write out of order we must obtain an exclusive lock
        // on the session for the entire time it is open.  If another AJAX call, or page is using
        // the session then we just wait until it finishes before we can open the session.
        while (!$haslock) {
            $expiry = time() + $this->lockexpire;
            $haslock = $this->get_lock($lockkey);
            if ($haslock) {
                $this->locks[$id] = $expiry;
                break;
            }

            // We want a random delay to stagger the polling load. Ideally
            // this delay should be a fraction of the average response
            // time. If it is too small we will poll too much and if it is
            // too large we will waste time waiting for no reason. 100ms is
            // the default starting point.
            $delay = rand($this->lockretry, (int)($this->lockretry * 1.1));

            // If we don't get a lock within 5 seconds then there must be a
            // very long lived process holding the lock so throttle back to
            // just polling roughly once a second.
            if (time() > $startlocktime + 5) {
                $delay = min(rand(1000, 1100), $delay);
            }

            // If we're a GET request and we have waiting-room enabled,
            // give the user a 'waiting-room' type page when they've waited
            // long enough to trigger it.
            if ($reqget && !AJAX_SCRIPT
                && $this->config['waitingroom_start'] > 0
                && (time() > $startlocktime + $waitroompoll)) {
                $this->decrement($waitkey);
                $this->waiting = false;
                if (!empty($this->config['waitingroom_statuscode'])) {
                    header("HTTP/1.1 {$this->config['waitingroom_statuscode']}");
                }
                header("X-OLMS-Reason: sessionbusy");
                echo $this->render_waitingroom();
                exit;
            }

            if (time() > $startlocktime + $this->acquiretimeout) {
                // This is a fatal error, better inform users.
                // It should not happen very often - all pages that need long time to execute
                // should close session immediately after access control checks.
                debugging('Cannot obtain session lock for sid: '.$id.' within '.$this->acquiretimeout.
                    '. It is likely another page has a long session lock, or the session lock was never released.',
                    DEBUG_DEVELOPER);
                break;
            }

            usleep($delay * 1000);
        }

        $this->decrement($waitkey);
        $this->waiting = false;

        if (!$haslock) {
            $this->error('sessionwaiterr');
        }
        return true;
    }

    public function render_waitingroom() {
        global $CFG, $SITE;

        // Session backoff.
        $sbo = optional_param('sbo', 1, PARAM_INT) * 2;

        // Time we started waiting.
        $sst = optional_param('sst', time(), PARAM_INT);

        // Max time between refreshing of 8 seconds.
        if ($sbo > $this->config['waitingroom_backoffmax']) {
            $sbo = $this->config['waitingroom_backoffmax'];
        }

        $timestamp = date('Y-m-d h:i:s A T');

        $requrl = $CFG->wwwroot.$_SERVER['DOCUMENT_URI'];
        // DOCUMENU_URI includes index.php when a users request may not have.
        // Lets make sure our base URL here only includes it if they had it.
        if (preg_match('#index\.php#', $requrl) && strpos($_SERVER['REQUEST_URI'], 'index.php') === false) {
            $requrl = str_replace('index.php', '', $requrl);
        }

        $params = array_merge($_GET, ['sst' => $sst, 'sbo' => $sbo]);
        $redirect = $requrl.'?'.http_build_query($params);

        $autoreload = time() - $sst < $this->config['waitingroom_maxwait'];

        unset($params['sbo']);
        unset($params['sst']);
        $cleanurl = $requrl;
        if (!empty($params)) {
            $cleanurl .= '?'.http_build_query($params);
        }

        if ($autoreload) {
            return <<<EOF
<html>
    <head>
        <title>{$SITE->fullname}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="{$sbo}; URL='{$redirect}'" />
        <style>*{box-sizing:border-box;margin:0;padding:0}body{line-height:1.4;font-size:1rem;font-family:ui-sans-serif,system-ui,
            -apple-system,BlinkMacSystemFont,"Segoe UI",
            Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif;padding:2rem;
            display:grid;place-items:center;min-height:100vh}.container{width:100%;max-width:800px}p{margin-top:.5rem}</style>
        <script>window.history.replaceState('', '{$SITE->fullname}', '{$cleanurl}');</script>
    </head>
    <body>
        <div class='container'>
            <h1>
                <div>Waiting on previous request.</div>
                <div>Thanks for your patience.</div>
            </h1>
            <p>Your previous request is still being processed.</p>
            <p><b>This page will automatically refresh, please do not close your browser.</b></p>
            <p><b>Last updated:</b> {$timestamp}</p>
        </div>
    </body>
</html>
EOF;
        }

        // Max waiting time used up, render a different page.
        return <<<EOF
<html>
    <head>
        <title>{$SITE->fullname}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>*{box-sizing:border-box;margin:0;padding:0}body{line-height:1.4;font-size:1rem;font-family:ui-sans-serif,system-ui,
            -apple-system,BlinkMacSystemFont,"Segoe UI",
            Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif;padding:2rem;
            display:grid;place-items:center;min-height:100vh}.container{width:100%;max-width:800px}p{margin-top:.5rem}</style>
        <script>window.history.replaceState('', '{$SITE->fullname}', '{$cleanurl}');</script>
    </head>
    <body>
        <div class='container'>
            <h1>
                <div>Waiting on previous request.</div>
                <div>Thanks for your patience.</div>
            </h1>
            <p>Your previous request is still being processed.</p>
            <p>Auto-refresh has now stopped. Please reload the page when you're ready to retry.</p>
            <p><b>Last updated:</b> {$timestamp}</p>
        </div>
    </body>
</html>
EOF;
    }

    public function release_waiter($waitkey) {
        if ($this->waiting) {
            debugging("REDIS SESSION: Still waiting [key=$waitkey] during request shutdown!", DEBUG_DEVELOPER);
            try {
                $this->decrement($waitkey);
            } catch (\Exception $e) {
                debugging("REDIS SESSION: Decrement failed for $waitkey", DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Get a lock, with some metadata embedded in it.
     *
     * @param $lockkey The lock key.
     *
     * @return bool Did we get a new lock for the provided lockkey.
     */
    protected function get_lock($lockkey) {
        global $CFG;

        $meta = [
            'createdat' => time(),
            'instance' => isset($CFG->puppet_instance) ? $CFG->puppet_instance : '',
            'lockkey' => $lockkey,
            'script' => isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '',
        ];

        // Try to get the lock itself.
        $haslock = $this->connection->command('set', $lockkey, json_encode($meta), ['NX', 'EX' => $this->lockexpire]);
        if ($haslock && $this->tracklockhost) {
            // Great, lets add it to the list of held locks for this host.
            $fulllockkey = "{$this->config['prefix']}{$lockkey}";
            $this->connection->command_raw('hset', $this->lockhostkey, $fulllockkey, $this->lockexpire);
        }
        return $haslock;
    }

    protected function error($error = 'sessionhandlerproblem') {
        if (!defined('NO_MOODLE_COOKIES')) {
            define('NO_MOODLE_COOKIES', true);
        }
        throw new \Exception($error);
    }

    protected function decrement($k) {
        $v = $this->connection->command('decr', $k);
        if ($v !== false) {
            return $v;
        }
        return 0;
    }


    protected function increment($k, $ttl) {
        // Ensure key is created with ttl before proceeding.
        if (empty($this->connection->command('exists', $k))) {
            // We don't want to potentially lose the expiry, so do it in a transaction.
            $this->connection->command('multi');
            $this->connection->command('incr', $k);
            $this->connection->command('expire', $k, $ttl);
            $this->connection->command('exec');
            return 0;
        }

        // Use simple form of increment as we cannot use binary protocol.
        $v = $this->connection->command('incr', $k);
        if ($v !== false) {
            return $v;
        }

        throw new \Exception('sessionhandlerproblem', 'error', '', null,
                    'Unable to get a session waiter.');
    }

    /**
     * Check the backend contains data for this session id.
     *
     * Note: this is intended to be called from manager::session_exists() only.
     *
     * @param string $sid
     * @return bool true if session found.
     */
    public function session_exists($sid) {
        if (!$this->connection) {
            return false;
        }

        try {
            return !empty($this->connection->command('exists', $sid));
        } catch (\RedisException $e) {
            return false;
        }
    }

    /**
     * Kill all active sessions, the core sessions table is purged afterwards.
     */
    public function kill_all_sessions() {
        global $DB;
        if (!$this->connection) {
            return;
        }

        $rs = $DB->get_recordset('sessions', [], 'id DESC', 'id, sid');
        foreach ($rs as $record) {
            $this->handler_destroy($record->sid);
        }
        $rs->close();
    }

    /**
     * Kill one session, the session record is removed afterwards.
     *
     * @param string $sid
     */
    public function kill_session($sid) {
        if (!$this->connection) {
            return;
        }

        $this->handler_destroy($sid);
    }

    // The following functions exist to facilitate unit tests and will not do
    // anything outside of phpunit.

    public function cleanup_test_instance() {
        if (!PHPUNIT_TEST) {
            return;
        }

        if (!\cachestore_rediscluster::are_requirements_met() || empty($this->connection)) {
            return false;
        }

        $list = $this->connection->command('keys', 'phpunit*');
        foreach ($list as $keyname) {
            $this->connection->command('del', $keyname);
        }
        $this->connection->close();
    }

    public function test_find_keys($search) {
        if (!PHPUNIT_TEST) {
            return;
        }
        return $this->connection->command('keys', "{$this->config['prefix']}{$search}");
    }

}
