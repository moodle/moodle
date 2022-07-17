<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Extensions;

use PHPCompatibility\Tests\BaseSniffTest;
use PHPCompatibility\PHPCSHelper;

/**
 * Test the RemovedExtensions sniff.
 *
 * @group removedExtensions
 * @group extensions
 *
 * @covers \PHPCompatibility\Sniffs\Extensions\RemovedExtensionsSniff
 *
 * @since 5.5
 */
class RemovedExtensionsUnitTest extends BaseSniffTest
{

    /**
     * testRemovedExtension
     *
     * @dataProvider dataRemovedExtension
     *
     * @param string $extensionName  Name of the PHP extension.
     * @param string $removedIn      The PHP version in which the extension was removed.
     * @param array  $lines          The line numbers in the test file which apply to this extension.
     * @param string $okVersion      A PHP version in which the extension was still present.
     * @param string $removedVersion Optional PHP version to test removal message with -
     *                               if different from the $removedIn version.
     *
     * @return void
     */
    public function testRemovedExtension($extensionName, $removedIn, $lines, $okVersion, $removedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($removedVersion)) ? $removedVersion : $removedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "Extension '{$extensionName}' is removed since PHP {$removedIn}";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testRemovedExtension()
     *
     * @return array
     */
    public function dataRemovedExtension()
    {
        return array(
            array('dbase', '5.3', array(10), '5.2'),
            array('fam', '5.1', array(16), '5.0'),
            array('fbsql', '5.3', array(18), '5.2'),
            array('filepro', '5.2', array(22), '5.1'),
            array('hw_api', '5.2', array(24), '5.1'),
            array('ircg', '5.1', array(28), '5.0'),
            array('mnogosearch', '5.1', array(34), '5.0'),
            array('msql', '5.3', array(36), '5.2'),
            array('mssql', '7.0', array(63), '5.6'),
            array('ovrimos', '5.1', array(44), '5.0'),
            array('pfpro_', '5.1', array(46), '5.0'),
            array('sqlite', '5.4', array(48), '5.3'),
            // array('sybase', '7.0', array(xx), '5.6'), sybase_ct ???
            array('yp', '5.1', array(54), '5.0'),
        );
    }

    /**
     * testRemovedExtensionWithAlternative
     *
     * @dataProvider dataRemovedExtensionWithAlternative
     *
     * @param string $extensionName  Name of the PHP extension.
     * @param string $removedIn      The PHP version in which the extension was removed.
     * @param string $alternative    An alternative extension.
     * @param array  $lines          The line numbers in the test file which apply to this extension.
     * @param string $okVersion      A PHP version in which the extension was still present.
     * @param string $removedVersion Optional PHP version to test removal message with -
     *                               if different from the $removedIn version.
     *
     * @return void
     */
    public function testRemovedExtensionWithAlternative($extensionName, $removedIn, $alternative, $lines, $okVersion, $removedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($removedVersion)) ? $removedVersion : $removedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "Extension '{$extensionName}' is removed since PHP {$removedIn}; Use {$alternative} instead";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testRemovedExtensionWithAlternative()
     *
     * @return array
     */
    public function dataRemovedExtensionWithAlternative()
    {
        return array(
            array('activescript', '5.1', 'pecl/activescript', array(3, 4), '5.0'),
            array('cpdf', '5.1', 'pecl/pdflib', array(6, 7, 8), '5.0'),
            array('dbx', '5.1', 'pecl/dbx', array(12), '5.0'),
            array('dio', '5.1', 'pecl/dio', array(14), '5.0'),
            array('fdf', '5.3', 'pecl/fdf', array(20), '5.2'),
            array('ibase', '7.4', 'pecl/ibase', array(78), '7.3'),
            array('ingres', '5.1', 'pecl/ingres', array(26), '5.0'),
            array('mcve', '5.1', 'pecl/mcve', array(30), '5.0'),
            array('ming', '5.3', 'pecl/ming', array(32), '5.2'),
            array('ncurses', '5.3', 'pecl/ncurses', array(40), '5.2'),
            array('oracle', '5.1', 'oci8 or pdo_oci', array(42), '5.0'),
            array('recode', '7.4', 'iconv or mbstring', array(80), '7.3'),
            array('sybase', '5.3', 'sybase_ct', array(50), '5.2'),
            array('w32api', '5.1', 'pecl/ffi', array(52), '5.0'),
            array('wddx', '7.4', 'pecl/wddx', array(79), '7.3'),
        );
    }


    /**
     * testDeprecatedRemovedExtensionWithAlternative
     *
     * @dataProvider dataDeprecatedRemovedExtensionWithAlternative
     *
     * @param string $extensionName     Name of the PHP extension.
     * @param string $deprecatedIn      The PHP version in which the extension was deprecated.
     * @param string $removedIn         The PHP version in which the extension was removed.
     * @param string $alternative       An alternative extension.
     * @param array  $lines             The line numbers in the test file which apply to this extension.
     * @param string $okVersion         A PHP version in which the extension was still present.
     * @param string $deprecatedVersion Optional PHP version to test deprecation message with -
     *                                  if different from the $deprecatedIn version.
     * @param string $removedVersion    Optional PHP version to test removal message with -
     *                                  if different from the $removedIn version.
     *
     * @return void
     */
    public function testDeprecatedRemovedExtensionWithAlternative($extensionName, $deprecatedIn, $removedIn, $alternative, $lines, $okVersion, $deprecatedVersion = null, $removedVersion = null)
    {
        $file = $this->sniffFile(__FILE__, $okVersion);
        foreach ($lines as $line) {
            $this->assertNoViolation($file, $line);
        }

        $errorVersion = (isset($deprecatedVersion)) ? $deprecatedVersion : $deprecatedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "Extension '{$extensionName}' is deprecated since PHP {$deprecatedIn}; Use {$alternative} instead";
        foreach ($lines as $line) {
            $this->assertWarning($file, $line, $error);
        }

        $errorVersion = (isset($removedVersion)) ? $removedVersion : $removedIn;
        $file         = $this->sniffFile(__FILE__, $errorVersion);
        $error        = "Extension '{$extensionName}' is deprecated since PHP {$deprecatedIn} and removed since PHP {$removedIn}; Use {$alternative} instead";
        foreach ($lines as $line) {
            $this->assertError($file, $line, $error);
        }
    }

    /**
     * Data provider.
     *
     * @see testDeprecatedRemovedExtensionWithAlternative()
     *
     * @return array
     */
    public function dataDeprecatedRemovedExtensionWithAlternative()
    {
        return array(
            array('ereg', '5.3', '7.0', 'pcre', array(65, 76), '5.2'),
            array('mysql_', '5.5', '7.0', 'mysqli', array(38), '5.4'),
            array('mcrypt', '7.1', '7.2', 'openssl (preferred) or pecl/mcrypt once available', array(71), '7.0'),
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
        $file = $this->sniffFile(__FILE__, '99.0'); // High version beyond latest deprecation.
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
        $data = array(
            array(57), // Not a function call.
            array(58), // Function declaration.
            array(59), // Class instantiation.
            array(60), // Method call.
            array(82), // Live coding.
        );

        // Inline setting changes in combination with namespaced sniffs is only supported since PHPCS 2.6.0.
        if (version_compare(PHPCSHelper::getVersion(), '2.6.0', '>=')) {
            $data[] = array(68); // Whitelisted function.
            $data[] = array(74); // Whitelisted function array.
            $data[] = array(75); // Whitelisted function array.
        }

        return $data;
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.0'); // Low version below the first deprecation.
        $this->assertNoViolation($file);
    }
}
