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

namespace search_solr\check;

use core\check\check;
use core\check\result;
use core\output\html_writer;

/**
 * Check that the connection to Solr works.
 *
 * @package search_solr
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connection extends check {
    #[\Override]
    public function get_name(): string {
        return get_string('pluginname', 'search_solr');
    }

    #[\Override]
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/settings.php', ['section' => 'searchsolr']),
            get_string('settings'));
    }


    #[\Override]
    public function get_result(): result {
        global $CFG;

        $result = result::OK;
        $resultstr = '';
        $resultdetails = '';

        try {
            // We do not use manager::instance as this will already try to connect to the engine,
            // we only want to do the specific get_status call below and nothing else. So use
            // search_engine_instance. We know it will be a Solr instance if we got here.
            /** @var \search_solr\engine $engine */
            $engine = \core_search\manager::search_engine_instance();

            // Get engine status.
            $status = $engine->get_status(5);

            $time = number_format($status['time'], 2) . 's';
            $resultstr = get_string('check_time', 'search_solr', $time);
        } catch (\Throwable $t) {
            $status = [
                'connected' => false,
                'foundcore' => false,
                'error' => 'Exception when creating search manager: ' . $t->getMessage(),
                'exception' => $t,
            ];
        }

        if (!$status['connected']) {
            // No connection at all.
            $result = result::ERROR;
            $resultstr = get_string('check_notconnected', 'search_solr');
            $resultdetails .= \html_writer::tag('p', s($status['error']));

        } else if (!$status['foundcore']) {
            // There's a connection, but the core doesn't seem to exist.
            $result = result::ERROR;
            $resultstr = get_string('check_nocore', 'search_solr');
            $resultdetails .= \html_writer::tag('p', s($status['error']));

        } else {
            // Errors related to finding the core size only show if the size warning is configured.
            $sizelimit = get_config('search_solr', 'indexsizelimit');
            if (!array_key_exists('indexsize', $status)) {
                if ($sizelimit) {
                    $result = result::ERROR;
                    $resultstr = get_string('check_nosize', 'search_solr');
                    $resultdetails .= \html_writer::tag('p', s($status['error']));
                }
            } else {
                // Show the index size in result, even if we aren't checking it.
                $sizestr = get_string(
                    'indexsize',
                    'search_solr',
                    display_size($status['indexsize']),
                );
                $resultdetails .= \html_writer::tag('p', $sizestr);
                if ($sizelimit) {
                    // Error at specified index size, warning at 90% of it.
                    $sizewarning = ($sizelimit * 9) / 10;
                    if ($status['indexsize'] > $sizewarning) {
                        if ($status['indexsize'] > $sizelimit) {
                            $resultstr = get_string('check_indextoobig', 'search_solr');
                            $result = result::ERROR;
                        } else {
                            // We don't say it's too big because it isn't yet, just show the size.
                            $resultstr = $sizestr;
                            $result = result::WARNING;
                        }
                    }
                }
            }
        }

        $ex = $status['exception'] ?? null;
        if ($ex) {
            $resultdetails .= \html_writer::tag('pre', str_replace($CFG->dirroot, '', s($ex->getTraceAsString())));
        }

        return new result($result, $resultstr, $resultdetails);
    }
}
