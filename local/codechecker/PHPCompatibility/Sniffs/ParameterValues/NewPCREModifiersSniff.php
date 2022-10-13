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

use PHPCompatibility\Sniffs\ParameterValues\RemovedPCREModifiersSniff;
use PHP_CodeSniffer_File as File;

/**
 * Check for the use of newly added regex modifiers for PCRE functions.
 *
 * Initially just checks for the PHP 7.2 new `J` modifier.
 *
 * PHP version 7.2+
 *
 * @link https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
 * @link https://www.php.net/manual/en/migration72.new-features.php#migration72.new-features.pcre
 *
 * @since 8.2.0
 * @since 9.0.0 Renamed from `PCRENewModifiersSniff` to `NewPCREModifiersSniff`.
 */
class NewPCREModifiersSniff extends RemovedPCREModifiersSniff
{

    /**
     * Functions to check for.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $targetFunctions = array(
        'preg_filter'                 => true,
        'preg_grep'                   => true,
        'preg_match_all'              => true,
        'preg_match'                  => true,
        'preg_replace_callback_array' => true,
        'preg_replace_callback'       => true,
        'preg_replace'                => true,
        'preg_split'                  => true,
    );

    /**
     * Array listing newly introduced regex modifiers.
     *
     * The key should be the modifier (case-sensitive!).
     * The value should be the PHP version in which the modifier was introduced.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $newModifiers = array(
        'J' => array(
            '7.1' => false,
            '7.2' => true,
        ),
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
        // Version used here should be the highest version from the `$newModifiers` array,
        // i.e. the last PHP version in which a new modifier was introduced.
        return ($this->supportsBelow('7.2') === false);
    }


    /**
     * Examine the regex modifier string.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                   $stackPtr     The position of the current token in the
     *                                            stack passed in $tokens.
     * @param string                $functionName The function which contained the pattern.
     * @param string                $modifiers    The regex modifiers found.
     *
     * @return void
     */
    protected function examineModifiers(File $phpcsFile, $stackPtr, $functionName, $modifiers)
    {
        $error = 'The PCRE regex modifier "%s" is not present in PHP version %s or earlier';

        foreach ($this->newModifiers as $modifier => $versionArray) {
            if (strpos($modifiers, $modifier) === false) {
                continue;
            }

            $notInVersion = '';
            foreach ($versionArray as $version => $present) {
                if ($notInVersion === '' && $present === false
                    && $this->supportsBelow($version) === true
                ) {
                    $notInVersion = $version;
                }
            }

            if ($notInVersion === '') {
                continue;
            }

            $errorCode = $modifier . 'ModifierFound';
            $data      = array(
                $modifier,
                $notInVersion,
            );

            $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
        }
    }
}
