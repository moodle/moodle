<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Classes;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewClasses sniff.
 *
 * @group newClasses
 * @group classes
 *
 * @covers \PHPCompatibility\Sniffs\Classes\NewClassesSniff
 * @covers \PHPCompatibility\Sniff::getReturnTypeHintName
 * @covers \PHPCompatibility\Sniff::getReturnTypeHintToken
 * @covers \PHPCompatibility\Sniff::getTypeHintsFromFunctionDeclaration
 *
 * @since 5.5
 */
class NewClassesUnitTest extends BaseSniffTest
{

    /**
     * testNewClass
     *
     * @dataProvider dataNewClass
     *
     * @param string $className         Class name.
     * @param string $lastVersionBefore The PHP version just *before* the class was introduced.
     * @param array  $lines             The line numbers in the test file which apply to this class.
     * @param string $okVersion         A PHP version in which the class was ok to be used.
     * @param string $testVersion       Optional. A PHP version in which to test for the error if different
     *                                  from the $lastVersionBefore.
     *
     * @return void
     */
    public function testNewClass($className, $lastVersionBefore, $lines, $okVersion, $testVersion = null)
    {
        $errorVersion = (isset($testVersion)) ? $testVersion : $lastVersionBefore;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "The built-in class {$className} is not present in PHP version {$lastVersionBefore} or earlier";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }

        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewClass()
     *
     * @return array
     */
    public function dataNewClass()
    {
        return array(
            array('ArrayObject', '4.4', array(305), '5.0'),
            array('ArrayIterator', '4.4', array(283), '5.0'),
            array('CachingIterator', '4.4', array(284), '5.0'),
            array('DirectoryIterator', '4.4', array(285), '5.0'),
            array('RecursiveDirectoryIterator', '4.4', array(286), '5.0'),
            array('RecursiveIteratorIterator', '4.4', array(287), '5.0'),
            array('php_user_filter', '4.4', array(299), '5.0'),
            array('tidy', '4.4', array(300), '5.0'),
            array('SimpleXMLElement', '5.0.0', array(310), '5.1', '5.0'),
            array('tidyNode', '5.0.0', array(301), '5.1', '5.0'),
            array('libXMLError', '5.0', array(61, 101, 141), '5.1'),
            array('PDO', '5.0', array(314), '5.1'),
            array('PDOStatement', '5.0', array(315), '5.1'),
            array('AppendIterator', '5.0', array(288), '5.1'),
            array('EmptyIterator', '5.0', array(289), '5.1'),
            array('FilterIterator', '5.0', array(290), '5.1'),
            array('InfiniteIterator', '5.0', array(291), '5.1'),
            array('IteratorIterator', '5.0', array(292), '5.1'),
            array('LimitIterator', '5.0', array(293), '5.1'),
            array('NoRewindIterator', '5.0', array(294), '5.1'),
            array('ParentIterator', '5.0', array(295), '5.1'),
            array('RecursiveArrayIterator', '5.0', array(296), '5.1'),
            array('RecursiveCachingIterator', '5.0', array(297), '5.1'),
            array('RecursiveFilterIterator', '5.0', array(298), '5.1'),
            array('SimpleXMLIterator', '5.0', array(311), '5.1'),
            array('XMLReader', '5.0', array(312, 336), '5.1'),
            array('SplFileObject', '5.0', array(302, 336), '5.1'),
            array('SplFileInfo', '5.1.1', array(303), '5.2', '5.1'),
            array('SplTempFileObject', '5.1.1', array(304), '5.2', '5.1'),
            array('XMLWriter', '5.1.1', array(313), '5.2', '5.1'),
            array('DateTime', '5.1', array(25, 65, 105, 151, 318, 319, 321, 331), '5.2'),
            array('DateTimeZone', '5.1', array(26, 66, 106, 162, 331), '5.2'),
            array('RegexIterator', '5.1', array(27, 67, 107, 163), '5.2'),
            array('RecursiveRegexIterator', '5.1', array(28, 68, 108), '5.2'),
            array('ReflectionFunctionAbstract', '5.1', array(307), '5.2'),
            array('ZipArchive', '5.1', array(268), '5.2'),
            array('Closure', '5.2', array(279), '5.3'),
            array('DateInterval', '5.2', array(17, 18, 19, 20, 29, 69, 109), '5.3'),
            array('DatePeriod', '5.2', array(30, 70, 110, 173), '5.3'),
            array('finfo', '5.2', array(278), '5.3'),
            array('Collator', '5.2', array(269), '5.3'),
            array('NumberFormatter', '5.2', array(270), '5.3'),
            array('Locale', '5.2', array(271), '5.3'),
            array('Normalizer', '5.2', array(272), '5.3'),
            array('MessageFormatter', '5.2', array(273), '5.3'),
            array('IntlDateFormatter', '5.2', array(274), '5.3'),
            array('Phar', '5.2', array(31, 71, 111, 152), '5.3'),
            array('PharData', '5.2', array(32, 72, 112), '5.3'),
            array('PharException', '5.2', array(33, 73, 113), '5.3'),
            array('PharFileInfo', '5.2', array(34, 74, 114), '5.3'),
            array('FilesystemIterator', '5.2', array(35, 75, 115, 174), '5.3'),
            array('GlobIterator', '5.2', array(36, 76, 116, 168), '5.3'),
            array('MultipleIterator', '5.2', array(37, 77, 117, 178), '5.3'),
            array('RecursiveTreeIterator', '5.2', array(38, 78, 118), '5.3'),
            array('SplDoublyLinkedList', '5.2', array(39, 79, 119), '5.3'),
            array('SplFixedArray', '5.2', array(40, 80, 120), '5.3'),
            array('SplHeap', '5.2', array(41, 81, 121, 164), '5.3'),
            array('SplMaxHeap', '5.2', array(42, 82, 122), '5.3'),
            array('SplMinHeap', '5.2', array(43, 83, 123, 153), '5.3'),
            array('SplObjectStorage', '5.2', array(282), '5.3'),
            array('SplPriorityQueue', '5.2', array(44, 84, 124), '5.3'),
            array('SplQueue', '5.2', array(45, 85, 125), '5.3'),
            array('SplStack', '5.2', array(46, 86, 126), '5.3'),
            array('ResourceBundle', '5.3.1', array(275), '5.4', '5.3'),
            array('CallbackFilterIterator', '5.3', array(47, 87, 127), '5.4'),
            array('RecursiveCallbackFilterIterator', '5.3', array(48, 88, 128, 179), '5.4'),
            array('ReflectionZendExtension', '5.3', array(49, 89, 129), '5.4'),
            array('SessionHandler', '5.3', array(50, 90, 130), '5.4'),
            array('SNMP', '5.3', array(51, 91, 131, 180), '5.4'),
            array('Transliterator', '5.3', array(52, 92, 132, 154), '5.4'),
            array('Generator', '5.4', array(280), '5.5'),
            array('CURLFile', '5.4', array(53, 93, 133), '5.5'),
            array('DateTimeImmutable', '5.4', array(54, 94, 134), '5.5'),
            array('IntlCalendar', '5.4', array(55, 95, 135, 165), '5.5'),
            array('IntlGregorianCalendar', '5.4', array(56, 96, 136), '5.5'),
            array('IntlTimeZone', '5.4', array(57, 97, 137), '5.5'),
            array('IntlBreakIterator', '5.4', array(58, 98, 138), '5.5'),
            array('IntlRuleBasedBreakIterator', '5.4', array(59, 99, 139), '5.5'),
            array('IntlCodePointBreakIterator', '5.4', array(60, 100, 140), '5.5'),
            array('UConverter', '5.4', array(276), '5.5'),
            array('GMP', '5.5', array(281), '5.6'),
            array('IntlChar', '5.6', array(277), '7.0'),
            array('ReflectionType', '5.6', array(308), '7.0'),
            array('ReflectionGenerator', '5.6', array(309), '7.0'),
            array('ReflectionClassConstant', '7.0', array(306), '7.1'),
            array('FFI', '7.3', array(346), '7.4'),
            array('FFI\CData', '7.3', array(347), '7.4'),
            array('FFI\CType', '7.3', array(347), '7.4'),
            array('ReflectionReference', '7.3', array(344), '7.4'),
            array('WeakReference', '7.3', array(345), '7.4'),

            array('DATETIME', '5.1', array(146), '5.2'),
            array('datetime', '5.1', array(147, 320), '5.2'),
            array('dATeTiMe', '5.1', array(148), '5.2'),

            array('com_exception', '4.4', array(343), '5.0'),
            array('DOMException', '4.4', array(232, 260), '5.0'),
            array('Exception', '4.4', array(190, 217), '5.0'),
            array('ReflectionException', '4.4', array(187, 235), '5.0'),
            array('SoapFault', '4.4', array(236), '5.0'),
            array('SQLiteException', '4.4', array(340), '5.0'),
            array('ErrorException', '5.0', array(194, 218), '5.1'),
            array('BadFunctionCallException', '5.0', array(201, 219), '5.1'),
            array('BadMethodCallException', '5.0', array(207, 220), '5.1'),
            array('DomainException', '5.0', array(186, 221), '5.1'),
            array('InvalidArgumentException', '5.0', array(222, 255), '5.1'),
            array('LengthException', '5.0', array(195, 223), '5.1'),
            array('LogicException', '5.0', array(224, 255), '5.1'),
            array('mysqli_sql_exception', '5.0', array(202, 233), '5.1'),
            array('PDOException', '5.0', array(198, 234), '5.1'),
            array('OutOfBoundsException', '5.0', array(225, 255), '5.1'),
            array('OutOfRangeException', '5.0', array(226, 255), '5.1'),
            array('OverflowException', '5.0', array(196, 227), '5.1'),
            array('RangeException', '5.0', array(208, 228), '5.1'),
            array('RuntimeException', '5.0', array(229, 255), '5.1'),
            array('UnderflowException', '5.0', array(197, 230), '5.1'),
            array('UnexpectedValueException', '5.0', array(191, 231), '5.1'),
            array('PharException', '5.2', array(237), '5.3'),
            array('SNMPException', '5.3', array(238), '5.4'),
            array('IntlException', '5.4', array(239), '5.5'),
            array('Error', '5.6', array(214, 240), '7.0'),
            array('ArithmeticError', '5.6', array(209, 241), '7.0'),
            array('AssertionError', '5.6', array(242), '7.0'),
            array('DivisionByZeroError', '5.6', array(203, 243), '7.0'),
            array('ParseError', '5.6', array(244), '7.0'),
            array('TypeError', '5.6', array(245), '7.0'),
            array('ClosedGeneratorException', '5.6', array(341), '7.0'),
            array('UI\Exception\InvalidArgumentException', '5.6', array(192, 210, 246, 322), '7.0'),
            array('UI\Exception\RuntimeException', '5.6', array(188, 199, 247), '7.0'),
            array('ArgumentCountError', '7.0', array(248), '7.1'),
            array('SodiumException', '7.1', array(342), '7.2'),
            array('CompileError', '7.2', array(249), '7.3'),
            array('JsonException', '7.2', array(250, 339), '7.3'),
            array('FFI\Exception', '7.3', array(349), '7.4'),
            array('FFI\ParserException', '7.3', array(349), '7.4'),
        );
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.1'); // TestVersion based on the specific classes being tested.
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return array(
            array(6),
            array(7),
            array(8),
            array(9),
            array(157),
            array(158),
            array(169),
            array(170),
            array(181),
            array(265),
            array(325),
            array(326),
            array(327),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond newest addition.
        $this->assertNoViolation($file);
    }
}
