<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Checks that each file contains necessary login checks.
 *
 * @package    local_codechecker
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class RequireLoginSniff implements Sniff {
    public $loginfunctions = ['require_login', 'require_course_login', 'require_admin', 'admin_externalpage_setup'];
    public $ignorewhendefined = ['NO_MOODLE_COOKIES', 'CLI_SCRIPT', 'ABORT_AFTER_CONFIG'];
    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return array(T_OPEN_TAG);
    }

    /**
     * Processes php files and for required login checks if includeing config.php
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
    public function process(File $file, $pointer) {
        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $pointer - 1);
        if ($prevopentag !== false) {
            return; // @codeCoverageIgnore
        }

        $pointer = $this->get_config_inclusion_position($file, $pointer);
        if ($pointer === false) {
            // Config.php not included form file.
            return;
        }

        // OK, we've got a config.php.
        if ($this->should_skip_login_checks($file, $pointer)) {
            // Login function check not necessary.
            return;
        }

        if (!$this->is_login_function_present($file, $pointer)) {
            $loginfunctionsstr = implode(', ', $this->loginfunctions);
            $file->addWarning("Expected login check ($loginfunctionsstr) following config inclusion. None found.",
                $pointer, 'Missing');
        }
    }

    /**
     * Returns the position of a config.php require statement in the stack.
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return int|false the position in the file or false if no require statement found.
     */
    protected function get_config_inclusion_position(File $file, $pointer) {
        for ($i = $pointer; $i < $file->numTokens; $i++) {
            $i = $file->findNext([T_REQUIRE, T_REQUIRE_ONCE], $i);
            if ($i === false) {
                // No require statement in file.
                return false;
            }

            if ($this->is_a_config_php_incluson($file, $i)) {
                // Found config.php.
                return $i;
            }
        }
        return false;
    }

    /**
     * Is the current position a config.php inclusion?
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if it is a config inclusion
     */
    protected function is_a_config_php_incluson(File $file, $pointer) {
        $tokens = $file->getTokens();

        // It's a require() or require_once() statement. Is it require(config.php)?
        $requirecontent = $file->getTokensAsString($pointer, ($file->findEndOfStatement($pointer) - $pointer));
        if (strpos($requirecontent, '/config.php') === false) {
            return false;
        }

        return true;
    }

    /**
     * Should we skip the login checks? We look back up the stack to see if there are any
     * define statements which cause us to skip the checks (e.g. CLI_SCRIPT)
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if the checks should be skipped
     */
    protected function should_skip_login_checks(File $file, $pointer) {
        $tokens = $file->getTokens();

        for ($i = $pointer; $i > 0; $i--) {

            $i = $file->findPrevious(T_STRING, $i);
            if ($i === false) {
                // We've got to the start. A login function must be necessary.
                return false;
            }

            if (strtolower($tokens[$i]['content']) !== 'define') {
                // Not a define statement, let move onto the next string.
                continue;
            }

            // TODO, do this the non-lazy more strict way?
            $definecontent = $file->getTokensAsString($i, ($file->findEndOfStatement($i) - $i));
            foreach ($this->ignorewhendefined as $name) {
                if (strpos($definecontent, $name) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Is the current position a login function?
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if the current point in stack is a login function.
     */
    protected function is_a_login_function(File $file, $pointer) {
        $tokens = $file->getTokens();

        if (in_array($tokens[$pointer]['content'], $this->loginfunctions)) {
            return true;
        }

        return false;
    }

    /**
     * Is there a login function present in the following code?
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return true if login function is present.
     */
    protected function is_login_function_present(File $file, $pointer) {
        for ($i = $pointer; $i < $file->numTokens; $i++) {
            $i = $file->findNext(T_STRING, $i);
            if ($i === false) {
                return false;
            }

            if ($this->is_a_login_function($file, $i)) {
                return true;
            }

            // FIXME: jumping to next semi colon to reduce the tokens checked. Perhaps a better way?
            $i = $file->findNext(T_SEMICOLON, $i);
            if ($i === false) {
                return false;
            }
        }

    }
}
