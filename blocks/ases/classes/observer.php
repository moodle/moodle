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
 *
 *
 *
 * @package    block_ases
 * @copyright  2018 Iader E. García G.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once dirname(__FILE__) . '/../../../config.php';
require_once $CFG->dirroot . '/blocks/ases/managers/lib/student_lib.php';


class block_ases_observer
{   
    /**
     * Verify if a \core\event\user_graded event is launched
     *
     * @see user_graded(\core\event\user_graded $event)
     * @param $event --> event launched
     *
     * @return void 
     */
    public static function user_graded(\core\event\user_graded $event)
    {
        global $DB;
        self::process_event($event);
    }

    /**
     * Collects the information of the throwing event and if the note is less than 3 and the student belongs to ASES 
     * it keeps a record in the table 'talentospilos_alertas_academ' and sends the mail with the alert
     *
     * @see _process_event($event)
     * @param $event --> event launched
     *
     * @return void 
     */
    protected static function process_event($event)
    {
        global $DB;
        $eventData = $event->get_data();
        $alerta = new stdClass;
        date_default_timezone_set("America/Bogota");
        $today = time();
        $alerta->id_estudiante = $event->relateduserid;
        $alerta->id_item = $event->other['itemid'];
        $alerta->id_user_registra = $event->userid;
        $alerta->nota = $event->other['finalgrade'];
        $alerta->fecha = $today;
        if ($alerta->id_user_registra != -1 and $alerta->nota < 3 and self::isASES($alerta->id_estudiante)) {
            $succes = $DB->insert_record('talentospilos_alertas_academ', $alerta);
        }

        if ($succes) {
            self::send_email_alert($alerta->id_estudiante, $alerta->id_item, $alerta->nota, $event->courseid);
        }
    }

