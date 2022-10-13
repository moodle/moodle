<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2019 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\Upgrade;

use PHPCompatibility\Sniff;
use PHPCompatibility\PHPCSHelper;
use PHP_CodeSniffer_File as File;

/**
 * Add a notification for users of low PHP versions.
 *
 * Originally PHPCompatibility supported PHP 5.1 and higher.
 * As of PHPCompatibility 8.0.0, support for PHP < 5.3 has been dropped.
 *
 * The intention is to drop support for PHP 5.3 in the (near) future.
 *
 * This sniff adds an explicit error/warning for users of the standard
 * using a PHP version below the recommended version.
 *
 * @link https://github.com/PHPCompatibility/PHPCompatibility/issues/835
 *
 * @since 9.3.0
 */
class LowPHPSniff extends Sniff
{
    /**
     * The minimum supported PHP version.
     *
     * Users on PHP versions below this will see an ERROR message.
     *
     * @since 9.3.0
     *
     * @var string
     */
    const MIN_SUPPORTED_VERSION = '5.3';

    /**
     * The minimum recommended PHP version.
     *
     * Users on PHP versions below this will see a WARNING.
     *
     * @since 9.3.0
     *
     * @var string
     */
    const MIN_RECOMMENDED_VERSION = '5.4';

    /**
     * Keep track of whether this sniff needs to actually run.
     *
     * This will be set to `false` when either a high enough PHP
     * version is detected or once the error/warning has been thrown,
     * to make sure that the notice will only be thrown once per run.
     *
     * @since 9.3.0
     *
     * @var bool
     */
    private $examine = true;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 9.3.0
     *
     * @return array
     */
    public function register()
    {
        return array(
            \T_OPEN_TAG,
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @since 9.3.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Don't do anything if the warning has already been thrown or is not necessary.
        if ($this->examine === false) {
            return ($phpcsFile->numTokens + 1);
        }

        $phpVersion = phpversion();

        // Don't do anything if the PHPCS version used is above the minimum recommended version.
        if (version_compare($phpVersion, self::MIN_RECOMMENDED_VERSION, '>=')) {
            $this->examine = false;
            return ($phpcsFile->numTokens + 1);
        }

        if (version_compare($phpVersion, self::MIN_SUPPORTED_VERSION, '<')) {
            $isError      = true;
            $message      = 'IMPORTANT: Please be advised that the minimum PHP version the PHPCompatibility standard supports is %s. You are currently using PHP %s. Please upgrade your PHP installation. The recommended version of PHP for PHPCompatibility is %s or higher.';
            $errorCode    = 'Unsupported_' . $this->stringToErrorCode(self::MIN_SUPPORTED_VERSION);
            $replacements = array(
                self::MIN_SUPPORTED_VERSION,
                $phpVersion,
                self::MIN_RECOMMENDED_VERSION,
                $errorCode,
            );
        } else {
            $isError      = false;
            $message      = 'IMPORTANT: Please be advised that for the most reliable PHPCompatibility results, PHP %s or higher should be used. Support for lower versions will be dropped in the foreseeable future. You are currently using PHP %s. Please upgrade your PHP installation to version %s or higher.';
            $errorCode    = 'BelowRecommended_' . $this->stringToErrorCode(self::MIN_RECOMMENDED_VERSION);
            $replacements = array(
                self::MIN_RECOMMENDED_VERSION,
                $phpVersion,
                self::MIN_RECOMMENDED_VERSION,
                $errorCode,
            );
        }

        /*
         * Figure out the report width to determine how long the delimiter lines should be.
         *
         * This is not an exact calculation as there are a number of unknowns at the time the
         * notice is thrown (whether there are other notices for the file, whether those are
         * warnings or errors, whether there are auto-fixable issues etc).
         *
         * In other words, this is just an approximation to get a reasonably stable and
         * readable message layout format.
         *
         * {@internal
         * PHPCS has had some changes as to how the messages display over the years.
         * Most significantly in 2.4.0 it was attempted to solve an issue with messages
         * containing new lines. Unfortunately, that solution is buggy.
         * An improved version has been pulled upstream and will hopefully make it
         * into PHPCS 3.3.1/3.4.0.
         *
         * Anyway, this means that instead of new lines, delimiter lines will be used to improved
         * the readability of the (long) message.
         *
         * Also, as of PHPCS 2.2.0, the report width when using the `-s` option is 8 wider than
         * it should be. A patch for that is included in the same upstream PR.
         *
         * If/when the upstream PR has been merged and the minimum supported/recommended version
         * of PHPCompatibility would go beyond that, the below code should be adjusted.}
         */
        $reportWidth = PHPCSHelper::getCommandLineData($phpcsFile, 'reportWidth');
        if (empty($reportWidth)) {
            $reportWidth = 80;
        }
        $showSources = PHPCSHelper::getCommandLineData($phpcsFile, 'showSources');
        if ($showSources === true) {
            $reportWidth += 6;
        }

        $messageWidth  = ($reportWidth - 15); // 15 is length of " # | WARNING | ".
        $delimiterLine = str_repeat('-', ($messageWidth));
        $disableNotice = 'To disable this notice, add --exclude=PHPCompatibility.Upgrade.LowPHP to your command or add <exclude name="PHPCompatibility.Upgrade.LowPHP.%s"/> to your custom ruleset. ';
        $thankYou      = 'Thank you for using PHPCompatibility!';

        $message .= ' ' . $delimiterLine;
        $message .= ' ' . $disableNotice;
        $message .= ' ' . $delimiterLine;
        $message .= ' ' . $thankYou;

        $this->addMessage($phpcsFile, $message, 0, $isError, $errorCode, $replacements);

        $this->examine = false;
    }
}
