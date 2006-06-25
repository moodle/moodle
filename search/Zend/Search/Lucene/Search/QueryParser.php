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
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Search_QueryTokenizer */
require_once 'Zend/Search/Lucene/Search/QueryTokenizer.php';

/** Zend_Search_Lucene_Index_Term */
require_once 'Zend/Search/Lucene/Index/Term.php';

/** Zend_Search_Lucene_Search_Query_Term */
require_once 'Zend/Search/Lucene/Search/Query/Term.php';

/** Zend_Search_Lucene_Search_Query_MultiTerm */
require_once 'Zend/Search/Lucene/Search/Query/MultiTerm.php';

/** Zend_Search_Lucene_Search_Query_Phrase */
require_once 'Zend/Search/Lucene/Search/Query/Phrase.php';


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_QueryParser
{

    /**
     * Parses a query string, returning a Zend_Search_Lucene_Search_Query
     *
     * @param string $strQuery
     * @return Zend_Search_Lucene_Search_Query
     */
    static public function parse($strQuery)
    {
        $tokens = new Zend_Search_Lucene_Search_QueryTokenizer($strQuery);

        // Empty query
        if (!$tokens->count()) {
            throw new Zend_Search_Lucene_Exception('Syntax error: query string cannot be empty.');
        }

        // Term query
        if ($tokens->count() == 1) {
            if ($tokens->current()->type == Zend_Search_Lucene_Search_QueryToken::TOKTYPE_WORD) {
                return new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($tokens->current()->text, 'contents'));
            } else {
                throw new Zend_Search_Lucene_Exception('Syntax error: query string must contain at least one word.');
            }
        }


        /**
         * MultiTerm Query
         *
         * Process each token that was returned by the tokenizer.
         */
        $terms = array();
        $signs = array();
        $prevToken = null;
        $openBrackets = 0;
        $field = 'contents';
        foreach ($tokens as $token) {
            switch ($token->type) {
                case Zend_Search_Lucene_Search_QueryToken::TOKTYPE_WORD:
                    $terms[] = new Zend_Search_Lucene_Index_Term($token->text, $field);
                    $field = 'contents';
                    if ($prevToken !== null &&
                        $prevToken->type == Zend_Search_Lucene_Search_QueryToken::TOKTYPE_SIGN) {
                            if ($prevToken->text == "+") {
                                $signs[] = true;
                            } else {
                                $signs[] = false;
                            }
                    } else {
                        $signs[] = null;
                    }
                    break;
                case Zend_Search_Lucene_Search_QueryToken::TOKTYPE_SIGN:
                    if ($prevToken !== null &&
                        $prevToken->type == Zend_Search_Lucene_Search_QueryToken::TOKTYPE_SIGN) {
                            throw new Zend_Search_Lucene_Exception('Syntax error: sign operator must be followed by a word.');
                    }
                    break;
                case Zend_Search_Lucene_Search_QueryToken::TOKTYPE_FIELD:
                    $field = $token->text;
                    // let previous token to be signed as next $prevToken
                    $token = $prevToken;
                    break;
                case Zend_Search_Lucene_Search_QueryToken::TOKTYPE_BRACKET:
                    $token->text=='(' ? $openBrackets++ : $openBrackets--;
            }
            $prevToken = $token;
        }

        // Finish up parsing: check the last token in the query for an opening sign or parenthesis.
        if ($prevToken->type == Zend_Search_Lucene_Search_QueryToken::TOKTYPE_SIGN) {
            throw new Zend_Search_Lucene_Exception('Syntax Error: sign operator must be followed by a word.');
        }

        // Finish up parsing: check that every opening bracket has a matching closing bracket.
        if ($openBrackets != 0) {
            throw new Zend_Search_Lucene_Exception('Syntax Error: mismatched parentheses, every opening must have closing.');
        }

        switch (count($terms)) {
            case 0:
                throw new Zend_Search_Lucene_Exception('Syntax error: bad term count.');
            case 1:
                return new Zend_Search_Lucene_Search_Query_Term($terms[0],$signs[0] !== false);
            default:
                return new Zend_Search_Lucene_Search_Query_MultiTerm($terms,$signs);
        }
    }

}

