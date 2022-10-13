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

use PHPCompatibility\Sniff;
use PHP_CodeSniffer_File as File;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Detects the use of reserved keywords as class, function, namespace or constant names.
 *
 * PHP version All
 *
 * @link https://www.php.net/manual/en/reserved.keywords.php
 *
 * @since 5.5
 */
class ForbiddenNamesSniff extends Sniff
{

    /**
     * A list of keywords that can not be used as function, class and namespace name or constant name.
     * Mentions since which version it's not allowed.
     *
     * @since 5.5
     *
     * @var array(string => string)
     */
    protected $invalidNames = array(
        'abstract'      => '5.0',
        'and'           => 'all',
        'array'         => 'all',
        'as'            => 'all',
        'break'         => 'all',
        'callable'      => '5.4',
        'case'          => 'all',
        'catch'         => '5.0',
        'class'         => 'all',
        'clone'         => '5.0',
        'const'         => 'all',
        'continue'      => 'all',
        'declare'       => 'all',
        'default'       => 'all',
        'die'           => 'all',
        'do'            => 'all',
        'echo'          => 'all',
        'else'          => 'all',
        'elseif'        => 'all',
        'empty'         => 'all',
        'enddeclare'    => 'all',
        'endfor'        => 'all',
        'endforeach'    => 'all',
        'endif'         => 'all',
        'endswitch'     => 'all',
        'endwhile'      => 'all',
        'eval'          => 'all',
        'exit'          => 'all',
        'extends'       => 'all',
        'final'         => '5.0',
        'finally'       => '5.5',
        'for'           => 'all',
        'foreach'       => 'all',
        'function'      => 'all',
        'global'        => 'all',
        'goto'          => '5.3',
        'if'            => 'all',
        'implements'    => '5.0',
        'include'       => 'all',
        'include_once'  => 'all',
        'instanceof'    => '5.0',
        'insteadof'     => '5.4',
        'interface'     => '5.0',
        'isset'         => 'all',
        'list'          => 'all',
        'namespace'     => '5.3',
        'new'           => 'all',
        'or'            => 'all',
        'print'         => 'all',
        'private'       => '5.0',
        'protected'     => '5.0',
        'public'        => '5.0',
        'require'       => 'all',
        'require_once'  => 'all',
        'return'        => 'all',
        'static'        => 'all',
        'switch'        => 'all',
        'throw'         => '5.0',
        'trait'         => '5.4',
        'try'           => '5.0',
        'unset'         => 'all',
        'use'           => 'all',
        'var'           => 'all',
        'while'         => 'all',
        'xor'           => 'all',
        'yield'         => '5.5',
        '__class__'     => 'all',
        '__dir__'       => '5.3',
        '__file__'      => 'all',
        '__function__'  => 'all',
        '__method__'    => 'all',
        '__namespace__' => '5.3',
    );

    /**
     * A list of keywords that can follow use statements.
     *
     * @since 7.0.1
     *
     * @var array(string => string)
     */
    protected $validUseNames = array(
        'const'    => true,
        'function' => true,
    );

    /**
     * Scope modifiers and other keywords allowed in trait use statements.
     *
     * @since 7.1.4
     *
     * @var array
     */
    private $allowedModifiers = array();

