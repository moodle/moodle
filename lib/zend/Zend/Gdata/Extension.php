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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_Extension
 */
require_once 'Zend/Gdata/App/Extension.php';

/**
 * Represents a GData extension
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Extension extends Zend_Gdata_App_Extension
{

    protected $_rootNamespace = 'gd';

    public function __construct()
    {
        /* NOTE: namespaces must be registered before calling parent */
        $this->registerNamespace('openSearch',
                'http://a9.com/-/spec/opensearchrss/1.0/');
        $this->registerNamespace('rss',
                'http://blogs.law.harvard.edu/tech/rss');
        $this->registerNamespace('gd',
                'http://schemas.google.com/g/2005');
        parent::__construct();
    }

}
