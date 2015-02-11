<?php

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
 * My Media display library
 *
 * @package    local_mymedia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(dirname(dirname(__FILE__))) . '/lib/tablelib.php');

class local_mymedia_renderer extends plugin_renderer_base {

    /**
     * This function outputs a table layout for display videos
     *
     * @param array - array of Kaltura video entry objects
     *
     * @return HTML markup
     */
    public function create_vidoes_table($video_list = array()) {
        global $OUTPUT;

        $output      = '';
        $max_columns = 3;

        $table = new html_table();

        $table->id     = 'mymedia_vidoes';
        $table->size = array('25%', '25%', '25%');
        $table->colclasses = array('mymedia column 1', 'mymedia column 2', 'mymedia column 3');

        $table->align = array('center', 'center', 'center');
        $table->data = array();

        $i    = 0;
        $x    = 0;
        $data = array();

        foreach ($video_list as $key => $video) {

            if (KalturaEntryStatus::READY == $video->status) {
                $data[] = $this->create_video_entry_markup($video);
            } else {
               $data[] = $this->create_video_entry_markup($video, false);
            }


            // When the max number of columns is reached, add the data to the table object
            if ($max_columns == count($data)) {

                $table->data[]       = $data;
                $table->rowclasses[] = 'row_' . $i;
                $data                = array();
                $i++;

            } else if ($x == count($video_list) -1 ) {

                $left_over_cells = $max_columns - count($data);

                // Add some extra cells to make the table symetrical
                if ($left_over_cells) {
                    for ($t = 1; $t <= $left_over_cells; $t++) {
                        $data[] = '';
                    }
                }
                $table->data[]       = $data;
                $table->rowclasses[] = 'row_' . $i;

            }

            $x++;
        }

        $attr   = array('style' => 'overflow:auto;overflow-y:hidden');
        $output .= html_writer::start_tag('center');
        $output .= html_writer::start_tag('div', $attr);
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('center');

        echo $output;
    }

    /**
     * This function creates HTML markup used to sort the video listing.
     *
     * @return HTML Markup for sorting pulldown.
     */
    public function create_sort_option() {
        global $CFG, $SESSION;

        $recent = null;
        $old = null;
        $nameasc = null;
        $namedesc = null;
        $sorturl = $CFG->wwwroot.'/local/mymedia/mymedia.php?sort=';

        if (isset($SESSION->mymediasort) && !empty($SESSION->mymediasort)) {
            $sort = $SESSION->mymediasort;
            if ($sort == 'recent') {
                $recent = "selected";
            } else if ($sort == 'old') {
                $old = "selected";
            } else if ($sort == 'name_asc') {
                $nameasc = "selected";
            } else if ($sort == 'name_desc') {
                $namedesc = "selected";
            } else {
                $recent = "selected";
            }
        } else {
            $recent = "selected";
        }

        $sort = html_writer::tag('label', get_string('sortby', 'local_mymedia').':');
        $sort .= html_writer::start_tag('select', array('id' => 'mymediasort'));
        $sort .= html_writer::tag('option', get_string('mostrecent', 'local_mymedia'), array('value' => $sorturl.'recent', 'selected' => $recent));
        $sort .= html_writer::tag('option', get_string('oldest', 'local_mymedia'), array('value' => $sorturl.'old', 'selected' => $old));
        $sort .= html_writer::tag('option', get_string('medianameasc', 'local_mymedia'), array('value' => $sorturl.'name_asc', 'selected' => $nameasc));
        $sort .= html_writer::tag('option', get_string('medianamedesc', 'local_mymedia'), array('value' => $sorturl.'name_desc', 'selected' => $namedesc));
        $sort .= html_writer::end_tag('select');

        return $sort;
    }

