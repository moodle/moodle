<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Interfaces;

use PHPCompatibility\Sniff;
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;

/**
 * Detect classes which implement PHP native interfaces intended only for PHP internal use.
 *
 * PHP version 5.0+
 *
 * @link https://www.php.net/manual/en/class.traversable.php
 * @link https://www.php.net/manual/en/class.throwable.php
 * @link https://www.php.net/manual/en/class.datetimeinterface.php
 *
 * @since 7.0.3
 */
class InternalInterfacesSniff extends Sniff
{

    /**
     * A list of PHP internal interfaces, not intended to be implemented by userland classes.
     *
     * The array lists : the error message to use.
     *
     * @since 7.0.3
     *
     * @var array(string => string)
     */
    protected $internalInterfaces = array(
        'Traversable'       => 'shouldn\'t be implemented directly, implement the Iterator or IteratorAggregate interface instead.',
        'DateTimeInterface' => 'is intended for type hints only and is not implementable.',
        'Throwable'         => 'cannot be implemented directly, extend the Exception class instead.',
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 7.0.3
     *
     * @return array
     */
    public function register()
    {
        // Handle case-insensitivity of interface names.
        $this->internalInterfaces = $this->arrayKeysToLowercase($this->internalInterfaces);

        $targets = array(\T_CLASS);

        if (\defined('T_ANON_CLASS')) {
            $targets[] = \T_ANON_CLASS;
        }

        return $targets;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 7.0.3
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $interfaces = PHPCSHelper::findImplementedInterfaceNames($phpcsFile, $stackPtr);

        if (\is_array($interfaces) === false || $interfaces === array()) {
            return;
        }

        foreach ($interfaces as $interface) {
            $interface   = ltrim($interface, '\\');
            $interfaceLc = strtolower($interface);
            if (isset($this->internalInterfaces[$interfaceLc]) === true) {
                $error     = 'The interface %s %s';
                $errorCode = $this->stringToErrorCode($interfaceLc) . 'Found';
                $data      = array(
                    $interface,
                    $this->internalInterfaces[$interfaceLc],
                );

                $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
            }
        }
    }
}
