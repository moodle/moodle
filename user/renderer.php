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
 * Provides user rendering functionality such as printing private files tree and displaying a search utility
 *
 * @package    core_user
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides user rendering functionality such as printing private files tree and displaying a search utility
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_renderer extends plugin_renderer_base {

    /**
     * Prints user search utility that can search user by first initial of firstname and/or first initial of lastname
     * Prints a header with a title and the number of users found within that subset
     * @param string $url the url to return to, complete with any parameters needed for the return
     * @param string $firstinitial the first initial of the firstname
     * @param string $lastinitial the first initial of the lastname
     * @param int $usercount the amount of users meeting the search criteria
     * @param int $totalcount the amount of users of the set/subset being searched
     * @param string $heading heading of the subset being searched, default is All Participants
     * @return string html output
     */
    public function user_search($url, $firstinitial, $lastinitial, $usercount, $totalcount, $heading = null) {

        if ($firstinitial !== 'all') {
            set_user_preference('ifirst', $firstinitial);
        }
        if ($lastinitial !== 'all') {
            set_user_preference('ilast', $lastinitial);
        }

        if (!isset($heading)) {
            $heading = get_string('allparticipants');
        }

        $content = html_writer::start_tag('form', array('action' => new moodle_url($url)));
        $content .= html_writer::start_tag('div');

        // Search utility heading.
        $content .= $this->output->heading($heading.get_string('labelsep', 'langconfig').$usercount.'/'.$totalcount, 3);

        // Initials bar.
        $prefixfirst = 'sifirst';
        $prefixlast = 'silast';
        $content .= $this->output->initials_bar($firstinitial, 'firstinitial', get_string('firstname'), $prefixfirst, $url);
        $content .= $this->output->initials_bar($lastinitial, 'lastinitial', get_string('lastname'), $prefixlast, $url);

        $content .= html_writer::end_tag('div');
        $content .= html_writer::tag('div', '&nbsp;');
        $content .= html_writer::end_tag('form');

        return $content;
    }

    /**
     * Construct a partial user search that'll require form handling implemented by the caller.
     * This allows the developer to have an initials bar setup that does not automatically redirect.
     *
     * @param string $url the url to return to, complete with any parameters needed for the return
     * @param string $firstinitial the first initial of the firstname
     * @param string $lastinitial the first initial of the lastname
     * @param bool $minirender Return a trimmed down view of the initials bar.
     * @return string html output
     * @throws coding_exception
     */
    public function partial_user_search(String $url, String $firstinitial, String $lastinitial, Bool $minirender = false): String {

        $content = '';

        if ($firstinitial !== 'all') {
            set_user_preference('ifirst', $firstinitial);
        }
        if ($lastinitial !== 'all') {
            set_user_preference('ilast', $lastinitial);
        }

        // Initials bar.
        $prefixfirst = 'sifirst';
        $prefixlast = 'silast';
        $content .= $this->output->initials_bar(
            $firstinitial,
            'firstinitial',
            get_string('firstname'),
            $prefixfirst,
            $url,
            null,
            $minirender
        );
        $content .= $this->output->initials_bar(
            $lastinitial,
            'lastinitial',
            get_string('lastname'),
            $prefixlast,
            $url,
            null,
            $minirender
        );

        return $content;
    }

    /**
     * Displays the list of tagged users
     *
     * @param array $userlist
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @return string
     */
    public function user_list($userlist, $exclusivemode) {
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($userlist as $user) {
            $userpicture = $this->output->user_picture($user, array('size' => $exclusivemode ? 100 : 35));
            $fullname = fullname($user);
            if (user_can_view_profile($user)) {
                $profilelink = new moodle_url('/user/view.php', array('id' => $user->id));
                $fullname = html_writer::link($profilelink, $fullname);
            }
            $tagfeed->add($userpicture, $fullname);
        }

        $items = $tagfeed->export_for_template($this->output);

        if ($exclusivemode) {
            $output = '<div><ul class="inline-list">';
            foreach ($items['items'] as $item) {
                $output .= '<li><div class="user-box">'. $item['img'] . $item['heading'] ."</div></li>\n";
            }
            $output .= "</ul></div>\n";
            return $output;
        }

        return $this->output->render_from_template('core_tag/tagfeed', $items);
    }

    /**
     * Renders the unified filter element for the course participants page.
     * @deprecated since 3.9
     * @throws coding_exception
     */
    public function unified_filter() {
        throw new coding_exception('unified_filter cannot be used any more, please use participants_filter instead');

    }

    /**
     * Render the data required for the participants filter on the course participants page.
     *
     * @param context $context The context of the course being displayed
     * @param string $tableregionid Container of the table to be updated by this filter, is used to retrieve the table
     * @return string
     */
    public function participants_filter(context $context, string $tableregionid): string {
        $renderable = new \core_user\output\participants_filter($context, $tableregionid);
        $templatecontext = $renderable->export_for_template($this->output);

        return $this->output->render_from_template('core_user/participantsfilter', $templatecontext);
    }

    /**
     * Returns a formatted filter option.
     *
     * @param int $filtertype The filter type (e.g. status, role, group, enrolment, last access).
     * @param string $criteria The string label of the filter type.
     * @param int $value The value for the filter option.
     * @param string $label The string representation of the filter option's value.
     * @return array The formatted option with the ['filtertype:value' => 'criteria: label'] format.
     */
    protected function format_filter_option($filtertype, $criteria, $value, $label) {
        $optionlabel = get_string('filteroption', 'moodle', (object)['criteria' => $criteria, 'value' => $label]);
        $optionvalue = "$filtertype:$value";
        return [$optionvalue => $optionlabel];
    }

    /**
     * Handles cases when after reloading the applied filters are missing in the filter options.
     *
     * @param array $filtersapplied The applied filters.
     * @param array $filteroptions The filter options.
     * @return array The formatted options with the ['filtertype:value' => 'criteria: label'] format.
     */
    private function handle_missing_applied_filters($filtersapplied, $filteroptions) {
        global $DB;

        foreach ($filtersapplied as $filter) {
            if (!array_key_exists($filter, $filteroptions)) {
                $filtervalue = explode(':', $filter);
                if (count($filtervalue) !== 2) {
                    continue;
                }
                $key = $filtervalue[0];
                $value = $filtervalue[1];

                switch($key) {
                    case USER_FILTER_LAST_ACCESS:
                        $now = usergetmidnight(time());
                        $criteria = get_string('usersnoaccesssince');
                        // Days.
                        for ($i = 1; $i < 7; $i++) {
                            $timestamp = strtotime('-' . $i . ' days', $now);
                            if ($timestamp < $value) {
                                break;
                            }
                            $val = get_string('numdays', 'moodle', $i);
                            $filteroptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $val);
                        }
                        // Weeks.
                        for ($i = 1; $i < 10; $i++) {
                            $timestamp = strtotime('-'.$i.' weeks', $now);
                            if ($timestamp < $value) {
                                break;
                            }
                            $val = get_string('numweeks', 'moodle', $i);
                            $filteroptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $val);
                        }
                        // Months.
                        for ($i = 2; $i < 12; $i++) {
                            $timestamp = strtotime('-'.$i.' months', $now);
                            if ($timestamp < $value) {
                                break;
                            }
                            $val = get_string('nummonths', 'moodle', $i);
                            $filteroptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $val);
                        }
                        // Try a year.
                        $timestamp = strtotime('-1 year', $now);
                        if ($timestamp >= $value) {
                            $val = get_string('numyear', 'moodle', 1);
                            $filteroptions += $this->format_filter_option(USER_FILTER_LAST_ACCESS, $criteria, $timestamp, $val);
                        }
                        break;
                    case USER_FILTER_ROLE:
                        $criteria = get_string('role');
                        if ($role = $DB->get_record('role', array('id' => $value))) {
                            $role = role_get_name($role);
                            $filteroptions += $this->format_filter_option(USER_FILTER_ROLE, $criteria, $value, $role);
                        }
                        break;
                }
            }
        }
        return $filteroptions;
    }
}