    public function create_options_table_upper($page, $partner_id = '', $login_session = '') {
        global $USER;

        $output = '';

        $attr   = array('border' => 0, 'width' => '100%',
                        'class' => 'mymedia upper paging upload search');
        $output .= html_writer::start_tag('table', $attr);

        $attr   = array('class' => 'mymedia upper row_0 upload search');
        $output .= html_writer::start_tag('tr', $attr);

        $attr   = array('colspan' => 3, 'align' => 'right',
                        'class' => 'mymedia upper col_0');
        $output .= html_writer::start_tag('td', $attr);

        $upload        = '';
        $simple_search = '';
        $screenrec     = '';
        $enable_ksr    = get_config(KALTURA_PLUGIN_NAME, 'enable_screen_recorder');

        $context = context_user::instance($USER->id);

        if (has_capability('local/mymedia:upload', $context, $USER)) {
            $upload = $this->create_upload_markup();
        }
 
        if ($enable_ksr && has_capability('local/mymedia:screenrecorder', $context, $USER)) {
            $screenrec = $this->create_screenrecorder_markup($partner_id, $login_session);
        }

        if (has_capability('local/mymedia:search', $context, $USER)) {
            $simple_search = $this->create_search_markup();
        }

        $output .= $upload . '&nbsp;&nbsp;' . $screenrec . $simple_search;

        $output .= html_writer::end_tag('td');

        $output .= html_writer::end_tag('tr');

        $attr   = array('class' => 'mymedia upper row_1 paging');
        $output .= html_writer::start_tag('tr', $attr);

        $attr   = array('colspan' => 3, 'align' => 'center',
                        'class' => 'mymedia upper col_0');
        $output .= html_writer::start_tag('td', $attr);

        if (!empty($page)) {
            $output .= $this->create_sort_option();
            $output .= $page;
        }

        $output .= html_writer::end_tag('td');

        $output .= html_writer::end_tag('tr');

        $output .= html_writer::end_tag('table');

        return $output;
    }

    public function create_options_table_lower($page) {
        global $USER;

        $output = '';

        $attr   = array('border' => 0, 'width' => '100%');
        $output .= html_writer::start_tag('table', $attr);

        $output .= html_writer::start_tag('tr');

        $attr   = array('colspan' => 3, 'align' => 'center');
        $output .= html_writer::start_tag('td', $attr);

        $output .= $page;

        $output .= html_writer::end_tag('td');

        $output .= html_writer::end_tag('tr');

        $output .= html_writer::end_tag('table');

        return $output;
    }

