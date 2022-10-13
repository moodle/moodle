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
 * Detect code affected by the changed list assignment order in PHP 7.0+.
 *
 * The `list()` construct no longer assigns variables in reverse order.
 * This affects all list constructs where non-unique variables are used.
 *
 * PHP version 7.0
 *
 * @link https://www.php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.list.order
 * @link https://wiki.php.net/rfc/abstract_syntax_tree#changes_to_list
 * @link https://www.php.net/manual/en/function.list.php
 *
 * @since 9.0.0
 */
class AssignmentOrderSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.0.0
     *
     * @return array
     */
    public function register()
    {
        return array(
            \T_LIST,
            \T_OPEN_SHORT_ARRAY,
        );
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
     * @return void|int Void if not a valid list. If a list construct has been
     *                  examined, the stack pointer to the list closer to skip
     *                  passed any nested lists which don't need to be examined again.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.0') === false) {
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

        /*
         * OK, so we have the opener & closer, now we need to check all the variables in the
         * list() to see if there are duplicates as that's the problem.
         */
        $hasVars = $phpcsFile->findNext(array(\T_VARIABLE, \T_DOLLAR), ($opener + 1), $closer);
        if ($hasVars === false) {
            // Empty list, not our concern.
            return ($closer + 1);
        }

        // Set the variable delimiters based on the list type being examined.
        $stopPoints = array(\T_COMMA);
        if ($tokens[$stackPtr]['code'] === \T_OPEN_SHORT_ARRAY) {
            $stopPoints[] = \T_CLOSE_SHORT_ARRAY;
        } else {
            $stopPoints[] = \T_CLOSE_PARENTHESIS;
        }

        $listVars      = array();
        $lastStopPoint = $opener;

        /*
         * Create a list of all variables used within the `list()` construct.
         * We're not concerned with whether these are nested or not, as any duplicate
         * variable name used will be problematic, independent of nesting.
         */
        do {
            $nextStopPoint = $phpcsFile->findNext($stopPoints, ($lastStopPoint + 1), $closer);
            if ($nextStopPoint === false) {
                $nextStopPoint = $closer;
            }

            // Also detect this in PHP 7.1 keyed lists.
            $hasDoubleArrow = $phpcsFile->findNext(\T_DOUBLE_ARROW, ($lastStopPoint + 1), $nextStopPoint);
            if ($hasDoubleArrow !== false) {
                $lastStopPoint = $hasDoubleArrow;
            }

            // Find the start of the variable, allowing for variable variables.
            $nextStartPoint = $phpcsFile->findNext(array(\T_VARIABLE, \T_DOLLAR), ($lastStopPoint + 1), $nextStopPoint);
            if ($nextStartPoint === false) {
                // Skip past empty bits in the list, i.e. `list( $a, , ,)`.
                $lastStopPoint = $nextStopPoint;
                continue;
            }

            /*
             * Gather the content of all non-empty tokens to determine the "variable name".
             * Variable name in this context includes array or object property syntaxes, such
             * as `$a['name']` and `$b->property`.
             */
            $varContent = '';

            for ($i = $nextStartPoint; $i < $nextStopPoint; $i++) {
                if (isset(Tokens::$emptyTokens[$tokens[$i]['code']])) {
                    continue;
                }

                $varContent .= $tokens[$i]['content'];
            }

            if ($varContent !== '') {
                $listVars[] = $varContent;
            }

            $lastStopPoint = $nextStopPoint;

        } while ($lastStopPoint < $closer);

        if (empty($listVars)) {
            // Shouldn't be possible, but just in case.
            return ($closer + 1);
        }

        // Verify that all variables used in the list() construct are unique.
        if (\count($listVars) !== \count(array_unique($listVars))) {
            $phpcsFile->addError(
                'list() will assign variable from left-to-right since PHP 7.0. Ensure all variables in list() are unique to prevent unexpected results.',
                $stackPtr,
                'Affected'
            );
        }

        return ($closer + 1);
    }
}