    /**
     * Verify if a student belongs to ASES and his ases_status is seguimiento
     *
     * @see isASES($id_estudiante)
     * @param $id_estudiante --> user id
     *
     * @return boolean --> true if belongs, false otherwise.
     */
    public static function isASES($id_estudiante)
    {
        global $DB;

        $query = "SELECT id_ases_user
                  FROM {talentospilos_user_extended} extended
                  INNER JOIN {talentospilos_est_estadoases} estado_u ON extended.id_ases_user = estado_u.id_estudiante
                  INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                  WHERE estados.nombre = 'seguimiento' AND id_moodle_user = $id_estudiante
                  LIMIT 1";

        $exists = $DB->get_record_sql($query);
        if ($exists) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sends an email alert in case a student final grade is less than 3.0
     *
     * @see send_email_alert($userid, $itemid,$grade,$courseid)
     * @param $userid --> user id
     * @param $itemid --> item id
     * @param $grade --> grade value
     * @param $courseid --> course id
     *
     * @return boolean --> true if there's a successful update, false otherwise.
     */

    public static function send_email_alert($userid, $itemid, $grade, $courseid)
    {
        global $USER;
        global $DB;

        $resp = new stdClass;
        $resp->nota = true;

        $sending_user = $DB->get_record_sql("SELECT * FROM {user} WHERE username = 'sistemas1008'");

        $userFromEmail = new stdClass;

        $userFromEmail->email = $sending_user->email;
        $userFromEmail->firstname = $sending_user->firstname;
        $userFromEmail->lastname = $sending_user->lastname;
        $userFromEmail->maildisplay = true;
        $userFromEmail->mailformat = 1;
        $userFromEmail->id = $sending_user->id;
        $userFromEmail->alternatename = '';
        $userFromEmail->middlename = '';
        $userFromEmail->firstnamephonetic = '';
        $userFromEmail->lastnamephonetic = '';

        $user_moodle = get_full_user($userid);
        $nombre_estudiante = $user_moodle->firstname . " " . $user_moodle->lastname;

        $subject = "ALERTA ACADÉMICA $nombre_estudiante";

        $curso = $DB->get_record_sql("SELECT fullname, shortname FROM {course} WHERE id = $courseid");
        $nombre_curso = $curso->fullname . " " . $curso->shortname;
        $query_teacher = "SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM {course} cursoP
              INNER JOIN {context} cont ON cont.instanceid = cursoP.id
              INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
              INNER JOIN {user} usuario ON rol.userid = usuario.id
              INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
              INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = $courseid
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
        $profesor = $DB->get_record_sql($query_teacher)->fullname;
        $item = $DB->get_record_sql("SELECT itemname FROM {grade_items} WHERE id = $itemid");
        $itemname = $item->itemname;
        $nota = number_format($grade, 2);
        $nom_may = strtoupper($nombre_curso);
        $titulo = "<b>ALERTA ACADÉMICA CURSO $nom_may <br> PROFESOR: $profesor</b><br> ";
        $mensaje = "Se le informa que se ha presentado una alerta académica del estudiante $nombre_estudiante en el curso $nombre_curso<br>
        El estudiante ha obtenido la siguiente calificación:<br> <br> <b>$itemname: <b> $nota <br><br>
        Cordialmente<br>
        <b>Oficina TIC<br>
        Estrategia ASES<br>
        Universidad del Valle</b>";

        $user_ases = get_adds_fields_mi($userid);
        $id_tal = $user_ases->idtalentos;

        $monitor = get_assigned_monitor($id_tal);
        $nombre_monitor = $monitor->firstname . " " . $monitor->lastname;
        $saludo_mon = "Estimado monitor $nombre_monitor<br><br>";

        $monitorToEmail = new stdClass;
        $monitorToEmail->email = $monitor->email;
        $monitorToEmail->firstname = $monitor->firstname;
        $monitorToEmail->lastname = $monitor->lastname;
        $monitorToEmail->maildisplay = true;
        $monitorToEmail->mailformat = 1;
        $monitorToEmail->id = $monitor->id;
        $monitorToEmail->alternatename = '';
        $monitorToEmail->middlename = '';
        $monitorToEmail->firstnamephonetic = '';
        $monitorToEmail->lastnamephonetic = '';

        $messageHtml_mon = $titulo . $saludo_mon . $mensaje;
        $messageText_mon = html_to_text($messageHtml_mon);

        $email_result = email_to_user($monitorToEmail, $userFromEmail, $subject, $messageText_mon, $messageHtml_mon, ", ", true);

        if ($email_result != 1) {
            $resp->monitor = false;
        } else {
            $resp->monitor = true;

            $practicante = get_assigned_pract($id_tal);
            $nombre_practicante = $practicante->firstname . " " . $practicante->lastname;
            $saludo_prac = "Estimado practicante $nombre_practicante<br><br>";

            $practicanteToEmail = new stdClass;
            $practicanteToEmail->email = $practicante->email;
            $practicanteToEmail->firstname = $practicante->firstname;
            $practicanteToEmail->lastname = $practicante->lastname;
            $practicanteToEmail->maildisplay = true;
            $practicanteToEmail->mailformat = 1;
            $practicanteToEmail->id = $practicante->id;
            $practicanteToEmail->alternatename = '';
            $practicanteToEmail->middlename = '';
            $practicanteToEmail->firstnamephonetic = '';
            $practicanteToEmail->lastnamephonetic = '';

            $messageHtml_prac = $titulo . $saludo_prac . $mensaje;
            $messageText_prac = html_to_text($messageHtml_prac);

            $email_result_prac = email_to_user($practicanteToEmail, $userFromEmail, $subject, $messageText_prac, $messageHtml_prac, ", ", true);

            if ($email_result_prac != 1) {
                $resp->practicante = false;
            } else {
                $resp->practicante = true;

                $profesional = get_assigned_professional($id_tal);
                $nombre_profesional = $profesional->firstname . " " . $profesional->lastname;
                $saludo_prof = "Estimado profesional $nombre_profesional<br><br>";

                $profesionalToEmail = new stdClass;
                $profesionalToEmail->email = $profesional->email;
                $profesionalToEmail->firstname = $profesional->firstname;
                $profesionalToEmail->lastname = $profesional->lastname;
                $profesionalToEmail->maildisplay = true;
                $profesionalToEmail->mailformat = 1;
                $profesionalToEmail->id = $profesional->id;
                $profesionalToEmail->alternatename = '';
                $profesionalToEmail->middlename = '';
                $profesionalToEmail->firstnamephonetic = '';
                $profesionalToEmail->lastnamephonetic = '';

                $messageHtml_prof = $titulo . $saludo_prof . $mensaje;
                $messageText_prof = html_to_text($messageHtml_prof);

                $email_result_prof = email_to_user($profesionalToEmail, $userFromEmail, $subject, $messageText_prof, $messageHtml_prof, ", ", true);

                if ($email_result_prof != 1) {
                    $resp->profesional = false;
                } else {
                    $resp->profesional = true;
                }

            }
        }

        return $resp;

    }

}
