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
 * Utility class for static functions needed by Zend_Gdata_App
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_Util
{

    /**
     *  Convert timestamp into RFC 3339 date string.
     *  2005-04-19T15:30:00
     *
     * @param int $timestamp
     * @throws Zend_Gdata_App_InvalidArgumentException
     */
    public static function formatTimestamp($timestamp)
    {
        $rfc3339 = '/^(\d{4})\-?(\d{2})\-?(\d{2})((T|t)(\d{2})\:?(\d{2})' .
                   '\:?(\d{2})(\.\d{1,})?((Z|z)|([\+\-])(\d{2})\:?(\d{2})))?$/';

        if (ctype_digit($timestamp)) {
            return gmdate('Y-m-d\TH:i:sP', $timestamp);
        } elseif (preg_match($rfc3339, $timestamp) > 0) {
            // timestamp is already properly formatted
            return $timestamp;
        } else {
            $ts = strtotime($timestamp);
            if ($ts === false) {
                require_once 'Zend/Gdata/App/InvalidArgumentException.php';
                throw new Zend_Gdata_App_InvalidArgumentException("Invalid timestamp: $timestamp.");
            }
            return date('Y-m-d\TH:i:s', $ts);
        }
    }

}
