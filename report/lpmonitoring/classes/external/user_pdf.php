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
 * Class for generating a PDF export of a user's learning plans.
 *
 * @package    report_lpmonitoring
 * @author     Jason Maur <jason.maur@umontreal.ca>
 * @copyright  2021 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lpmonitoring\external;

/**
 * Class for generating a PDF export of users' learning plans.
 *
 * The information in this class is meant to be rendered by the
 * report_lpmonitoring/user_report_pdf mustache template.
 *
 * @author     Jason Maur <jason.maur@umontreal.ca>
 * @copyright  Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_pdf {

    /** @var stdClass Stores basic user information (name, student ID). */
    private $user = null;

    /** @var array An array of learning plans associated with the user. */
    private $plans = null;

    /** @var string Various CSS rules for rendering with PDF with TCPDF->WriteHTML(). */
    private $styles = null;

    /** @var array An array of plan IDs to "force". Other plans won't be included. */
    private $forceplans = null;

    /** COMPLINESOVERHEAD The number of lines that are printed out with each competency, regardless of the # of scale items. */
    const COMPLINESOVERHEAD = 6;

    /** NLINESPAGEONE The number of lines we can fit on page one of the PDF. */
    const NLINESPAGEONE = 36; // Recommendation: 36 for Letter, 39 for A4.

    /** NLINESPAGEOTHER The number of lines we can fit on pages other than page one in the PDF. */
    const NLINESPAGEOTHER = 44; // Recommendation: 44 for Letter, 47 for A4.

    /** COHORTMATCHOVERRIDE Whether or not to call the cohort_match_override function to match the users' cohorts. */
    const COHORTMATCHOVERRIDE = true;

    /**
     * Class constructor. Populates the class according to the given user.
     *
     * @param int $userid The user ID of the user we want to generate the PDF for.
     * @param string $cohort The cohort idnumber to include (will check the competency_templatecohort table).
     * @param array $forceplans Array of plan IDs to "force". Other plans won't be considered.
     * @return void
     * @throws Exception if no plans are found for the given user.
     */
    public function __construct($userid, $cohort = false, $forceplans = null) {
        global $DB, $CFG;

        $studentidfield = \get_config('report_lpmonitoring', 'studentidmapping');

        $user = $DB->get_record("user", array("id" => $userid));
        \profile_load_data($user);

        $this->user = new \stdClass();
        $this->user->studentname = $user->firstname . " " . $user->lastname;

        // Use the configured field for Student ID, or fall back to the Moodle ID.
        if (isset($user->{$studentidfield})) {
            $this->user->studentid = $user->{$studentidfield};
        } else {
            $this->user->studentid = $user->id;
        }

        // Grab the name of the configured field.
        if ($studentidfield != 'id') {
            $shortname = explode("profile_field_", $studentidfield)[1];
            $userfield = $DB->get_record('user_info_field', array('shortname' => $shortname));
            $this->idfieldname = $userfield->name;
        } else {
            $this->idfieldname = 'ID';
        }

        $this->timecreated = time();
        $this->pdfreporttitle = get_string("pdfreporttitle", "report_lpmonitoring", $this->user->studentname);
        $this->dategenerated = get_string("dategenerated", "report_lpmonitoring", userdate($this->timecreated));

        $plans = \core_competency\api::list_user_plans($userid);

        if (count($plans) == 0) {
            throw new \Exception(get_string("noplansforusererror", "report_lpmonitoring", $userid));
        }

        $firstpage = true;

        foreach ($plans as $planid => $plan) {

            if (!is_null($forceplans) && !in_array($planid, $forceplans)) {
                unset($plans[$planid]);
                continue;
            }

            // If we passed the $cohort parameter and we don't get a match, ignore the learning plan for this PDF.
            if ($cohort && !$this->cohort_match($planid, $userid, $cohort)) {
                unset($plans[$planid]);
                continue;
            }
            $competencies = \report_lpmonitoring\external::list_plan_competencies($planid);

            // Don't bother if this plan has no competencies.
            if (count($competencies) == 0) {
                continue;
            }

            if (!is_array($this->plans)) {
                $this->plans = array();
            }

            $tmpplan = new \stdClass();
            $tmpplan->planname = $plan->get('name');
            $tmpplan->firstpage = $firstpage;

            if (!isset($tmpplan->competencies) || !is_array($tmpplan->competencies)) {
                $tmpplan->competencies = array();
            }

            $i = 0; // Counter for what competency we're on.
            $curline = 0; // Track what line number we're on.

            foreach ($competencies as $comp) {

                $compid = $comp->competency->id;

                $tmpcomp = new \stdClass();

                $tmpcomp = \report_lpmonitoring\external::get_competency_detail($userid, $compid, $planid);
                $tmpcomp->shortname = $comp->competency->shortname;
                $tmpcomp->idnumber = $comp->competency->idnumber;

                $tmpframework = $DB->get_record('competency_framework', array('id' => $comp->competency->competencyframeworkid));
                $tmpcomp->framework = $tmpframework->shortname;

                // Grab the taxonomy so we can label properly.
                $path = trim($comp->competency->path, '/');
                $taxonomy = explode(",", $tmpframework->taxonomies)[substr_count($path, '/')];
                $tmpcomp->taxonomylabel = get_string("taxonomy_$taxonomy", "core_competency");
                unset($tmpframework);

                $nlines = self::COMPLINESOVERHEAD + count($tmpcomp->scalecompetencyitems);
                $curline += $nlines;

                // Keep track of where we are. For readability, don't break up the summary for a competency
                // on multiple pages; so if a competency summary will be too long, insert a pagebreak and start
                // on a new page.
                if ($i == count($competencies) - 1) {
                    $tmpcomp->last = true; // Tell mustache to close the table and move on to the next learning plan.
                } else if ($firstpage && ($curline + $nlines >= self::NLINESPAGEONE)) {
                    $tmpcomp->contd = true; // Tell mustache to print the learning plan header with "(cont'd)" as a suffix.
                    $firstpage = false;
                    $curline = 0;
                    $tmpcomp->last = true; // Tell mustache to close the table and move on to the next page.
                } else if (!$firstpage && ($curline + $nlines >= self::NLINESPAGEOTHER)) {
                    $tmpcomp->contd = true; // Tell mustache to print the learning plan header with "(cont'd)" as a suffix.
                    $curline = 0;
                    $tmpcomp->last = true; // Tell mustache to close the table and move on to the next page.
                }

                $tmpplan->competencies[] = $tmpcomp;
                unset($tmpcomp);

                $i++;
            }
            $this->plans[] = $tmpplan;
        }

        // Check again. We might have removed all the elements if we're looking for a specific cohort / template.
        if (count($plans) == 0 && !$cohort) {
            throw new \Exception(get_string("noplansforusererror", "report_lpmonitoring", $userid));
        } else if (count($plans) == 0 && $cohort) {
            // Nothing found for specific cohort. Call with cohort == false to generate PDF regardless of cohorts.
            if (!is_null($this->forceplans) && is_array($this->forceplans) && count($this->forceplans) > 0) {
                self::__construct($userid, false, $this->forceplans);
            }
        }

        // If we get here and $this->plans is 0, it means we found no competencies for any of the plans.
        // Throw an exception, because we have nothing useful to write to a PDF file.
        if (!is_array($this->plans) || count($this->plans) == 0) {
            throw new \Exception(get_string("nocompetenciesforusererror", "report_lpmonitoring", $userid));
        }

        $this->styles = $this->get_css();
    }

    /**
     * Gets a context object so that we can render the mustache template
     * (specifically, the report_lpmonitoring/user_report_pdf template).
     *
     * @return stdClass Object containing the variables needed by the mustache template.
     */
    private function get_context() {
        global $CFG;

        $context = new \stdClass();
        $context->plans = $this->plans;
        $context->styles = $this->styles;
        $context->studentname = $this->user->studentname;
        $context->studentid = $this->user->studentid;
        $context->idfieldname = $this->idfieldname;
        $context->incourses = ucfirst(get_string('incourses', 'report_lpmonitoring'));
        $context->incms = ucfirst(get_string('incms', 'report_lpmonitoring'));
        $context->pdfreporttitle = $this->pdfreporttitle;
        $context->dategenerated = $this->dategenerated;
        $context->pdfimage = $this->get_logo_base64();
        return $context;
    }

    /**
     * Get the base64 encoded logo to use in the user report PDF.
     *
     * @return string The base64 encoded image.
     */
    private function get_logo_base64() {
        global $CFG;
        $logo = \get_config('report_lpmonitoring', 'userpdflogo');

        if (empty($logo)) {
            return false;
        }

        // TCPDF needs direct access to our file, without running any scripts like pluginfile.php. We need to grab the
        // logo image and copy it to a tempdir so we can use the path to the file in our HTML so TCPDF
        // can make use of it.
        $fs = \get_file_storage();
        $files = $fs->get_area_files(\context_system::instance()->id, 'report_lpmonitoring', 'pdflogo', false, '', false);
        $file = reset($files);
        $filename = $file->get_filename();

        if (!file_exists("$CFG->tempdir/pdflogo/$filename")) {
            $tmpdir = make_temp_directory("pdflogo");
        } else {
            $tmpdir = "$CFG->tempdir/pdflogo";
        }

        $filepath = "$tmpdir/$filename";
        $file->copy_content_to($filepath);
        $base64 = "@" . \base64_encode(\file_get_contents($filepath));

        return $base64;
    }

    /**
     * Gets the CSS to use when generating the PDF. This function substitutes the
     * border colour with whatever is configured.
     *
     * @return string The full CSS to be used.
     */
    private function get_css() {
        global $CFG;
        $colour = \get_config('report_lpmonitoring', 'bordercolour');
        $css = file_get_contents($CFG->dirroot . "/report/lpmonitoring/style/userreportpdf.css");
        return str_replace("[[setting:bordercolour]]", $colour, $css);
    }

    /**
     * Gets the HTML code for the user's competency report. The HTML here
     * is very rudimentary and is meant to be passed to the TCPDF->WriteHTML function.
     * Note this function uses the report_lpmonitoring/user_report_pdf mustache template.
     *
     * @return string The HTML that was rendered from the mustache template.
     */
    private function get_html() {
        global $OUTPUT;

        $html = $OUTPUT->render_from_template("report_lpmonitoring/user_report_pdf", $this->get_context());
        return $html;
    }

    /**
     * Returns a base64 encoded PDF file for this user. Useful for transferring the
     * PDF via a web service.
     *
     * @return string A base64 encoded string of the complete PDF file.
     */
    public function get_encoded_pdf() {
        global $SITE;
        $pdf = new \pdf("P", "in", "LETTER");
        $pdf->SetAuthor($SITE->fullname);
        $pdf->SetCreator($SITE->fullname);
        $pdf->SetTitle($this->pdfreporttitle);
        $pdf->AddPage();
        $pdf->WriteHTML($this->get_html(), true, false, true, false, '');
        return base64_encode($pdf->Output('ignored', 'S'));
    }

    /**
     * Returns the Moodle ID of the user identified by $fieldname and $value.
     * Useful if we are using a unique identifier other than the Moodle user ID.
     *
     * @param string $fieldname The shortname of the field we're looking at in the user_info_field table.
     * @param string $value The value of the fieldname we're looking for in the user_info_data table.
     * @throws Exception If the number of results isn't equal to exactly 1.
     * @return int The Moodle user ID.
     */
    public static function get_userid_from_profile_field($fieldname, $value) {
        global $DB;

        $results = $DB->get_records_sql("SELECT u.id FROM {user} u
                                            INNER JOIN {user_info_data} d ON d.userid = u.id
                                            INNER JOIN {user_info_field} f ON f.id = d.fieldid
                                            WHERE f.shortname = ? AND d.data = ?",
                                            array($fieldname, $value));
        if (count($results) !== 1) {
            throw new \Exception(get_string("profilefieldnotuniqueerror", "report_lpmonitoring", count($results)));
        } else {
            return array_keys($results)[0];
        }
    }

    /**
     * Function to check if the user's plan is linked to the given cohort.
     * We look at the cohort's idnumber and see if that cohort is in the
     * competency_templatecohort table for the given user / plan combination.
     *
     * @param int $planid The learning plan ID
     * @param int $userid The Moodle user ID
     * @param string $cohort The idnumber of the cohort
     * @return bool True if we found a match, false otherwise.
     */
    private function cohort_match($planid, $userid, $cohort) {
        global $DB;

        // The default behaviour is to match the cohort parameter with the cohort.idnumber.
        // You can override this behaviour by editing the function cohort_match_override below.
        if (self::COHORTMATCHOVERRIDE == true) {
            return $this->cohort_match_override($planid, $userid, $cohort);
        }

        $cohortmatch = $DB->get_records_sql("SELECT p.id FROM {competency_plan} p
                                INNER JOIN {competency_templatecohort} tc ON tc.templateid = p.templateid
                                INNER JOIN {cohort} c ON c.id = tc.cohortid
                                WHERE p.id = :planid AND c.idnumber = :cohort AND p.userid = :userid" ,
                                array('planid' => $planid, 'cohort' => $cohort, 'userid' => $userid));

        if (count($cohortmatch) == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * We override the cohort_match function because at the University of
     * Montreal, our cohort idnumbers are formulated as follows: <programnumber>-<sessionnumber>.
     * For the get_user_pdf webservice, we expect the $cohort parameter to only
     * be the programnumber. So this function overrides the default behaviour to accommodate.
     *
     * @param int $planid The learning plan ID
     * @param int $userid The Moodle user ID
     * @param string $progno The program number at U de M
     * @return bool True if we found a match, false otherwise.
     */
    private function cohort_match_override($planid, $userid, $progno) {
        global $DB;

        $cohortmatch = $DB->get_records_sql("SELECT p.id FROM {competency_plan} p
                            INNER JOIN {competency_templatecohort} tc ON tc.templateid = p.templateid
                            INNER JOIN {cohort} c ON c.id = tc.cohortid
                            WHERE " . $DB->sql_like('c.idnumber', ':progno') . "
                            AND p.id = :planid AND p.userid = :userid" ,
                            array('planid' => $planid, 'progno' => $DB->sql_like_escape($progno) . '-%', 'userid' => $userid));

        // Nothing found. Try with origtemplateid before giving up.
        if (count($cohortmatch) == 0) {
            $cohortmatch = $DB->get_records_sql("SELECT p.id FROM {competency_plan} p
                                INNER JOIN {competency_templatecohort} tc ON tc.templateid = p.origtemplateid
                                INNER JOIN {cohort} c ON c.id = tc.cohortid
                                WHERE " . $DB->sql_like('c.idnumber', ':progno') . "
                                AND p.id = :planid AND p.userid = :userid" ,
                                array('planid' => $planid, 'progno' => $DB->sql_like_escape($progno) . '-%', 'userid' => $userid));
        }

        if (count($cohortmatch) == 0) {
            // Save plan IDs that have no cohort association into $this->forceplans.
            // If the constructor doesn't come up with any plans related to the cohort, we'll
            // recall the constructor with whatever IDs we have in $this->forceplans as a failover.
            $pid = $DB->get_field_sql("SELECT p.id FROM {competency_plan} p
                                WHERE p.templateid NOT IN
                                (SELECT tc.templateid FROM {competency_templatecohort} tc
                                 WHERE tc.templateid = p.templateid OR tc.templateid = p.origtemplateid)
                                AND p.id = :planid AND p.userid = :userid",
                                array('planid' => $planid, 'userid' => $userid));
            if ($pid) {
                if (is_null($this->forceplans)) {
                    $this->forceplans = array();
                }
                $this->forceplans[] = $pid;
            }
            return false;
        } else {
            return true;
        }
    }
}
