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

namespace mod_glossary\output;

use moodle_url;
use context_module;
use renderable;
use renderer_base;
use single_button;
use templatable;
use url_select;

/**
 * Class standard_action_bar - Display the action bar
 *
 * @package   mod_glossary
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class standard_action_bar implements renderable, templatable {
    /** @var object $cm The course module. */
    private $cm;
    /** @var string $mode The type of view. */
    private $mode;
    /** @var string $hook The term, entry, cat, etc... to look for based on mode. */
    private $hook;
    /** @var string $sortkey Sorted view: CREATION | UPDATE | FIRSTNAME | LASTNAME. */
    private $sortkey;
    /** @var string $sortorder The sort order (ASC or DESC). */
    private $sortorder;
    /** @var int $offset Entries to bypass (for paging purposes). */
    private $offset;
    /** @var int $pagelimit The page to resume with. */
    private $pagelimit;
    /** @var int $context The context of the glossary. */
    private $context;
    /** @var object $module The glossary record . */
    private $module;
    /** @var int $fullsearch Full search (concept and definition) when searching. */
    private $fullsearch;
    /** @var object $displayformat Override of the glossary display format. */
    private $displayformat;
    /** @var string $tab Browsing entries by categories. */
    private $tab;

    /**
     * standard_action_bar constructor.
     *
     * @param object $cm
     * @param object $module
     * @param object $displayformat
     * @param string $mode
     * @param string $hook
     * @param string $sortkey
     * @param string $sortorder
     * @param int $offset
     * @param int $pagelimit
     * @param int $fullsearch
     * @param string $tab
     * @param string $defaulttab
     * @throws \coding_exception
     */
    public function __construct(object $cm, object $module, object $displayformat, string $mode, string $hook,
            string $sortkey, string $sortorder, int $offset, int $pagelimit, int $fullsearch,
            string $tab, string $defaulttab) {
        $this->cm = $cm;
        $this->module = $module;
        $this->displayformat = $displayformat;
        $this->mode = $mode;
        $this->tab = $tab;
        $this->hook = $hook;
        $this->sortkey = $sortkey;
        $this->sortorder = $sortorder;
        $this->offset = $offset;
        $this->pagelimit = $pagelimit;
        $this->fullsearch = $fullsearch;
        $this->context = context_module::instance($this->cm->id);

        if (!has_capability('mod/glossary:approve', $this->context) && $this->tab == GLOSSARY_APPROVAL_VIEW) {
            // Non-teachers going to approval view go to defaulttab.
            $this->tab = $defaulttab;
        }
    }

    /**
     * Export the action bar
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        return [
            'addnewbutton' => $this->create_add_button($output),
            'searchbox' => $this->create_search_box(),
            'tools' => $this->get_additional_tools($output),
            'tabjumps' => $this->generate_tab_jumps($output)
        ];
    }

    /**
     * Render the search box with the checkbox
     *
     * @return array
     */
    private function create_search_box(): array {
        global $OUTPUT;
        $fullsearchchecked = false;
        if ($this->fullsearch || $this->mode != 'search') {
            $fullsearchchecked = true;
        }

        $check = [
            'name' => 'fullsearch',
            'id' => 'fullsearch',
            'value' => '1',
            'checked' => $fullsearchchecked,
            'label' => get_string("searchindefinition", "glossary"),
        ];

        $checkbox = $OUTPUT->render_from_template('core/checkbox', $check);

        $hiddenfields = [
            (object) ['name' => 'id', 'value' => $this->cm->id],
            (object) ['name' => 'mode', 'value' => 'search'],
        ];
        $data = [
            'action' => new moodle_url('/mod/glossary/view.php'),
            'hiddenfields' => $hiddenfields,
            'otherfields' => $checkbox,
            'inputname' => 'hook',
            'query' => ($this->mode == 'search') ? s($this->hook) : '',
            'searchstring' => get_string('search'),
        ];

        return $data;
    }

    /**
     * Render the add entry button
     *
     * @param renderer_base $output
     * @return \stdClass|null
     */
    private function create_add_button(renderer_base $output): ?\stdClass {
        if (!has_capability('mod/glossary:write', $this->context)) {
            return null;
        }
        $btn = new single_button(new moodle_url('/mod/glossary/edit.php', ['cmid' => $this->cm->id]),
            get_string('addsingleentry', 'glossary'), 'post', true);

        return $btn->export_for_template($output);
    }

    /**
     * Render the additional tools required by the glossary
     *
     * @param renderer_base $output
     * @return array
     */
    private function get_additional_tools(renderer_base $output): array {
        global $USER, $CFG;
        $items = [];
        $buttons = [];
        $openinnewwindow = [];

        if (has_capability('mod/glossary:import', $this->context)) {
            $items['button'] = new single_button(
                new moodle_url('/mod/glossary/import.php', ['id' => $this->cm->id]),
                get_string('importentries', 'glossary')
            );
        }

        if (has_capability('mod/glossary:export', $this->context)) {
            $url = new moodle_url('/mod/glossary/export.php', [
                'id' => $this->cm->id,
                'mode' => $this->mode,
                'hook' => $this->hook
            ]);
            $buttons[get_string('export', 'glossary')] = $url->out(false);
        }

        if (has_capability('mod/glossary:manageentries', $this->context) or $this->module->allowprintview) {
            $params = array(
                'id'        => $this->cm->id,
                'mode'      => $this->mode,
                'hook'      => $this->hook,
                'sortkey'   => $this->sortkey,
                'sortorder' => $this->sortorder,
                'offset'    => $this->offset,
                'pagelimit' => $this->pagelimit
            );
            $printurl = new moodle_url('/mod/glossary/print.php', $params);
            $buttons[get_string('printerfriendly', 'glossary')] = $printurl->out(false);
            $openinnewwindow[] = $printurl->out(false);
        }

        if (!empty($CFG->enablerssfeeds) && !empty($CFG->glossary_enablerssfeeds)
                && $this->module->rsstype && $this->module->rssarticles
                && has_capability('mod/glossary:view', $this->context)) {
            require_once("$CFG->libdir/rsslib.php");
            $string = get_string('rssfeed', 'glossary');
            $url = new moodle_url(rss_get_url($this->context->id, $USER->id, 'mod_glossary', $this->cm->instance));
            $buttons[$string] = $url->out(false);
            $openinnewwindow[] = $url->out(false);
        }

        foreach ($items as $key => $value) {
            $items[$key] = $value->export_for_template($output);
        }

        if ($buttons) {
            foreach ($buttons as $index => $value) {
                $items['select']['options'][] = [
                    'url' => $value,
                    'string' => $index,
                    'openinnewwindow' => ($openinnewwindow ? in_array($value, $openinnewwindow) : false)
                ];
            }
        }

        return $items;
    }

    /**
     * Generate a url select to match any types of glossary views
     *
     * @param renderer_base $output
     * @return \stdClass|null
     */
    private function generate_tab_jumps(renderer_base $output) {
        $tabs = glossary_get_visible_tabs($this->displayformat);
        $validtabs = [
            GLOSSARY_STANDARD => [
                'mode' => 'letter',
                'descriptor' => 'standardview'
            ],
            GLOSSARY_CATEGORY => [
                'mode' => 'cat',
                'descriptor' => 'categoryview'
            ],
            GLOSSARY_DATE => [
                'mode' => 'date',
                'descriptor' => 'dateview'
            ],
            GLOSSARY_AUTHOR => [
                'mode' => 'author',
                'descriptor' => 'authorview'
            ],
        ];

        $baseurl = new moodle_url('/mod/glossary/view.php', ['id' => $this->cm->id]);
        $active = null;
        $options = [];
        foreach ($validtabs as $key => $tabinfo) {
            if (in_array($key, $tabs)) {
                $baseurl->params(['mode' => $tabinfo['mode']]);
                $active = $active ?? $baseurl->out(false);
                $active = ($tabinfo['mode'] == $this->mode ? $baseurl->out(false) : $active);
                $options[get_string($tabinfo['descriptor'], 'glossary')] = $baseurl->out(false);
            }
        }

        if ($this->tab < GLOSSARY_STANDARD_VIEW || $this->tab > GLOSSARY_AUTHOR_VIEW) {
            $options[get_string('edit')] = '#';
        }

        if (count($options) > 1) {
            $select = new url_select(array_flip($options), $active, null);
            $select->set_label(get_string('explainalphabet', 'glossary'), ['class' => 'sr-only']);
            return $select->export_for_template($output);
        }

        return null;
    }
}
