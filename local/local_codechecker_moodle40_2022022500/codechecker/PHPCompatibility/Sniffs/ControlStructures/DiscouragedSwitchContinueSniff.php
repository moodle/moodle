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
 * Detect use of `continue` in `switch` control structures.
 *
 * As of PHP 7.3, PHP will throw a warning when `continue` is used to target a `switch`
 * control structure.
 * The sniff takes numeric arguments used with `continue` into account.
 *
 * PHP version 7.3
 *
 * @link https://www.php.net/manual/en/migration73.incompatible.php#migration73.incompatible.core.continue-targeting-switch
 * @link https://wiki.php.net/rfc/continue_on_switch_deprecation
 * @link https://github.com/php/php-src/commit/04e3523b7d095341f65ed5e71a3cac82fca690e4
 *       (actual implementation which is different from the RFC).
 * @link https://www.php.net/manual/en/control-structures.switch.php
 *
 * @since 8.2.0
 */
class DiscouragedSwitchContinueSniff extends Sniff
{

    /**
     * Token codes of control structures which can be targeted using continue.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $loopStructures = array(
        \T_FOR     => \T_FOR,
        \T_FOREACH => \T_FOREACH,
        \T_WHILE   => \T_WHILE,
        \T_DO      => \T_DO,
        \T_SWITCH  => \T_SWITCH,
    );

    /**
     * Tokens which start a new case within a switch.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $caseTokens = array(
        \T_CASE    => \T_CASE,
        \T_DEFAULT => \T_DEFAULT,
    );

    /**
     * Token codes which are accepted to determine the level for the continue.
     *
     * This array is enriched with the arithmetic operators in the register() method.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $acceptedLevelTokens = array(
        \T_LNUMBER           => \T_LNUMBER,
        \T_OPEN_PARENTHESIS  => \T_OPEN_PARENTHESIS,
        \T_CLOSE_PARENTHESIS => \T_CLOSE_PARENTHESIS,
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 8.2.0
     *
     * @return array
     */
    public function register()
    {
        $this->acceptedLevelTokens += Tokens::$arithmeticTokens;
        $this->acceptedLevelTokens += Tokens::$emptyTokens;

        return array(\T_SWITCH);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->supportsAbove('7.3') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $switchOpener = $tokens[$stackPtr]['scope_opener'];
        $switchCloser = $tokens[$stackPtr]['scope_closer'];

        // Quick check whether we need to bother with the more complex logic.
        $hasContinue = $phpcsFile->findNext(\T_CONTINUE, ($switchOpener + 1), $switchCloser);
        if ($hasContinue === false) {
            return;
        }

        $caseDefault = $switchOpener;

        do {
            $caseDefault = $phpcsFile->findNext($this->caseTokens, ($caseDefault + 1), $switchCloser);
            if ($caseDefault === false) {
                break;
            }

            if (isset($tokens[$caseDefault]['scope_opener']) === false) {
                // Unknown start of the case, skip.
                continue;
            }

            $caseOpener      = $tokens[$caseDefault]['scope_opener'];
            $nextCaseDefault = $phpcsFile->findNext($this->caseTokens, ($caseDefault + 1), $switchCloser);
            if ($nextCaseDefault === false) {
                $caseCloser = $switchCloser;
            } else {
                $caseCloser = $nextCaseDefault;
            }

            // Check for unscoped control structures within the case.
            $controlStructure = $caseOpener;
            $doCount          = 0;
            while (($controlStructure = $phpcsFile->findNext($this->loopStructures, ($controlStructure + 1), $caseCloser)) !== false) {
                if ($tokens[$controlStructure]['code'] === \T_DO) {
                    $doCount++;
                }

                if (isset($tokens[$controlStructure]['scope_opener'], $tokens[$controlStructure]['scope_closer']) === false) {
                    if ($tokens[$controlStructure]['code'] === \T_WHILE && $doCount > 0) {
                        // While in a do-while construct.
                        $doCount--;
                        continue;
                    }

                    // Control structure without braces found within the case, ignore this case.
                    continue 2;
                }
            }

            // Examine the contents of the case.
            $continue = $caseOpener;

            do {
                $continue = $phpcsFile->findNext(\T_CONTINUE, ($continue + 1), $caseCloser);
                if ($continue === false) {
                    break;
                }

                $nextSemicolon = $phpcsFile->findNext(array(\T_SEMICOLON, \T_CLOSE_TAG), ($continue + 1), $caseCloser);
                $codeString    = '';
                for ($i = ($continue + 1); $i < $nextSemicolon; $i++) {
                    if (isset($this->acceptedLevelTokens[$tokens[$i]['code']]) === false) {
                        // Function call/variable or other token which make numeric level impossible to determine.
                        continue 2;
                    }

                    if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                        continue;
                    }

                    $codeString .= $tokens[$i]['content'];
                }

                $level = null;
                if ($codeString !== '') {
                    if (is_numeric($codeString)) {
                        $level = (int) $codeString;
                    } else {
                        // With the above logic, the string can only contain digits and operators, eval!
                        $level = eval("return ( $codeString );");
                    }
                }

                if (isset($level) === false || $level === 0) {
                    $level = 1;
                }

                // Examine which control structure is being targeted by the continue statement.
                if (isset($tokens[$continue]['conditions']) === false) {
                    continue;
                }

                $conditions = array_reverse($tokens[$continue]['conditions'], true);
                // PHPCS adds more structures to the conditions array than we want to take into
                // consideration, so clean up the array.
                foreach ($conditions as $tokenPtr => $tokenCode) {
                    if (isset($this->loopStructures[$tokenCode]) === false) {
                        unset($conditions[$tokenPtr]);
                    }
                }

                $targetCondition = \array_slice($conditions, ($level - 1), 1, true);
                if (empty($targetCondition)) {
                    continue;
                }

                $conditionToken = key($targetCondition);
                if ($conditionToken === $stackPtr) {
                    $phpcsFile->addWarning(
                        "Targeting a 'switch' control structure with a 'continue' statement is strongly discouraged and will throw a warning as of PHP 7.3.",
                        $continue,
                        'Found'
                    );
                }

            } while ($continue < $caseCloser);

        } while ($caseDefault < $switchCloser);
    }
}
