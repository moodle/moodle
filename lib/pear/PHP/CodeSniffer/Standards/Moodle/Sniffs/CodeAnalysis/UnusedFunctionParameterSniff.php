<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * This file is part of the CodeAnalysis addon for PHP_CodeSniffer.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-codeanalysis
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks the for unused function parameters.
 *
 * This sniff checks that all function parameters are used in the function body.
 * One exception is made for empty function bodies or function bodies that only
 * contain comments. This could be usefull for the classes that implement an
 * interface that defines multiple methods but the implementation only needs some
 * of them.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_codeanalysis_unusedfunctionparametersniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_FUNCTION);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();
        $token  = $tokens[$stackptr];

        // Skip broken function declarations.
        if (isset($token['scope_opener']) === false || isset($token['parenthesis_opener']) === false) {
            return;
        }

        $params = array();

        foreach ($phpcsfile->getmethodparameters($stackptr) as $param) {
            $params[$param['name']] = $stackptr;
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        $emptybody = true;

        for (; $next <= $end; ++$next) {
            $token = $tokens[$next];
            $code  = $token['code'];

            // Ingorable tokens.
            if (in_array($code, PHP_CodeSniffer_tokens::$emptyTokens) === true) {
                continue;

            } else if ($code === T_THROW && $emptybody === true) {
                // Throw statement and an empty body indicate an interface method.
                return;

            } else if ($code === T_RETURN && $emptybody === true) {
                // Return statement and an empty body indicate an interface method.
                $tmp = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($next + 1), null, true);

                if ($tmp === false) {
                    return;
                }

                // There is a return.
                if ($tokens[$tmp] === T_SEMICOLON) {
                    return;
                }

                $tmp = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($tmp + 1), null, true);

                // There is a return <token>.
                if ($tmp !== false && $tokens[$tmp] === T_SEMICOLON) {
                     return;
                }
            }

            $emptybody = false;

            if ($code === T_VARIABLE && isset($params[$token['content']]) === true) {
                unset($params[$token['content']]);

            } else if ($code === T_DOUBLE_QUOTED_STRING) {
                // tokenize double quote string.
                $strtokens = token_get_all(sprintf('<?php %s;?>', $token['content']));

                foreach ($strtokens as $tok) {

                    if (is_array($tok) === false || $tok[0] !== T_VARIABLE ) {
                        continue;
                    }

                    if (isset($params[$tok[1]]) === true) {
                        unset($params[$tok[1]]);
                    }
                }
            }
        }

        if ($emptybody === false && count($params) > 0) {

            foreach ($params as $paramname => $position) {
                $error = 'The method parameter '.$paramname.' is never used';
                $phpcsfile->addwarning($error, $position);
            }
        }
    }
}
