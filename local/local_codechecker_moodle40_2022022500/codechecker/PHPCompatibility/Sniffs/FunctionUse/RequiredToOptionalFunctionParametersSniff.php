<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\FunctionUse;

use PHPCompatibility\AbstractComplexVersionSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect missing required function parameters in calls to native PHP functions.
 *
 * Specifically when those function parameters are no longer required in more recent PHP versions.
 *
 * PHP version All
 *
 * @link https://www.php.net/manual/en/doc.changelog.php
 *
 * @since 7.0.3
 * @since 7.1.0 Now extends the `AbstractComplexVersionSniff` instead of the base `Sniff` class.
 * @since 9.0.0 Renamed from `RequiredOptionalFunctionParametersSniff` to `RequiredToOptionalFunctionParametersSniff`.
 */
class RequiredToOptionalFunctionParametersSniff extends AbstractComplexVersionSniff
{

    /**
     * A list of function parameters, which were required in older versions and became optional later on.
     *
     * The array lists : version number with true (required) and false (optional).
     *
     * The index is the location of the parameter in the parameter list, starting at 0 !
     * If's sufficient to list the last version in which the parameter was still required.
     *
     * @since 7.0.3
     *
     * @var array
     */
    protected $functionParameters = array(
        'array_merge' => array(
            0 => array(
                'name' => 'array(s) to merge',
                '7.3'  => true,
                '7.4'  => false,
            ),
        ),
        'array_merge_recursive' => array(
            0 => array(
                'name' => 'array(s) to merge',
                '7.3'  => true,
                '7.4'  => false,
            ),
        ),
        'array_push' => array(
            1 => array(
                'name' => 'element to push',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'array_unshift' => array(
            1 => array(
                'name' => 'element to prepend',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'bcscale' => array(
            0 => array(
                'name' => 'scale',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_fget' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_fput' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_get' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_nb_fget' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_nb_fput' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_nb_get' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_nb_put' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'ftp_put' => array(
            3 => array(
                'name' => 'mode',
                '7.2'  => true,
                '7.3'  => false,
            ),
        ),
        'getenv' => array(
            0 => array(
                'name' => 'varname',
                '7.0'  => true,
                '7.1'  => false,
            ),
        ),
        'preg_match_all' => array(
            2 => array(
                'name' => 'matches',
                '5.3'  => true,
                '5.4'  => false,
            ),
        ),
        'stream_socket_enable_crypto' => array(
            2 => array(
                'name' => 'crypto_type',
                '5.5'  => true,
                '5.6'  => false,
            ),
        ),
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
        // Handle case-insensitivity of function names.
        $this->functionParameters = $this->arrayKeysToLowercase($this->functionParameters);

        return array(\T_STRING);
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
        $tokens = $phpcsFile->getTokens();

        $ignore = array(
            \T_DOUBLE_COLON    => true,
            \T_OBJECT_OPERATOR => true,
            \T_FUNCTION        => true,
            \T_CONST           => true,
            \T_NEW             => true,
        );

        $prevToken = $phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);
        if (isset($ignore[$tokens[$prevToken]['code']]) === true) {
            // Not a call to a PHP function.
            return;
        }

        $function   = $tokens[$stackPtr]['content'];
        $functionLc = strtolower($function);

        if (isset($this->functionParameters[$functionLc]) === false) {
            return;
        }

        $parameterCount  = $this->getFunctionCallParameterCount($phpcsFile, $stackPtr);
        $openParenthesis = $phpcsFile->findNext(Tokens::$emptyTokens, $stackPtr + 1, null, true, null, true);

        // If the parameter count returned > 0, we know there will be valid open parenthesis.
        if ($parameterCount === 0 && $tokens[$openParenthesis]['code'] !== \T_OPEN_PARENTHESIS) {
            return;
        }

        $parameterOffsetFound = $parameterCount - 1;

        foreach ($this->functionParameters[$functionLc] as $offset => $parameterDetails) {
            if ($offset > $parameterOffsetFound) {
                $itemInfo = array(
                    'name'   => $function,
                    'nameLc' => $functionLc,
                    'offset' => $offset,
                );
                $this->handleFeature($phpcsFile, $openParenthesis, $itemInfo);
            }
        }
    }


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
        return ($errorInfo['requiredVersion'] !== '');
    }


    /**
     * Get the relevant sub-array for a specific item from a multi-dimensional array.
     *
     * @since 7.1.0
     *
     * @param array $itemInfo Base information about the item.
     *
     * @return array Version and other information about the item.
     */
    public function getItemArray(array $itemInfo)
    {
        return $this->functionParameters[$itemInfo['nameLc']][$itemInfo['offset']];
    }


    /**
     * Get an array of the non-PHP-version array keys used in a sub-array.
     *
     * @since 7.1.0
     *
     * @return array
     */
    protected function getNonVersionArrayKeys()
    {
        return array('name');
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
            'paramName'       => '',
            'requiredVersion' => '',
        );

        $versionArray = $this->getVersionArray($itemArray);

        if (empty($versionArray) === false) {
            foreach ($versionArray as $version => $required) {
                if ($required === true && $this->supportsBelow($version) === true) {
                    $errorInfo['requiredVersion'] = $version;
                }
            }
        }

        $errorInfo['paramName'] = $itemArray['name'];

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
        return 'The "%s" parameter for function %s() is missing, but was required for PHP version %s and lower';
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
        $error     = $this->getErrorMsgTemplate();
        $errorCode = $this->stringToErrorCode($itemInfo['name'] . '_' . $errorInfo['paramName']) . 'Missing';
        $data      = array(
            $errorInfo['paramName'],
            $itemInfo['name'],
            $errorInfo['requiredVersion'],
        );

        $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
    }
}
