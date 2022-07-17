<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionDeclarations;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewParamTypeDeclarations sniff.
 *
 * @group newParamTypeDeclarations
 * @group functionDeclarations
 * @group typeDeclarations
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\NewParamTypeDeclarationsSniff
 *
 * @since 7.0.0
 */
class NewParamTypeDeclarationsUnitTest extends BaseSniffTest
{

    /**
     * testNewTypeDeclaration
     *
     * @dataProvider dataNewTypeDeclaration
     *
     * @param string $type              The scalar type.
     * @param string $lastVersionBefore The PHP version just *before* the type hint was introduced.
     * @param array  $line              The line number where the error is expected.
     * @param string $okVersion         A PHP version in which the type hint was ok to be used.
     * @param bool   $testNoViolation   Whether or not to test noViolation.
     *                                  Defaults to true. Only set to false for self/parent outside class scope.
     *
     * @return void
     */
    public function testNewTypeDeclaration($type, $lastVersionBefore, $line, $okVersion, $testNoViolation = true)
    {
        $file = $this->sniffFile(__FILE__, $lastVersionBefore);
        $this->assertError($file, $line, "'{$type}' type declaration is not present in PHP version {$lastVersionBefore} or earlier");

        if ($testNoViolation === true) {
            $file = $this->sniffFile(__FILE__, $okVersion);
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewTypeDeclaration()
     *
     * @return array
     */
    public function dataNewTypeDeclaration()
    {
        return array(
            array('array', '5.0', 4, '5.1'),
            array('array', '5.0', 5, '5.1'),
            array('callable', '5.3', 8, '5.4'),
            array('bool', '5.6', 11, '7.0'),
            array('int', '5.6', 12, '7.0'),
            array('float', '5.6', 13, '7.0'),
            array('string', '5.6', 14, '7.0'),
            array('iterable', '7.0', 17, '7.1'),
            array('parent', '5.1', 24, '5.2'),
            array('self', '5.1', 34, '5.2'),
            array('self', '5.1', 37, '5.2', false),
            array('self', '5.1', 41, '5.2'),
            array('self', '5.1', 44, '5.2', false),
            array('callable', '5.3', 52, '5.4'),
            array('int', '5.6', 53, '7.0'),
            array('callable', '5.3', 56, '5.4'),
            array('int', '5.6', 57, '7.0'),
            array('object', '7.1', 60, '7.2'),
            array('parent', '5.1', 63, '5.2', false),
            array('parent', '5.1', 66, '5.2', false),
        );
    }


    /**
     * testInvalidTypeDeclaration
     *
     * @dataProvider dataInvalidTypeDeclaration
     *
     * @param string $type        The scalar type.
     * @param string $alternative Alternative for the invalid type hint.
     * @param int    $line        Line number on which to expect an error.
     *
     * @return void
     */
    public function testInvalidTypeDeclaration($type, $alternative, $line)
    {
        $file = $this->sniffFile(__FILE__, '5.0'); // Lowest version in which this message will show.
        $this->assertError($file, $line, "'{$type}' is not a valid type declaration. Did you mean {$alternative} ?");
    }

    /**
     * Data provider.
     *
     * @see testInvalidTypeDeclaration()
     *
     * @return array
     */
    public function dataInvalidTypeDeclaration()
    {
        return array(
            array('boolean', 'bool', 20),
            array('integer', 'int', 21),
            array('static', 'self', 25),
        );
    }


    /**
     * testInvalidSelfTypeDeclaration
     *
     * @dataProvider dataInvalidSelfTypeDeclaration
     *
     * @param int    $line Line number on which to expect an error.
     * @param string $type The invalid type which should eb expected.
     *
     * @return void
     */
    public function testInvalidSelfTypeDeclaration($line, $type)
    {
        $file = $this->sniffFile(__FILE__, '5.2'); // Lowest version in which this message will show.
        $this->assertError($file, $line, "'$type' type cannot be used outside of class scope");
    }

    /**
     * Data provider.
     *
     * @see testInvalidSelfTypeDeclaration()
     *
     * @return array
     */
    public function dataInvalidSelfTypeDeclaration()
    {
        return array(
            array(37, 'self'),
            array(44, 'self'),
            array(63, 'parent'),
            array(66, 'parent'),
        );
    }


    /**
     * testTypeDeclaration
     *
     * @dataProvider dataTypeDeclaration
     *
     * @param int  $line            Line number on which to expect an error.
     * @param bool $testNoViolation Whether or not to test noViolation for PHP 5.0.
     *                              This covers the remaining few cases not covered
     *                              by the above tests.
     *
     * @return void
     */
    public function testTypeDeclaration($line, $testNoViolation = false)
    {
        $file = $this->sniffFile(__FILE__, '4.4');
        $this->assertError($file, $line, 'Type declarations were not present in PHP 4.4 or earlier');

        if ($testNoViolation === true) {
            $file = $this->sniffFile(__FILE__, '5.0');
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testTypeDeclaration()
     *
     * @return array
     */
    public function dataTypeDeclaration()
    {
        return array(
            array(4),
            array(5),
            array(8),
            array(11),
            array(12),
            array(13),
            array(14),
            array(17),
            array(20),
            array(21),
            array(24),
            array(25),
            array(29, true),
            array(30, true),
            array(34),
            array(37),
            array(41),
            array(44),
            array(52),
            array(53),
            array(56),
            array(57),
            array(60),
            array(63),
            array(66),
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
        $file = $this->sniffFile(__FILE__, '4.4'); // Low version below the first addition.
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
            array(48),
            array(49),
        );
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw errors
     * for invalid type hints and incorrect usage.
     */
}
