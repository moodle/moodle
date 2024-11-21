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
 * Leaderboard table.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

use block_xp\local\config\course_world_config;
use block_xp\local\leaderboard\leaderboard;
use block_xp\local\routing\url;
use block_xp\local\sql\limit;
use block_xp\local\xp\state_with_subject;
use flexible_table;
use html_writer;
use paging_bar;
use renderer_base;

/**
 * Leaderboard table.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class leaderboard_table extends flexible_table {

    /** @var \context The context. */
    protected $context;
    /** @var bool Whether the user can view profiles. */
    protected $canviewprofiles = false;

    /** @var leaderboard The leaderboard. */
    protected $leaderboard;

    /** @var block_xp_renderer XP Renderer. */
    protected $xpoutput = null;

    /** @var int The user ID we're viewing the ladder for. */
    protected $userid;

    /** @var int The identity mode. */
    protected $identitymode = course_world_config::IDENTITY_ON;

    /** @var int The rank mode. */
    protected $rankmode = course_world_config::RANK_ON;

    /** @var bool Whether to show the pagesize selector. */
    protected $showpagesizeselector = false;

    /** @var limit The fence. */
    protected $fence;

    /**
     * Constructor.
     *
     * @param leaderboard $leaderboard The leaderboard.
     * @param renderer_base $renderer The renderer.
     * @param array $options Options.
     * @param int $userid The user viewing this.
     */
    public function __construct(
            leaderboard $leaderboard,
            renderer_base $renderer,
            array $options,
            $userid
        ) {

        global $CFG, $PAGE, $USER;
        parent::__construct('block_xp_ladder');

        // The user ID we're viewing the ladder for.
        $this->userid = $userid;

        // Block XP stuff.
        $this->leaderboard = $leaderboard;
        $this->xpoutput = $renderer;

        // Check options.
        if (isset($options['context'])) {
            $this->context = $options['context'];
        } else {
            $this->context = $PAGE->context;
        }
        if (isset($options['rankmode'])) {
            $this->rankmode = $options['rankmode'];
        }
        if (isset($options['identitymode'])) {
            $this->identitymode = $options['identitymode'];
        }
        if (isset($options['fence'])) {
            $this->fence = $options['fence'];
        }
        $leaderboardcols = $this->leaderboard->get_columns();
        if (isset($options['discardcolumns'])) {
            $leaderboardcols = array_diff_key($leaderboardcols, array_flip($options['discardcolumns']));
        }

        $this->canviewprofiles = has_capability('moodle/user:viewdetails', $this->context);

        // Define columns, and headers.
        $columns = array_keys($leaderboardcols);
        $headers = array_map(function($header) {
            return (string) $header;
        }, array_values($leaderboardcols));
        $this->define_columns($columns);
        $this->define_headers($headers);

        // Define various table settings.
        $this->sortable(false);
        $this->collapsible(false);
        $this->set_attribute('class', 'block_xp-table');
        $this->column_class('rank', 'col-rank');
        $this->column_class('level', 'col-lvl');
        $this->column_class('userpic', 'col-userpic');
    }

    /**
     * Output the table.
     *
     * @param int $pagesize The page size.
     */
    public function out($pagesize) {
        $this->setup();

        if ($this->is_downloading()) {
            $limit = new limit(0);

        } else if (empty($this->fence)) {
            // Compute where to start from.
            $requestedpage = optional_param($this->request[TABLE_VAR_PAGE], null, PARAM_INT);
            if ($requestedpage === null) {
                $mypos = $this->leaderboard->get_position($this->userid);
                if ($mypos !== null) {
                    $this->currpage = floor($mypos / $pagesize);
                }
            }
            $this->pagesize($pagesize, $this->leaderboard->get_count());
            $limit = new limit($pagesize, (int) $this->get_page_start());

        } else {
            $this->pagesize($this->fence->get_count(), $this->fence->get_count());
            $limit = $this->fence;
        }

        $ranking = $this->leaderboard->get_ranking($limit);
        foreach ($ranking as $rank) {
            $classes = ($this->userid == $rank->get_state()->get_id()) ? 'highlight-row' : '';
            $this->add_data_keyed($this->rank_to_keyed_data($rank), $classes);
        }
        $this->finish_output();
    }

    /**
     * Formats the column fullname.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_fullname($row) {
        $o = $this->col_userpic($row);

        $link = null;
        $name = null;
        $state = $row->state;
        if ($state instanceof state_with_subject) {
            $link = $state->get_link();
            $name = $state->get_name();
        }

        $name = empty($name) ? '?' : $name;
        if ($link && $this->canviewprofiles) {
            $o .= html_writer::link($link->out(false), $name);
        } else {
            $o .= $name;
        }

        return $o;
    }

    /**
     * Formats the level.
     *
     * @param stdClass $row Table row.
     * @return string
     */
    public function col_lvl($row) {
        return $this->xpoutput->small_level_badge($row->state->get_level());
    }

    /**
     * Formats the column progress.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_progress($row) {
        return $this->xpoutput->progress_bar($row->state);
    }

    /**
     * Formats the rank column.
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_rank($row) {
        if ($this->rankmode == course_world_config::RANK_REL) {
            $symbol = '';
            if ($row->rank > 0) {
                $symbol = '+';
            }
            // We want + when it's positive, and - when it's negative, else nothing.
            return $symbol . $this->xpoutput->xp($row->rank);
        }
        return $row->rank;
    }

    /**
     * Formats the rank column.
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_xp($row) {
        return $this->xpoutput->xp($row->state->get_xp());
    }

    /**
     * Formats the column userpic.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    public function col_userpic($row) {
        $picture = null;
        $link = null;
        $state = $row->state;
        if ($state instanceof state_with_subject) {
            $picture = $state->get_picture();
            $link = $this->canviewprofiles ? $state->get_link() : null;
        }
        return $this->xpoutput->user_avatar($picture, $link);
    }

    /**
     * Start HTML.
     *
     * Complete override to suppress some features.
     *
     * @return void
     */
    public function start_html() {
        if (in_array(TABLE_P_TOP, $this->showdownloadbuttonsat)) {
            echo $this->download_buttons();
        }

        $this->wrap_html_start();
        echo html_writer::start_tag('div', ['class' => 'no-overflow']);
        echo html_writer::start_tag('table', $this->attributes);
    }

    /**
     * Finish HTML.
     *
     * @return void
     */
    public function finish_html() {
        if (!$this->started_output) {
            $this->print_nothing_to_display();

        } else {

            // Copied from parent method.
            $emptyrow = array_fill(0, count($this->columns), '');
            while ($this->currentrow < $this->pagesize) {
                $this->print_row($emptyrow, 'emptyrow');
            }
            echo html_writer::end_tag('tbody');
            echo html_writer::end_tag('table');
            echo html_writer::end_tag('div');
            $this->wrap_html_finish();

            // Paging bar.
            if (in_array(TABLE_P_BOTTOM, $this->showdownloadbuttonsat)) {
                echo $this->download_buttons();
            }
            // End copy from parent method.

            $url20 = new url($this->baseurl);
            $url50 = new url($this->baseurl);
            $url100 = new url($this->baseurl);
            $url20->param('pagesize', 20);
            $url50->param('pagesize', 50);
            $url100->param('pagesize', 100);

            if ($this->use_pages) {

                // If there are more rows than the minimum selector, and enabled.
                if ($this->showpagesizeselector && $this->totalrows > 20) {
                    echo $this->xpoutput->pagesize_selector([
                        [20, $url20],
                        [50, $url50],
                        [100, $url100],
                    ], $this->pagesize);
                }

                // Paging bar as per parent method.
                $pagingbar = new paging_bar($this->totalrows, $this->currpage, $this->pagesize, $this->baseurl);
                $pagingbar->pagevar = $this->request[TABLE_VAR_PAGE];
                echo $this->xpoutput->render($pagingbar);
            }
        }
    }

    /**
     * Override to rephrase.
     *
     * @return void
     */
    public function print_nothing_to_display() {
        echo \html_writer::div(
            \block_xp\di::get('renderer')->notification_without_close(
                get_string('ladderempty', 'block_xp'),
                'info'
            ),
            '',
            ['style' => 'margin: 1em 0']
        );
    }

    /**
     * Convert a rank to keyed table data.
     *
     * @param rank $rank The rank object.
     * @return array Will be passed to {@see self::add_data_keyed}.
     */
    protected function rank_to_keyed_data($rank) {
        $row = (object) [
            'rank' => $rank->get_rank(),
            'state' => $rank->get_state(),
        ];
        return [
            'fullname' => $this->col_fullname($row),
            'level' => $this->col_lvl($row),
            'progress' => $this->col_progress($row),
            'rank' => $this->col_rank($row),
            'xp' => $this->col_xp($row),
            'userpic' => $this->col_userpic($row),
        ];
    }

    /**
     * Whether to display the pagesize selector.
     *
     * This only has effect when pagination is enabled, and there are more rows than the minimum.
     *
     * @param bool $value The value.
     * @return void
     */
    public function show_pagesize_selector($value) {
        $this->showpagesizeselector = $value;
    }
}
