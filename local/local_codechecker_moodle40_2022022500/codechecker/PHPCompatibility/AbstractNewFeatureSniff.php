<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility;

use PHP_CodeSniffer_File as File;

/**
 * Base class for new feature sniffs.
 *
 * @since 7.1.0
 */
abstract class AbstractNewFeatureSniff extends AbstractComplexVersionSniff
{


    /**
     * Determine whether an error/warning should be thrown for an item based on collected information.
     *
     * @since 7.1.0
     *
     * @param array $errorInfo Detail information about an item.
     *
     * @return bool
     */
    protected function shouldThrowError(array $errorInfo)
    {
        return ($errorInfo['not_in_version'] !== '');
    }


    /**
     * Retrieve the relevant detail (version) information for use in an error message.
     *
     * @since 7.1.0
     *
     * @param array $itemArray Version and other information about the item.
     * @param array $itemInfo  Base information about the item.
     *
     * @return array
     */
    public function getErrorInfo(array $itemArray, array $itemInfo)
    {
        $errorInfo = array(
            'not_in_version' => '',
            'error'          => true,
        );

        $versionArray = $this->getVersionArray($itemArray);

        if (empty($versionArray) === false) {
            foreach ($versionArray as $version => $present) {
                if ($errorInfo['not_in_version'] === '' && $present === false
                    && $this->supportsBelow($version) === true
                ) {
                    $errorInfo['not_in_version'] = $version;
                }
            }
        }

        return $errorInfo;
    }


    /**
     * Get the error message template for this sniff.
     *
     * @since 7.1.0
     *
     * @return string
     */
    protected function getErrorMsgTemplate()
    {
        return '%s is not present in PHP version %s or earlier';
    }


    /**
     * Generates the error or warning for this item.
     *
     * @since 7.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the relevant token in
     *                                         the stack.
     * @param array                 $itemInfo  Base information about the item.
     * @param array                 $errorInfo Array with detail (version) information
     *                                         relevant to the item.
     *
     * @return void
     */
    public function addError(File $phpcsFile, $stackPtr, array $itemInfo, array $errorInfo)
    {
        $itemName = $this->getItemName($itemInfo, $errorInfo);
        $error    = $this->getErrorMsgTemplate();

        $errorCode = $this->stringToErrorCode($itemName) . 'Found';
        $data      = array(
            $itemName,
            $errorInfo['not_in_version'],
        );

        $error = $this->filterErrorMsg($error, $itemInfo, $errorInfo);
        $data  = $this->filterErrorData($data, $itemInfo, $errorInfo);

        $this->addMessage($phpcsFile, $error, $stackPtr, $errorInfo['error'], $errorCode, $data);
    }
}
