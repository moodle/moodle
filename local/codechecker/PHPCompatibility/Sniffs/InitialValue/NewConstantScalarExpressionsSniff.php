<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\InitialValue;

use PHPCompatibility\Sniff;
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detect constant scalar expressions being used to set an initial value.
 *
 * Since PHP 5.6, it is now possible to provide a scalar expression involving
 * numeric and string literals and/or constants in contexts where PHP previously
 * expected a static value, such as constant and property declarations and
 * default values for function parameters.
 *
 * PHP version 5.6
 *
 * @link https://www.php.net/manual/en/migration56.new-features.php#migration56.new-features.const-scalar-exprs
 * @link https://wiki.php.net/rfc/const_scalar_exprs
 *
 * @since 8.2.0
 */
class NewConstantScalarExpressionsSniff extends Sniff
{

    /**
     * Error message.
     *
     * @since 8.2.0
     *
     * @var string
     */
    const ERROR_PHRASE = 'Constant scalar expressions are not allowed %s in PHP 5.5 or earlier.';

    /**
     * Partial error phrases to be used in combination with the error message constant.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $errorPhrases = array(
        'const'     => 'when defining constants using the const keyword',
        'property'  => 'in property declarations',
        'staticvar' => 'in static variable declarations',
        'default'   => 'in default function arguments',
    );

    /**
     * Tokens which were allowed to be used in these declarations prior to PHP 5.6.
     *
     * This list will be enriched in the setProperties() method.
     *
     * @since 8.2.0
     *
     * @var array
     */
    protected $safeOperands = array(
        \T_LNUMBER                  => \T_LNUMBER,
        \T_DNUMBER                  => \T_DNUMBER,
        \T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
        \T_TRUE                     => \T_TRUE,
        \T_FALSE                    => \T_FALSE,
        \T_NULL                     => \T_NULL,

        \T_LINE                     => \T_LINE,
        \T_FILE                     => \T_FILE,
        \T_DIR                      => \T_DIR,
        \T_FUNC_C                   => \T_FUNC_C,
        \T_CLASS_C                  => \T_CLASS_C,
        \T_TRAIT_C                  => \T_TRAIT_C,
        \T_METHOD_C                 => \T_METHOD_C,
        \T_NS_C                     => \T_NS_C,

        // Special cases:
        \T_NS_SEPARATOR             => \T_NS_SEPARATOR,
        /*
         * This can be neigh anything, but for any usage except constants,
         * the T_STRING will be combined with non-allowed tokens, so we should be good.
         */
        \T_STRING                   => \T_STRING,
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
        // Set the properties up only once.
        $this->setProperties();

        return array(
            \T_CONST,
            \T_VARIABLE,
            \T_FUNCTION,
            \T_CLOSURE,
            \T_STATIC,
        );
    }


