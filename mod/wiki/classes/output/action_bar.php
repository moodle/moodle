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

namespace mod_wiki\output;

use moodle_url;
use templatable;
use renderable;

/**
 * Renderable class for the action bar elements in the wiki activity pages.
 *
 * @package    mod_wiki
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_bar implements templatable, renderable {

    /** @var int $pageid The database module id. */
    private $pageid;

    /** @var moodle_url $currenturl The URL of the current page. */
    private $currenturl;

    /** @var bool $displayprint Whether to display print wiki button. */
    private $displayprint;

    /**
     * The class constructor.
     *
     * @param int $pageid The wiki page id.
     * @param moodle_url $pageurl The URL of the current page.
     * @param bool $displayprint Whether to display print wiki button.
     */
    public function __construct(int $pageid, moodle_url $pageurl, bool $displayprint = false) {
        $this->pageid = $pageid;
        $this->currenturl = $pageurl;
        $this->displayprint = $displayprint;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {

        $urlselect = $this->get_action_selector();

        $data = [
            'urlselect' => $urlselect->export_for_template($output),
        ];

        if ($this->displayprint) {
            $printlink = new moodle_url('/mod/wiki/prettyview.php', ['pageid' => $this->pageid]);

            $data['printbutton'] = \html_writer::link($printlink, get_string('print', 'mod_wiki'),
                ['class' => 'btn btn-secondary', 'target' => "_blank"]);
        }

        return $data;
    }

    /**
     * Returns the URL selector object.
     *
     * @return \url_select The URL select object.
     */
    private function get_action_selector(): \url_select {
        global $PAGE;

        $menu = [];

        if (has_capability('mod/wiki:viewpage', $PAGE->context)) {
            $viewlink = new moodle_url('/mod/wiki/view.php', ['pageid' => $this->pageid]);
            $menu[$viewlink->out(false)] = get_string('view', 'mod_wiki');
        }

        if (has_capability('mod/wiki:editpage', $PAGE->context)) {
            $editlink = new moodle_url('/mod/wiki/edit.php', ['pageid' => $this->pageid]);
            $menu[$editlink->out(false)] = get_string('edit', 'mod_wiki');
        }

        if (has_capability('mod/wiki:viewcomment', $PAGE->context)) {
            $commentslink = new moodle_url('/mod/wiki/comments.php', ['pageid' => $this->pageid]);
            $menu[$commentslink->out(false)] = get_string('comments', 'mod_wiki');
        }

        if (has_capability('mod/wiki:viewpage', $PAGE->context)) {
            $historylink = new moodle_url('/mod/wiki/history.php', ['pageid' => $this->pageid]);
            $menu[$historylink->out(false)] = get_string('history', 'mod_wiki');
        }

        if (has_capability('mod/wiki:viewpage', $PAGE->context)) {
            $maplink = new moodle_url('/mod/wiki/map.php', ['pageid' => $this->pageid]);
            $menu[$maplink->out(false)] = get_string('map', 'mod_wiki');
        }

        if (has_capability('mod/wiki:viewpage', $PAGE->context)) {
            $fileslink = new moodle_url('/mod/wiki/files.php', ['pageid' => $this->pageid]);
            $menu[$fileslink->out(false)] = get_string('files', 'mod_wiki');
        }

        if (has_capability('mod/wiki:managewiki', $PAGE->context)) {
            $adminlink = new moodle_url('/mod/wiki/admin.php', ['pageid' => $this->pageid]);
            $menu[$adminlink->out(false)] = get_string('admin', 'mod_wiki');
        }

        return new \url_select($menu, $this->currenturl->out(false), null, 'wikiactionselect');
    }
}
