<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\InitialValue;

use PHPCompatibility\Sniffs\InitialValue\NewConstantScalarExpressionsSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect a heredoc being used to set an initial value.
 *
 * As of PHP 5.3.0, it's possible to initialize static variables, class properties
 * and constants declared using the `const` keyword, using the Heredoc syntax.
 * And while not documented, heredoc initialization can now also be used for function param defaults.
 * See: https://3v4l.org/JVH8W
 *
 * These heredocs (still) cannot contain variables. That's, however, outside the scope of the
 * PHPCompatibility library until such time as there is a PHP version in which this would be accepted.
 *
 * PHP version 5.3
 *
 * @link https://www.php.net/manual/en/migration53.new-features.php
 * @link https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc
 *
 * @since 7.1.4
 * @since 8.2.0 Now extends the NewConstantScalarExpressionsSniff instead of the base Sniff class.
 * @since 9.0.0 Renamed from `NewHeredocInitializeSniff` to `NewHeredocSniff`.
 */
class NewHeredocSniff extends NewConstantScalarExpressionsSniff
{

    /**
     * Error message.
     *
     * @since 8.2.0
     *
     * @var string
     */
    const ERROR_PHRASE = 'Initializing %s using the Heredoc syntax was not supported in PHP 5.2 or earlier';

    /**
     * Partial error phrases to be used in combination with the error message constant.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $errorPhrases = array(
        'const'     => 'constants',
        'property'  => 'class properties',
        'staticvar' => 'static variables',
        'default'   => 'default parameter values',
    );


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 8.2.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('5.2') !== true);
    }


    /**
     * Is a value declared and does the declared value not contain an heredoc ?
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     * @param int                   $end       The end of the value definition.
     *
     * @return bool True if no heredoc (or assignment) is found, false otherwise.
     */
    protected function isValidAssignment(File $phpcsFile, $stackPtr, $end)
    {
        $tokens = $phpcsFile->getTokens();
        $next   = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), $end, true);
        if ($next === false || $tokens[$next]['code'] !== \T_EQUAL) {
            // No value assigned.
            return true;
        }

        return ($phpcsFile->findNext(\T_START_HEREDOC, ($next + 1), $end, false, null, true) === false);
    }
}
