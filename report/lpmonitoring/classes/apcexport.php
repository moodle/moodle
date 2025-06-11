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
 * Export APC data.
 *
 * @package    report_lpmonitoring
 * @copyright  2018 Université de Montréal
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lpmonitoring;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/csvlib.class.php');

use report_lpmonitoring\external as report_lpmonitoring_external;

/**
 * Class to export APC to csv file.
 *
 * @package    report_lpmonitoring
 * @copyright  2018 Université de Montréal
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class apcexport {

    /** @var array errors when processing export */
    protected $errors = [];

    /** @var array of options */
    protected $params = [];

    /** @var array trace */
    protected $formatteddata = [];

    /** @var progress_trace trace */
    protected $trace = null;

    /** @var emplid field */
    protected $emplid = null;

    /**
     * PLANETUDE_COLUMNNAME the PLAN_ETUDE column name.
     */
    const PLANETUDE_COLUMNNAME = 'PLAN_ETUDE';

    /**
     * NOMETUDIANT_COLUMNNAME the NOM_ETUDIANT column name.
     */
    const NOMETUDIANT_COLUMNNAME = 'NOM_ETUDIANT';

    /**
     * PRENOMETUDIANT_COLUMNNAME the PRENOM_ETUDIANT column name.
     */
    const PRENOMETUDIANT_COLUMNNAME = 'PRENOM_ETUDIANT';

    /**
     * IDETUDIANT_COLUMNNAME the ID_ETUDIANT column name.
     */
    const IDETUDIANT_COLUMNNAME = 'ID_ETUDIANT';

    /**
     * MATRICULEETUDIANT_COLUMNNAME the MATRICULE_ETUDIANT column name.
     */
    const MATRICULEETUDIANT_COLUMNNAME = 'MATRICULE_ETUDIANT';

    /**
     * COURRIELETUDIANT_COLUMNNAME the COURRIEL_ETUDIANT column name.
     */
    const COURRIELETUDIANT_COLUMNNAME = 'COURRIEL_ETUDIANT';

    /**
     * IDCOMPETENCE_COLUMNNAME the ID_COMPETENCE column name.
     */
    const IDCOMPETENCE_COLUMNNAME = 'ID_COMPETENCE';

    /**
     * COMPETENCE_COLUMNNAME the COMPETENCE column name.
     */
    const NOMCOMPETENCE_COLUMNNAME = 'NOM_COMPETENCE';

    /**
     * CHEMINCOMPETENCE_COLUMNNAME the CHEMIN_COMPETENCE column name.
     */
    const CHEMINCOMPETENCE_COLUMNNAME = 'CHEMIN_COMPETENCE';

    /**
     * NBR_PREUVE_COLUMNNAME the NBR_PREUVE column name.
     */
    const NBR_PREUVE_COLUMNNAME = 'NBR_PREUVE';

    /**
     * COURS_COLUMNNAME the COURS column name.
     */
    const COURS_COLUMNNAME = 'COURS';

    /**
     * EVALUATION_COLUMNNAME the EVALUATION column name.
     */
    const EVALUATION_COLUMNNAME = 'EVALUATION';

    /**
     * COMMENTAIRE_COLUMNNAME the COMMENTAIRE column name.
     */
    const COMMENTAIRE_COLUMNNAME = 'COMMENTAIRE';

    /**
     * Class constructor.
     *
     * @param progress_trace $trace
     * @param array $params Options for processing file
     */
    public function __construct($trace, $params = []) {
        global $DB;

        $this->trace = $trace;
        $this->params = $params;

        if (!isset($params['templateid']) || empty($params['templateid'])) {
            $this->errors[] = "Missing template id";
        } else {
            if (!$DB->get_record('competency_template', ['id' => $params['templateid']])) {
                $this->errors[] = "Template with id '". $params['templateid']. "' does not exist. \n";
            }
        }

        if (!empty($params['userid']) && !$DB->get_record('user', ['id' => $params['userid']])) {
            $this->errors[] = "User with id '". $params['userid']. "' does not exist. \n";
        }

        if (!isset($params['filepath']) || empty($params['filepath'])) {
            $this->errors[] = "Missing path for csv file";
        } else {
            // Validate the file path.
            if (file_exists($params['filepath']) && is_file($params['filepath'])) {
                $this->errors[] = "The file '" . $params['filepath'] ."' already exits.";
            } else {
                try {
                    if (!$fp = fopen($params['filepath'], 'w')) {
                        $this->errors[] = "The file '" . $params['filepath'] ."' is invalid.";
                    }
                } catch (\Exception $e) {
                    $this->errors[] = "The file '" . $params['filepath'] ."' is invalid.";
                }
            }
        }

        // Validate the delimiter.
        $delimiters = \csv_import_reader::get_delimiter_list();
        if (!in_array($params['flatfiledelimiter'], array_keys($delimiters))) {
            $this->errors[] = "Unknown delimiter : " . $params['flatfiledelimiter'];
        } else {
            $this->params['flatfiledelimiter'] = $delimiters[$params['flatfiledelimiter']];
        }

        // Get the employeid field.
        $userinfofield = $DB->get_record('user_info_field', ['shortname' => 'emplid']);
        if (!$userinfofield) {
            $this->errors[] = "Missing emplid in user_info_field";
        } else {
            $this->emplid = $userinfofield->id;
        }

    }

    /**
     * Prepare data for csv file.
     */
    public function prepare_data() {
        global $DB;

        $header = [self::PLANETUDE_COLUMNNAME, self::NOMETUDIANT_COLUMNNAME, self::PRENOMETUDIANT_COLUMNNAME,
            self::IDETUDIANT_COLUMNNAME, self::MATRICULEETUDIANT_COLUMNNAME, self::COURRIELETUDIANT_COLUMNNAME,
            self::IDCOMPETENCE_COLUMNNAME, self::NOMCOMPETENCE_COLUMNNAME, self::CHEMINCOMPETENCE_COLUMNNAME,
            self::NBR_PREUVE_COLUMNNAME, self::COURS_COLUMNNAME, self::EVALUATION_COLUMNNAME, self::COMMENTAIRE_COLUMNNAME];

        $this->formatteddata[] = $header;

        $sql = "SELECT p.*, u.firstname, u.lastname, u.email, ui.data emplid
                  FROM {" . \core_competency\plan::TABLE . "} p
                  JOIN {user} u ON u.id = p.userid
                  JOIN {user_info_data} ui ON ui.userid = p.userid
                 WHERE p.templateid = :templateid
                   AND ui.fieldid = :emplid";

        $params = ['templateid' => $this->params['templateid'], 'emplid' => $this->emplid];
        if (!empty($this->params['userid'])) {
            $sql .= " AND p.userid = :userid";
            $params += ['userid' => $this->params['userid']];
        }
        $sql .= " ORDER BY p.userid";

        $plans = $DB->get_recordset_sql($sql, $params);
        foreach ($plans as $plan) {
            $message = "Learning plan " . $plan->name . ' for student ' . $plan->firstname . ' ' .$plan->lastname;
            $this->trace->output($message);

            $competencies = report_lpmonitoring_external::list_plan_competencies($plan->id);
            foreach ($competencies as $competency) {
                $competencydetail = report_lpmonitoring_external::get_competency_detail($plan->userid, $competency->competency->id,
                        $plan->id);

                $row = [];
                $row[] = $plan->name;
                $row[] = $plan->lastname;
                $row[] = $plan->firstname;
                $row[] = $plan->userid;
                $row[] = $plan->emplid;
                $row[] = $plan->email;
                $row[] = $competency->competency->idnumber;
                $row[] = $competency->competency->shortname;

                $competencypath = null;
                if (!empty($competencydetail->competencypath[0]->ancestors)) {
                    foreach ($competencydetail->competencypath[0]->ancestors as $ancestor) {
                        $competencypath .= $ancestor->name . ' / ';
                    }
                    $competencypath = substr($competencypath, 0, -3);
                }
                $row[] = $competencypath;

                $row[] = $competencydetail->nbevidence;

                // Competency is not evaluated in course.
                if (empty($competencydetail->listtotalcourses)) {
                    $this->formatteddata[] = array_merge($row, ['', '', '']);
                } else {
                    // Courses that should evaluate the competency.
                    $listcourses = [];
                    foreach ($competencydetail->listtotalcourses as $course) {
                        $listcourses[$course->coursename] = ['grade' => null, 'comment' => ''];
                    }

                    // Associate the evaluation to the course.
                    foreach ($competencydetail->scalecompetencyitems as $scaleitem) {
                        foreach ($scaleitem->listcourses as $course) {
                            $listcourses[$course->shortname]['grade'] = $scaleitem->name;
                            $listcourses[$course->shortname]['comment'] = $course->lastcomment;
                        }
                    }

                    // Add evaluation done in each course.
                    foreach ($listcourses as $course => $value) {
                        $this->formatteddata[] = array_merge($row, [$course] , $value);
                    }
                }
            }
        }
    }

    /**
     * Create file.
     *
     */
    public function create_file() {

        $fp = fopen($this->params['filepath'], 'w');
        foreach ($this->formatteddata as $data) {
            $row = [];
            foreach ($data as $value) {
                $row[] = iconv(mb_detect_encoding($value), "UTF-8", $value);
            }
            fputcsv($fp, $row, $this->params['flatfiledelimiter']);
        }
        fclose($fp);
    }

    /**
     * Return list of errors.
     *
     * @return array error list
     */
    public function get_errors() {
        return $this->errors;
    }
}
