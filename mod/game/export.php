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
 * This page exports a game to another platform e.g. html, jar
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
ob_start();

require_once( "headergame.php");

require_login($course->id, false, $cm);
$context = game_get_context_module_instance( $cm->id);
require_capability('mod/game:view', $context);
require_once( $CFG->dirroot.'/lib/formslib.php');

require_login($course->id, false, $cm);

if (!has_capability('mod/game:viewreports', $context)) {
    return;
}

$target = optional_param('target', "", PARAM_ALPHANUM); // The target is HTML or JavaMe.

/**
 * The mod_game_exporthtml_form show the export form.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_game_exporthtml_form extends moodleform {

    /**
     * Definition of form.
     */
    public function definition() {
        global $CFG, $game;

        $mform = $this->_form;
        $html = $this->_customdata['html'];

        $mform->addElement('header', 'general', get_string('general', 'form'));

        if ( $game->gamekind == 'hangman') {
            $options = array();
            $options[ '0'] = 'Hangman with phrases';
            $options[ 'hangmanp'] = 'Hangman with pictures';
            $mform->addElement('select', 'type', get_string('javame_type', 'game'), $options);
            if ( $html->type == 0) {
                $mform->setDefault('type', '0');
            } else {
                $mform->setDefault('type', 'hangmanp');
            }
        }

        // Input the filename.
        $mform->addElement('text', 'filename', get_string('javame_filename', 'game'), array('size' => '30'));
        $mform->setDefault('filename', $html->filename);
        $mform->setType('filename', PARAM_TEXT);

        // Input the html title.
        $mform->addElement('text', 'title', get_string('html_title', 'game'), array('size' => '80'));
        $mform->setDefault('title', $html->title);
        $mform->setType('title', PARAM_TEXT);

        // Inputs special fields for hangman.
        if ($game->gamekind == 'hangman') {
            $mform->addElement('text', 'maxpicturewidth', get_string('javame_maxpicturewidth', 'game'), array('size' => '5'));
            $mform->setDefault('maxpicturewidth', $html->maxpicturewidth);
            $mform->setType('maxpicturewidth', PARAM_INT);
            $mform->addElement('text', 'maxpictureheight', get_string('javame_maxpictureheight', 'game'), array('size' => '5'));
            $mform->setDefault('maxpictureheight', $html->maxpictureheight);
            $mform->setType('maxpictureheight', PARAM_INT);
        }

        // Input special fields for crossword.
        if ( $game->gamekind == 'cross') {
            $mform->addElement('selectyesno', 'checkbutton', get_string('html_hascheckbutton', 'game'));
            $mform->setDefault('checkbutton', $html->checkbutton);
            $mform->addElement('selectyesno', 'printbutton', get_string('html_hasprintbutton', 'game'));
            $mform->setDefault('printbutton', $html->printbutton);
        }

        $mform->addElement('hidden', 'q', $game->id);
        $mform->setType('q', PARAM_INT);
        $mform->addElement('hidden', 'target', 'html');
        $mform->setType('target', PARAM_TEXT);

        $mform->addElement('submit', 'submitbutton', get_string( 'export', 'game'));
        $mform->closeHeaderBefore('submitbutton');
    }

    /**
     * Validation of form.
     *
     * @param stdClass $data
     * @param stdClass $files
     *
     * @return errors
     */
    public function validation($data, $files) {
        global $CFG, $USER, $DB;
        $errors = parent::validation($data, $files);

        return $errors;
    }

    /**
     * Do the exporting.
     */
    public function export() {
        global $game, $DB;

        $mform = $this->_form;

        $html = new stdClass();
        $html->id = $this->_customdata['html']->id;
        $html->type = optional_param('type', 0, PARAM_ALPHANUM);
        $html->filename = $mform->getElementValue('filename');
        $html->title = $mform->getElementValue('title');
        $html->maxpicturewidth = optional_param('maxpicturewidth', 0, PARAM_INT);
        $html->maxpictureheight = optional_param('maxpictureheight', 0, PARAM_INT);
        if ( $mform->elementExists( 'checkbutton')) {
            $checkbuttonvalue = $mform->getElementValue('checkbutton');
            $html->checkbutton = $checkbuttonvalue[ 0];
        }
        if ( $mform->elementExists( 'printbutton')) {
            $printbuttonvalue = $mform->getElementValue('printbutton');
            $html->printbutton = $printbuttonvalue[ 0];
        }

        if (!($DB->update_record( 'game_export_html', $html))) {
            print_error("game_export_html: not updated id=$html->id");
        }

        $cm = get_coursemodule_from_instance('game', $game->id, $game->course);
        $context = game_get_context_module_instance( $cm->id);

        require_once("export/exporthtml.php");
        game_OnExportHTML( $game, $context, $html);
    }
}