    /**
     * Targeted tokens.
     *
     * @since 5.5
     *
     * @var array
     */
    protected $targetedTokens = array(
        \T_CLASS,
        \T_FUNCTION,
        \T_NAMESPACE,
        \T_STRING,
        \T_CONST,
        \T_USE,
        \T_AS,
        \T_EXTENDS,
        \T_INTERFACE,
        \T_TRAIT,
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 5.5
     *
     * @return array
     */
    public function register()
    {
        $this->allowedModifiers           = Tokens::$scopeModifiers;
        $this->allowedModifiers[\T_FINAL] = \T_FINAL;

        $tokens = $this->targetedTokens;

        if (\defined('T_ANON_CLASS')) {
            $tokens[] = \T_ANON_CLASS;
        }

        return $tokens;
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        /*
         * We distinguish between the class, function and namespace names vs the define statements.
         */
        if ($tokens[$stackPtr]['type'] === 'T_STRING') {
            $this->processString($phpcsFile, $stackPtr, $tokens);
        } else {
            $this->processNonString($phpcsFile, $stackPtr, $tokens);
        }
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     * @param array                 $tokens    The stack of tokens that make up
     *                                         the file.
     *
     * @return void
     */
    public function processNonString(File $phpcsFile, $stackPtr, $tokens)
    {
        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($nextNonEmpty === false) {
            return;
        }

        /*
         * Deal with anonymous classes - `class` before a reserved keyword is sometimes
         * misidentified as `T_ANON_CLASS`.
         * In PHPCS < 2.3.4 these were tokenized as T_CLASS no matter what.
         */
        if ($tokens[$stackPtr]['type'] === 'T_ANON_CLASS' || $tokens[$stackPtr]['type'] === 'T_CLASS') {
            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
            if ($prevNonEmpty !== false && $tokens[$prevNonEmpty]['type'] === 'T_NEW') {
                return;
            }
        }

        /*
         * PHP 5.6 allows for use const and use function, but only if followed by the function/constant name.
         * - `use function HelloWorld` => move to the next token (HelloWorld) to verify.
         * - `use const HelloWorld` => move to the next token (HelloWorld) to verify.
         */
        elseif ($tokens[$stackPtr]['type'] === 'T_USE'
            && isset($this->validUseNames[strtolower($tokens[$nextNonEmpty]['content'])]) === true
        ) {
            $maybeUseNext = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonEmpty + 1), null, true, null, true);
            if ($maybeUseNext !== false && $this->isEndOfUseStatement($tokens[$maybeUseNext]) === false) {
                $nextNonEmpty = $maybeUseNext;
            }
        }

        /*
         * Deal with visibility modifiers.
         * - `use HelloWorld { sayHello as protected; }` => valid, bow out.
         * - `use HelloWorld { sayHello as private myPrivateHello; }` => move to the next token to verify.
         */
        elseif ($tokens[$stackPtr]['type'] === 'T_AS'
            && isset($this->allowedModifiers[$tokens[$nextNonEmpty]['code']]) === true
            && $phpcsFile->hasCondition($stackPtr, \T_USE) === true
        ) {
            $maybeUseNext = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonEmpty + 1), null, true, null, true);
            if ($maybeUseNext === false || $this->isEndOfUseStatement($tokens[$maybeUseNext]) === true) {
                return;
            }

