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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Resource for creating database adapter
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Db extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Adapter to use
     *
     * @var string
     */
    protected $_adapter = null;

    /**
     * @var Zend_Db_Adapter_Interface
     */
    protected $_db;
    
    /**
     * Parameters to use
     *
     * @var array
     */
    protected $_params = array();
    
    /**
     * Wether to register the created adapter as default table adapter
     *
     * @var boolean
     */
    protected $_isDefaultTableAdapter = true; 
    
    /**
     * Set the adapter
     * 
     * @param  $adapter string
     * @return Zend_Application_Resource_Db
     */
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Adapter type to use
     * 
     * @return string
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set the adapter params
     * 
     * @param  $adapter string
     * @return Zend_Application_Resource_Db
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Adapter parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
    
    /**
     * Set whether to use this as default table adapter
     *
     * @param  boolean $defaultTableAdapter
     * @return Zend_Application_Resource_Db
     */
    public function setIsDefaultTableAdapter($isDefaultTableAdapter)
    {
        $this->_isDefaultTableAdapter = $isDefaultTableAdapter;
        return $this;
    }

    /**
     * Is this adapter the default table adapter?
     * 
     * @return void
     */
    public function isDefaultTableAdapter()
    {
        return $this->_isDefaultTableAdapter;
    }

    /**
     * Retrieve initialized DB connection
     * 
     * @return null|Zend_Db_Adapter_Interface
     */
    public function getDbAdapter()
    {
        if ((null === $this->_db) 
            && (null !== ($adapter = $this->getAdapter()))
        ) {
            $this->_db = Zend_Db::factory($adapter, $this->getParams());
        }
        return $this->_db;
    }
    
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Db_Adapter_Abstract|null
     */
    public function init()
    {
        if (null !== ($db = $this->getDbAdapter())) {
            if ($this->isDefaultTableAdapter()) {
                Zend_Db_Table::setDefaultAdapter($db);
            }
            return $db;
        }
    }
}
