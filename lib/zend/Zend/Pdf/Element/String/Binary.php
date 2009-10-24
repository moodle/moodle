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
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** Zend_Pdf_Element_String */
require_once 'Zend/Pdf/Element/String.php';


/**
 * PDF file 'binary string' element implementation
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Element_String_Binary extends Zend_Pdf_Element_String
{
    /**
     * Object value
     *
     * @var string
     */
    public $value;


    /**
     * Escape string according to the PDF rules
     *
     * @param string $inStr
     * @return string
     */
    public static function escape($inStr)
    {
        $outStr = '';

        for ($count = 0; $count < strlen($inStr); $count++) {
            $outStr .= sprintf('%02X', ord($inStr[$count]));
        }
        return $outStr;
    }


    /**
     * Unescape string according to the PDF rules
     *
     * @param string $inStr
     * @return string
     */
    public static function unescape($inStr)
    {
        $outStr = '';
        $nextHexCode = '';

        for ($count = 0; $count < strlen($inStr); $count++) {
            $nextCharCode = ord($inStr[$count]);

            if( ($nextCharCode >= 48  /*'0'*/ &&
                 $nextCharCode <= 57  /*'9'*/   ) ||
                ($nextCharCode >= 97  /*'a'*/ &&
                 $nextCharCode <= 102 /*'f'*/   ) ||
                ($nextCharCode >= 65  /*'A'*/ &&
                 $nextCharCode <= 70  /*'F'*/   ) ) {
                $nextHexCode .= $inStr[$count];
            }

            if (strlen($nextHexCode) == 2) {
                $outStr .= chr(intval($nextHexCode, 16));
                $nextHexCode = '';
            }
        }

        if ($nextHexCode != '') {
            // We have odd number of digits.
            // Final digit is assumed to be '0'
            $outStr .= chr(base_convert($nextHexCode . '0', 16, 10));
        }

        return $outStr;
    }


    /**
     * Return object as string
     *
     * @param Zend_Pdf_Factory $factory
     * @return string
     */
    public function toString($factory = null)
    {
        return '<' . self::escape((string)$this->value) . '>';
    }
}
