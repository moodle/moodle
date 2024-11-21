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
 * Main widget.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

use action_link;
use lang_string;
use moodle_url;
use renderable;
use block_xp\local\xp\rank;
use block_xp\local\xp\state;
use block_xp\local\activity\activity;
use block_xp\local\utils\user_utils;
use block_xp\local\xp\level;
use block_xp\local\xp\state_with_subject;
use renderer_base;
use templatable;

/**
 * Main widget.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xp_widget implements renderable, templatable {

    /** @var rank The user's leaderboard rank. */
    public $rank;
    /** @var bool Whether to show the user's rank.  */
    public $showrank = false;
    /** @var bool Whether the ranks is relative. */
    public $rankisrel = false;
    /** @var state The user's state. */
    public $state;
    /** @var activity[] The activity objects. */
    public $recentactivity;
    /** @var moodle_url The URL to see more. */
    public $recentactivityurl;
    /** @var bool Whether to force showing the recent activity. */
    public $forcerecentactivity = false;
    /** @var renderable The introduction text. */
    public $intro;
    /** @var action_link[] The navigation links.*/
    public $actions;
    /** @var lang_string[] Manager notices. */
    public $managernotices = [];
    /** @var level|null The next level, null if none or not shown. */
    public $nextlevel;
    /** @var bool Whether to show the next level. */
    public $shownextlevel = false;
    /** @var rank[] A ranking snapshot. */
    public $rankingsnapshot = [];
    /** @var bool Whether to show the ranks in the ranking snapshot. */
    public $showrankingsnapshot = false;
    /** @var bool Whether to show the diffs in the ranking snapshot.  */
    public $showdiffsinrankingsnapshot = true;

    /**
     * Constructor.
     *
     * @param state $state The state.
     * @param array $recentactivity The recent activity.
     * @param renderable|null $intro The introduction text.
     * @param array $actions The actions.
     * @param moodle_url|null $recentactivityurl The URL to see more.
     */
    public function __construct(state $state, array $recentactivity, renderable $intro = null, array $actions,
            moodle_url $recentactivityurl = null) {

        $this->state = $state;
        $this->intro = $intro;
        $this->recentactivityurl = $recentactivityurl;

        $this->recentactivity = array_filter($recentactivity, function($activity) {
            return $activity instanceof activity;
        });
        $this->actions = array_filter($actions, function($action) {
            return $action instanceof action_link;
        });
    }

    /**
     * Add a manager notice.
     *
     * @param lang_string $managernotice The notice.
     * @return void
     */
    public function add_manager_notice(lang_string $managernotice) {
        $this->managernotices[] = $managernotice;
    }

    /**
     * Set param.
     *
     * @param mixed $value The value.
     */
    public function set_force_recent_activity($value) {
        $this->forcerecentactivity = (bool) $value;
    }

    /**
     * Set intro.
     *
     * @param renderable $intro The intro.
     */
    public function set_intro(renderable $intro) {
        $this->intro = $intro;
    }

    /**
     * Set next level.
     *
     * @param level $nextlevel The next level.
     */
    public function set_next_level(level $nextlevel = null) {
        $this->nextlevel = $nextlevel;
    }

    /**
     * Set show next level.
     *
     * @param bool $shownextlevel The value.
     */
    public function set_show_next_level($shownextlevel) {
        $this->shownextlevel = $shownextlevel;
    }

    /**
     * Set rank.
     *
     * @param rank $rank The rank.
     */
    public function set_rank(rank $rank = null) {
        $this->rank = $rank;
    }

    /**
     * Set rank is relative.
     *
     * @param bool $rankisrel The value.
     */
    public function set_rank_is_rel($rankisrel) {
        $this->rankisrel = $rankisrel;
    }

    /**
     * Set show diff.
     *
     * @param bool $showdiffs The value.
     */
    public function set_show_diffs_in_ranking_snapshot($showdiffs) {
        $this->showdiffsinrankingsnapshot = $showdiffs;
    }

    /**
     * Set show rank.
     *
     * @param bool $showrank The value.
     */
    public function set_show_rank($showrank) {
        $this->showrank = $showrank;
    }

    /**
     * Set ranking snapshot.
     *
     * @param rank[] $rankingsnapshot The ranking snapshot.
     */
    public function set_ranking_snapshot($rankingsnapshot) {
        $this->rankingsnapshot = $rankingsnapshot;
    }

    /**
     * Set show ranking snapshot.
     *
     * @param bool $showrankingsnapshot The value.
     */
    public function set_show_ranking_snapshot($showrankingsnapshot) {
        $this->showrankingsnapshot = $showrankingsnapshot;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $renderer The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $renderer) {
        $level = $this->state->get_level();
        $badgehtml = $renderer->level_badge($level);
        $showrecentactivity = !empty($this->recentactivity) || $this->forcerecentactivity;
        $shownextlevel = $this->shownextlevel && !empty($this->nextlevel);
        $fallbackpic = user_utils::default_picture();

        $rankingsnapshot = [];
        foreach ($this->rankingsnapshot as $rank) {
            $rankingsnapshot[] = $rank;
        }
        $rankingsnapshot = array_slice(array_merge($rankingsnapshot, [null, null, null]), 0, 3);

        return [
            'introhtml' => $this->intro ? $renderer->render($this->intro) : '',

            // Level and XP.
            'badgehtml' => $badgehtml,
            'levelnamehtml' => $renderer->level_name($level),
            'xp' => $renderer->xp_human($this->state->get_xp()),
            'xphtml' => $renderer->xp($this->state->get_xp()),

            // Next level.
            'shownextlevel' => $shownextlevel,
            'nextbadgehtml' => $shownextlevel ? $renderer->medium_level_badge($this->nextlevel) : '',

            // Progress bar.
            'progressbarhtml' => $renderer->progress_bar($this->state),

            // Ranking snapshot.
            'showrankingsnapshot' => $this->showrankingsnapshot,
            'hasrankingsnapshot' => !empty($rankingsnapshot),
            'showranksinrankingsnapshot' => !$this->rankisrel,
            'showdiffsinrankingsnapshot' => $this->showdiffsinrankingsnapshot,

            'rankingsnapshot' => array_values(array_map(function($rank, $idx) use ($fallbackpic, $renderer) {
                if (!$rank) {
                    return [
                        'idx' => $idx,
                        'isplaceholder' => true,
                        'name' => '',
                        'picurl' => $fallbackpic,
                    ];
                }

                $state = $rank->get_state();
                $ishighlight = $state->get_id() == $this->state->get_id();
                $pic = $state->get_picture();
                $name = '';
                if ($state instanceof state_with_subject) {
                    $name = $state->get_name();
                }

                $diff = 0;
                if ($this->rankisrel) {
                    $diff = $rank->get_rank();
                } else {
                    $diff = $state->get_xp() - $this->state->get_xp();
                }
                $diffprefix = $diff > 0 ? '+' : '';

                return [
                    'idx' => $idx,
                    'isplaceholder' => false,

                    'diff' => $diffprefix . $renderer->xp_human($diff),
                    'diffhtml' => $diffprefix . $renderer->xp($diff),
                    'hasdiff' => $diff != 0 && !$ishighlight,

                    'rankhtml' => $rank->get_rank(),

                    'name' => $name,
                    'picurl' => $pic ?? $fallbackpic,
                    'highlight' => $ishighlight,
                ];
            }, $rankingsnapshot, array_keys($rankingsnapshot))),

            // Recent activity.
            'showrecentactivity' => $showrecentactivity,
            'recentactivityhtml' => $showrecentactivity ? $renderer->recent_activity($this->recentactivity) : '',

            // Actions.
            'showactions' => !empty($this->actions),
            'actionshtml' => !empty($this->actions) ? $renderer->xp_widget_navigation($this->actions) : '',

            // Manager notices.
            'hasmanagernotices' => !empty($this->managernotices),
            'managernotices' => array_values(array_map(function($notice) use ($renderer) {
                return $renderer->notification_without_close($notice, 'warning');
            }, $this->managernotices)),
        ];
    }
}
