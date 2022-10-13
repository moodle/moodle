<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Miscellaneous;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the RemovedAlternativePHPTags sniff.
 *
 * @group removedAlternativePHPTags
 * @group miscellaneous
 *
 * @covers \PHPCompatibility\Sniffs\Miscellaneous\RemovedAlternativePHPTagsSniff
 *
 * @since 7.0.4
 */
class RemovedAlternativePHPTagsUnitTest extends BaseSniffTest
{

    /**
     * Whether or not ASP tags are on.
     *
     * @var bool
     */
    protected static $aspTags = false;


    /**
     * Set up skip condition.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        if (version_compare(\PHP_VERSION_ID, '70000', '<')) {
            // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.asp_tagsRemoved
            self::$aspTags = (bool) ini_get('asp_tags');
        }

        parent::setUpBeforeClass();
    }


    /**
     * testAlternativePHPTags
     *
     * @dataProvider dataAlternativePHPTags
     *
     * @param string $type    The type of opening tags, either ASP or Script.
     * @param string $snippet The text string found.
     * @param int    $line    The line number.
     *
     * @return void
     */
    public function testAlternativePHPTags($type, $snippet, $line)
    {
        if ($type === 'ASP' && self::$aspTags === false) {
            $this->markTestSkipped('ASP tags are unavailable (PHP 7+) or disabled.');
            return;
        }

        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, "{$type} style opening tags have been removed in PHP 7.0. Found \"{$snippet}\"");
    }

    /**
     * Data provider.
     *
     * @see testAlternativePHPTags()
     *
     * @return array
     */
    public function dataAlternativePHPTags()
    {
        return array(
            array('Script', '<script language="php">', 7),
            array('Script', "<script language='php'>", 10),
            array('Script', '<script type="text/php" language="php">', 13),
            array('Script', "<script language='PHP' type='text/php'>", 16),
            array('ASP', '<%', 21),
            array('ASP', '<%', 22),
            array('ASP', '<%=', 23),
            array('ASP', '<%=', 24),
        );
    }


    /**
     * testMaybeASPOpenTag
     *
     * @dataProvider dataMaybeASPOpenTag
     *
     * @param int    $line    The line number.
     * @param string $snippet Part of the text string found.
     *
     * @return void
     */
    public function testMaybeASPOpenTag($line, $snippet)
    {
        if (self::$aspTags === true) {
            $this->markTestSkipped('ASP tags are unavailable (PHP 7+) or disabled.');
            return;
        }

        $file    = $this->sniffFile(__FILE__, '7.0');
        $warning = "Possible use of ASP style opening tags detected. ASP style opening tags have been removed in PHP 7.0. Found: {$snippet}";
        $this->assertWarning($file, $line, $warning);
    }

    /**
     * Data provider.
     *
     * @see testMaybeASPOpenTag()
     *
     * @return array
     */
    public function dataMaybeASPOpenTag()
    {
        return array(
            array(21, '<% echo $var; %>'),
            array(22, '<% echo $var; %> and some m...'),
            array(23, '<%= $var . \' and some more ...'),
            array(24, '<%= $var %> and some more t...'),
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
        $file = $this->sniffFile(__FILE__, '7.0');
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
            array(3),
        );
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.6');
        $this->assertNoViolation($file);
    }
}
