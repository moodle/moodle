<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ControlStructures;

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detects using 0 and variable numeric arguments on `break` and `continue` statements.
 *
 * This sniff checks for:
 * - Using `break` and/or `continue` with a variable as the numeric argument.
 * - Using `break` and/or `continue` with a zero - 0 - as the numeric argument.
 *
 * PHP version 5.4
 *
 * @link https://www.php.net/manual/en/migration54.incompatible.php
 * @link https://www.php.net/manual/en/control-structures.break.php
 * @link https://www.php.net/manual/en/control-structures.continue.php
 *
 * @since 5.5
 * @since 5.6 Now extends the base `Sniff` class.
 */
class ForbiddenBreakContinueVariableArgumentsSniff extends Sniff
{
    /**
     * Error types this sniff handles for forbidden break/continue arguments.
     *
     * Array key is the error code. Array value will be used as part of the error message.
     *
     * @since 7.0.5
     * @since 7.1.0 Changed from class constants to property.
     *
     * @var array
     */
    private $errorTypes = array(
        'variableArgument' => 'a variable argument',
        'zeroArgument'     => '0 as an argument',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     *
     * @return array
     */
    public function register()
    {
        return array(\T_BREAK, \T_CONTINUE);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('5.4') === false) {
            return;
        }

        $tokens             = $phpcsFile->getTokens();
        $nextSemicolonToken = $phpcsFile->findNext(array(\T_SEMICOLON, \T_CLOSE_TAG), ($stackPtr), null, false);
        $errorType          = '';
        for ($curToken = $stackPtr + 1; $curToken < $nextSemicolonToken; $curToken++) {
            if ($tokens[$curToken]['type'] === 'T_STRING') {
                // If the next non-whitespace token after the string
                // is an opening parenthesis then it's a function call.
                $openBracket = $phpcsFile->findNext(Tokens::$emptyTokens, $curToken + 1, null, true);
                if ($tokens[$openBracket]['code'] === \T_OPEN_PARENTHESIS) {
                    $errorType = 'variableArgument';
                    break;
                }

            } elseif (\in_array($tokens[$curToken]['type'], array('T_VARIABLE', 'T_FUNCTION', 'T_CLOSURE'), true)) {
                $errorType = 'variableArgument';
                break;

            } elseif ($tokens[$curToken]['type'] === 'T_LNUMBER' && $tokens[$curToken]['content'] === '0') {
                $errorType = 'zeroArgument';
                break;
            }
        }

        if ($errorType !== '') {
            $error     = 'Using %s on break or continue is forbidden since PHP 5.4';
            $errorCode = $errorType . 'Found';
            $data      = array($this->errorTypes[$errorType]);

            $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
        }
    }
}
