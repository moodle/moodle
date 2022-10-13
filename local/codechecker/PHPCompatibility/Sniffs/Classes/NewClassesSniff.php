<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Classes;

use PHPCompatibility\AbstractNewFeatureSniff;
use PHP_CodeSniffer_File as File;

/**
 * Detect use of new PHP native classes.
 *
 * The sniff analyses the following constructs to find usage of new classes:
 * - Class instantiation using the `new` keyword.
 * - (Anonymous) Class declarations to detect new classes being extended by userland classes.
 * - Static use of class properties, constants or functions using the double colon.
 * - Function/closure declarations to detect new classes used as parameter type declarations.
 * - Function/closure declarations to detect new classes used as return type declarations.
 * - Try/catch statements to detect new exception classes being caught.
 *
 * PHP version All
 *
 * @since 5.5
 * @since 5.6   Now extends the base `Sniff` class.
 * @since 7.1.0 Now extends the `AbstractNewFeatureSniff` class.
 */
class NewClassesSniff extends AbstractNewFeatureSniff
{

    /**
     * A list of new classes, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the class appears.
     *
     * @since 5.5
     *
     * @var array(string => array(string => bool))
     */
    protected $newClasses = array(
        'ArrayObject' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'ArrayIterator' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'CachingIterator' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'DirectoryIterator' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'RecursiveDirectoryIterator' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'RecursiveIteratorIterator' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'php_user_filter' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'tidy' => array(
            '4.4' => false,
            '5.0' => true,
        ),

        'SimpleXMLElement' => array(
            '5.0.0' => false,
            '5.0.1' => true,
        ),
        'tidyNode' => array(
            '5.0.0' => false,
            '5.0.1' => true,
        ),

        'libXMLError' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'PDO' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'PDOStatement' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'AppendIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'EmptyIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'FilterIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'InfiniteIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'IteratorIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'LimitIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'NoRewindIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'ParentIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'RecursiveArrayIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'RecursiveCachingIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'RecursiveFilterIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'SimpleXMLIterator' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'SplFileObject' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'XMLReader' => array(
            '5.0' => false,
            '5.1' => true,
        ),

        'SplFileInfo' => array(
            '5.1.1' => false,
            '5.1.2' => true,
        ),
        'SplTempFileObject' => array(
            '5.1.1' => false,
            '5.1.2' => true,
        ),
        'XMLWriter' => array(
            '5.1.1' => false,
            '5.1.2' => true,
        ),

        'DateTime' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'DateTimeZone' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'RegexIterator' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'RecursiveRegexIterator' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'ReflectionFunctionAbstract' => array(
            '5.1' => false,
            '5.2' => true,
        ),
        'ZipArchive' => array(
            '5.1' => false,
            '5.2' => true,
        ),

        'Closure' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'DateInterval' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'DatePeriod' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'finfo' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'Collator' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'NumberFormatter' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'Locale' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'Normalizer' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'MessageFormatter' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'IntlDateFormatter' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'Phar' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'PharData' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'PharFileInfo' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'FilesystemIterator' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'GlobIterator' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'MultipleIterator' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'RecursiveTreeIterator' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplDoublyLinkedList' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplFixedArray' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplHeap' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplMaxHeap' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplMinHeap' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplObjectStorage' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplPriorityQueue' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplQueue' => array(
            '5.2' => false,
            '5.3' => true,
        ),
        'SplStack' => array(
            '5.2' => false,
            '5.3' => true,
        ),

        'ResourceBundle' => array(
            '5.3.1' => false,
            '5.3.2' => true,
        ),

        'CallbackFilterIterator' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'RecursiveCallbackFilterIterator' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'ReflectionZendExtension' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'SessionHandler' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'SNMP' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'Transliterator' => array(
            '5.3' => false,
            '5.4' => true,
        ),
        'Spoofchecker' => array(
            '5.3' => false,
            '5.4' => true,
        ),

        'Generator' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'CURLFile' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'DateTimeImmutable' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'IntlCalendar' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'IntlGregorianCalendar' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'IntlTimeZone' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'IntlBreakIterator' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'IntlRuleBasedBreakIterator' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'IntlCodePointBreakIterator' => array(
            '5.4' => false,
            '5.5' => true,
        ),
        'UConverter' => array(
            '5.4' => false,
            '5.5' => true,
        ),

        'GMP' => array(
            '5.5' => false,
            '5.6' => true,
        ),

        'IntlChar' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'ReflectionType' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'ReflectionGenerator' => array(
            '5.6' => false,
            '7.0' => true,
        ),

        'ReflectionClassConstant' => array(
            '7.0' => false,
            '7.1' => true,
        ),

