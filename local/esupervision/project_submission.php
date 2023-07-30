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
 * Project Submision Form
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once('../../config.php');
require_login(false);
require_once($CFG->dirroot.'/local/esupervision/classes/submission_form.php');
 
 // Set up the page context
 $PAGE->set_context(context_system::instance());
 $PAGE->set_url('/local/esupervision/project_submission.php');
 $PAGE->set_pagelayout('standard');
 $PAGE->set_title('Project Submision');
 $PAGE->set_heading('Project Submision');

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $project_submission_form = new local_esupervision_submission_form();
   
       if ($project_submission_form->is_cancelled()) {
           redirect(new moodle_url('/local/esupervision/classes/submission_form.php'));
       } else if ($data = $project_submission_form->get_data()) {
           // Handling file uploads
           $filemanager = $project_submission_form->get_file_manager('project_document');
           $filemanager->set_maxbytes(1024 * 1024); // Set maximum file size (1 MB in this example)
           $filemanager->set_acceptable_files('.pdf,.doc,.docx'); // Set accepted file types (PDF, Word documents)
           $filemanager->set_maxfiles(1); // Set maximum number of uploaded files (1 in this example)
   
           $filemanager->process_file_uploads('project_document', 1); // Process file upload
   
           if (!$filemanager->file_exists()) {
               echo 'Failed to upload file!';
           }
   
           // Handle form submission
           $project_submission_form->submission($data, null); // You can pass files if needed
           echo 'Submission successful!';
           redirect(new moodle_url('/local/esupervision/index.php'));
       }
   }
  
$project_submission_form = new local_esupervision_submission_form();
 
 // Output the content of the page
echo $OUTPUT->header();
$project_submission_form->display();
echo $OUTPUT->footer();