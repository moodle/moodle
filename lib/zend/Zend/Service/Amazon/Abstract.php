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
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Service/Abstract.php';

/**
 * Abstract Amazon class that handles the credentials for any of the Web Services that
 * Amazon offers
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Service_Amazon_Abstract extends Zend_Service_Abstract
{
    /**
     * @var string Amazon Access Key
     */
    protected static $_defaultAccessKey = null;

    /**
     * @var string Amazon Secret Key
     */
    protected static $_defaultSecretKey = null;

    /**
     * @var string Amazon Region
     */
    protected static $_defaultRegion = null;

    /**
     * @var string Amazon Secret Key
     */
    protected $_secretKey;

    /**
     * @var string Amazon Access Key
     */
    protected $_accessKey;

    /**
     * @var string Amazon Region
     */
    protected $_region;

    /**
     * An array that contains all the valid Amazon Ec2 Regions.
     *
     * @var array
     */
    protected static $_validEc2Regions = array('eu-west-1', 'us-east-1');

    /**
     * Set the keys to use when accessing SQS.
     *
     * @param  string $access_key       Set the default Access Key
     * @param  string $secret_key       Set the default Secret Key
     * @return void
     */
    public static function setKeys($accessKey, $secretKey)
    {
        self::$_defaultAccessKey = $accessKey;
        self::$_defaultSecretKey = $secretKey;
    }

    /**
     * Set which region you are working in.  It will append the
     * end point automaticly
     *
     * @param string $region
     */
    public static function setRegion($region)
    {
        if(in_array(strtolower($region), self::$_validEc2Regions, true)) {
            self::$_defaultRegion = $region;
        } else {
            require_once 'Zend/Service/Amazon/Exception.php';
            throw new Zend_Service_Amazon_Exception('Invalid Amazon Ec2 Region');
        }
    }

    /**
     * Create Amazon Sqs client.
     *
     * @param  string $access_key       Override the default Access Key
     * @param  string $secret_key       Override the default Secret Key
     * @param  string $region           Sets the AWS Region
     * @return void
     */
    public function __construct($accessKey=null, $secretKey=null, $region=null)
    {
        if(!$accessKey) {
            $accessKey = self::$_defaultAccessKey;
        }
        if(!$secretKey) {
            $secretKey = self::$_defaultSecretKey;
        }
        if(!$region) {
            $region = self::$_defaultRegion;
        } else {
            // make rue the region is valid
            if(!empty($region) && !in_array(strtolower($region), self::$_validEc2Regions, true)) {
                require_once 'Zend/Service/Amazon/Exception.php';
                throw new Zend_Service_Amazon_Exception('Invalid Amazon Ec2 Region');
            }
        }

        if(!$accessKey || !$secretKey) {
            require_once 'Zend/Service/Amazon/Exception.php';
            throw new Zend_Service_Amazon_Exception("AWS keys were not supplied");
        }
        $this->_accessKey = $accessKey;
        $this->_secretKey = $secretKey;
        $this->_region = $region;
    }

    /**
     * Method to fetch the AWS Region
     *
     * @return string
     */
    protected function _getRegion()
    {
        return (!empty($this->_region)) ? $this->_region . '.' : '';
    }

    /**
     * Method to fetch the Access Key
     *
     * @return string
     */
    protected function _getAccessKey()
    {
        return $this->_accessKey;
    }

    /**
     * Method to fetch the Secret AWS Key
     *
     * @return string
     */
    protected function _getSecretKey()
    {
        return $this->_secretKey;
    }
}
