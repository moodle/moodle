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
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @see Zend_File_Transfer_Adapter_Abstract
 */
require_once 'Zend/File/Transfer/Adapter/Abstract.php';

/**
 * File transfer adapter class for the HTTP protocol
 *
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_File_Transfer_Adapter_Http extends Zend_File_Transfer_Adapter_Abstract
{
    protected static $_callbackApc            = 'apc_fetch';
    protected static $_callbackUploadProgress = 'uploadprogress_get_info';

    /**
     * Constructor for Http File Transfers
     *
     * @param array $options OPTIONAL Options to set
     */
    public function __construct($options = array())
    {
        if (ini_get('file_uploads') == false) {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('File uploads are not allowed in your php config!');
        }

        $this->_files = $this->_prepareFiles($_FILES);
        $this->addValidator('Upload', false, $this->_files);

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Sets a validator for the class, erasing all previous set
     *
     * @param  string|array $validator Validator to set
     * @param  string|array $files     Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setValidators(array $validators, $files = null)
    {
        $this->clearValidators();
        return $this->addValidators($validators, $files);
    }

    /**
     * Remove an individual validator
     *
     * @param  string $name
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function removeValidator($name)
    {
        if ($name == 'Upload') {
            return $this;
        }

        return parent::removeValidator($name);
    }

    /**
     * Remove an individual validator
     *
     * @param  string $name
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function clearValidators()
    {
        parent::clearValidators();
        $this->addValidator('Upload', false, $this->_files);

        return $this;
    }

    /**
     * Send the file to the client (Download)
     *
     * @param  string|array $options Options for the file(s) to send
     * @return void
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function send($options = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Checks if the files are valid
     *
     * @param  string|array $files (Optional) Files to check
     * @return boolean True if all checks are valid
     */
    public function isValid($files = null)
    {
        // Workaround for a PHP error returning empty $_FILES when form data exceeds php settings
        if (empty($this->_files) && ($_SERVER['CONTENT_LENGTH'] > 0)) {
            if (is_array($files)) {
                $files = current($files);
            }

            $temp = array($files => array(
                'name'  => $files,
                'error' => 1));
            $validator = $this->_validators['Zend_Validate_File_Upload'];
            $validator->setFiles($temp)
                      ->isValid($files, null);
            $this->_messages += $validator->getMessages();
            return false;
        }

        return parent::isValid($files);
    }

    /**
     * Receive the file from the client (Upload)
     *
     * @param  string|array $files (Optional) Files to receive
     * @return bool
     */
    public function receive($files = null)
    {
        if (!$this->isValid($files)) {
            return false;
        }

        $check = $this->_getFiles($files);
        foreach ($check as $file => $content) {
            if (!$content['received']) {
                $directory   = '';
                $destination = $this->getDestination($file);
                if ($destination !== null) {
                    $directory = $destination . DIRECTORY_SEPARATOR;
                }

                $filename = $directory . $content['name'];
                $rename   = $this->getFilter('Rename');
                if ($rename !== null) {
                    $tmp = $rename->getNewName($content['tmp_name']);
                    if ($tmp != $content['tmp_name']) {
                        $filename = $tmp;
                    }

                    if (dirname($filename) == '.') {
                        $filename = $directory . $filename;
                    }

                    $key = array_search(get_class($rename), $this->_files[$file]['filters']);
                    unset($this->_files[$file]['filters'][$key]);
                }

                // Should never return false when it's tested by the upload validator
                if (!move_uploaded_file($content['tmp_name'], $filename)) {
                    if ($content['options']['ignoreNoFile']) {
                        $this->_files[$file]['received'] = true;
                        $this->_files[$file]['filtered'] = true;
                        continue;
                    }

                    $this->_files[$file]['received'] = false;
                    return false;
                }

                if ($rename !== null) {
                    $this->_files[$file]['destination'] = dirname($filename);
                    $this->_files[$file]['name']        = basename($filename);
                }

                $this->_files[$file]['tmp_name'] = $filename;
                $this->_files[$file]['received'] = true;
            }

            if (!$content['filtered']) {
                if (!$this->_filter($file)) {
                    $this->_files[$file]['filtered'] = false;
                    return false;
                }

                $this->_files[$file]['filtered'] = true;
            }
        }

        return true;
    }

    /**
     * Checks if the file was already sent
     *
     * @param  string|array $file Files to check
     * @return bool
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function isSent($files = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Checks if the file was already received
     *
     * @param  string|array $files (Optional) Files to check
     * @return bool
     */
    public function isReceived($files = null)
    {
        $files = $this->_getFiles($files, false, true);
        if (empty($files)) {
            return false;
        }

        foreach ($files as $content) {
            if ($content['received'] !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the file was already filtered
     *
     * @param  string|array $files (Optional) Files to check
     * @return bool
     */
    public function isFiltered($files = null)
    {
        $files = $this->_getFiles($files, false, true);
        if (empty($files)) {
            return false;
        }

        foreach ($files as $content) {
            if ($content['filtered'] !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * Has a file been uploaded ?
     *
     * @param  array|string|null $file
     * @return bool
     */
    public function isUploaded($files = null)
    {
        $files = $this->_getFiles($files, false, true);
        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            if (empty($file['name'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the actual progress of file up-/downloads
     *
     * @param  string $id The upload to get the progress for
     * @return array|null
     */
    public static function getProgress($id = null)
    {
        if (!function_exists('apc_fetch') and !function_exists('uploadprogress_get_info')) {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new Zend_File_Transfer_Exception('Wether APC nor uploadprogress extension installed');
        }

        $session = 'Zend_File_Transfer_Adapter_Http_ProgressBar';
        $status  = array(
            'total'    => 0,
            'current'  => 0,
            'rate'     => 0,
            'message'  => '',
            'done'     => false
        );

        if (is_array($id)) {
            if (isset($id['progress'])) {
                $adapter = $id['progress'];
            }

            if (isset($id['session'])) {
                $session = $id['session'];
            }

            if (isset($id['id'])) {
                $id = $id['id'];
            } else {
                unset($id);
            }
        }

        if (!empty($id) && (($id instanceof Zend_ProgressBar_Adapter) || ($id instanceof Zend_ProgressBar))) {
            $adapter = $id;
            unset($id);
        }

        if (empty($id)) {
            if (!isset($_GET['progress_key'])) {
                $status['message'] = 'No upload in progress';
                $status['done']    = true;
            } else {
                $id = $_GET['progress_key'];
            }
        }

        if (!empty($id)) {
            if (self::isApcAvailable()) {

                $call = call_user_func(self::$_callbackApc, 'upload_' . $id);
                if (is_array($call)) {
                    $status = $call + $status;
                }
            } else if (self::isUploadProgressAvailable()) {
                $call = call_user_func(self::$_callbackUploadProgress, $id);
                if (is_array($call)) {
                    $status = $call + $status;
                    $status['total']   = $status['bytes_total'];
                    $status['current'] = $status['bytes_uploaded'];
                    $status['rate']    = $status['speed_average'];
                    if ($status['total'] == $status['current']) {
                        $status['done'] = true;
                    }
                }
            }

            if (!is_array($call)) {
                $status['done']    = true;
                $status['message'] = 'Failure while retrieving the upload progress';
            } else if (!empty($status['cancel_upload'])) {
                $status['done']    = true;
                $status['message'] = 'The upload has been canceled';
            } else {
                $status['message'] = self::_toByteString($status['current']) . " - " . self::_toByteString($status['total']);
            }

            $status['id'] = $id;
        }

        if (isset($adapter) && isset($status['id'])) {
            if ($adapter instanceof Zend_ProgressBar_Adapter) {
                require_once 'Zend/ProgressBar.php';
                $adapter = new Zend_ProgressBar($adapter, 0, $status['total'], $session);
            }

            if (!($adapter instanceof Zend_ProgressBar)) {
                require_once 'Zend/File/Transfer/Exception.php';
                throw new Zend_File_Transfer_Exception('Unknown Adapter given');
            }

            if ($status['done']) {
                $adapter->finish();
            } else {
                $adapter->update($status['current'], $status['message']);
            }

            $status['progress'] = $adapter;
        }

        return $status;
    }

    /**
     * Checks the APC extension for progress information
     *
     * @return boolean
     */
    public static function isApcAvailable()
    {
        return (bool) ini_get('apc.enabled') && (bool) ini_get('apc.rfc1867') && is_callable(self::$_callbackApc);
    }

    /**
     * Checks the UploadProgress extension for progress information
     *
     * @return boolean
     */
    public static function isUploadProgressAvailable()
    {
        return is_callable(self::$_callbackUploadProgress);
    }

    /**
     * Prepare the $_FILES array to match the internal syntax of one file per entry
     *
     * @param  array $files
     * @return array
     */
    protected function _prepareFiles(array $files = array())
    {
        $result = array();
        foreach ($files as $form => $content) {
            if (is_array($content['name'])) {
                foreach ($content as $param => $file) {
                    foreach ($file as $number => $target) {
                        $result[$form . '_' . $number . '_'][$param]      = $target;
                        $result[$form . '_' . $number . '_']['options']   = $this->_options;
                        $result[$form . '_' . $number . '_']['validated'] = false;
                        $result[$form . '_' . $number . '_']['received']  = false;
                        $result[$form . '_' . $number . '_']['filtered']  = false;
                        $result[$form]['multifiles'][$number] = $form . '_' . $number . '_';
                        $result[$form]['name'] = $form;
                    }
                }
            } else {
                $result[$form]              = $content;
                $result[$form]['options']   = $this->_options;
                $result[$form]['validated'] = false;
                $result[$form]['received']  = false;
                $result[$form]['filtered']  = false;
            }
        }

        return $result;
    }
}
