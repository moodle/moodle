<?php
/**
 ** This class abstracts PHP's PECL memcached
 ** API to provide
 ** 
 ** - get()
 ** - set()
 ** - delete()
 ** - getforfill()
 ** - releaseforfill()
 **
 ** Author: Martin Langhoff <martin@catalyst.net.nz>
 **
 ** Note: do NOT store booleans here. With memcached, a false value 
 ** is indistinguisable from a "not found in cache" response. 
 **/


class memcached {

    function memcached() {
        global $CFG;

        if (!function_exists('memcache_connect')) {
            debugging("Memcached is set to true but the memcached extension is not installed");
            return false;
        }
        $this->_cache = new Memcache;

        $hosts = split(',', $CFG->memcachedhosts);
        if (count($hosts) === 1 && !empty($CFG->memcachedpconn)) {
            // the faster pconnect is only available
            // for single-server setups
            // NOTE: PHP-PECL client is buggy and pconnect() 
            // will segfault if the server is unavailable
            $this->_cache->pconnect($hosts[0]);
        } else {
            // multi-host setup will share key space
            foreach ($hosts as $host) {
                $host = trim($host);
                $this->_cache->addServer($host);
            }
        }

        $this->prefix = $CFG->dbname .'|' . $CFG->prefix . '|';
    }

    function status() {
        if (is_object($this->_cache)) {
            return true;
        }
        return false;
    }

    function set($key, $value, $ttl=0) {

        // we may have acquired a lock via getforfill
        // release if it exists
        @$this->_cache->delete($this->prefix . $key . '_forfill');

        return $this->_cache->set($this->prefix . $key, $value, false);
    }

    function get($key) {
        $rec = $this->_cache->get($this->prefix . $key);
        return $rec;
    } 
        
    function delete($key) {
        return $this->_cache->delete($this->prefix . $key);
    }

    /**
     * In the simple case, this function will 
     * get the cached value if available. If the entry
     * is not cached, it will try to get an exclusive
     * lock that announces that this process will
     * populate the cache.
     *
     * If we fail to get the lock -- this means another
     * process is doing it. 
     * so we wait (block) for a few microseconds while we wait for
     * the cache to be filled or the lock to timeout.
     * 
     * If you get a false from this call, you _must_
     * populate the cache ASAP or indicate that
     * you won't by calling releaseforfill().
     *
     * This technique forces serialisation and so helps deal 
     * with thundering herd scenarios where a lot of clients 
     * ask the for the same idempotent (and costly) operation. 
     * The implementation is based on suggestions in this message
     * http://marc.theaimsgroup.com/?l=git&m=116562052506776&w=2
     *
     * @param $key string
     * @return mixed on cache hit, NULL otherwise
     */
    function getforfill ($key) {
        
        $rec = $this->_cache->get($this->prefix . $key);
        if ($rec) {
            return $rec;
        }
        if ($this->_cache->add($this->prefix . $key . '_forfill', 'true', false, 1)) {
            // we obtained the _forfill lock
            // our caller will compute and set the value
            return false;
        }
        // someone else has the lock
        // "block" till we can get the value
        // actually, loop .05s waiting for it
        for ($n=0;$n<5;$n++) {
            usleep(10000);
            $rec = $this->_cache->get($this->prefix . $key);
            if ($rec) {
                return $rec;
            }
        }
        return false;
    }

    /**
     * Release the exclusive lock obtained by 
     * getforfill(). See getforfill()
     * for more details.
     *
     * @param $key string
     * @return bool
     */
    function releaseforfill ($key) {
        return $this->_cache->delete($this->prefix . $key . '_forfill');
    }
}

?>