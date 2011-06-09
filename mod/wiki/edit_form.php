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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains all necessary code to define and process an edit form
 *
 * @package mod-wiki-2.0
 * @copyrigth 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyrigth 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Josep Arus
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/mod/wiki/editors/wikieditor.php');

class mod_wiki_edit_form extends moodleform {

    protected function definition() {
        global $CFG;

        $mform =& $this->_form;

        $version = $this->_customdata['version'];
        $format  = $this->_customdata['format'];
        $tags    = !isset($this->_customdata['tags'])?"":$this->_customdata['tags'];

        if ($format != 'html') {
            $contextid  = $this->_customdata['contextid'];
            $filearea   = $this->_customdata['filearea'];
            $fileitemid = $this->_customdata['fileitemid'];
        }

        if (isset($this->_customdata['pagetitle'])) {
            $pagetitle = get_string('editingpage', 'wiki', $this->_customdata['pagetitle']);
        } else {
            $pagetitle = get_string('editing', 'wiki');
        }

        //editor
        $mform->addElement('header', 'general', $pagetitle);

        $fieldname = get_string('format' . $format, 'wiki');
        if ($format != 'html') {
            // Use wiki editor
            $ft = new filetype_parser;
            $extensions = $ft->get_extensions('image');
            $fs = get_file_storage();
            $tree = $fs->get_area_tree($contextid, 'mod_wiki', 'attachments', $fileitemid);
            $files = array();
            foreach ($tree['files'] as $file) {
                $filename = $file->get_filename();
                foreach ($extensions as $ext) {
                    if (preg_match('#'.$ext.'$#', $filename)) {
                        $files[] = $filename;
                    }
                }
            }
            $mform->addElement('wikieditor', 'newcontent', $fieldname, array('cols' => 100, 'rows' => 20, 'wiki_format' => $format, 'files'=>$files));
            $mform->addHelpButton('newcontent', 'format'.$format, 'wiki');
        } else {
            $mform->addElement('editor', 'newcontent_editor', $fieldname, null, page_wiki_edit::$attachmentoptions);
            $mform->addHelpButton('newcontent_editor', 'formathtml', 'wiki');
        }

        //hiddens
        if ($version >= 0) {
            $mform->addElement('hidden', 'version');
            $mform->setDefault('version', $version);
        }

        $mform->addElement('hidden', 'contentformat');
        $mform->setDefault('contentformat', $format);

        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));
            $mform->addElement('tags', 'tags', get_string('tags'));
            $mform->setDefault('tags', $tags);
        }

        $buttongroup = array();
        $buttongroup[] =& $mform->createElement('submit', 'editoption', get_string('save', 'wiki'), array('id' => 'save'));
        $buttongroup[] =& $mform->createElement('submit', 'editoption', get_string('preview'), array('id' => 'preview'));
        $buttongroup[] =& $mform->createElement('submit', 'editoption', get_string('cancel'), array('id' => 'cancel'));

        $mform->addGroup($buttongroup, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

}
