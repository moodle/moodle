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
 * Estrategia ASES
 *
 * @author     Isabella Serna RamĆ­rez
 * @package    block_ases
 * @copyright  2017 Isabella Serna RamĆ­rez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once dirname(__FILE__) . '/../../../../config.php';

require_once $CFG->dirroot . '/grade/querylib.php';

require_once $CFG->dirroot . '/grade/report/user/lib.php';

require_once $CFG->dirroot . '/grade/lib.php';

require_once dirname(__FILE__) . '/../dphpforms/dphpforms_get_record.php';

require_once dirname(__FILE__) . '/../student_profile/studentprofile_lib.php';

require_once ('tracking_time_control_functions.php');

/**
 * Function that gets object of {user} with id_moodle
 * @see get_info_monitor($id_moodle)
 * @return object
 */

function get_info_monitor($id_moodle)
{
    global $DB;
    $sql_query = "select * from {user} where id='$id_moodle'";
    $info_monitor = $DB->get_record_sql($sql_query);
    return $info_monitor;
}

/**
 * Check trackings of a monitor that are not reviewed, belonging to an instance, in the *current period
 * @see get_unreviewed_trackings($monitorid,$instanceid)
 * @return object
 */

function get_unreviewed_trackings($monitorid, $instanceid)
{
    global $DB;
    $current_semester = get_current_semester();
    $semester_interval = get_semester_interval($current_semester->max);
    $semester_interval->fecha_inicio = strtotime($semester_interval->fecha_inicio);
    $semester_interval->fecha_fin = strtotime($semester_interval->fecha_fin);
    $sql_query = "SELECT * FROM {talentospilos_seguimiento} seguimiento
        INNER JOIN {talentospilos_seg_estudiante} seg ON seguimiento.id = seg.id_seguimiento
        where id_instancia=$instanceid and id_monitor=$monitorid and revisado_profesional=0 and (fecha between '$semester_interval->fecha_inicio' and '$semester_interval->fecha_fin');";
    $trackings = $DB->get_records_sql($sql_query);
    return $trackings;
}

/**
 * Function that obtains the initial and final hours in which a monitor was monitored in a * time interval
 * @see get_report_by_date()
 * @return array
 */

function get_report_by_date($initial_date, $final_date, $default)
{
    global $DB;
    $sql_query = "SELECT seg.id,id_monitor,fecha,hora_ini,hora_fin
    FROM {user}  usuario INNER JOIN {talentospilos_seguimiento}  seg ON usuario.id = seg.id_monitor where fecha<=$final_date and fecha>=$initial_date ";
    if ($default)
    {
        $sql_query.= " and revisado_profesional=0";
    }

    $sql_query.= "and status<>0 order by fecha asc";
    return $DB->get_records_sql($sql_query);
}

function get_report_by_date_2($initial_date, $final_date,$monitorid)
{
    global $DB;
    $interval = [];
    $interval[0] = $initial_date;
    $interval[1] = $final_date;
    $monitor_code = $monitorid;

    $seguimiento_monitor =   dphpforms_find_records( 'seguimiento_pares', 'seguimiento_pares_id_creado_por', $monitor_code, 'DESC' );

    $trackings = json_decode( $seguimiento_monitor )->results;

     return get_trackings_in_interval($trackings,$initial_date,$final_date);
}

/**
 * Function that adds a button to see details of hours worked
 * @see get_hours_per_days($init,$final)
 * @return array
 */

function get_hours_per_days($init, $final,$monitorid)
{
    global $DB;

    $register = new stdClass();
    $register->hours = 0;
    $register->minutes = 0;
    $register->total_minutes = 0;
    $final_array = [];
    $peer_tracking_v2 = get_report_by_date_2($init, $final,$monitorid);
    $first_date;
    date_default_timezone_set("America/Bogota");
    
    if ($peer_tracking_v2)
    {

        foreach ($peer_tracking_v2 as $key => $tracking) {
            foreach ($tracking->campos as $key => $review) {
                if ($review->local_alias == 'fecha')
                    {


                        $first = $review->respuesta;

                        // Get the start and end of the day in unix format

                        $init_day = strtotime($first);
                        $final_day = date_create($first);



                        $final_day = strtotime(date_time_set($final_day, 23, 59, 59)->format('Y-m-d H:i:s'));

                        if ($tracking === reset($peer_tracking_v2))

                        {
                            $first_date = strtotime($review->respuesta);
                        }
                        // if $first_date is different than $first, all the time calculation variables are reset.

                        if (!($first_date >= $init_day && $first_date <= $final_day))
                        {


                            $register->fecha=date('d-m-Y', $first_date);
                            $register->total_minutes+= $register->minutes;
                            $register->minutes = 0;
                            if ($register->hours > 0)
                            {
                                $register->total = "" . $register->hours . " Horas y " . $register->total_minutes . " Minutos.";
                            }
                            else
                            {
                                $register->total = $register->total_minutes . " Minutos.";
                            }

                            array_push($final_array, $register);
                            $register = new stdClass();
                            $register->hours = 0;
                            $register->minutes = 0;
                            $register->total_minutes = 0;
                            $register->fecha = $first;
                            $first_date = strtotime($first);
                        }

                        $calculated_time = calculate_hours_2($tracking);
                        if (isset($calculated_time->hours))
                        {
                            $register->hours+= $calculated_time->hours;
                            if (isset($calculated_time->minutes))
                            {
                                $register->minutes+= $calculated_time->minutes;

                            }
                        }
                        else
                        {
                            $register->total_minutes+= $calculated_time->total_minutes;

                        }



                        if ($tracking === end($peer_tracking_v2))
                        {
                            $register->fecha=date('d-m-Y', $first_date);
                            $register->total_minutes+= $register->minutes;
                            $register->minutes = 0;
                            if ($register->hours > 0)
                            {
                                $register->total = "" . $register->hours . " Horas y " . $register->total_minutes . " Minutos.";
                            }
                            else
                            {
                                $register->total = $register->total_minutes . " Minutos.";
                            }
                            array_push($final_array, $register);
                        }
                    }

            }
        }

            }

    return $final_array;

}