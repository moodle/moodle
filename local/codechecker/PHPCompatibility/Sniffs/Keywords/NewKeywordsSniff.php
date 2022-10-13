<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Keywords;

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect use of new PHP keywords.
 *
 * PHP version All
 *
 * @link https://wiki.php.net/rfc/heredoc-with-double-quotes
 * @link https://wiki.php.net/rfc/horizontalreuse (traits)
 * @link https://wiki.php.net/rfc/generators
 * @link https://wiki.php.net/rfc/finally
 * @link https://wiki.php.net/rfc/generator-delegation
 *
 * @since 5.5
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` instead of the base `Sniff` class..
 */
class NewKeywordsSniff extends AbstractNewFeatureSniff
{

    /**
     * A list of new keywords, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the last version which did not contain the keyword.
     *
     * Description will be used as part of the error message.
     * Condition is the name of a callback method within this class or the parent class
     * which checks whether the token complies with a certain condition.
     * The callback function will be passed the $phpcsFile and the $stackPtr.
     * The callback function should return `true` if the condition is met and the
     * error should *not* be thrown.
     *
     * @since 5.5
     * @since 7.0.3 Support for 'condition' has been added.
     *
     * @var array(string => array(string => bool|string))
     */
    protected $newKeywords = array(
        'T_HALT_COMPILER' => array(
            '5.0'         => false,
            '5.1'         => true,
            'description' => '"__halt_compiler" keyword',
        ),
        'T_CONST' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '"const" keyword',
            'condition'   => 'isClassConstant', // Keyword is only new when not in class context.
        ),
        'T_CALLABLE' => array(
            '5.3'         => false,
            '5.4'         => true,
            'description' => '"callable" keyword',
            'content'     => 'callable',
        ),
        'T_DIR' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '__DIR__ magic constant',
            'content'     => '__DIR__',
        ),
        'T_GOTO' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '"goto" keyword',
            'content'     => 'goto',
        ),
        'T_INSTEADOF' => array(
            '5.3'         => false,
            '5.4'         => true,
            'description' => '"insteadof" keyword (for traits)',
            'content'     => 'insteadof',
        ),
        'T_NAMESPACE' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '"namespace" keyword',
            'content'     => 'namespace',
        ),
        'T_NS_C' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '__NAMESPACE__ magic constant',
            'content'     => '__NAMESPACE__',
        ),
        'T_USE' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '"use" keyword (for traits/namespaces/anonymous functions)',
        ),
        'T_START_NOWDOC' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => 'nowdoc functionality',
        ),
        'T_END_NOWDOC' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => 'nowdoc functionality',
        ),
        'T_START_HEREDOC' => array(
            '5.2'         => false,
            '5.3'         => true,
            'description' => '(Double) quoted Heredoc identifier',
            'condition'   => 'isNotQuoted', // Heredoc is only new with quoted identifier.
        ),
        'T_TRAIT' => array(
            '5.3'         => false,
            '5.4'         => true,
            'description' => '"trait" keyword',
            'content'     => 'trait',
        ),
        'T_TRAIT_C' => array(
            '5.3'         => false,
            '5.4'         => true,
            'description' => '__TRAIT__ magic constant',
            'content'     => '__TRAIT__',
        ),
        // The specifics for distinguishing between 'yield' and 'yield from' are dealt
        // with in the translation logic.
        // This token has to be placed above the `T_YIELD` token in this array to allow for this.
        'T_YIELD_FROM' => array(
            '5.6'         => false,
            '7.0'         => true,
            'description' => '"yield from" keyword (for generators)',
            'content'     => 'yield',
        ),
        'T_YIELD' => array(
            '5.4'         => false,
            '5.5'         => true,
            'description' => '"yield" keyword (for generators)',
            'content'     => 'yield',
        ),
        'T_FINALLY' => array(
            '5.4'         => false,
            '5.5'         => true,
            'description' => '"finally" keyword (in exception handling)',
            'content'     => 'finally',
        ),
    );

    /**
     * Translation table for T_STRING tokens.
     *
     * Will be set up from the register() method.
     *
     * @since 7.0.5
     *
     * @var array(string => string)
     */
    protected $translateContentToToken = array();


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     *
     * @return array
     */
    public function register()
    {
        $tokens    = array();
        $translate = array();
        foreach ($this->newKeywords as $token => $versions) {
            if (\defined($token)) {
                $tokens[] = constant($token);
            }
            if (isset($versions['content'])) {
                $translate[strtolower($versions['content'])] = $token;
            }
        }

        /*
         * Deal with tokens not recognized by the PHP version the sniffer is run
         * under and (not correctly) compensated for by PHPCS.
         */
        if (empty($translate) === false) {
            $this->translateContentToToken = $translate;
            $tokens[]                      = \T_STRING;
        }

        return $tokens;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $tokenType = $tokens[$stackPtr]['type'];

        // Allow for dealing with multi-token keywords, like "yield from".
        $end = $stackPtr;

        // Translate T_STRING token if necessary.
        if ($tokens[$stackPtr]['type'] === 'T_STRING') {
            $content = strtolower($tokens[$stackPtr]['content']);

            if (isset($this->translateContentToToken[$content]) === false) {
                // Not one of the tokens we're looking for.
                return;
            }

            $tokenType = $this->translateContentToToken[$content];
        }

        /*
         * Special case: distinguish between `yield` and `yield from`.
         *
         * PHPCS currently (at least up to v 3.0.1) does not backfill for the
         * `yield` nor the `yield from` keywords.
         * See: https://github.com/squizlabs/PHP_CodeSniffer/issues/1524
         *
         * In PHP < 5.5, both `yield` as well as `from` are tokenized as T_STRING.
         * In PHP 5.5 - 5.6, `yield` is tokenized as T_YIELD and `from` as T_STRING,
         * but the `T_YIELD_FROM` token *is* defined in PHP.
         * In PHP 7.0+ both are tokenized as their respective token, however,
         * a multi-line "yield from" is tokenized as two tokens.
         */
        if ($tokenType === 'T_YIELD') {
            $nextToken = $phpcsFile->findNext(\T_WHITESPACE, ($end + 1), null, true);
            if ($tokens[$nextToken]['code'] === \T_STRING
                && $tokens[$nextToken]['content'] === 'from'
            ) {
                $tokenType = 'T_YIELD_FROM';
                $end       = $nextToken;
            }
            unset($nextToken);
        }

        if ($tokenType === 'T_YIELD_FROM' && $tokens[($stackPtr - 1)]['type'] === 'T_YIELD_FROM') {
            // Multi-line "yield from", no need to report it twice.
            return;
        }

        if (isset($this->newKeywords[$tokenType]) === false) {
            return;
        }

        $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), null, true);
        $prevToken = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($prevToken !== false
            && ($tokens[$prevToken]['code'] === \T_DOUBLE_COLON
            || $tokens[$prevToken]['code'] === \T_OBJECT_OPERATOR)
        ) {
            // Class property of the same name as one of the keywords. Ignore.
            return;
        }

        // Skip attempts to use keywords as functions or class names - the former
        // will be reported by ForbiddenNamesAsInvokedFunctionsSniff, whilst the
        // latter will be (partially) reported by the ForbiddenNames sniff.
        // Either type will result in false-positives when targetting lower versions
        // of PHP where the name was not reserved, unless we explicitly check for
        // them.
        if (($nextToken === false
                || $tokens[$nextToken]['type'] !== 'T_OPEN_PARENTHESIS')
            && ($prevToken === false
                || $tokens[$prevToken]['type'] !== 'T_CLASS'
                || $tokens[$prevToken]['type'] !== 'T_INTERFACE')
        ) {
            // Skip based on token scope condition.
            if (isset($this->newKeywords[$tokenType]['condition'])
                && \call_user_func(array($this, $this->newKeywords[$tokenType]['condition']), $phpcsFile, $stackPtr) === true
            ) {
                return;
            }

            $itemInfo = array(
                'name'   => $tokenType,
            );
            $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
        }
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
        return $this->newKeywords[$itemInfo['name']];
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
        return array(
            'description',
            'condition',
            'content',
        );
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
        $errorInfo                = parent::getErrorInfo($itemArray, $itemInfo);
        $errorInfo['description'] = $itemArray['description'];

        return $errorInfo;
    }


    /**
     * Allow for concrete child classes to filter the error data before it's passed to PHPCS.
     *
     * @since 7.1.0
     *
     * @param array $data      The error data array which was created.
     * @param array $itemInfo  Base information about the item this error message applies to.
     * @param array $errorInfo Detail information about an item this error message applies to.
     *
     * @return array
     */
    protected function filterErrorData(array $data, array $itemInfo, array $errorInfo)
    {
        $data[0] = $errorInfo['description'];
        return $data;
    }


    /**
     * Callback for the quoted heredoc identifier condition.
     *
     * A double quoted identifier will have the opening quote on position 3
     * in the string: `<<<"ID"`.
     *
     * @since 8.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return bool
     */
    public function isNotQuoted(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        return ($tokens[$stackPtr]['content'][3] !== '"');
    }
}
