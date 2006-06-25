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
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Analysis_Analyzer_Common */
require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Search_Lucene_Analysis_Analyzer_Common_Text extends Zend_Search_Lucene_Analysis_Analyzer_Common
{
    /**
     * Tokenize text to a terms
     * Returns array of Zend_Search_Lucene_Analysis_Token objects
     *
     * @param string $data
     * @return array
     */
    public function tokenize($data)
    {
        $tokenStream = array();

        $position = 0;
        while ($position < strlen($data)) {
            // skip white space
            while ($position < strlen($data) && !ctype_alpha( $data{$position} )) {
                $position++;
            }

            $termStartPosition = $position;

            // read token
            while ($position < strlen($data) && ctype_alpha( $data{$position} )) {
                $position++;
            }

            // Empty token, end of stream.
            if ($position == $termStartPosition) {
                break;
            }

            $token = new Zend_Search_Lucene_Analysis_Token(substr($data,
                                             $termStartPosition,
                                             $position-$termStartPosition),
                                      $termStartPosition,
                                      $position);
            $tokenStream[] = $this->normalize($token);
        }

        return $tokenStream;
    }
}

