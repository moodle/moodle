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
 * Settings block
 *
 * @package    block_settings
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_mediasearch_renderer extends plugin_renderer_base {

    public function search_form(moodle_url $formtarget, $searchvalue) {
        $content = html_writer::start_tag('form', array('class'=>'mediasearchform', 'method'=>'get', 'action'=>$formtarget, 'role' => 'search'));
        $content .= html_writer::start_tag('div');
        $content .= html_writer::tag('label', s(get_string('search'), array('for'=>'mediasearchquery', 'class'=>'accesshide')));
        $content .= html_writer::empty_tag('input', array('id'=>'mediasearchquery', 'type'=>'text', 'name'=>'search', 'value'=>s($searchvalue)));
        $content .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>s(get_string('search'))));
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('form');
        return $content;
    }

    public function display_managetop($searchvalue ='') {
        $content = html_writer::start_tag('div');
        $content .= self::search_form(new moodle_url('/blocks/mediasearch/manage.php'), $searchvalue);
        $content .= $this->single_button(new moodle_url('/blocks/mediasearch/entryedit.php',
                                                        array('new' => 1)),
                                                        get_string('addnew', 'block_mediasearch'));
        $content .= ' ' .$this->single_button(new moodle_url('/blocks/mediasearch/manage.php',
                                                             array('action' => 'download')),
                                                             get_string('downloadall', 'block_mediasearch'));
        $content .= ' ' . $this->single_button(new moodle_url('/blocks/mediasearch/uploadmediadata.php'),
                                                              get_string('uploadcsv', 'block_mediasearch'));
        $content .= html_writer::end_tag('div');
        echo $content;
    }       

    public function display_entries($entries = null, $sort = 'course', $dir = 'ASC', $page, $perpage, $search) {
        global $DB;

        if (empty($entries->entries)) {
            notice(get_string("noentries", 'block_mediasearch'));
            return;
        }

        $table = new html_table();

        $table->head = array(get_string('course'),
                             get_string('title', 'block_mediasearch'),
                             get_string('description', 'block_mediasearch'),
                             get_string('keywords', 'block_mediasearch'),
                             get_string('action', 'block_mediasearch'));

        foreach ($entries->entries as $entry) {
            $tablerow = array($entry->coursefullname,
                              $entry->title,
                              $entry->description,
                              $entry->keywords,
                              $entry->link,
                              $this->action_icon(new moodle_url('/blocks/mediasearch/entryedit.php',
                                                          array('id' => $entry->id)),
                                                          new pix_icon('i/edit', get_string('edit'))). ' ' .
                              $this->action_icon(new moodle_url('/blocks/mediasearch/manage.php',
                                                          array('id' => $entry->id,
                                                                'action' => 'delete',
                                                                'sesskey' => sesskey())),
                                                          new pix_icon('i/delete', get_string('delete')))
                              );
            $table->data[] = $tablerow;
        }

        // Output a paging bar
        $pagingurl = new moodle_url('/blocks/mediasearch/manage.php', array('search' => $search, 'sort' => $sort, 'dir' => $dir));
        echo $this->paging_bar($entries->totalcount, $page, $perpage, $pagingurl);

        // Output the entries table.
        echo html_writer::table($table);
    }

    public function do_entrydownload($entries) {
        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"mediasearch_data.csv\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");
        
        echo '"' . get_string('course') . '","' .
             get_string('title', 'block_mediasearch') . '","' .
             get_string('description', 'block_mediasearch') . '","' .
             get_string('link', 'block_mediasearch') . '","' .
             get_string('keywords', 'block_mediasearch') . "\"\n";

        // Display the entries.
        foreach ($entries as $entry) {
            echo '"' . $entry->coursefullname . '","' .
                 $entry->title . '","' .
                 $entry->description . '","' .
                 $entry->link . '","' .
                 $entry->keywords . "\"\n";
        }
    }

    public function show_search_results($entries, $page, $perpage, $search) {
        global $DB;

        if (empty($entries->entries)) {
            notice(get_string("nosearchresults", 'block_mediasearch'));
            return;
        }

        $table = new html_table();

        $table->head = array(get_string('playfromstart', 'block_mediasearch'),
                             get_string('course'),
                             get_string('title', 'block_mediasearch'),
                             get_string('description', 'block_mediasearch'),
                             get_string('playfromsection', 'block_mediasearch'));

        foreach ($entries->entries as $entry) {
            $fromstarturl = "<a href='" . $entry->link ."' class='button'>" . $entry->link . "</a>";
            $fromlinkurl = "<a href='" . $entry->link ."' class='button'>" . $entry->link . "</a>";
            $tablerow = array($fromstarturl,
                              $entry->fullname,
                              $entry->title,
                              $entry->description,
                              $fromlinkurl);
            $table->data[] = $tablerow;
        }

        // Output a paging bar
        $pagingurl = new moodle_url('/blocks/mediasearch/search.php', array('search' => $search));
        echo $this->paging_bar($entries->totalcount, $page, $perpage, $pagingurl);

        // Output the entries table.
        echo html_writer::table($table);
    }
}
