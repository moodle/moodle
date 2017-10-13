<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Query the capabilities of a server.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.24.0
 */
class Horde_Imap_Client_Data_Capability
implements Serializable, SplSubject
{
    /**
     * Capability data.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Observers.
     *
     * @var array
     */
    protected $_observers = array();

    /**
     * Add a capability (and optional parameters).
     *
     * @param string $capability  The capability to add.
     * @param mixed $params       A parameter (or array of parameters) to add.
     */
    public function add($capability, $params = null)
    {
        $capability = Horde_String::upper($capability);

        if (is_null($params)) {
            if (isset($this->_data[$capability])) {
                return;
            }
            $params = true;
        } else {
            if (!is_array($params)) {
                $params = array($params);
            }
            $params = array_map('Horde_String::upper', $params);

            if (isset($this->_data[$capability]) &&
                is_array($this->_data[$capability])) {
                $params = array_merge($this->_data[$capability], $params);
            }
        }

        $this->_data[$capability] = $params;
        $this->notify();
    }

    /**
     * Remove a capability.
     *
     * @param string $capability  The capability to remove.
     * @param string $params      A parameter (or array of parameters) to
     *                            remove from the capability.
     */
    public function remove($capability, $params = null)
    {
        $capability = Horde_String::upper($capability);

        if (is_null($params)) {
            unset($this->_data[$capability]);
        } elseif (isset($this->_data[$capability])) {
            if (!is_array($params)) {
                $params = array($params);
            }
            $params = array_map('Horde_String::upper', $params);

            $this->_data[$capability] = is_array($this->_data[$capability])
                ? array_diff($this->_data[$capability], $params)
                : array();

            if (empty($this->_data[$capability])) {
                unset($this->_data[$capability]);
            }
        }

        $this->notify();
    }

    /**
     * Returns whether the server supports the given capability.
     *
     * @param string $capability  The capability string to query.
     * @param string $parameter   If set, require the parameter to exist.
     *
     * @return boolean  True if the capability (and parameter) exist.
     */
    public function query($capability, $parameter = null)
    {
        $capability = Horde_String::upper($capability);

        if (!isset($this->_data[$capability])) {
            return false;
        }

        return is_null($parameter) ?:
               (is_array($this->_data[$capability]) &&
                in_array(Horde_String::upper($parameter), $this->_data[$capability]));
    }

    /**
     * Return the list of parameters for an extension.
     *
     * @param string $capability  The capability string to query.
     *
     * @return array  An array of parameters if the extension exists and
     *                supports parameters.  Otherwise, an empty array.
     */
    public function getParams($capability)
    {
        return ($this->query($capability) && is_array($out = $this->_data[Horde_String::upper($capability)]))
            ? $out
            : array();
    }

    /**
     * Is the extension enabled?
     *
     * @param string $capability  The extension (+ parameter) to query. If
     *                            null, returns all enabled extensions.
     *
     * @return mixed  If $capability is null, return all enabled extensions.
     *                Otherwise, true if the extension (+ parameter) is
     *                enabled.
     */
    public function isEnabled($capability = null)
    {
        return is_null($capability)
            ? array()
            : false;
    }

    /**
     * Returns the raw data.
     *
     * @deprecated
     *
     * @return array  Capability data.
     */
    public function toArray()
    {
        return $this->_data;
    }

    /* SplSubject methods. */

    /**
     */
    public function attach(SplObserver $observer)
    {
        $this->detach($observer);
        $this->_observers[] = $observer;
    }

    /**
     */
    public function detach(SplObserver $observer)
    {
        if (($key = array_search($observer, $this->_observers, true)) !== false) {
            unset($this->_observers[$key]);
        }
    }

    /**
     * Notification is triggered internally whenever the object's internal
     * data storage is altered.
     */
    public function notify()
    {
        foreach ($this->_observers as $val) {
            $val->update($this);
        }
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return json_encode($this->_data);
    }

    /**
     */
    public function unserialize($data)
    {
        $this->_data = json_decode($data, true);
    }

}