    /**
     * This function creates HTML markup used to display the video name
     *
     * @param string - name of video
     * @return HTML markup
     */
    public function create_video_name_markup($name) {

        $output = '';
        $attr   = array('class' => 'mymedia video name',
                        'title' => $name);

        $output .= html_writer::start_tag('div', $attr);
        $output .= html_writer::tag('label', $name);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * This function creates HTML markup used to display the video thumbnail
     *
     * @param string - thumbnail URL
     * @param string - alternate text
     *
     * @param HTML markup
     */
    public function create_video_thumbnail_markup($url, $alt) {

        $output = '';
        $attr   = array('class' => 'mymedia video thumbnail');

        $output .= html_writer::start_tag('div', $attr);

        $attr    = array('src' => $url . '/width/150/height/100/type/3',
                         'alt' => $alt,
                         'height' => 100,
                         'width'  => 150,
                         'title' => $alt);

        $output .= html_writer::empty_tag('img', $attr);

        $output .= html_writer::end_tag('div');

        return $output;
    }

    public function create_video_created_markup($date) {

        $output = '';
        $attr   = array('class' => 'mymedia video created',
                        'title' => userdate($date));

        $output .= html_writer::start_tag('div', $attr);
        $output .= html_writer::tag('label', userdate($date));
        $output .= html_writer::end_tag('div');

        return $output;
    }

    public function create_video_preview_link_markup() {

        $output = '';

        $attr   = array('class' => 'mymedia video preview container');
        $output .= html_writer::start_tag('span', $attr);

        $attr   = array('class' => 'mymedia video preview',
                        'href' => '#',
                        'title' => get_string('preview_link', 'local_mymedia')
                        );

        $output .= html_writer::start_tag('a', $attr);
        $output .= get_string('preview_link', 'local_mymedia');
        $output .= html_writer::end_tag('a');

        $output .= html_writer::end_tag('span');

        return $output;
    }

    public function create_video_share_link_markup() {

        $output = '';

        $attr   = array('class' => 'mymedia video share container');
        $output .= html_writer::start_tag('span', $attr);

        $attr   = array('class' => 'mymedia video share',
                        'href' => '#',
                        'title' => get_string('share_link', 'local_mymedia')
                        );

        $output .= html_writer::start_tag('a', $attr);
        $output .= get_string('share_link', 'local_mymedia');
        $output .= html_writer::end_tag('a');

        $output .= html_writer::end_tag('span');

        return $output;
    }

    public function create_video_edit_link_markup() {

        $output = '';

        $attr   = array('class' => 'mymedia video edit container');
        $output .= html_writer::start_tag('span', $attr);

        $attr   = array('class' => 'mymedia video edit',
                        'href' => '#',
                        'title' => get_string('edit_link', 'local_mymedia')
                        );

        $output .= html_writer::start_tag('a', $attr);
        $output .= get_string('edit_link', 'local_mymedia');
        $output .= html_writer::end_tag('a');

        $output .= html_writer::end_tag('span');

        return $output;
    }

    public function create_video_clip_link_markup() {

        $output = '';

        $attr   = array('class' => 'mymedia video clip container');
        $output .= html_writer::start_tag('span', $attr);

        $attr   = array('class' => 'mymedia video clip',
                        'href' => '#',
                        );

        $output .= html_writer::start_tag('a', $attr);
        $output .= get_string('clip_link', 'local_mymedia');
        $output .= html_writer::end_tag('a');

        $output .= html_writer::end_tag('span');

        return $output;
    }

    public function create_video_delete_link_markup($entry) {

        global $CFG;

        $output = '';

        $attr   = array('class' => 'mymedia video delete container');
        $output .= html_writer::start_tag('span', $attr);

        $attr   = array('class' => 'mymedia video delete',
                        'href' => new moodle_url($CFG->wwwroot . '/local/mymedia/delete_video.php', array('entry_id' => $entry->id))
                        );

        $output .= html_writer::start_tag('a', $attr);
        $output .= get_string('delete_link', 'local_mymedia');
        $output .= html_writer::end_tag('a');

        $output .= html_writer::end_tag('span');

        return $output;
    }

    /**
     * This function creates HTML markup for a video entry
     *
     * @param obj - Kaltura video object
     */
    public function create_video_entry_markup($entry, $entry_ready = true) {

        global $USER;

        $output = '';

        $attr   = array('class' => 'mymedia video entry',
                        'id' => $entry->id);

        $output .= html_writer::start_tag('div', $attr);

        if ($entry_ready) {

            $output .= $this->create_video_name_markup($entry->name);

            $output .= $this->create_video_thumbnail_markup($entry->thumbnailUrl,
                                                            $entry->name);
        } else {

            $output .= $this->create_video_name_markup($entry->name . ' (' .
                                                       get_string('converting', 'local_mymedia') . ')');

            $output .= $this->create_video_thumbnail_markup($entry->thumbnailUrl,
                                                            $entry->name);
        }


        $output .= $this->create_video_created_markup($entry->createdAt);

        $attr   = array('class' => 'mymedia video action bar',
                        'id' => $entry->id . '_action');

        $output .= html_writer::start_tag('div', $attr);

        $context = context_user::instance($USER->id);

        $output .= $this->create_video_preview_link_markup();
        $output .= '&nbsp;&nbsp;';

        if (has_capability('local/mymedia:editmetadata', $context, $USER)) {
            $output .= $this->create_video_edit_link_markup();
            $output .= '&nbsp;&nbsp;';
        }

        if (local_mymedia_check_capability('local/mymedia:sharesite') || local_mymedia_check_capability('local/mymedia:sharecourse')) {
            $output .= $this->create_video_share_link_markup();
        }

/*
        if (has_capability('local/mymedia:delete', $context, $USER)) {
            $output .= $this->create_video_delete_link_markup($entry);
        }
*/
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');

        // Add entry to cache
        $entries = new KalturaStaticEntries();
        KalturaStaticEntries::addEntryObject($entry);
        return $output;

    }

    /**
     * Displays the YUI panel markup used to display embedded video markup
     *
     * @return string - HTML markup
     */
    public function video_details_markup($courses) {
        $output = '';

        $attr = array('id' => 'id_video_details',
                      'class' => 'video_details');
        $output .= html_writer::start_tag('div', $attr);

        $attr = array('class' => 'hd');
        $output .= html_writer::tag('div', get_string('details', 'local_mymedia'), $attr);

        $attr = array('class' => 'bd');
        $output .= html_writer::tag('div', $this->video_details_tabs_markup($courses), $attr);


        $attr = array('id' => 'id_video_details_save',
                      'type' => 'submit',
                      'value' => get_string('save', 'local_mymedia'));

        $button = html_writer::empty_tag('input', $attr);

        $attr = array('class' => 'ft');
        $output .= html_writer::tag('div', "<center>$button</center>", $attr);

        $output .= html_writer::end_tag('div');

        return $output;

    }

    /**
     * This function returns YUI TabView HTML markup
     *
     * @param none
     * @return string - HTML markup
     */
    public function video_details_tabs_markup($courses) {

        $output = '';

        $attr = array('id' => 'id_video_details_tab');

        $output .= html_writer::start_tag('div', $attr);

        $output .= html_writer::start_tag('ul');

        $attr = array('href' => '#preview',
                      'title' => get_string('tab_preview', 'local_mymedia'));
        $element = html_writer::tag('a', get_string('tab_preview', 'local_mymedia'), $attr);
        $output .= html_writer::tag('li', $element);


        $attr = array('href' => '#metadata',
                      'title' => get_string('tab_metadata', 'local_mymedia'));
        $element = html_writer::tag('a', get_string('tab_metadata', 'local_mymedia'), $attr);
        $output .= html_writer::tag('li', $element);

        $attr = array('href' => '#share',
                      'title' => get_string('tab_share', 'local_mymedia'));
        $element = html_writer::tag('a', get_string('tab_share', 'local_mymedia'), $attr);
        $output .= html_writer::tag('li', $element);

        $output .= html_writer::end_tag('ul');

        $output .= html_writer::start_tag('div');

        $attr = array('id' => 'preview');
        $output .= html_writer::tag('div', '', $attr);

        $attr = array('id' => 'metadata');
        $output .= html_writer::tag('div', $this->video_metadata_form(), $attr);

        $attr = array('id' => 'share');
        $output .= html_writer::tag('div', $this->enrolled_course_share_markup($courses), $attr);

        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * This function outputs the video edit metadata elements
     *
     * @param none
     * @return string - HTML markup
     */
    public function video_metadata_form() {
        $output = '';


        $attr = array('id' => 'mymedia_video_metadata_table',
                      'class' => 'mymedia video metadata_table',
                      'border' => 0);

        $output .= html_writer::start_tag('table', $attr);

        $output .= html_writer::start_tag('tr');

        $output .= html_writer::start_tag('td');

        $output .= html_writer::tag('label', get_string('metadata_video_name', 'local_mymedia'));

        $output .= html_writer::end_tag('td');

        // Add video name text field
        $attr = array('type' => 'text',
                      'size' => 35,
                      'maxlength' => 100,
                      'id' => 'metadata_video_name',
                      'name' => 'video_name',
                      'class' => 'mymedia video name metadata',
                      'title' => get_string('metadata_video_name', 'local_mymedia'));

        $output .= html_writer::start_tag('td');

        $output .= html_writer::empty_tag('input', $attr);

        $output .= html_writer::end_tag('td');

        $output .= html_writer::end_tag('tr');
        $output .= html_writer::start_tag('tr');

        $output .= html_writer::start_tag('td');

        $output .= html_writer::tag('label', get_string('metadata_video_tags', 'local_mymedia'));

        $output .= html_writer::end_tag('td');

        // Add video tags text field
        $attr = array('type' => 'text',
                      'size' => 35,
                      'maxlength' => 100,
                      'id' => 'metadata_video_tags',
                      'name' => 'video_tags',
                      'class' => 'mymedia video tags metadata',
                      'title' => get_string('metadata_video_tags', 'local_mymedia'));

        $output .= html_writer::start_tag('td');

        $output .= html_writer::empty_tag('input', $attr);

        $output .= html_writer::end_tag('td');

        $output .= html_writer::end_tag('tr');
        $output .= html_writer::start_tag('tr');

        $output .= html_writer::start_tag('td');

        $output .= html_writer::tag('label', get_string('metadata_video_desc', 'local_mymedia'));

        $output .= html_writer::end_tag('td');

        // Add description text area
        $attr = array('rows' => '7',
                      'cols' => '35',
                      'id' => 'metadata_video_desc',
                      'name' => 'video_desc',
                      'class' => 'mymedia video desc metadata',
                      'title' => get_string('metadata_video_desc', 'local_mymedia'));

        $output .= html_writer::start_tag('td');

        $output .= html_writer::tag('textarea', '', $attr);

        // Add hidden element
        $attr = array('type' => 'hidden',
                      'id' => 'metadata_entry_id',
                      'name' => 'metadata_entry_id');

        $output .= html_writer::empty_tag('input', $attr);

        $output .= html_writer::end_tag('td');


        $output .= html_writer::end_tag('tr');

        $output .= html_writer::end_tag('table');

        return $output;

    }

    /**
     * This function prints a global share checkbox and a list of courses as
     * checkboxes
     *
     * @param array - array of courses (minimum id and fullname fields)
     */
    public function enrolled_course_share_markup($courses) {

        // Print beginning of div container
        $attr = array('id' => 'mymedia_course_list',
                      'class' => 'mymedia course list checkboxes',
                      );

        $output = html_writer::start_tag('div', $attr);

        // Print site share checkbox
        $attr = array('type' => 'checkbox',
                      'name' => 'site_share',
                      'class' => 'mymedia course checkbox site_share',
                      'id' => 'site_share',
                      'value' => '1',
                      'title' => get_string('site_share', 'local_mymedia'));

        $output .= html_writer::empty_tag('input', $attr);

        $output .= '&nbsp;' . get_string('site_share', 'local_mymedia') . '<br /><br />';


        // Print check all checkbox
        if (!empty($courses)) {
            $attr = array('type' => 'checkbox',
                          'name' => 'check_all_courses',
                          'class' => 'mymedia course checkbox checkall',
                          'id' => 'check_all',
                          'value' => '0',
                          'title' => get_string('check_all', 'local_mymedia'));

            $output .= html_writer::empty_tag('input', $attr);

            $output .= '&nbsp;' . get_string('check_all', 'local_mymedia') . '<br />';
        }

        // Print beginning of table
        $attr = array('border' => 0,
                      'class' => 'mymedia course checkbox table',
                      'id' => 'mymedia_courses_table');

        $output .= html_writer::start_tag('table', $attr);


        // Print courses and table cols/rows
        $attr = array('type' => 'checkbox',
                      'name' => 'enrolled_courses',
                      'class' => 'mymedia course chexkbox');

        $row_attr = array('class' => 'mymedia course checkbox table row');
        $col_attr = array('class' => 'mymedia course checkbox table col checkbox');
        $col2_attr = array('class' => 'mymedia course checkbox table col name');
        foreach ($courses as $course) {

            $checkbox_name = $course->fullname;
            $attr['value'] = $course->id;
            $attr['title'] = $checkbox_name;

            $checkbox = html_writer::empty_tag('input', $attr);

            $output .= html_writer::start_tag('tr', $row_attr);

            $output .= html_writer::tag('td', $checkbox, $col_attr);

            $output .= html_writer::tag('td', $checkbox_name, $col2_attr);

            $output .= html_writer::end_tag('tr');
        }

        $output .= html_writer::end_tag('table');

        $output .= html_writer::end_tag('div');

        return $output;

    }

    public function create_simple_dialog_markup() {

        $attr   = array('id' => 'mymedia_simple_dialog');
        $output = html_writer::start_tag('div');

        $attr   = array('class'  => 'hd');
        $output .= html_writer::tag('div', '', $attr);

        $attr   = array('class'  => 'bd');
        $output .= html_writer::tag('div', '', $attr);

        $output .= html_writer::end_tag('div');

        // tabindex -1 is required in order for the focus event to be capture
        // amongst all browsers
        $attr = array('id'       => 'notification',
                      'class'    => 'mymedia notification',
                      'tabindex' => '-1');
        $output .= html_writer::tag('div', '', $attr);

        return $output;
    }

    public function create_kcw_panel_markup() {

        $output = '';

        $attr = array('id' => 'kcw_panel');
        $output .= html_writer::start_tag('div', $attr);

        $attr = array('class' => 'hd');
        $output .= html_writer::tag('div', '', $attr);

        $attr = array('class' => 'bd');
        $output .= html_writer::tag('div', '', $attr);

        $output .= html_writer::end_tag('div');

        return $output;
    }

    public function create_search_markup() {
        global $SESSION;

        $attr   = array('id' => 'simple_search_container',
                        'class' => 'mymedia simple search container');

        $output = html_writer::start_tag('span', $attr);

        $attr   = array('method' => 'post',
                        'action' => new moodle_url('/local/mymedia/mymedia.php'),
                        'class' => 'mymedia search form');

        $output .= html_writer::start_tag('form', $attr);

        $default_value = (isset($SESSION->mymedia) && !empty($SESSION->mymedia)) ? $SESSION->mymedia : '';
        $attr   = array('type' => 'text',
                        'id' => 'simple_search',
                        'class' => 'mymedia simple search',
                        'name' => 'simple_search_name',
                        'value' => $default_value,
                        'title' => get_string('search_text_tooltip', 'local_mymedia'));

        $output .= html_writer::empty_tag('input', $attr);

        $attr   = array('type' => 'hidden',
                        'id' => 'sesskey_id',
                        'name' => 'sesskey',
                        'value' => sesskey());

        $output .= html_writer::empty_tag('input', $attr);

        $output .= '&nbsp;&nbsp;';

        $attr   = array('type' => 'submit',
                        'id'   => 'simple_search_btn',
                        'name' => 'simple_search_btn_name',
                        'value' => get_string('search', 'local_mymedia'),
                        'class' => 'mymedia simple search button',
                        'title' => get_string('search', 'local_mymedia'));

        $output .= html_writer::empty_tag('input', $attr);

        $attr   = array('type' => 'submit',
                        'id'   => 'clear_simple_search_btn',
                        'name' => 'clear_simple_search_btn_name',
                        'value' => get_string('search_clear', 'local_mymedia'),
                        'class' => 'mymedia simple search button clear',
                        'title' => get_string('search_clear', 'local_mymedia'));

        $output .= html_writer::empty_tag('input', $attr);

        $output .= html_writer::end_tag('form');

        $output .= html_writer::end_tag('span');

        return $output;
    }

    public function create_upload_markup() {

        $attr   = array('id' => 'upload_btn_container',
                        'class' => 'mymedia upload button container');

        $output = html_writer::start_tag('span', $attr);

        $attr   = array('id' => 'upload_btn',
                        'class' => 'mymedia upload button',
                        'value'  => get_string('upload', 'local_mymedia'),
                        'type' => 'button',
                        'title' => get_string('upload', 'local_mymedia'));

        $output .= html_writer::empty_tag('input', $attr);

        $output .= html_writer::end_tag('span');

        return $output;

    }

    public function create_loading_screen_markup() {

        $attr = array('id' => 'wait');
        $output =  html_writer::start_tag('div', $attr);

        $attr = array('class' => 'hd');
        $output .= html_writer::tag('div', '', $attr);

        $attr = array('class' => 'bd');

        $output .= html_writer::tag('div', '', $attr);

        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Generate the screen recorder button markup.
     *
     * @param int $partner_id The Kaltura partner ID
     * @param string $login_session The Kaltura session
     * @return string HTML Markup for screen recorder button
     */
    public function create_screenrecorder_markup($partner_id, $login_session) {

        $attr   = array('id' => 'screenrecorder_btn_container',
                        'class' => 'mymedia screenrecorder button container');

        $output = html_writer::start_tag('span', $attr);

        $attr   = array('id' => 'scr_btn',
                        'class' => 'mymedia screenrecorder button',
                        'value'  => get_string('screenrecorder', 'local_mymedia'),
                        'type' => 'button',
                        'title' => get_string('screenrecorder', 'local_mymedia'),
                        'onclick' => "document.getElementById('progress_bar_container').style.visibility = 'visible';".
                                     "document.getElementById('slider_border').style.borderStyle = 'none';".
                                     "document.getElementById('loading_text').innerHTML = '".get_string('checkingforjava', 'local_mymedia')."';".
                                     "kalturaScreenRecord.setDetectResultErrorMessageElementId('loading_text');".
                                     "kalturaScreenRecord.setDetectTextJavaDisabled('".get_string('javanotenabled', 'local_mymedia')."');".
                                     "kalturaScreenRecord.setDetectTextmacLionNeedsInstall('".get_string('javanotenabled', 'local_mymedia')."');".
                                     "kalturaScreenRecord.setDetectTextjavaNotDetected('".get_string('javanotenabled', 'local_mymedia')."');".
                                     "kalturaScreenRecord.startCallBack.detection_in_progress = true;".
                                     "kalturaScreenRecord.startCallBack.detection_process = setTimeout('kalturaScreenRecord.clearDetectionFlagAndDisplayError()', 30000);".
                                     "kalturaScreenRecord.startKsr('{$partner_id}', '{$login_session}', 'true');"
                       );

        $output .= html_writer::empty_tag('input', $attr);

        $output .= html_writer::end_tag('span');

        // Add progress bar
        $attr         = array('id' => 'progress_bar');
        $progress_bar = html_writer::tag('span', '', $attr);

        $attr          = array('id' => 'slider_border');
        $slider_border = html_writer::tag('div', $progress_bar, $attr);

        $attr          = array('id' => 'loading_text');
        $loading_text  = html_writer::tag('div', get_string('checkingforjava', 'local_mymedia'), $attr);

        $attr   = array('id' => 'progress_bar_container');
        $output = $output . html_writer::tag('span', $slider_border . $loading_text, $attr);

        return $output;

    }
}
