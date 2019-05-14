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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class used to restore logs, converting all the information as needed
 *
 * This class allows each restore task to specify which logs (by action) will
 * be handled on restore and which transformations will be performed in order
 * to accomodate them into their new destination
 *
 * TODO: Complete phpdocs
 */
class restore_log_rule implements processable {

    protected $module;   // module of the log record
    protected $action;   // action of the log record

    protected $urlread;   // url format of the log record in backup file
    protected $inforead;  // info format of the log record in backup file

    protected $modulewrite;// module of the log record to be written (defaults to $module if not specified)
    protected $actionwrite;// action of the log record to be written (defaults to $action if not specified)

    protected $urlwrite; // url format of the log record to be written (defaults to $urlread if not specified)
    protected $infowrite;// info format of the log record to be written (defaults to $inforead if not specified)

    protected $urlreadregexp; // Regexps for extracting information from url and info
    protected $inforeadregexp;

    protected $allpairs; // to acummulate all tokens and values pairs on each log record restored

    protected $urltokens; // tokens present int the $urlread attribute
    protected $infotokens;// tokens present in the $inforead attribute

    protected $fixedvalues;    // Some values that will have precedence over mappings to save tons of DB mappings

    protected $restoreid;

    public function __construct($module, $action, $urlread, $inforead,
                                $modulewrite = null, $actionwrite = null, $urlwrite = null, $infowrite = null) {
        $this->module    = $module;
        $this->action    = $action;
        $this->urlread   = $urlread;
        $this->inforead  = $inforead;
        $this->modulewrite = is_null($modulewrite) ? $module : $modulewrite;
        $this->actionwrite= is_null($actionwrite) ? $action : $actionwrite;
        $this->urlwrite = is_null($urlwrite) ? $urlread : $urlwrite;
        $this->infowrite= is_null($infowrite) ? $inforead : $infowrite;
        $this->allpairs = array();
        $this->urltokens = array();
        $this->infotokens= array();
        $this->urlreadregexp = null;
        $this->inforeadregexp = null;
        $this->fixedvalues = array();
        $this->restoreid = null;

        // TODO: validate module, action are valid => exception

        // Calculate regexps and tokens, both for urlread and inforead
        $this->calculate_url_regexp($this->urlread);
        $this->calculate_info_regexp($this->inforead);
    }

    public function set_restoreid($restoreid) {
        $this->restoreid = $restoreid;
    }

    public function set_fixed_values($values) {
        //TODO: check $values is array => exception
        $this->fixedvalues = $values;
    }

    public function get_key_name() {
        return $this->module . '-' . $this->action;
    }

    public function process($inputlog) {

        // There might be multiple rules that process this log, we can't alter it in the process of checking it.
        $log = clone($inputlog);

        // Reset the allpairs array
        $this->allpairs = array();

        $urlmatches  = array();
        $infomatches = array();
        // Apply urlreadregexp to the $log->url if necessary
        if ($this->urlreadregexp) {
            preg_match($this->urlreadregexp, $log->url, $urlmatches);
            if (empty($urlmatches)) {
                return false;
            }
        } else {
            if (!is_null($this->urlread)) { // If not null, use it (null means unmodified)
                $log->url = $this->urlread;
            }
        }
        // Apply inforeadregexp to the $log->info if necessary
        if ($this->inforeadregexp) {
            preg_match($this->inforeadregexp, $log->info, $infomatches);
            if (empty($infomatches)) {
                return false;
            }
        } else {
            if (!is_null($this->inforead)) { // If not null, use it (null means unmodified)
                $log->info = $this->inforead;
            }
        }

        // If there are $urlmatches, let's process them
        if (!empty($urlmatches)) {
            array_shift($urlmatches); // Take out first element
            if (count($urlmatches) !== count($this->urltokens)) { // Number of matches must be number of tokens
                return false;
            }
            // Let's process all the tokens and matches, using them to parse the urlwrite
            $log->url = $this->parse_tokens_and_matches($this->urltokens, $urlmatches, $this->urlwrite);
        }

        // If there are $infomatches, let's process them
        if (!empty($infomatches)) {
            array_shift($infomatches); // Take out first element
            if (count($infomatches) !== count($this->infotokens)) { // Number of matches must be number of tokens
                return false;
            }
            // Let's process all the tokens and matches, using them to parse the infowrite
            $log->info = $this->parse_tokens_and_matches($this->infotokens, $infomatches, $this->infowrite);
        }

        // Arrived here, if there is any pending token in $log->url or $log->info, stop
        if ($this->extract_tokens($log->url) || $this->extract_tokens($log->info)) {
            return false;
        }

        // Finally, set module and action
        $log->module = $this->modulewrite;
        $log->action = $this->actionwrite;

        return $log;
    }

// Protected API starts here