    /**
     * Make some adjustments to the $safeOperands property.
     *
     * @since 8.2.0
     *
     * @return void
     */
    public function setProperties()
    {
        $this->safeOperands += Tokens::$heredocTokens;
        $this->safeOperands += Tokens::$emptyTokens;
    }


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * @since 8.2.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('5.5') !== true);
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
     * @return void|int Null or integer stack pointer to skip forward.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->bowOutEarly() === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['type']) {
            case 'T_FUNCTION':
            case 'T_CLOSURE':
                $params = PHPCSHelper::getMethodParameters($phpcsFile, $stackPtr);
                if (empty($params)) {
                    // No parameters.
                    return;
                }

                $funcToken = $tokens[$stackPtr];

                if (isset($funcToken['parenthesis_owner'], $funcToken['parenthesis_opener'], $funcToken['parenthesis_closer']) === false
                    || $funcToken['parenthesis_owner'] !== $stackPtr
                    || isset($tokens[$funcToken['parenthesis_opener']], $tokens[$funcToken['parenthesis_closer']]) === false
                ) {
                    // Hmm.. something is going wrong as these should all be available & valid.
                    return;
                }

                $opener = $funcToken['parenthesis_opener'];
                $closer = $funcToken['parenthesis_closer'];

                // Which nesting level is the one we are interested in ?
                $nestedParenthesisCount = 1;
                if (isset($tokens[$opener]['nested_parenthesis'])) {
                    $nestedParenthesisCount += \count($tokens[$opener]['nested_parenthesis']);
                }

                foreach ($params as $param) {
                    if (isset($param['default']) === false) {
                        continue;
                    }

                    $end = $param['token'];
                    while (($end = $phpcsFile->findNext(array(\T_COMMA, \T_CLOSE_PARENTHESIS), ($end + 1), ($closer + 1))) !== false) {
                        $maybeSkipTo = $this->isRealEndOfDeclaration($tokens, $end, $nestedParenthesisCount);
                        if ($maybeSkipTo !== true) {
                            $end = $maybeSkipTo;
                            continue;
                        }

                        // Ignore closing parenthesis/bracket if not 'ours'.
                        if ($tokens[$end]['code'] === \T_CLOSE_PARENTHESIS && $end !== $closer) {
                            continue;
                        }

                        // Ok, we've found the end of the param default value declaration.
                        break;
                    }

                    if ($this->isValidAssignment($phpcsFile, $param['token'], $end) === false) {
                        $this->throwError($phpcsFile, $param['token'], 'default', $param['content']);
                    }
                }

                /*
                 * No need for the sniff to be triggered by the T_VARIABLEs in the function
                 * definition as we've already examined them above, so let's skip over them.
                 */
                return $closer;

            case 'T_VARIABLE':
            case 'T_STATIC':
            case 'T_CONST':
                $type = 'const';

                // Filter out non-property declarations.
                if ($tokens[$stackPtr]['code'] === \T_VARIABLE) {
                    if ($this->isClassProperty($phpcsFile, $stackPtr) === false) {
                        return;
                    }

                    $type = 'property';

                    // Move back one token to have the same starting point as the others.
                    $stackPtr = ($stackPtr - 1);
                }

                // Filter out late static binding and class properties.
                if ($tokens[$stackPtr]['code'] === \T_STATIC) {
                    $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true, null, true);
                    if ($next === false || $tokens[$next]['code'] !== \T_VARIABLE) {
                        // Late static binding.
                        return;
                    }

                    if ($this->isClassProperty($phpcsFile, $next) === true) {
                        // Class properties are examined based on the T_VARIABLE token.
                        return;
                    }
                    unset($next);

                    $type = 'staticvar';
                }

                $endOfStatement = $phpcsFile->findNext(array(\T_SEMICOLON, \T_CLOSE_TAG), ($stackPtr + 1));
                if ($endOfStatement === false) {
                    // No semi-colon - live coding.
                    return;
                }

                $targetNestingLevel = 0;
                if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
                    $targetNestingLevel = \count($tokens[$stackPtr]['nested_parenthesis']);
                }

                // Examine each variable/constant in multi-declarations.
                $start = $stackPtr;
                $end   = $stackPtr;
                while (($end = $phpcsFile->findNext(array(\T_COMMA, \T_SEMICOLON, \T_OPEN_SHORT_ARRAY, \T_CLOSE_TAG), ($end + 1), ($endOfStatement + 1))) !== false) {

                    $maybeSkipTo = $this->isRealEndOfDeclaration($tokens, $end, $targetNestingLevel);
                    if ($maybeSkipTo !== true) {
                        $end = $maybeSkipTo;
                        continue;
                    }

                    $start = $phpcsFile->findNext(Tokens::$emptyTokens, ($start + 1), $end, true);
                    if ($start === false
                        || ($tokens[$stackPtr]['code'] === \T_CONST && $tokens[$start]['code'] !== \T_STRING)
                        || ($tokens[$stackPtr]['code'] !== \T_CONST && $tokens[$start]['code'] !== \T_VARIABLE)
                    ) {
                        // Shouldn't be possible.
                        continue;
                    }

                    if ($this->isValidAssignment($phpcsFile, $start, $end) === false) {
                        // Create the "found" snippet.
                        $content    = '';
                        $tokenCount = ($end - $start);
                        if ($tokenCount < 20) {
                            // Prevent large arrays from being added to the error message.
                            $content = $phpcsFile->getTokensAsString($start, ($tokenCount + 1));
                        }

                        $this->throwError($phpcsFile, $start, $type, $content);
                    }

                    $start = $end;
                }

                // Skip to the end of the statement to prevent duplicate messages for multi-declarations.
                return $endOfStatement;
        }
    }


    /**
     * Is a value declared and is the value declared valid pre-PHP 5.6 ?
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     * @param int                   $end       The end of the value definition.
     *                                         This will normally be a comma or semi-colon.
     *
     * @return bool
     */
    protected function isValidAssignment(File $phpcsFile, $stackPtr, $end)
    {
        $tokens = $phpcsFile->getTokens();
        $next   = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), $end, true);
        if ($next === false || $tokens[$next]['code'] !== \T_EQUAL) {
            // No value assigned.
            return true;
        }

        return $this->isStaticValue($phpcsFile, $tokens, ($next + 1), ($end - 1));
    }


    /**
     * Is a value declared and is the value declared constant as accepted in PHP 5.5 and lower ?
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param array                 $tokens       The token stack of the current file.
     * @param int                   $start        The stackPtr from which to start examining.
     * @param int                   $end          The end of the value definition (inclusive),
     *                                            i.e. this token will be examined as part of
     *                                            the snippet.
     * @param int                   $nestedArrays Optional. Array nesting level when examining
     *                                            the content of an array.
     *
     * @return bool
     */
    protected function isStaticValue(File $phpcsFile, $tokens, $start, $end, $nestedArrays = 0)
    {
        $nextNonSimple = $phpcsFile->findNext($this->safeOperands, $start, ($end + 1), true);
        if ($nextNonSimple === false) {
            return true;
        }

        /*
         * OK, so we have at least one token which needs extra examination.
         */
        switch ($tokens[$nextNonSimple]['code']) {
            case \T_MINUS:
            case \T_PLUS:
                if ($this->isNumber($phpcsFile, $start, $end, true) !== false) {
                    // Int or float with sign.
                    return true;
                }

                return false;

            case \T_NAMESPACE:
            case \T_PARENT:
            case \T_SELF:
            case \T_DOUBLE_COLON:
                $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonSimple + 1), ($end + 1), true);

                if ($tokens[$nextNonSimple]['code'] === \T_NAMESPACE) {
                    // Allow only `namespace\...`.
                    if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_NS_SEPARATOR) {
                        return false;
                    }
                } elseif ($tokens[$nextNonSimple]['code'] === \T_PARENT
                    || $tokens[$nextNonSimple]['code'] === \T_SELF
                ) {
                    // Allow only `parent::` and `self::`.
                    if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_DOUBLE_COLON) {
                        return false;
                    }
                } elseif ($tokens[$nextNonSimple]['code'] === \T_DOUBLE_COLON) {
                    // Allow only `T_STRING::T_STRING`.
                    if ($nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== \T_STRING) {
                        return false;
                    }

                    $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($nextNonSimple - 1), null, true);
                    // No need to worry about parent/self, that's handled above and
                    // the double colon is skipped over in that case.
                    if ($prevNonEmpty === false || $tokens[$prevNonEmpty]['code'] !== \T_STRING) {
                        return false;
                    }
                }

                // Examine what comes after the namespace/parent/self/double colon, if anything.
                return $this->isStaticValue($phpcsFile, $tokens, ($nextNonEmpty + 1), $end, $nestedArrays);

            case \T_ARRAY:
            case \T_OPEN_SHORT_ARRAY:
                ++$nestedArrays;

                $arrayItems = $this->getFunctionCallParameters($phpcsFile, $nextNonSimple);
                if (empty($arrayItems) === false) {
                    foreach ($arrayItems as $item) {
                        // Check for a double arrow, but only if it's for this array item, not for a nested array.
                        $doubleArrow = false;

                        $maybeDoubleArrow = $phpcsFile->findNext(
                            array(\T_DOUBLE_ARROW, \T_ARRAY, \T_OPEN_SHORT_ARRAY),
                            $item['start'],
                            ($item['end'] + 1)
                        );
                        if ($maybeDoubleArrow !== false && $tokens[$maybeDoubleArrow]['code'] === \T_DOUBLE_ARROW) {
                            // Double arrow is for this nesting level.
                            $doubleArrow = $maybeDoubleArrow;
                        }

                        if ($doubleArrow === false) {
                            if ($this->isStaticValue($phpcsFile, $tokens, $item['start'], $item['end'], $nestedArrays) === false) {
                                return false;
                            }

                        } else {
                            // Examine array key.
                            if ($this->isStaticValue($phpcsFile, $tokens, $item['start'], ($doubleArrow - 1), $nestedArrays) === false) {
                                return false;
                            }

                            // Examine array value.
                            if ($this->isStaticValue($phpcsFile, $tokens, ($doubleArrow + 1), $item['end'], $nestedArrays) === false) {
                                return false;
                            }
                        }
                    }
                }

                --$nestedArrays;

                /*
                 * Find the end of the array.
                 * We already know we will have a valid closer as otherwise we wouldn't have been
                 * able to get the array items.
                 */
                $closer = ($nextNonSimple + 1);
                if ($tokens[$nextNonSimple]['code'] === \T_OPEN_SHORT_ARRAY
                    && isset($tokens[$nextNonSimple]['bracket_closer']) === true
                ) {
                    $closer = $tokens[$nextNonSimple]['bracket_closer'];
                } else {
                    $maybeOpener = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonSimple + 1), ($end + 1), true);
                    if ($tokens[$maybeOpener]['code'] === \T_OPEN_PARENTHESIS) {
                        $opener = $maybeOpener;
                        if (isset($tokens[$opener]['parenthesis_closer']) === true) {
                            $closer = $tokens[$opener]['parenthesis_closer'];
                        }
                    }
                }

                if ($closer === $end) {
                    return true;
                }

                // Examine what comes after the array, if anything.
                return $this->isStaticValue($phpcsFile, $tokens, ($closer + 1), $end, $nestedArrays);

        }

        // Ok, so this unsafe token was not one of the exceptions, i.e. this is a PHP 5.6+ syntax.
        return false;
    }


    /**
     * Throw an error if a scalar expression is found.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the token to link the error to.
     * @param string                $type      Type of usage found.
     * @param string                $content   Optional. The value for the declaration as found.
     *
     * @return void
     */
    protected function throwError(File $phpcsFile, $stackPtr, $type, $content = '')
    {
        $error     = static::ERROR_PHRASE;
        $phrase    = '';
        $errorCode = 'Found';

        if (isset($this->errorPhrases[$type]) === true) {
            $errorCode = $this->stringToErrorCode($type) . 'Found';
            $phrase    = $this->errorPhrases[$type];
        }

        $data = array($phrase);

        if (empty($content) === false) {
            $error .= ' Found: %s';
            $data[] = $content;
        }

        $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
    }


    /**
     * Helper function to find the end of multi variable/constant declarations.
     *
     * Checks whether a certain part of a declaration needs to be skipped over or
     * if it is the real end of the declaration.
     *
     * @since 8.2.0
     *
     * @param array $tokens      Token stack of the current file.
     * @param int   $endPtr      The token to examine as a candidate end pointer.
     * @param int   $targetLevel Target nesting level.
     *
     * @return bool|int True if this is the real end. Int stackPtr to skip to if not.
     */
    private function isRealEndOfDeclaration($tokens, $endPtr, $targetLevel)
    {
        // Ignore anything within short array definition brackets for now.
        if ($tokens[$endPtr]['code'] === \T_OPEN_SHORT_ARRAY
            && (isset($tokens[$endPtr]['bracket_opener'])
                && $tokens[$endPtr]['bracket_opener'] === $endPtr)
            && isset($tokens[$endPtr]['bracket_closer'])
        ) {
            // Skip forward to the end of the short array definition.
            return $tokens[$endPtr]['bracket_closer'];
        }

        // Skip past comma's at a lower nesting level.
        if ($tokens[$endPtr]['code'] === \T_COMMA) {
            // Check if a comma is at the nesting level we're targetting.
            $nestingLevel = 0;
            if (isset($tokens[$endPtr]['nested_parenthesis']) === true) {
                $nestingLevel = \count($tokens[$endPtr]['nested_parenthesis']);
            }
            if ($nestingLevel > $targetLevel) {
                return $endPtr;
            }
        }

        return true;
    }
}
