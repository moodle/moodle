<?php
/**
 ** This class abstracts eaccelerator/turckmmcache
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
 ** Note: do NOT store booleans here. For compatibility with
 ** memcached, a false value is indistinguisable from a 
 ** "not found in cache" response.
 **/


class eaccelerator {

    function eaccelerator() {
        global $CFG;
        if ( function_exists('eaccelerator_get')) {
            $this->mode = 'eaccelerator';
        } elseif (function_exists('mmcache_get')) {
            $this->mode = 'mmcache';
        } else {
            debugging("\$CFG->eaccelerator is set to true but the required functions are not available. You need to have either eaccelerator or turckmmcache extensions installed, compiled with the shmem keys option enabled.");
        }

        $this->prefix = $CFG->dbname .'|' . $CFG->prefix . '|';
    }

    function status() {
        if (isset($this->mode)) {
            return true;
        }
        return false;
    }

    function set($key, $value, $ttl=0) {
        $set    = $this->mode . '_put';
        $unlock = $this->mode . '_unlock';

        // we may have acquired a lock via getforfill
        // release if it exists
        @$unlock($this->prefix . $key . '_forfill');

        return $set($this->prefix . $key, serialize($value), $ttl);
    }

    function get($key) {
        $fn = $this->mode . '_get';
        $rec = $fn($this->prefix . $key);
        if (is_null($rec)) {
            return false;
        }
        return unserialize($rec);
    } 
        
    function delete($key) {
        $fn = $this->mode . '_rm';
        return $fn($this->prefix . $key);
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
     * @return mixed on cache hit, false otherwise
     */
    function getforfill ($key) {
        $get    = $this->mode . '_get';
        $lock   = $this->mode . '_lock';
        
        $rec = $get($this->prefix . $key);
        if (!is_null($rec)) {
            return unserialize($rec);
        }
        if ($lock($this->prefix . $key . '_forfill')) {
            // we obtained the _forfill lock
            // our caller will compute and set the value
            return false;
        }
        // someone else has the lock
        // "block" till we can get the value
        // actually, loop .05s waiting for it
        for ($n=0;$n<5;$n++) {
            usleep(10000);
            $rec = $get($this->prefix . $key);
            if (!is_null($rec)) {
                return unserialize($rec);
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
        $unlock = $this->mode . '_unlock';
        return $unlock($this->prefix . $key . '_forfill');
    }
}

?>