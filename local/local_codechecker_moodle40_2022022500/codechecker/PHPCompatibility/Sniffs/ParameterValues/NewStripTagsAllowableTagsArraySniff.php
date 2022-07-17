<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ParameterValues;

use PHPCompatibility\AbstractFunctionCallParameterSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * As of PHP 7.4, `strip_tags()` now also accepts an array of `$allowable_tags`.
 *
 * PHP version 7.4
 *
 * @link https://www.php.net/manual/en/migration74.new-features.php#migration74.new-features.standard.strip-tags
 * @link https://www.php.net/manual/en/function.strip-tags.php
 *
 * @since 9.3.0
 */
class NewStripTagsAllowableTagsArraySniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * @since 9.3.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'strip_tags' => true,
    );

    /**
     * Text string tokens to examine.
     *
     * @since 9.3.0
     *
     * @var array
     */
    private $textStringTokens = array(
        \T_CONSTANT_ENCAPSED_STRING => true,
        \T_DOUBLE_QUOTED_STRING     => true,
        \T_INLINE_HTML              => true,
        \T_HEREDOC                  => true,
        \T_NOWDOC                   => true,
    );


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 9.3.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return false;
    }


    /**
     * Process the parameters of a matched function.
     *
     * @since 9.3.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                   $stackPtr     The position of the current token in the stack.
     * @param string                $functionName The token content (function name) which was matched.
     * @param array                 $parameters   Array with information about the parameters.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function processParameters(File $phpcsFile, $stackPtr, $functionName, $parameters)
    {
        if (isset($parameters[2]) === false) {
            return;
        }

        $tokens       = $phpcsFile->getTokens();
        $targetParam  = $parameters[2];
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $targetParam['start'], $targetParam['end'], true);

        if ($nextNonEmpty === false) {
            // Shouldn't be possible.
            return;
        }

        if ($tokens[$nextNonEmpty]['code'] !== \T_ARRAY
            && $tokens[$nextNonEmpty]['code'] !== \T_OPEN_SHORT_ARRAY
        ) {
            // Not passed as a hard-coded array.
            return;
        }

        if ($this->supportsBelow('7.3') === true) {
            $phpcsFile->addError(
                'The strip_tags() function did not accept $allowable_tags to be passed in array format in PHP 7.3 and earlier.',
                $nextNonEmpty,
                'Found'
            );
        }

        if ($this->supportsAbove('7.4') === true) {
            if (strpos($targetParam['raw'], '>') === false) {
                // Efficiency: prevent needlessly walking the array.
                return;
            }

            $items = $this->getFunctionCallParameters($phpcsFile, $nextNonEmpty);

            if (empty($items)) {
                return;
            }

            foreach ($items as $item) {
                for ($i = $item['start']; $i <= $item['end']; $i++) {
                    if ($tokens[$i]['code'] === \T_STRING
                        || $tokens[$i]['code'] === \T_VARIABLE
                    ) {
                        // Variable, constant, function call. Ignore complete item as undetermined.
                        break;
                    }

                    if (isset($this->textStringTokens[$tokens[$i]['code']]) === true
                        && strpos($tokens[$i]['content'], '>') !== false
                    ) {

                        $phpcsFile->addWarning(
                            'When passing strip_tags() the $allowable_tags parameter as an array, the tags should not be enclosed in <> brackets. Found: %s',
                            $i,
                            'Invalid',
                            array($item['raw'])
                        );

                        // Only throw one error per array item.
                        break;
                    }
                }
            }
        }
    }
}
