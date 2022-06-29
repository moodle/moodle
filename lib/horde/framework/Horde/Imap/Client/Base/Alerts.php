<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Handle IMAP alerts sent from the server.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.24.0
 */
class Horde_Imap_Client_Base_Alerts
implements SplSubject
{
    /**
     * Alert data.
     *
     * @var object
     */
    protected $_alert;

    /**
     * Observers.
     *
     * @var array
     */
    protected $_observers = array();

    /**
     * Add an alert.
     *
     * @param string $alert  The alert string.
     * @param string $type   The alert type.
     */
    public function add($alert, $type = null)
    {
        $this->_alert = new stdClass;
        $this->_alert->alert = $alert;
        if (!is_null($type)) {
            $this->_alert->type = $type;
        }

        $this->notify();
    }

    /**
     * Returns the last alert received.
     *
     * @return object  Alert information. Object with these properties:
     * <pre>
     *   - alert: (string) Alert string.
     *   - type: (string) [OPTIONAL] Alert type.
     * </pre>
     */
    public function getLast()
    {
        return $this->_alert;
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

}
