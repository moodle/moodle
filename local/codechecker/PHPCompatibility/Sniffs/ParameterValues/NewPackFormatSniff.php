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
 * Check for valid values for the `$format` passed to `pack()`.
 *
 * PHP version 5.4+
 *
 * @link https://www.php.net/manual/en/function.pack.php#refsect1-function.pack-changelog
 *
 * @since 9.0.0
 */
class NewPackFormatSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * @since 9.0.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'pack' => true,
    );

    /**
     * List of new format character codes added to pack().
     *
     * @since 9.0.0
     *
     * @var array Regex pattern => Version array.
     */
    protected $newFormats = array(
        '`([Z])`'    => array(
            '5.4' => false,
            '5.5' => true,
        ),
        '`([qQJP])`' => array(
            '5.6.2' => false,
            '5.6.3' => true,
        ),
        '`([eEgG])`' => array(
            '7.0.14' => false,
            '7.0.15' => true, // And 7.1.1.
        ),
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
        return ($this->supportsBelow('7.1') === false);
    }


    /**
     * Process the parameters of a matched function.
     *
     * @since 9.0.0
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
        if (isset($parameters[1]) === false) {
            return;
        }

        $tokens      = $phpcsFile->getTokens();
        $targetParam = $parameters[1];

        for ($i = $targetParam['start']; $i <= $targetParam['end']; $i++) {
            if ($tokens[$i]['code'] !== \T_CONSTANT_ENCAPSED_STRING
                && $tokens[$i]['code'] !== \T_DOUBLE_QUOTED_STRING
            ) {
                continue;
            }

            $content = $tokens[$i]['content'];
            if ($tokens[$i]['code'] === \T_DOUBLE_QUOTED_STRING) {
                $content = $this->stripVariables($content);
            }

            foreach ($this->newFormats as $pattern => $versionArray) {
                if (preg_match($pattern, $content, $matches) !== 1) {
                    continue;
                }

                foreach ($versionArray as $version => $present) {
                    if ($present === false && $this->supportsBelow($version) === true) {
                        $phpcsFile->addError(
                            'Passing the $format(s) "%s" to pack() is not supported in PHP %s or lower. Found %s',
                            $targetParam['start'],
                            'NewFormatFound',
                            array(
                                $matches[1],
                                $version,
                                $targetParam['raw'],
                            )
                        );
                        continue 2;
                    }
                }
            }
        }
    }
}
