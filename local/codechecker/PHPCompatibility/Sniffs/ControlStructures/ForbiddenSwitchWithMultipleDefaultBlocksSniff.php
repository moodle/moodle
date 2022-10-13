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

/**
 * Switch statements can not have multiple default blocks since PHP 7.0.
 *
 * PHP version 7.0
 *
 * @link https://wiki.php.net/rfc/switch.default.multiple
 * @link https://www.php.net/manual/en/control-structures.switch.php
 *
 * @since 7.0.0
 */
class ForbiddenSwitchWithMultipleDefaultBlocksSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.0
     *
     * @return array
     */
    public function register()
    {
        return array(\T_SWITCH);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.0') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $defaultToken = $stackPtr;
        $defaultCount = 0;
        $targetLevel  = $tokens[$stackPtr]['level'] + 1;
        while ($defaultCount < 2 && ($defaultToken = $phpcsFile->findNext(array(\T_DEFAULT), $defaultToken + 1, $tokens[$stackPtr]['scope_closer'])) !== false) {
            // Same level or one below (= two default cases after each other).
            if ($tokens[$defaultToken]['level'] === $targetLevel || $tokens[$defaultToken]['level'] === ($targetLevel + 1)) {
                $defaultCount++;
            }
        }

        if ($defaultCount > 1) {
            $phpcsFile->addError(
                'Switch statements can not have multiple default blocks since PHP 7.0',
                $stackPtr,
                'Found'
            );
        }
    }
}
