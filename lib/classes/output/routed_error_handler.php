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

namespace core\output;

use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

// phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase

/**
 * Class routed_error_handler
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class routed_error_handler implements ErrorRendererInterface {
    #[\Override]
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string {
        // @codeCoverageIgnoreStart
        if (defined('ABORT_AFTER_CONFIG') && !defined('ABORT_AFTER_CONFIG_CANCEL')) {
            define('ABORT_AFTER_CONFIG_CANCEL', true);
            require(__DIR__ . '/../../setup.php');
        }
        // @codeCoverageIgnoreEnd

        if ($whoops = get_whoops()) {
            $whoops->sendHttpCode($exception->getCode());
            $whoops->handleException($exception);
        } else {
            default_exception_handler($exception);
        }

        return '';
    }
}
