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

/**
 * Since PHP 5.3.4, `strip_tags()` ignores self-closing XHTML tags in allowable_tags
 *
 * PHP version 5.3.4
 *
 * @link https://www.php.net/manual/en/function.strip-tags.php#refsect1-function.strip-tags-changelog
 *
 * @since 9.3.0
 */
class ForbiddenStripTagsSelfClosingXHTMLSniff extends AbstractFunctionCallParameterSniff
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
        return ($this->supportsAbove('5.4') === false);
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

        $tokens      = $phpcsFile->getTokens();
        $targetParam = $parameters[2];
        for ($i = $targetParam['start']; $i <= $targetParam['end']; $i++) {
            if ($tokens[$i]['code'] === \T_STRING
                || $tokens[$i]['code'] === \T_VARIABLE
            ) {
                // Variable, constant, function call. Ignore as undetermined.
                return;
            }

            if (isset($this->textStringTokens[$tokens[$i]['code']]) === true
                && strpos($tokens[$i]['content'], '/>') !== false
            ) {

                $phpcsFile->addError(
                    'Self-closing XHTML tags are ignored. Only non-self-closing tags should be used in the strip_tags() $allowable_tags parameter since PHP 5.3.4. Found: %s',
                    $i,
                    'Found',
                    array($targetParam['raw'])
                );

                // Only throw one error per function call.
                return;
            }
        }
    }
}
