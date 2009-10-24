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
 * @see Zend_Tool_Project_Context_Filesystem_Directory
 */
require_once 'Zend/Tool/Project/Context/Filesystem/Directory.php';

/**
 * @see Zend_Tool_Project_Context_System_Interface
 */
require_once 'Zend/Tool/Project/Context/System/Interface.php';

/**
 * @see Zend_Tool_Project_Context_System_NotOverwritable
 */
require_once 'Zend/Tool/Project/Context/System/NotOverwritable.php';

/**
 * This class is the front most class for utilizing Zend_Tool_Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 * 
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Context_System_ProjectProvidersDirectory 
    extends Zend_Tool_Project_Context_Filesystem_Directory
    implements Zend_Tool_Project_Context_System_Interface,
               Zend_Tool_Project_Context_System_NotOverwritable
{
    
    /**
     * @var string
     */
    protected $_filesystemName = 'providers';
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ProjectProvidersDirectory';
    }
    
    /**
     * init()
     *
     * @return Zend_Tool_Project_Context_System_ProjectProvidersDirectory
     */
    public function init()
    {
        parent::init();
        
        if (file_exists($this->getPath())) {

            foreach (new DirectoryIterator($this->getPath()) as $item) {
                if ($item->isFile()) {
                    $loadableFiles[] = $item->getPathname();
                }
            }
            
            if ($loadableFiles) {
                
                // @todo process and add the files to the system for usage.
                
            }
        }
        
        return $this;
    }
    
}