    protected function parse_tokens_and_matches($tokens, $values, $content) {

        $pairs = array_combine($tokens, $values);
        ksort($pairs); // First literals, then mappings
        foreach ($pairs as $token => $value) {
            // If one token has already been processed, continue
            if (array_key_exists($token, $this->allpairs)) {
                continue;
            }

            // If the pair is one literal token, just keep it unmodified
            if (substr($token, 0, 1) == '[') {
                $this->allpairs[$token] = $value;

            // If the pair is one mapping token, let's process it
            } else if (substr($token, 0, 1) == '{') {
                $ctoken = $token;

                // First, resolve mappings to literals if necessary
                if (substr($token, 1, 1) == '[') {
                    $literaltoken = trim($token, '{}');
                    if (array_key_exists($literaltoken, $this->allpairs)) {
                        $ctoken = '{' . $this->allpairs[$literaltoken] . '}';
                    }
                }

                // Look for mapping in fixedvalues before going to DB
                $plaintoken = trim($ctoken, '{}');
                if (array_key_exists($plaintoken, $this->fixedvalues)) {
                    $this->allpairs[$token] = $this->fixedvalues[$plaintoken];

                 // Last chance, fetch value from backup_ids_temp, via mapping
                } else {
                    if ($mapping = restore_dbops::get_backup_ids_record($this->restoreid, $plaintoken, $value)) {
                        $this->allpairs[$token] = $mapping->newitemid;
                    }
                }
            }
        }

        // Apply all the conversions array (allpairs) to content
        krsort($this->allpairs); // First mappings, then literals
        $content = str_replace(array_keys($this->allpairs), $this->allpairs, $content);

        return $content;
    }

    protected function calculate_url_regexp($urlexpression) {
        // Detect all the tokens in the expression
        if ($tokens = $this->extract_tokens($urlexpression)) {
            $this->urltokens = $tokens;
            // Now, build the regexp
            $this->urlreadregexp = $this->build_regexp($urlexpression, $this->urltokens);
        }
    }

    protected function calculate_info_regexp($infoexpression) {
        // Detect all the tokens in the expression
        if ($tokens = $this->extract_tokens($infoexpression)) {
            $this->infotokens = $tokens;
            // Now, build the regexp
            $this->inforeadregexp = $this->build_regexp($infoexpression, $this->infotokens);
        }
    }

    protected function extract_tokens($expression) {
        // Extract all the tokens enclosed in square and curly brackets
        preg_match_all('~\[[^\]]+\]|\{[^\}]+\}~', $expression, $matches);
        return $matches[0];
    }

    protected function build_regexp($expression, $tokens) {
        // Replace to temp (and preg_quote() safe) placeholders
        foreach ($tokens as $token) {
            $expression = preg_replace('~' . preg_quote($token, '~') . '~', '%@@%@@%', $expression, 1);
        }
        // quote the expression
        $expression = preg_quote($expression, '~');
        // Replace all the placeholders
        $expression = preg_replace('~%@@%@@%~', '(.*)', $expression);
        return '~' . $expression . '~';
    }
}
