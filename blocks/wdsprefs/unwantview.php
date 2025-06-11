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
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');

// Ensure user is logged in.
require_login();

// Local requirements.
require_once($CFG->dirroot . '/blocks/wdsprefs/classes/wdsprefs.php');

// Workdaystudent enrollment stuff.
require_once($CFG->dirroot . '/enrol/workdaystudent/classes/workdaystudent.php');

// Get the system context.
$context = context_system::instance();

// Define the url for the page.
$url = new moodle_url('/blocks/wdsprefs/unwantview.php');

// Page setup.
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('wdsprefs:unwant', 'block_wdsprefs'));

// Add breadcrumbs.
$PAGE->navbar->add(
    get_string('home'),
    new moodle_url('/')
);
$PAGE->navbar->add(
    get_string('wdsprefs:unwant', 'block_wdsprefs'),
    new moodle_url('/blocks/wdsprefs/unwantview.php')
);

// Set page layout.
$PAGE->set_pagelayout('base');

// Add the css.
$PAGE->requires->css('/blocks/wdsprefs/styles.css');

class section_preferences_form extends moodleform {

    public function definition() {
        global $CFG, $DB, $USER;

        // Retrieve grouped sections from custom data.
        $gsections = $this->_customdata['gsections'] ?? [];

        // Get any crosssplits for this user
        $crosssplits = wdsprefs::get_user_crosssplits($USER->id);

        // Build a simple array.
        $csarray = [];

        // Populate that array.
        foreach ($crosssplits as $crosssplit) {

            // Build this for later.
            $csarray[$crosssplit->moodle_course_id] = $crosssplit->id;
        }

        // Instantiate the form.
        $mform = $this->_form;

        // If no sections exist, redirect home.
        if (empty($gsections)) {
            redirect(
                $CFG->wwwroot,
                'You have no Workday Student course sections.',
                null,
                core\output\notification::NOTIFY_WARNING
            );
        }

        // Iterate over academic periods.
        foreach ($gsections as $academic_period_id => $sections) {
            $periodname = wdsprefs::get_period_online($sections[0]->academic_period);

            // Add a header for each academic period.
            $mform->addElement('header',
                    "academic_period_$academic_period_id", 
                    $sections[0]->period_year .
                    " " .
                    $sections[0]->period_type . $periodname
            );

            // Iterate through sections in the academic period.
            foreach ($sections as $section) {

                // Build a fake object.
                $courseobj = new stdClass();

                // Populate it with userid and sectionid.
                $courseobj->userid = $USER->id;
                $courseobj->sectionids = $section->id; 

                $userprefs = workdaystudent::wds_get_faculty_preferences($courseobj);

                // Define the checkbox name.
                $checkboxname = 'section_' . $section->id;

                // Define the section name for the form.
                $section->name = $section->course_subject_abbreviation . ' ' .
                    $section->course_number . ' ' .
                    $section->section_number;

                // If we have a match to a crosssplit course id.
                if (isset($csarray[$section->moodle_courseid])) {

                    // Build out a url for the link to undo crosssplitting.
                    $clurlid = $csarray[$section->moodle_courseid];
                    $clurlparm = ['id' => $clurlid];
                    $clurl = new moodle_url('/blocks/wdsprefs/crosssplit_sections.php', $clurlparm);

                    // Add the HTML to not deviate much from the form.
                    $mform->addElement(
                        'html',
                        '<div class="form-group  fitem  ">' .
                        '<div class="checkbox crosslisted"><label>' .
                            $section->name .
                        ' is crosslisted! Please <a href="' .
                        $clurl->out() .
                        '">undo crosslisting</a> to unwant this section.' .
                        '</label></div></div>'
                    );


                } else {

                    // Add the form checkbox.
                    $checkbox = $mform->addElement(
                        'advcheckbox',
                        $checkboxname,
                        $section->name
                    );
                }

                // Check if the user has previously set the section as unwanted.
                $parms = ['userid' => $section->userid, 'sectionid' => $section->id];
                $existing = $DB->get_record('block_wdsprefs_unwants', $parms);

                // Set the default values.
                if (isset($existing->id) && $existing->unwanted == 1) {
                    $mform->setDefault($checkboxname, 1);
                } else if (
                    !isset($existing->id) &&
                    workdaystudent::get_numeric_course_value($section) > $userprefs->courselimit
                ) {
                    $mform->setDefault($checkboxname, 1);
                }
            }
        }

        // Add the submit button.
        $this->add_action_buttons(
            false,
            get_string('wdsprefs:saveprefs', 'block_wdsprefs')
        );
    }
}

// Fetch sections once and group them by academic_period_id.
$gsections = wdsprefs::get_courses($USER->id);

// Instantiate the form and pass grouped sections as custom data.
$form = new section_preferences_form('', ['gsections' => $gsections]);

// Process form submission.
if ($form->is_submitted() && $data = $form->get_data()) {

    // Loop through grouped sections by academic period.
    foreach ($gsections as $academic_period_id => $sections) {

        // Loop through the sections.
        foreach ($sections as $section) {

            // Define the key names.
            $key = 'section_' . $section->id;

            // Ignore unset checkboxes to avoid overriding existing preferences.
            if (!isset($data->$key)) {
                continue;
            }

            // Build these out for later.
            $sectionid = $section->id;
            $unwanted = $data->$key ? 1 : 0;

            // Check if this record already exists.
            $existing = $DB->get_record(
                'block_wdsprefs_unwants',
                ['userid' => $USER->id, 'sectionid' => $sectionid]
            );

            // If we have a record in the DB for this user in this section.
            if ($existing) {

                // Update only if the value actually changed.
                if ($existing->unwanted != $unwanted) {
                    $existing->unwanted = $unwanted;
                    $existing->lastupdated = time();

                    // Update the record.
                    $DB->update_record('block_wdsprefs_unwants', $existing);
                }

            // Insert a new record only if checked.
            } else if ($unwanted == 1 ||
                workdaystudent::get_numeric_course_value($section) > 5000
            )  {

                // Build the new record object.
                $newrecord = new stdClass();
                $newrecord->userid = $USER->id;
                $newrecord->sectionid = $sectionid;
                $newrecord->unwanted = $unwanted;
                $newrecord->lastupdated = time();

                // Insert the record.
                $DB->insert_record('block_wdsprefs_unwants', $newrecord);
            }

            // Here is where we're going to actually deal with enrollment and the course.
            if ($unwanted == 1) {
                wdsprefs::update_faculty_enrollment($USER->id, $sectionid);
            }
        }
    }

    // Redirect here once saved.
    redirect($url,
        get_string('wdsprefs:success', 'block_wdsprefs'),
        null,
        core\output\notification::NOTIFY_SUCCESS
    );
}

// Render the page.
echo $OUTPUT->header();

echo '<div id="spinner" class="spinner" style="display:none;">
        <div class="spinner-inner"></div>
      </div>';

$form->display();

echo $OUTPUT->footer();
die();