            $nextNonEmpty = $maybeUseNext;
        }

        /*
         * Deal with foreach ( ... as list() ).
         */
        elseif ($tokens[$stackPtr]['type'] === 'T_AS'
            && isset($tokens[$stackPtr]['nested_parenthesis']) === true
            && $tokens[$nextNonEmpty]['code'] === \T_LIST
        ) {
            $parentheses = array_reverse($tokens[$stackPtr]['nested_parenthesis'], true);
            foreach ($parentheses as $open => $close) {
                if (isset($tokens[$open]['parenthesis_owner'])
                    && $tokens[$tokens[$open]['parenthesis_owner']]['code'] === \T_FOREACH
                ) {
                    return;
                }
            }
        }

        /*
         * Deal with functions declared to return by reference.
         */
        elseif ($tokens[$stackPtr]['type'] === 'T_FUNCTION'
            && $tokens[$nextNonEmpty]['type'] === 'T_BITWISE_AND'
        ) {
            $maybeUseNext = $phpcsFile->findNext(Tokens::$emptyTokens, ($nextNonEmpty + 1), null, true, null, true);
            if ($maybeUseNext === false) {
                // Live coding.
                return;
            }

            $nextNonEmpty = $maybeUseNext;
        }

        /*
         * Deal with nested namespaces.
         */
        elseif ($tokens[$stackPtr]['type'] === 'T_NAMESPACE') {
            if ($tokens[$stackPtr + 1]['code'] === \T_NS_SEPARATOR) {
                // Not a namespace declaration, but use of, i.e. `namespace\someFunction();`.
                return;
            }

            $endToken      = $phpcsFile->findNext(array(\T_SEMICOLON, \T_OPEN_CURLY_BRACKET), ($stackPtr + 1), null, false, null, true);
            $namespaceName = trim($phpcsFile->getTokensAsString(($stackPtr + 1), ($endToken - $stackPtr - 1)));
            if (empty($namespaceName) === true) {
                return;
            }

            $namespaceParts = explode('\\', $namespaceName);
            foreach ($namespaceParts as $namespacePart) {
                $partLc = strtolower($namespacePart);
                if (isset($this->invalidNames[$partLc]) === false) {
                    continue;
                }

                // Find the token position of the part which matched.
                for ($i = ($stackPtr + 1); $i < $endToken; $i++) {
                    if ($tokens[$i]['content'] === $namespacePart) {
                        $nextNonEmpty = $i;
                        break;
                    }
                }
            }
            unset($i, $namespacePart, $partLc);
        }

        $nextContentLc = strtolower($tokens[$nextNonEmpty]['content']);
        if (isset($this->invalidNames[$nextContentLc]) === false) {
            return;
        }

        /*
         * Deal with PHP 7 relaxing the rules.
         * "As of PHP 7.0.0 these keywords are allowed as property, constant, and method names
         * of classes, interfaces and traits, except that class may not be used as constant name."
         */
        if ((($tokens[$stackPtr]['type'] === 'T_FUNCTION'
                && $this->inClassScope($phpcsFile, $stackPtr, false) === true)
            || ($tokens[$stackPtr]['type'] === 'T_CONST'
                && $this->isClassConstant($phpcsFile, $stackPtr) === true
                && $nextContentLc !== 'class'))
            && $this->supportsBelow('5.6') === false
        ) {
            return;
        }

        if ($this->supportsAbove($this->invalidNames[$nextContentLc])) {
            $data = array(
                $tokens[$nextNonEmpty]['content'],
                $this->invalidNames[$nextContentLc],
            );
            $this->addError($phpcsFile, $stackPtr, $tokens[$nextNonEmpty]['content'], $data);
        }
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 5.5
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     * @param array                 $tokens    The stack of tokens that make up
     *                                         the file.
     *
     * @return void
     */
    public function processString(File $phpcsFile, $stackPtr, $tokens)
    {
        $tokenContentLc = strtolower($tokens[$stackPtr]['content']);

        /*
         * Special case for PHP versions where the target is not yet identified as
         * its own token, but presents as T_STRING.
         * - trait keyword in PHP < 5.4
         */
        if (version_compare(\PHP_VERSION_ID, '50400', '<') && $tokenContentLc === 'trait') {
            $this->processNonString($phpcsFile, $stackPtr, $tokens);
            return;
        }

        // Look for any define/defined tokens (both T_STRING ones, blame Tokenizer).
        if ($tokenContentLc !== 'define' && $tokenContentLc !== 'defined') {
            return;
        }

        // Retrieve the define(d) constant name.
        $firstParam = $this->getFunctionCallParameter($phpcsFile, $stackPtr, 1);
        if ($firstParam === false) {
            return;
        }

        $defineName   = $this->stripQuotes($firstParam['raw']);
        $defineNameLc = strtolower($defineName);

        if (isset($this->invalidNames[$defineNameLc]) && $this->supportsAbove($this->invalidNames[$defineNameLc])) {
            $data = array(
                $defineName,
                $this->invalidNames[$defineNameLc],
            );
            $this->addError($phpcsFile, $stackPtr, $defineNameLc, $data);
        }
    }


    /**
     * Add the error message.
     *
     * @since 7.1.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     * @param string                $content   The token content found.
     * @param array                 $data      The data to pass into the error message.
     *
     * @return void
     */
    protected function addError(File $phpcsFile, $stackPtr, $content, $data)
    {
        $error     = "Function name, class name, namespace name or constant name can not be reserved keyword '%s' (since version %s)";
        $errorCode = $this->stringToErrorCode($content) . 'Found';
        $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
    }


    /**
     * Check if the current token code is for a token which can be considered
     * the end of a (partial) use statement.
     *
     * @since 7.0.8
     *
     * @param int $token The current token information.
     *
     * @return bool
     */
    protected function isEndOfUseStatement($token)
    {
        return \in_array($token['code'], array(\T_CLOSE_CURLY_BRACKET, \T_SEMICOLON, \T_COMMA), true);
    }
}