        'FFI' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'FFI\CData' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'FFI\CType' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'ReflectionReference' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'WeakReference' => array(
            '7.3' => false,
            '7.4' => true,
        ),
    );

    /**
     * A list of new Exception classes, not present in older versions.
     *
     * The array lists : version number with false (not present) or true (present).
     * If's sufficient to list the first version where the class appears.
     *
     * {@internal Classes listed here do not need to be added to the $newClasses
     *            property as well.
     *            This list is automatically added to the $newClasses property
     *            in the `register()` method.}
     *
     * {@internal Helper to update this list: https://3v4l.org/MhlUp}
     *
     * @since 7.1.4
     *
     * @var array(string => array(string => bool))
     */
    protected $newExceptions = array(
        'com_exception' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'DOMException' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'Exception' => array(
            // According to the docs introduced in PHP 5.1, but this appears to be.
            // an error.  Class was introduced with try/catch keywords in PHP 5.0.
            '4.4' => false,
            '5.0' => true,
        ),
        'ReflectionException' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'SoapFault' => array(
            '4.4' => false,
            '5.0' => true,
        ),
        'SQLiteException' => array(
            '4.4' => false,
            '5.0' => true,
        ),

        'ErrorException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'BadFunctionCallException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'BadMethodCallException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'DomainException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'InvalidArgumentException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'LengthException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'LogicException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'mysqli_sql_exception' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'OutOfBoundsException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'OutOfRangeException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'OverflowException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'PDOException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'RangeException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'RuntimeException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'UnderflowException' => array(
            '5.0' => false,
            '5.1' => true,
        ),
        'UnexpectedValueException' => array(
            '5.0' => false,
            '5.1' => true,
        ),

        'PharException' => array(
            '5.2' => false,
            '5.3' => true,
        ),

        'SNMPException' => array(
            '5.3' => false,
            '5.4' => true,
        ),

        'IntlException' => array(
            '5.4' => false,
            '5.5' => true,
        ),

        'Error' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'ArithmeticError' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'AssertionError' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'DivisionByZeroError' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'ParseError' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'TypeError' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'ClosedGeneratorException' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'UI\Exception\InvalidArgumentException' => array(
            '5.6' => false,
            '7.0' => true,
        ),
        'UI\Exception\RuntimeException' => array(
            '5.6' => false,
            '7.0' => true,
        ),

        'ArgumentCountError' => array(
            '7.0' => false,
            '7.1' => true,
        ),

        'SodiumException' => array(
            '7.1' => false,
            '7.2' => true,
        ),

        'CompileError' => array(
            '7.2' => false,
            '7.3' => true,
        ),
        'JsonException' => array(
            '7.2' => false,
            '7.3' => true,
        ),

        'FFI\Exception' => array(
            '7.3' => false,
            '7.4' => true,
        ),
        'FFI\ParserException' => array(
            '7.3' => false,
            '7.4' => true,
        ),
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     * @since 7.0.3 - Now also targets the `class` keyword to detect extended classes.
     *              - Now also targets double colons to detect static class use.
     * @since 7.1.4 - Now also targets anonymous classes to detect extended classes.
     *              - Now also targets functions/closures to detect new classes used
     *                as parameter type declarations.
     *              - Now also targets the `catch` control structure to detect new
     *                exception classes being caught.
     * @since 8.2.0 Now also targets the `T_RETURN_TYPE` token to detect new classes used
     *              as return type declarations.
     *
     * @return array
     */
    public function register()
    {
        // Handle case-insensitivity of class names.
        $this->newClasses    = $this->arrayKeysToLowercase($this->newClasses);
        $this->newExceptions = $this->arrayKeysToLowercase($this->newExceptions);

        // Add the Exception classes to the Classes list.
        $this->newClasses = array_merge($this->newClasses, $this->newExceptions);

        $targets = array(
            \T_NEW,
            \T_CLASS,
            \T_DOUBLE_COLON,
            \T_FUNCTION,
            \T_CLOSURE,
            \T_CATCH,
        );

        if (\defined('T_ANON_CLASS')) {
            $targets[] = \T_ANON_CLASS;
        }

        if (\defined('T_RETURN_TYPE')) {
            $targets[] = \T_RETURN_TYPE;
        }

        return $targets;
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
        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['type']) {
            case 'T_FUNCTION':
            case 'T_CLOSURE':
                $this->processFunctionToken($phpcsFile, $stackPtr);

                // Deal with older PHPCS version which don't recognize return type hints
                // as well as newer PHPCS versions (3.3.0+) where the tokenization has changed.
                $returnTypeHint = $this->getReturnTypeHintToken($phpcsFile, $stackPtr);
                if ($returnTypeHint !== false) {
                    $this->processReturnTypeToken($phpcsFile, $returnTypeHint);
                }
                break;

            case 'T_CATCH':
                $this->processCatchToken($phpcsFile, $stackPtr);
                break;

            case 'T_RETURN_TYPE':
                $this->processReturnTypeToken($phpcsFile, $stackPtr);
                break;

            default:
                $this->processSingularToken($phpcsFile, $stackPtr);
                break;
        }
    }


    /**
     * Processes this test for when a token resulting in a singular class name is encountered.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    private function processSingularToken(File $phpcsFile, $stackPtr)
    {
        $tokens      = $phpcsFile->getTokens();
        $FQClassName = '';

        if ($tokens[$stackPtr]['type'] === 'T_NEW') {
            $FQClassName = $this->getFQClassNameFromNewToken($phpcsFile, $stackPtr);

        } elseif ($tokens[$stackPtr]['type'] === 'T_CLASS' || $tokens[$stackPtr]['type'] === 'T_ANON_CLASS') {
            $FQClassName = $this->getFQExtendedClassName($phpcsFile, $stackPtr);

        } elseif ($tokens[$stackPtr]['type'] === 'T_DOUBLE_COLON') {
            $FQClassName = $this->getFQClassNameFromDoubleColonToken($phpcsFile, $stackPtr);
        }

        if ($FQClassName === '') {
            return;
        }

        $className   = substr($FQClassName, 1); // Remove global namespace indicator.
        $classNameLc = strtolower($className);

        if (isset($this->newClasses[$classNameLc]) === false) {
            return;
        }

        $itemInfo = array(
            'name'   => $className,
            'nameLc' => $classNameLc,
        );
        $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
    }


    /**
     * Processes this test for when a function token is encountered.
     *
     * - Detect new classes when used as a parameter type declaration.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    private function processFunctionToken(File $phpcsFile, $stackPtr)
    {
        // Retrieve typehints stripped of global NS indicator and/or nullable indicator.
        $typeHints = $this->getTypeHintsFromFunctionDeclaration($phpcsFile, $stackPtr);
        if (empty($typeHints) || \is_array($typeHints) === false) {
            return;
        }

        foreach ($typeHints as $hint) {

            $typeHintLc = strtolower($hint);

            if (isset($this->newClasses[$typeHintLc]) === true) {
                $itemInfo = array(
                    'name'   => $hint,
                    'nameLc' => $typeHintLc,
                );
                $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
            }
        }
    }


    /**
     * Processes this test for when a catch token is encountered.
     *
     * - Detect exceptions when used in a catch statement.
     *
     * @since 7.1.4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    private function processCatchToken(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Bow out during live coding.
        if (isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer']) === false) {
            return;
        }

        $opener = $tokens[$stackPtr]['parenthesis_opener'];
        $closer = ($tokens[$stackPtr]['parenthesis_closer'] + 1);
        $name   = '';
        $listen = array(
            // Parts of a (namespaced) class name.
            \T_STRING              => true,
            \T_NS_SEPARATOR        => true,
            // End/split tokens.
            \T_VARIABLE            => false,
            \T_BITWISE_OR          => false,
            \T_CLOSE_CURLY_BRACKET => false, // Shouldn't be needed as we expect a var before this.
        );

        for ($i = ($opener + 1); $i < $closer; $i++) {
            if (isset($listen[$tokens[$i]['code']]) === false) {
                continue;
            }

            if ($listen[$tokens[$i]['code']] === true) {
                $name .= $tokens[$i]['content'];
                continue;
            } else {
                if (empty($name) === true) {
                    // Weird, we should have a name by the time we encounter a variable or |.
                    // So this may be the closer.
                    continue;
                }

                $name   = ltrim($name, '\\');
                $nameLC = strtolower($name);

                if (isset($this->newExceptions[$nameLC]) === true) {
                    $itemInfo = array(
                        'name'   => $name,
                        'nameLc' => $nameLC,
                    );
                    $this->handleFeature($phpcsFile, $i, $itemInfo);
                }

                // Reset for a potential multi-catch.
                $name = '';
            }
        }
    }


    /**
     * Processes this test for when a return type token is encountered.
     *
     * - Detect new classes when used as a return type declaration.
     *
     * @since 8.2.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in
     *                                         the stack passed in $tokens.
     *
     * @return void
     */
    private function processReturnTypeToken(File $phpcsFile, $stackPtr)
    {
        $returnTypeHint = $this->getReturnTypeHintName($phpcsFile, $stackPtr);
        if (empty($returnTypeHint)) {
            return;
        }

        $returnTypeHint   = ltrim($returnTypeHint, '\\');
        $returnTypeHintLc = strtolower($returnTypeHint);

        if (isset($this->newClasses[$returnTypeHintLc]) === false) {
            return;
        }

        // Still here ? Then this is a return type declaration using a new class.
        $itemInfo = array(
            'name'   => $returnTypeHint,
            'nameLc' => $returnTypeHintLc,
        );
        $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);
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
        return $this->newClasses[$itemInfo['nameLc']];
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
        return 'The built-in class ' . parent::getErrorMsgTemplate();
    }
}
