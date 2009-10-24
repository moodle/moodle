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
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Concrete base class for bootstrap classes
 *
 * Registers and utilizes Zend_Controller_Front by default.
 *
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Bootstrap_Bootstrap 
    extends Zend_Application_Bootstrap_BootstrapAbstract
{
    /**
     * Constructor
     *
     * Ensure FrontController resource is registered
     * 
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application 
     * @return void
     */
    public function __construct($application)
    {
        parent::__construct($application);
        if (!$this->hasPluginResource('FrontController')) {
            $this->registerPluginResource('FrontController');
        }
    }

    /**
     * Run the application
     *
     * Checks to see that we have a default controller directory. If not, an 
     * exception is thrown.
     *
     * If so, it registers the bootstrap with the 'bootstrap' parameter of 
     * the front controller, and dispatches the front controller.
     * 
     * @return void
     * @throws Zend_Application_Bootstrap_Exception
     */
    public function run()
    {
        $front   = $this->getResource('FrontController');
        $default = $front->getDefaultModule();
        if (null === $front->getControllerDirectory($default)) {
            throw new Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $this);
        $front->dispatch();
    }
}