/**
 * The mod_game_exportjavame_form show the export form.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_game_exportjavame_form extends moodleform {

    /**
     * Definition of form.
     */
    public function definition() {
        global $CFG, $DB, $game;

        $mform = $this->_form;
        $javame = $this->_customdata['javame'];

        $mform->addElement('header', 'general', get_string('general', 'form'));

        if ( $game->gamekind == 'hangman') {
            $options = array();
            $options[ '0'] = 'Hangman with phrases';
            $options[ 'hangmanp'] = 'Hangman with pictures';
            $mform->addElement('select', 'type', get_string('javame_type', 'game'), $options);
        }

        $mform->addElement('text', 'filename', get_string('javame_filename', 'game'), array('size' => '30'));
        $mform->setDefault('filename', $javame->filename);
        $mform->setType('filename', PARAM_TEXT);
        $mform->addElement('text', 'icon', get_string('javame_icon', 'game'));
        $mform->setDefault('icon', $javame->icon);
        $mform->setType('icon', PARAM_TEXT);
        $mform->addElement('text', 'createdby', get_string('javame_createdby', 'game'));
        $mform->setDefault('createdby', $javame->createdby);
        $mform->setType('createdby', PARAM_TEXT);
        $mform->addElement('text', 'vendor', get_string('javame_vendor', 'game'));
        $mform->setDefault('vendor', $javame->vendor);
        $mform->setType('vendor', PARAM_TEXT);
        $mform->addElement('text', 'name', get_string('javame_name', 'game'), array('size' => '80'));
        $mform->setDefault('name', $javame->name);
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('text', 'description', get_string('javame_description', 'game'), array('size' => '80'));
        $mform->setDefault('description', $javame->description);
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('text', 'version', get_string('javame_version', 'game'), array('size' => '10'));
        $mform->setDefault('version', $javame->version);
        $mform->setType('version', PARAM_TEXT);
        $mform->addElement('text', 'maxpicturewidth', get_string('javame_maxpicturewidth', 'game'), array('size' => '5'));
        $mform->setDefault('maxpicturewidth', $javame->maxpicturewidth);
        $mform->setType('maxpicturewidth', PARAM_INT);
        $mform->addElement('text', 'maxpictureheight', get_string('javame_maxpictureheight', 'game'), array('size' => '5'));
        $mform->setDefault('maxpictureheight', $javame->maxpictureheight);
        $mform->setType('maxpictureheight', PARAM_INT);

        $mform->addElement('hidden', 'q', $game->id);
        $mform->setType('q', PARAM_INT);
        $mform->addElement('hidden', 'target', 'javame');
        $mform->setType('target', PARAM_TEXT);

        $mform->addElement('submit', 'submitbutton', get_string( 'export', 'game'));
        $mform->closeHeaderBefore('submitbutton');
    }

    /**
     * Validation of form.
     *
     * @param stdClass $data
     * @param stdClass $files
     *
     * @return errors
     */
    public function validation($data, $files) {
        global $CFG, $USER, $DB;
        $errors = parent::validation($data, $files);

        return $errors;
    }

    /**
     * Do the exporting.
     */
    public function export() {
        global $game, $DB;

        $mform = $this->_form;

        $javame = $this->_customdata['javame'];

        $javame->type = optional_param('type', 0, PARAM_ALPHANUM);
        $javame->filename = $mform->getElementValue('filename');
        $javame->icon = $mform->getElementValue('icon');
        $javame->createdby = $mform->getElementValue('createdby');
        $javame->vendor = $mform->getElementValue('vendor');
        $javame->name = $mform->getElementValue('name');
        $javame->description = $mform->getElementValue('description');
        $javame->version = $mform->getElementValue('version');
        $javame->maxpicturewidth = $mform->getElementValue('maxpicturewidth');
        $javame->maxpictureheight = $mform->getElementValue('maxpictureheight');

        if (!($DB->update_record( 'game_export_javame', $javame))) {
            print_error("game_export_javame: not updated id=$javame->id");
        }

        require_once("export/exportjavame.php");
        game_OnExportJavaME( $game, $javame);
    }

}

// Creates form and set initial data.
if ($target == 'html') {
    $html = $DB->get_record( 'game_export_html', array( 'id' => $game->id));
    if ($html == false) {
        $html = new stdClass();
        $html->id = $game->id;
        $html->checkbutton = 1;
        $html->printbutton = 1;
        game_insert_record( 'game_export_html', $html);
        $html = $DB->get_record( 'game_export_html', array( 'id' => $game->id));
    }
    $html->type = 0;
    $mform = new mod_game_exporthtml_form(null, array('id' => $id, 'html' => $html));
} else {
    $javame = $DB->get_record( 'game_export_javame', array( 'id' => $game->id));
    if ($javame == false) {
        $javame = new stdClass();
        $javame->id = $game->id;
        $javame->filename = $game->gamekind;
        game_insert_record( 'game_export_javame', $javame);
        $javame = $DB->get_record( 'game_export_javame', array( 'id' => $game->id));
    }
    $mform = new mod_game_exportjavame_form(null, array('id' => $id, 'javame' => $javame));
}

if ($mform->is_cancelled()) {
    ob_end_flush();
    if ($id) {
        redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=$id");
    } else {
        redirect("view.php?id=$cm->id");
    }
} else if ($entry = $mform->get_data()) {
    $mform->export();
} else {
    ob_end_flush();
    if (!empty($id)) {
        $PAGE->navbar->add(get_string('export', 'game'));
    }

    $mform->display();
}
echo $OUTPUT->footer();

/**
 * Sends via html a file.
 *
 * @param string $file
 */
function game_send_stored_file($file) {
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    } else {
        print_error("export.php: File does not exists ".$file);
    }
}
