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

namespace tool_ally\logging;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../vendor/autoload.php');

use Exception;

/**
 * Define database logging class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class loggerdb extends loggerbase {

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message - this can be raw text, or if it starts with 'logger:' it will use an ally language string
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = []) {
        global $DB;

        if (!$DB->get_manager()->table_exists('tool_ally_log')) {
            return;
        }

        $message = trim($message);

        $explanation = null;
        $exception = null;

        if (isset($context['_explanation'])) {
            $explanation = $context['_explanation'];
            unset($context['_explanation']);
        }

        if (isset($context['_exception'])) {
            $exception = $context['_exception'];
            unset($context['_exception']);
            if ($exception instanceof Exception) {
                $exmsg = $exception->getMessage();
                $excode = $exception->getCode();
                $exfile = $exception->getFile();
                $exline = $exception->getLine();
                $extrace = $exception->getTraceAsString();
                /** @var Exception $exception */
                $exception = 'Message: '.$exmsg."\n\n";
                $exception .= 'Code: '.$excode."\n\n";
                $exception .= 'File: '.$exfile."\n\n";
                $exception .= 'Line: '.$exline."\n\n";
                $exception .= '====Trace===='."\n\n".$extrace;
            } else if (is_array($exception) || is_object($exception)) {
                $exception = serialize($exception);
            }
        }

        $code = null;
        if (!empty($context['_code'])) {
            $code = $context['_code'];
            unset($context['_code']);
        } else if (strpos($message, 'logger:') === 0) {
            // OK, this is using a language string.
            $code = $message;
            $message = null;
        }

        $record = (object) [
            'time' => time(),
            'level' => $level,
            'code' => $code,
            'message' => $message,
            'explanation' => $explanation,
            'data' => serialize($context),
            'exception' => $exception
        ];
        return $DB->insert_record('tool_ally_log', $record);
    }
}
