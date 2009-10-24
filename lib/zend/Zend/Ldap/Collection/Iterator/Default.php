<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Ldap_Collection_Iterator_Interface
 */
require_once 'Zend/Ldap/Collection/Iterator/Interface.php';

/**
 * Zend_Ldap_Collection_Iterator_Default is the default collection iterator implementation
 * using ext/ldap
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap_Collection_Iterator_Default implements Zend_Ldap_Collection_Iterator_Interface
{
    /**
     * LDAP Connection
     *
     * @var Zend_Ldap
     */
    protected $_ldap = null;

    /**
     * Result identifier resource
     *
     * @var resource
     */
    protected $_resultId = null;

    /**
     * Current result entry identifier
     *
     * @var resource
     */
    protected $_current = null;

    /**
     * Current result entry DN
     *
     * @var string
     */
    protected $_currentDn = null;

    /**
     * Number of items in query result
     *
     * @var integer
     */
    protected $_itemCount = -1;

    /**
     * Constructor.
     *
     * @param  Zend_Ldap $ldap
     * @param  resource  $resultId
     * @return void
     */
    public function __construct(Zend_Ldap $ldap, $resultId)
    {
        $this->_ldap = $ldap;
        $this->_resultId = $resultId;
        $this->_itemCount = @ldap_count_entries($ldap->getResource(), $resultId);
        if ($this->_itemCount === false) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception($this->_ldap, 'counting entries');
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Closes the current result set
     *
     * @return bool
     */
    public function close()
    {
        $isClosed = false;
        if (is_resource($this->_resultId)) {
             $isClosed = @ldap_free_result($this->_resultId);
             $this->_resultId = null;
             $this->_currentDn = null;
             $this->_current = null;
        }
        return $isClosed;
    }

    /**
     * Gets the current LDAP connection.
     *
     * @return Zend_Ldap
     */
    public function getLdap()
    {
        return $this->_ldap;
    }

    /**
     * Returns the number of items in current result
     * Implements Countable
     *
     * @return int
     */
    public function count()
    {
        return $this->_itemCount;
    }

    /**
     * Return the current result item
     * Implements Iterator
     *
     * @return array
     * @throws Zend_Ldap_Exception
     */
    public function current()
    {
        if (!is_resource($this->_current) || !is_string($this->_currentDn)) return null;

        $entry = array('dn' => $this->_currentDn);
        $ber_identifier = null;
        $name = @ldap_first_attribute($this->_ldap->getResource(), $this->_current,
            $ber_identifier);
        while ($name)
        {
            $data = @ldap_get_values_len($this->_ldap->getResource(), $this->_current, $name);
            unset($data['count']);
            $entry[strtolower($name)] = $data;
            $name = @ldap_next_attribute($this->_ldap->getResource(), $this->_current,
                $ber_identifier);
        }
        ksort($entry, SORT_LOCALE_STRING);
        return $entry;
    }

    /**
     * Return the result item key
     * Implements Iterator
     *
     * @return int
     */
    public function key()
    {
        return $this->_currentDn;
    }

    /**
     * Move forward to next result item
     * Implements Iterator
     *
     * @throws Zend_Ldap_Exception
     */
    public function next()
    {
        if (!is_resource($this->_current)) return;
        $this->_current = @ldap_next_entry($this->_ldap->getResource(), $this->_current);
        /**
         * @see Zend_Ldap_Exception
         */
        require_once 'Zend/Ldap/Exception.php';
        if ($this->_current === false) {
            $msg = $this->_ldap->getLastError($code);
            if ($code === Zend_Ldap_Exception::LDAP_SIZELIMIT_EXCEEDED) {
                // we have reached the size limit enforced by the server
                return;
            } else if ($code > Zend_Ldap_Exception::LDAP_SUCCESS) {
                 throw new Zend_Ldap_Exception($this->_ldap, 'getting next entry (' . $msg . ')');
            }
        }
        $this->_storeCurrentDn();
    }

    /**
     * Rewind the Iterator to the first result item
     * Implements Iterator
     *
     * @throws Zend_Ldap_Exception
     */
    public function rewind()
    {
        if (!is_resource($this->_resultId)) return;
        $this->_current = @ldap_first_entry($this->_ldap->getResource(), $this->_resultId);
        /**
         * @see Zend_Ldap_Exception
         */
        require_once 'Zend/Ldap/Exception.php';
        if ($this->_current === false &&
                $this->_ldap->getLastErrorCode() > Zend_Ldap_Exception::LDAP_SUCCESS) {
            throw new Zend_Ldap_Exception($this->_ldap, 'getting first entry');
        }

        $this->_storeCurrentDn();
    }

    /**
     * Stores the current DN
     *
     * @return void
     * @throws Zend_Ldap_Exception
     */
    protected function _storeCurrentDn()
    {
        if (is_resource($this->_current)) {
            $this->_currentDn = @ldap_get_dn($this->_ldap->getResource(), $this->_current);
            if ($this->_currentDn === false) {
                throw new Zend_Ldap_Exception($this->_ldap, 'getting dn');
            }
        } else {
            $this->_currentDn = null;
        }
    }

    /**
     * Check if there is a current result item
     * after calls to rewind() or next()
     * Implements Iterator
     *
     * @return boolean
     */
    public function valid()
    {
        return (is_resource($this->_current) && is_string($this->_currentDn));
    }

}