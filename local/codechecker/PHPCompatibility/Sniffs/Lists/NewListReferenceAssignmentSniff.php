<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Lists;

use PHPCompatibility\Sniffs\Lists\NewKeyedListSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect reference assignments in array destructuring using (short) list.
 *
 * PHP version 7.3
 *
 * @link https://www.php.net/manual/en/migration73.new-features.php#migration73.new-features.core.destruct-reference
 * @link https://wiki.php.net/rfc/list_reference_assignment
 * @link https://www.php.net/manual/en/function.list.php
 *
 * @since 9.0.0
 */
class NewListReferenceAssignmentSniff extends NewKeyedListSniff
{
    /**
     * The token(s) within the list construct which is being targeted.
     *
     * @since 9.0.0
     *
     * @var array
     */
    protected $targetsInList = array(
        \T_BITWISE_AND => \T_BITWISE_AND,
    );

    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 9.0.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('7.2') === false);
    }

    /**
     * Examine the contents of a list construct to determine whether an error needs to be thrown.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $opener    The position of the list open token.
     * @param int                   $closer    The position of the list close token.
     *
     * @return void
     */
    protected function examineList(File $phpcsFile, $opener, $closer)
    {
        $start = $opener;
        while (($start = $this->hasTargetInList($phpcsFile, $start, $closer)) !== false) {
            $phpcsFile->addError(
                'Reference assignments within list constructs are not supported in PHP 7.2 or earlier.',
                $start,
                'Found'
            );
        }
    }
}
