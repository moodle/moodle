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
 * Provides the class that defines the form for the H5P authoring tool.
 *
 * @package    contenttype_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace contenttype_h5p\form;

use contenttype_h5p\content;
use contenttype_h5p\contenttype;
use core_contentbank\form\edit_content;
use core_h5p\api;
use core_h5p\editor as h5peditor;
use core_h5p\factory;
use core_h5p\helper;
use stdClass;

/**
 * Defines the form for editing an H5P content.
 *
 * @copyright 2020 Victor Deniz <victor@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor extends edit_content {

    /** @var $h5peditor H5P editor object */
    private $h5peditor;

    /** @var $content The content being edited */
    private $content;

    /**
     * Defines the form fields.
     */
    protected function definition() {
        global $DB, $OUTPUT;

        $mform = $this->_form;
        $errors = [];
        $notifications = [];

        // Id of the content to edit.
        $id = $this->_customdata['id'];
        // H5P content type to create.
        $library = optional_param('library', null, PARAM_TEXT);

        if (empty($id) && empty($library)) {
            $returnurl = new \moodle_url('/contentbank/index.php', ['contextid' => $this->_customdata['contextid']]);
            throw new \moodle_exception('invalidcontentid', 'error', $returnurl);
        }

        $this->h5peditor = new h5peditor();

        $this->set_display_vertical();
        $mform->addElement('html', $OUTPUT->heading($this->_customdata['heading'], 2));

        if ($id) {
            // The H5P editor needs the H5P content id (h5p table).
            $record = $DB->get_record('contentbank_content', ['id' => $id]);
            $this->content = new content($record);
            $file = $this->content->get_file();

            $h5p = api::get_content_from_pathnamehash($file->get_pathnamehash());
            if (!$h5p) {
                // H5P content has not been deployed yet. Let's check why.
                $factory = new \core_h5p\factory();
                $factory->get_framework()->set_file($file);

                $h5pid = helper::save_h5p($factory, $file, new stdClass());
                $errors = $factory->get_framework()->getMessages('error');
                $notifications = $factory->get_framework()->getMessages('info');
            } else {
                $h5pid = $h5p->id;
            }
            if ($h5pid) {
                $mform->addElement('hidden', 'h5pid', $h5pid);
                $mform->setType('h5pid', PARAM_INT);
                $this->h5peditor->set_content($h5pid);
            }
        } else {
            // The H5P editor needs the H5P content type library name for a new content.
            $mform->addElement('hidden', 'library', $library);
            $mform->setType('library', PARAM_TEXT);
            $this->h5peditor->set_library($library, $this->_customdata['contextid'], 'contentbank', 'public');
        }

        $mformid = 'coolh5peditor';
        $mform->setAttributes(array('id' => $mformid) + $mform->getAttributes());

        if ($errors || $notifications) {
            // Show the error messages and a Cancel button.
            foreach ($errors as $error) {
                $mform->addElement('warning', $error->code, 'notify', $error->message);
            }
            foreach ($notifications as $key => $notification) {
                $mform->addElement('warning', 'notification_'.$key, 'notify', $notification);
            }
            $mform->addElement('cancel', 'cancel', get_string('back'));
        } else {
            $this->h5peditor->add_editor_to_form($mform);
            parent::definition();
            $this->add_action_buttons();
        }
    }

    /**
     * Modify or create an H5P content from the form data.
     *
     * @param stdClass $data Form data to create or modify an H5P content.
     *
     * @return int The id of the edited or created content.
     */
    public function save_content(stdClass $data): int {
        global $DB;

        // The H5P libraries expect data->id as the H5P content id.
        // The method H5PCore::saveContent throws an error if id is set but empty.
        if (empty($data->id)) {
            unset($data->id);
        } else {
            // The H5P libraries save in $data->id the H5P content id (h5p table), so the content id is saved in another var.
            $contentid = $data->id;
        }

        $h5pcontentid = $this->h5peditor->save_content($data);

        $factory = new factory();
        $h5pfs = $factory->get_framework();

        // Needs the H5P file id to create or update the content bank record.
        $h5pcontent = $h5pfs->loadContent($h5pcontentid);
        $fs = get_file_storage();
        $file = $fs->get_file_by_hash($h5pcontent['pathnamehash']);
        // Creating new content.
        if (!isset($data->h5pid)) {
            // The initial name of the content is the title of the H5P content.
            $cbrecord = new stdClass();
            $cbrecord->name = json_decode($data->h5pparams)->metadata->title;
            $context = \context::instance_by_id($data->contextid, MUST_EXIST);
            // Create entry in content bank.
            $contenttype = new contenttype($context);
            $newcontent = $contenttype->create_content($cbrecord);
            $cfdata = fullclone($data);
            $cfdata->id = $newcontent->get_id();
            $handler = \core_contentbank\customfield\content_handler::create();
            $handler->instance_form_save($cfdata, true);
            if ($file && $newcontent) {
                $updatedfilerecord = new stdClass();
                $updatedfilerecord->id = $file->get_id();
                $updatedfilerecord->itemid = $newcontent->get_id();
                // As itemid changed, the pathnamehash has to be updated in the file table.
                $pathnamehash = \file_storage::get_pathname_hash($file->get_contextid(), $file->get_component(),
                    $file->get_filearea(), $updatedfilerecord->itemid, $file->get_filepath(), $file->get_filename());
                $updatedfilerecord->pathnamehash = $pathnamehash;
                $DB->update_record('files', $updatedfilerecord);
                // The pathnamehash in the h5p table must match the file pathnamehash.
                $h5pfs->updateContentFields($h5pcontentid, ['pathnamehash' => $pathnamehash]);
            }
        } else {
            // Update content.
            $this->content->update_content();
            $cfdata = fullclone($data);
            $cfdata->id = $this->content->get_id();
            $handler = \core_contentbank\customfield\content_handler::create();
            $handler->instance_form_save($cfdata, true);
        }

        return $contentid ?? $newcontent->get_id();
    }
}
