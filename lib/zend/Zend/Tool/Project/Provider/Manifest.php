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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Tool_Framework_Manifest_ProviderManifestable
 */
require_once 'Zend/Tool/Framework/Manifest/ProviderManifestable.php';

/**
 * @see Zend_Tool_Project_Provider_Profile
 */
require_once 'Zend/Tool/Project/Provider/Profile.php';

/**
 * @see Zend_Tool_Project_Provider_Project
 */
require_once 'Zend/Tool/Project/Provider/Project.php';

/**
 * @see Zend_Tool_Project_Provider_Controller
 */
require_once 'Zend/Tool/Project/Provider/Controller.php';

/**
 * @see Zend_Tool_Project_Provider_Action
 */
require_once 'Zend/Tool/Project/Provider/Action.php';

/**
 * @see Zend_Tool_Project_Provider_View
 */
require_once 'Zend/Tool/Project/Provider/View.php';

/**
 * @see Zend_Tool_Project_Provider_Module
 */
require_once 'Zend/Tool/Project/Provider/Module.php';

/**
 * @see Zend_Tool_Project_Provider_ProjectProvider
 */
require_once 'Zend/Tool/Project/Provider/ProjectProvider.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Provider_Manifest implements 
    Zend_Tool_Framework_Manifest_ProviderManifestable
{
    
    /**
     * getProviders()
     *
     * @return array Array of Providers
     */
    public function getProviders()
    {
        return array(
            new Zend_Tool_Project_Provider_Profile(),
            new Zend_Tool_Project_Provider_Project(),
            new Zend_Tool_Project_Provider_Controller(),
            new Zend_Tool_Project_Provider_Action(),
            new Zend_Tool_Project_Provider_View(),
            new Zend_Tool_Project_Provider_Module(),
            new Zend_Tool_Project_Provider_ProjectProvider()
        );
    }
}