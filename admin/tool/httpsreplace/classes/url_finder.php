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
 * url_finder class definition.
 *
 * @package    tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_httpsreplace;

use database_column_info;
use progress_bar;

defined('MOODLE_INTERNAL') || die();

/**
 * Examines DB for non-https src or data links
 *
 * @package tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url_finder {

    /**
     * Returns a hash of what hosts are referred to over http and would need to be changed.
     *
     * @param progress_bar $progress Progress bar keeping track of this process.
     * @return array Hash of domains with number of references as the value.
     */
    public function http_link_stats($progress = null) {
        return $this->process(false, $progress);
    }

    /**
     * Changes all resources referred to over http to https.
     *
     * @param progress_bar $progress Progress bar keeping track of this process.
     * @return bool True upon success
     */
    public function upgrade_http_links($progress = null) {
        return $this->process(true, $progress);
    }

    /**
     * Replace http domains with https equivalent, with two types of exceptions
     * for less straightforward swaps.
     *
     * @param string $table
     * @param database_column_info $column
     * @param string $domain
     * @param string $search search string that has prefix, protocol, domain name and one extra character,
     *      example1: src="http://host.com/
     *      example2: DATA="HTTP://MYDOMAIN.EDU"
     *      example3: src="HTTP://hello.world?
     * @return void
     */
    protected function domain_swap($table, $column, $domain, $search) {
        global $DB;

        $renames = json_decode(get_config('tool_httpsreplace', 'renames'), true);

        if (isset($renames[$domain])) {
            $replace = preg_replace('|http://'.preg_quote($domain).'|i', 'https://' . $renames[$domain], $search);
        } else {
            $replace = preg_replace('|http://|i', 'https://', $search);
        }
        $DB->set_debug(true);
        $DB->replace_all_text($table, $column, $search, $replace);
        $DB->set_debug(false);
    }

    /**
     * Returns SQL to be used to match embedded http links in the given column
     *
     * @param string $columnname name of the column (ready to be used in the SQL query)
     * @return array
     */
    protected function get_select_search_in_column($columnname) {
        global $DB;

        if ($DB->sql_regex_supported()) {
            // Database supports regex, use it for better match.
            $select = $columnname . ' ' . $DB->sql_regex() . ' ?';
            $params = ["(src|data)\ *=\ *[\\\"\']http://"];
        } else {
            // Databases without regex support should use case-insensitive LIKE.
            // This will have false positive matches and more results than we need, we'll have to filter them in php.
            $select = $DB->sql_like($columnname, '?', false);
            $params = ['%=%http://%'];
        }

        return [$select, $params];
    }

    /**
     * Originally forked from core function db_search().
     * @param bool $replacing Whether or not to replace the found urls.
     * @param progress_bar $progress Progress bar keeping track of this process.
     * @return bool|array If $replacing, return true on success. If not, return hash of http urls to number of times used.
     */
    protected function process($replacing = false, $progress = null) {
        global $DB, $CFG;

        require_once($CFG->libdir.'/filelib.php');

        // TODO: block_instances have HTML content as base64, need to decode then
        // search, currently just skipped. See MDL-60024.
        $skiptables = array(
            'block_instances',
            'config',
            'config_log',
            'config_plugins',
            'events_queue',
            'files',
            'filter_config',
            'grade_grades_history',
            'grade_items_history',
            'log',
            'logstore_standard_log',
            'repository_instance_config',
            'sessions',
            'upgrade_log',
            'grade_categories_history',
            '',
        );

        // Turn off time limits.
        \core_php_time_limit::raise();
        if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
            return false;
        }

        $urls = array();
        sort($tables); // Make it easier to see progress because they are ordered.
        $numberoftables = count($tables);
        $tablenumber = 0;
        sort($tables);
        foreach ($tables as $table) {
            if ($progress) {
                $progress->update($tablenumber, $numberoftables, get_string('searching', 'tool_httpsreplace', $table));
                $tablenumber++;
            }
            if (in_array($table, $skiptables)) {
                continue;
            }
            if ($columns = $DB->get_columns($table)) {
                foreach ($columns as $column) {

                    // Only convert columns that are either text or long varchar.
                    if ($column->meta_type == 'X' || ($column->meta_type == 'C' && $column->max_length > 255)) {
                        $columnname = $column->name;
                        $columnnamequoted = $DB->get_manager()->generator->getEncQuoted($columnname);
                        list($select, $params) = $this->get_select_search_in_column($columnnamequoted);
                        $rs = $DB->get_recordset_select($table, $select, $params, '', $columnnamequoted);

                        $found = array();
                        foreach ($rs as $record) {
                            // Regex to match src=http://etc. and data=http://etc.urls.
                            // Standard warning on expecting regex to perfectly parse HTML
                            // read http://stackoverflow.com/a/1732454 for more info.
                            $regex = '#((src|data)\ *=\ *[\'\"])(http://)([^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))[\'\"]#i';
                            preg_match_all($regex, $record->$columnname, $match);
                            foreach ($match[0] as $i => $fullmatch) {
                                if (\core_text::strpos($fullmatch, $CFG->wwwroot) !== false) {
                                    continue;
                                }
                                $prefix = $match[1][$i];
                                $protocol = $match[3][$i];
                                $url = $protocol . $match[4][$i];
                                $host = \core_text::strtolower(parse_url($url, PHP_URL_HOST));
                                if (empty($host)) {
                                    continue;
                                }
                                if ($replacing) {
                                    // For replace string use: prefix, protocol, host and one extra character.
                                    $found[$prefix . \core_text::substr($url, 0, \core_text::strlen($host) + 8)] = $host;
                                } else {
                                    $entry["table"] = $table;
                                    $entry["columnname"] = $columnname;
                                    $entry["url"] = $url;
                                    $entry["host"] = $host;
                                    $entry["raw"] = $record->$columnname;
                                    $entry["ssl"] = '';
                                    $urls[] = $entry;
                                }
                            }
                        }
                        $rs->close();

                        if ($replacing) {
                            foreach ($found as $search => $domain) {
                                $this->domain_swap($table, $column, $domain, $search);
                            }
                        }
                    }
                }
            }
        }

        if ($replacing) {
            rebuild_course_cache(0, true);
            purge_all_caches();
            return true;
        }

        $domains = array_map(function ($i) {
            return $i['host'];
        }, $urls);

        $uniquedomains = array_unique($domains);

        $sslfailures = array();

        foreach ($uniquedomains as $domain) {
            if (!$this->check_domain_availability("https://$domain/")) {
                $sslfailures[] = $domain;
            }
        }

        $results = array();
        foreach ($urls as $url) {
            $host = $url['host'];
            foreach ($sslfailures as $badhost) {
                if ($host == $badhost) {
                    if (!isset($results[$host])) {
                        $results[$host] = 1;
                    } else {
                        $results[$host]++;
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Check if url is available (GET request returns 200)
     *
     * @param string $url
     * @return bool
     */
    protected function check_domain_availability($url) {
        $curl = new \curl();
        $curl->head($url);
        $info = $curl->get_info();
        return !empty($info['http_code']) && $info['http_code'] == 200;
    }
}
