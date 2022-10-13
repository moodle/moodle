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

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Since PHP 7.1, you can specify keys in `list()`, or its new shorthand `[]` syntax.
 *
 * PHP version 7.1
 *
 * @link https://wiki.php.net/rfc/list_keys
 * @link https://www.php.net/manual/en/function.list.php
 *
 * @since 9.0.0
 */
class NewKeyedListSniff extends Sniff
{
    /**
     * Tokens which represent the start of a list construct.
     *
     * @since 9.0.0
     *
     * @var array
     */
    protected $sniffTargets =  array(
        \T_LIST             => \T_LIST,
        \T_OPEN_SHORT_ARRAY => \T_OPEN_SHORT_ARRAY,
    );

    /**
     * The token(s) within the list construct which is being targeted.
     *
     * @since 9.0.0
     *
     * @var array
     */
    protected $targetsInList = array(
        \T_DOUBLE_ARROW => \T_DOUBLE_ARROW,
    );

    /**
     * All tokens needed to walk through the list construct and
     * determine whether the target token is contained within.
     *
     * Set by the setUpAllTargets() method which is called from within register().
     *
     * @since 9.0.0
     *
     * @var array
     */
    protected $allTargets;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.0.0
     *
     * @return array
     */
    public function register()
    {
        $this->setUpAllTargets();

        return $this->sniffTargets;
    }

    /**
     * Prepare the $allTargets array only once.
     *
     * @since 9.0.0
     *
     * @return void
     */
    public function setUpAllTargets()
    {
        $this->allTargets = $this->sniffTargets + $this->targetsInList;
    }

    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 9.0.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('7.0') === false);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->bowOutEarly() === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === \T_OPEN_SHORT_ARRAY
            && $this->isShortList($phpcsFile, $stackPtr) === false
        ) {
            // Short array, not short list.
            return;
        }

        if ($tokens[$stackPtr]['code'] === \T_LIST) {
            $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($nextNonEmpty === false
                || $tokens[$nextNonEmpty]['code'] !== \T_OPEN_PARENTHESIS
                || isset($tokens[$nextNonEmpty]['parenthesis_closer']) === false
            ) {
                // Parse error or live coding.
                return;
            }

            $opener = $nextNonEmpty;
            $closer = $tokens[$nextNonEmpty]['parenthesis_closer'];
        } else {
            // Short list syntax.
            $opener = $stackPtr;

            if (isset($tokens[$stackPtr]['bracket_closer'])) {
                $closer = $tokens[$stackPtr]['bracket_closer'];
            }
        }

        if (isset($opener, $closer) === false) {
            return;
        }

        $this->examineList($phpcsFile, $opener, $closer);
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
                'Specifying keys in list constructs is not supported in PHP 7.0 or earlier.',
                $start,
                'Found'
            );
        }
    }


    /**
     * Check whether a certain target token exists within a list construct.
     *
     * Skips past nested list constructs, so these can be examined based on their own token.
     *
     * @since 9.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $start     The position of the list open token or a token
     *                                         within the list to start (resume) the examination from.
     * @param int                   $closer    The position of the list close token.
     *
     * @return int|bool Stack pointer to the target token if encountered. False otherwise.
     */
    protected function hasTargetInList(File $phpcsFile, $start, $closer)
    {
        $tokens = $phpcsFile->getTokens();

        for ($i = ($start + 1); $i < $closer; $i++) {
            if (isset($this->allTargets[$tokens[$i]['code']]) === false) {
                continue;
            }

            if (isset($this->targetsInList[$tokens[$i]['code']]) === true) {
                return $i;
            }

            // Skip past nested list constructs.
            if ($tokens[$i]['code'] === \T_LIST) {
                $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
                if ($nextNonEmpty !== false
                    && $tokens[$nextNonEmpty]['code'] === \T_OPEN_PARENTHESIS
                    && isset($tokens[$nextNonEmpty]['parenthesis_closer']) === true
                ) {
                    $i = $tokens[$nextNonEmpty]['parenthesis_closer'];
                }
            } elseif ($tokens[$i]['code'] === \T_OPEN_SHORT_ARRAY
                && isset($tokens[$i]['bracket_closer'])
            ) {
                $i = $tokens[$i]['bracket_closer'];
            }
        }

        return false;
    }
}
