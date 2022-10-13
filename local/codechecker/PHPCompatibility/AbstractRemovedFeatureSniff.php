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
 * Base class for removed feature sniffs.
 *
 * @since 7.1.0
 */
abstract class AbstractRemovedFeatureSniff extends AbstractComplexVersionSniff
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
        return ($errorInfo['deprecated'] !== '' || $errorInfo['removed'] !== '');
    }


    /**
     * Get an array of the non-PHP-version array keys used in a sub-array.
     *
     * By default, removed feature version arrays, contain an additional 'alternative' array key.
     *
     * @since 7.1.0
     *
     * @return array
     */
    protected function getNonVersionArrayKeys()
    {
        return array('alternative');
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
            'deprecated'  => '',
            'removed'     => '',
            'alternative' => '',
            'error'       => false,
        );

        $versionArray = $this->getVersionArray($itemArray);

        if (empty($versionArray) === false) {
            foreach ($versionArray as $version => $removed) {
                if ($this->supportsAbove($version) === true) {
                    if ($removed === true && $errorInfo['removed'] === '') {
                        $errorInfo['removed'] = $version;
                        $errorInfo['error']   = true;
                    } elseif ($errorInfo['deprecated'] === '') {
                        $errorInfo['deprecated'] = $version;
                    }
                }
            }
        }

        if (isset($itemArray['alternative']) === true) {
            $errorInfo['alternative'] = $itemArray['alternative'];
        }

        return $errorInfo;
    }


    /**
     * Get the error message template for suggesting an alternative for a specific sniff.
     *
     * @since 7.1.0
     *
     * @return string
     */
    protected function getAlternativeOptionTemplate()
    {
        return '; Use %s instead';
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

        $errorCode = $this->stringToErrorCode($itemName);
        $data      = array($itemName);

        if ($errorInfo['deprecated'] !== '') {
            $error     .= 'deprecated since PHP %s and ';
            $errorCode .= 'Deprecated';
            $data[]     = $errorInfo['deprecated'];
        }

        if ($errorInfo['removed'] !== '') {
            $error     .= 'removed since PHP %s and ';
            $errorCode .= 'Removed';
            $data[]     = $errorInfo['removed'];
        }

        // Remove the last 'and' from the message.
        $error = substr($error, 0, (\strlen($error) - 5));

        if ($errorInfo['alternative'] !== '') {
            $error .= $this->getAlternativeOptionTemplate();
            $data[] = $errorInfo['alternative'];
        }

        $error = $this->filterErrorMsg($error, $itemInfo, $errorInfo);
        $data  = $this->filterErrorData($data, $itemInfo, $errorInfo);

        $this->addMessage($phpcsFile, $error, $stackPtr, $errorInfo['error'], $errorCode, $data);
    }
}
