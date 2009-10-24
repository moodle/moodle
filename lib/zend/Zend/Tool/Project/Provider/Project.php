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
 * @see Zend_Tool_Project_Provider_Abstract
 */
require_once 'Zend/Tool/Project/Provider/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Project_Provider_Project extends Zend_Tool_Project_Provider_Abstract
{

    protected $_specialties = array('Info');
    
    /**
     * create()
     *
     * @param string $path
     */
    public function create($path, $nameOfProfile = null, $fileOfProfile = null)
    {
        if ($path == null) {
            $path = getcwd();
        } else {
            $path = trim($path);
            if (!file_exists($path)) {
                $created = mkdir($path);
                if (!$created) {
                    require_once 'Zend/Tool/Framework/Client/Exception.php';
                    throw new Zend_Tool_Framework_Client_Exception('Could not create requested project directory \'' . $path . '\'');
                }
            }
            $path = str_replace('\\', '/', realpath($path));
        }

        $profile = $this->_loadProfile(self::NO_PROFILE_RETURN_FALSE, $path);

        if ($profile !== false) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('A project already exists here');
        }

        $profileData = null;
        
        if ($fileOfProfile != null && file_exists($fileOfProfile)) {
            $profileData = file_get_contents($fileOfProfile);
        }
        
        $storage = $this->_registry->getStorage();
        if ($profileData == '' && $nameOfProfile != null && $storage->isEnabled()) {
            $profileData = $storage->get('project/profiles/' . $nameOfProfile . '.xml');
        }
        
        if ($profileData == '') {
            $profileData = $this->_getDefaultProfile();
        }
        
        $newProfile = new Zend_Tool_Project_Profile(array(
            'projectDirectory' => $path,
            'profileData' => $profileData
            ));

        $newProfile->loadFromData();
        
        $this->_registry->getResponse()->appendContent('Creating project at ' . $path);

        foreach ($newProfile->getIterator() as $resource) {
            $resource->create();
        }
    }
    
    public function show()
    {
        $this->_registry->getResponse()->appendContent('You probably meant to run "show project.info".', array('color' => 'yellow'));
    }
    
    public function showInfo()
    {
        $profile = $this->_loadProfile(self::NO_PROFILE_RETURN_FALSE);
        if (!$profile) {
            $this->_registry->getResponse()->appendContent('No project found.');
        } else {
            $this->_registry->getResponse()->appendContent('Working with project located at: ' . $profile->getAttribute('projectDirectory'));
        }
    }

    protected function _getDefaultProfile()
    {
        $data = <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
    <projectProfile type="default">
        <projectDirectory>
            <projectProfileFile />
            <applicationDirectory>
                <apisDirectory enabled="false" />
                <configsDirectory>
                    <applicationConfigFile type="ini" />
                </configsDirectory>
                <controllersDirectory>
                    <controllerFile controllerName="index">
                        <actionMethod actionName="index" />
                    </controllerFile>
                    <controllerFile controllerName="error" />
                </controllersDirectory>
                <layoutsDirectory enabled="false" />
                <modelsDirectory />
                <modulesDirectory enabled="false" />
                <viewsDirectory>
                    <viewScriptsDirectory>
                        <viewControllerScriptsDirectory forControllerName="index">
                            <viewScriptFile forActionName="index" />
                        </viewControllerScriptsDirectory>
                        <viewControllerScriptsDirectory forControllerName="error">
                            <viewScriptFile forActionName="error" />
                        </viewControllerScriptsDirectory>
                    </viewScriptsDirectory>
                    <viewHelpersDirectory />
                    <viewFiltersDirectory enabled="false" />
                </viewsDirectory>
                <bootstrapFile />
            </applicationDirectory>
            <dataDirectory enabled="false">
                <cacheDirectory enabled="false" />
                <searchIndexesDirectory enabled="false" />
                <localesDirectory enabled="false" />
                <logsDirectory enabled="false" />
                <sessionsDirectory enabled="false" />
                <uploadsDirectory enabled="false" />
            </dataDirectory>
            <libraryDirectory>
                <zfStandardLibraryDirectory enabled="false" />
            </libraryDirectory>
            <publicDirectory>
                <publicStylesheetsDirectory enabled="false" />
                <publicScriptsDirectory enabled="false" />
                <publicImagesDirectory enabled="false" />
                <publicIndexFile />
                <htaccessFile />
            </publicDirectory>
            <projectProvidersDirectory enabled="false" />
            <temporaryDirectory enabled="false" />
            <testsDirectory>
                <testPHPUnitConfigFile />
                <testApplicationDirectory>
                    <testApplicationBootstrapFile />
                </testApplicationDirectory>
                <testLibraryDirectory>
                    <testLibraryBootstrapFile />
                </testLibraryDirectory>
            </testsDirectory>
        </projectDirectory>
    </projectProfile>
EOS;
        return $data;
    }
}