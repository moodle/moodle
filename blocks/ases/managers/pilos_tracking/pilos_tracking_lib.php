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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/../periods_management/periods_lib.php');
require_once(dirname(__FILE__).'/../lib/student_lib.php');
require_once(dirname(__FILE__).'/../dphpforms/dphpforms_records_finder.php');
require_once(dirname(__FILE__).'/../dphpforms/dphpforms_get_record.php');

/**
 * Function that gets all semesters
 * @see get_semesters()
 * @return array with every semester on database
 */
function get_semesters(){
  
  global $DB;

    $sql_query = "select * from {talentospilos_semestre} order by fecha_fin desc";
    $semesters = $DB->get_records_sql($sql_query);
    return $semesters;
}

/**
 * Function that gets string with names of certain students
 * 
 * @see get_names_students($array_usernames)
 * @param $array_usernames --> string with username without program code separated with ","
 * @return string with names of students and codes.
 */
function get_names_students($array_usernames){

  $names ="";

  $students_code = explode(",", $array_usernames);
  global $DB;


  foreach ($students_code as $key =>$student) {

    $sql_query = "select * from {user} where username LIKE '$student%' ";
    $user = $DB->get_record_sql($sql_query);
    $names.=$user->username." - ".$user->firstname." ".$user->lastname."<br>";
  }
   return $names;

}

/**
 * Function that gets an user's rol given his id, instance and id_semester
 * 
 * @see get_users_rols($user,$semester,$id_instancia)
 * @param $user --> user's id 
 * @param $semester --> semester's id
 * @param $id_instancia --> instance id
 * @return object which contains role
 */


function get_users_rols($user,$semester,$id_instancia){
  global $DB;

    $sql_query = "select * from {talentospilos_user_rol} where id_usuario='$user' and id_semestre='$semester' and id_instancia='$id_instancia'";
    $rol = $DB->get_record_sql($sql_query);
    return $rol;
}
/**
 * Gets all roles which contain substring '_ps'
 * @see get_rol_ps()
 * @return array with all roles
 */
function get_rol_ps(){
  
  global $DB;

    $sql_query = "select * from {talentospilos_rol}";
    $rols = $DB->get_records_sql($sql_query);
    $roles_ps=[];

    foreach ($rols as $rol) {
      $esta = strpos($rol->nombre_rol, "_ps");
      if($esta!==false){
        array_push($roles_ps,$rol);
      }
    }
    return $roles_ps;
}


/**
 * Function that gets user's id given his shortname in user_info_field table
 * @see get_id_info_field($shortname)
 * @param $shortname --> user's shortname in database
 * @return object representing the id
 */


function get_id_info_field($shortname){
    global $DB;
    
    $sql_query = "select id from {user_info_field}  where shortname='$shortname'";
    $consulta=$DB->get_record_sql($sql_query);
    return $consulta;
    
}



/** 
 * Function that returns all users given a semester and role
 * 
 * @see get_people_onsemester($period,$rols,$id_instancia)
 * @param $period --> current semester
 * @param $rols --> user's role
 * @param $id_instancia --> instance id
 * @return array with every user with those specifications
 */
function get_people_onsemester($period,$rols,$id_instancia){
  
  global $DB;


    $sql_query = "SELECT usuario.id AS id_usuario ,id_rol,username,firstname,lastname FROM 
    {user} AS usuario INNER JOIN {talentospilos_user_rol} AS usuario_rol ON usuario.id = usuario_rol.id_usuario where id_semestre='$period' and id_instancia='$id_instancia'";
    
    
    $people_last_period = $DB->get_records_sql($sql_query);
    $people_ps=[];

    foreach ($people_last_period as $person_last_period) {
       foreach($rols as $rol){
        if($person_last_period->id_rol == $rol->id){
          array_push($people_ps,$person_last_period);
        }
      }
    }
    return $people_ps;
}

/**
 * Function that inserts a track
 *
 * @see insert_record($object, $id_est)
 * @param $object  ---> tracking object
 * @param $id_est  ---> student's id
 * @return true
 */
function insert_record($object, $id_est){
    global $DB;
    $id_seg = $DB->insert_record('talentospilos_seguimiento', $object,true);
    
    //the student is beeing related with the track
    insert_record_student($id_seg, $id_est);
    
    //risk updated
    if($object->tipo == 'PARES'){
        foreach ($id_est as $idStudent) {
            updateRisks($object, $idStudent);
        }
    }
    
    return true;
}





/**
 * Inserts a tracking record in {talentospilos_seg_estudiante} given the track id and student's id
 * 
 * @see insert_record_student($id_seg, $id_est)
 * @param $id_seg ---> track id
 * @param $id_est  ---> student's id
 * @return boolean true if it was successful, false otherwise
 */
function insert_record_student($id_seg, $id_est){
    global $DB;
    $object_seg_est = new stdClass();
    $id_seg_est = false;
    foreach ($id_est as $id){
        $object_seg_est->id_estudiante = $id;
        $object_seg_est->id_seguimiento = $id_seg;
        
        $id_seg_est= $DB->insert_record('talentospilos_seg_estudiante', $object_seg_est,true);
    }
    return $id_seg_est;
}

/**
 * Gets a track given an specific monitor
 *
 * @see get_record_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia)
 * @param $id_monitor  ---> monitor  id
 * @param $id_seg      ---> tracking id
 * @param $tipo        ---> type of tracking
 * @param $idinstancia ---> current instance id
 * @return array ---> monitor tracks
 */

function get_record_by_monitor($id_monitor, $id_seg= null, $tipo, $idinstancia){
    global $DB;
    $sql_query= "";
    $sql_query="SELECT seg.id as id_seg, to_timestamp(fecha) as fecha_formato,*  from {talentospilos_seguimiento} seg  where seg.id_monitor = ".$id_monitor." AND seg.tipo = '".$tipo."' AND seg.id_instancia=".$idinstancia." ORDER BY fecha_formato DESC;";

    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";
    }
   return $DB->get_records_sql($sql_query);
}

/**
 * Updates 'seguimientos pares'
 * @see updateSeguimiento_pares($object)
 * @param $object --> Object containing track information
 * @return integer (1 for success, 0 otherwise)
 */

function updateSeguimiento_pares($object){
     global $DB;
    $fecha_formato =str_replace( '/' , '-' , $object->fecha);
    date_default_timezone_set('America/Los_Angeles'); 
    $object->fecha=strtotime($fecha_formato);
    //student id is obtained
    $sql_query = "select id_estudiante from {talentospilos_seg_estudiante}  where id_seguimiento=".$object->id;
    $seg_estud = $DB->get_record_sql($sql_query);
    
    //Last student track is obtained
    $lastSeg = $DB->get_record_sql('SELECT id_seguimiento,MAX(id) FROM {talentospilos_seg_estudiante} seg_est WHERE seg_est.id_estudiante='.$seg_estud->id_estudiante.'GROUP BY id_seguimiento ORDER BY id_seguimiento DESC limit 1');
   
      if($lastSeg->id_seguimiento == $object->id) updateRisks($object, $seg_estud->id_estudiante );
     $lastinsertid = $DB->update_record('talentospilos_seguimiento', $object);

     if($lastinsertid){
         return '1';
     }else{
         return '0';
     }

}

/**
 * Function that qualifies given risks
 *
 * @see update_array_risk(&$array_student_risks, $name_risk, $calificacion, $idstudent)
 * @param $array_student_risk --> Array containing student risk
 * @param $name_risk --> risk name
 * @param $calificacion --> risk qualification
 * @param $idstudent --> student id
 * @return void inserts data into array_student
 */
function update_array_risk(&$array_student_risks, $name_risk, $calificacion, $idstudent){
    global $DB;
    //obatining availables risks
    $sql_query = "SELECT * FROM {talentospilos_riesgos_ases}";
    $array_risks = $DB->get_records_sql($sql_query);
    
    foreach($array_risks as $risk){
        if($name_risk == $risk->nombre){
            $object =  new stdClass();
            $object->id_usuario = $idstudent;
            $object->id_riesgo = $risk->id;
            $object->calificacion_riesgo = $calificacion;
            array_push($array_student_risks, $object);
        }
    }
}


/**
 * Function which creates an array with information to update a track risk
 * 
 * @param $segObject --> Track object with appropiate information
 * @param $idStudent --> student's id
 * @return true 
 */
function updateRisks($segObject, $idStudent){
    global $DB;
    
    //An array is created to storage information to update
    $array_student_risks = array();
    
    if($segObject->vida_uni_riesgo){
        update_array_risk($array_student_risks,'vida_universitaria', $segObject->vida_uni_riesgo,$idStudent);
    }
    
    if($segObject->economico_riesgo){
        update_array_risk($array_student_risks,'economico', $segObject->economico_riesgo,$idStudent);
    }
    
    if($segObject->academico_riesgo){
        update_array_risk($array_student_risks,'academico', $segObject->academico_riesgo,$idStudent);
    }
    
    if($segObject->familiar_riesgo){
        update_array_risk($array_student_risks,'familiar', $segObject->familiar_riesgo,$idStudent);
    }
    
    if($segObject->individual_riesgo){
        update_array_risk($array_student_risks,'individual', $segObject->individual_riesgo,$idStudent);
    }
    
    foreach($array_student_risks as $sr){
        $sql_query ="SELECT riesg_stud.id as id FROM {talentospilos_riesg_usuario} riesg_stud WHERE riesg_stud.id_usuario=".$idStudent." AND riesg_stud.id_riesgo=".$sr->id_riesgo;
        $exists = $DB->get_record_sql($sql_query);
        
        if($exists){
            $sr->id = $exists->id;
            $DB->update_record('talentospilos_riesg_usuario',$sr);
        }else{
            $DB->insert_record('talentospilos_riesg_usuario',$sr);
        }
    }
    return true;
}

/**
 * Returns user role  to show the correct interface in 'seguimiento_pilos'
 * 
 * @see get_id_rol_($userid,$instanceid)
 * @param $userid --> user id
 * @param $instanceid instance id
 * @return array with an object representing user
 */


function get_id_rol_($userid,$instanceid)
{
    global $DB;
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);

    foreach($consulta as $tomarId)
    {
        $idretornar=$tomarId->id_rol;
    }
    return $idretornar;
}

/**
 * Función que retorna el nombre del rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
 * Returns an user role to show the appropiate interface in 'seguimiento_pilos'
 *
 * @param $userid --> user id
 * @param $instanceid --> instance id
 * @return array containing role name for the given user 
 */


function get_name_rol($idrol)
{
    global $DB;
    $sql_query = "SELECT nombre_rol FROM {talentospilos_rol} WHERE id='$idrol'";
    $consulta=$DB->get_record_sql($sql_query);
    return $consulta->nombre_rol;
}

/**
 * Function that gets information of counting 'seguimientos PARES y GRUPALES'
 * 
 * @see consult_counting_tracking($revisado,$tipo,$instancia,$fechas_epoch,$persona)
 * @param $revisado ---> checked by professional (1 or 0)
 * @param $tipo     ---> type of track (PARES or GRUPAL) 
 * @param $instancia --> instance id
 * @param $fechas_epoch --> starting and ending date of current semester
 * @return string with the count track information
 */
 
 function consult_counting_tracking($revisado,$tipo,$instancia,$fechas_epoch,$persona){
    $sql = "";
    $aux = "";

    if ($tipo == 'PARES'){
      $sql.= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where ";
      $aux.="and id_estudiante='$persona->id_estudiante'";

    }else if($tipo == 'GRUPAL') {
      $sql .= "SELECT count(*)   FROM {talentospilos_seguimiento} where ";
      $aux .= "and id_monitor='$persona'";

    }
     $sql.="revisado_profesional='$revisado' and tipo='$tipo' and id_instancia='$instancia' and status<>0 and (fecha between '$fechas_epoch[0]' and '$fechas_epoch[1]')";
     $sql.=$aux;

     return $sql;
 }



/**
 * Function that returns tracks information given a monitor id, instance, range date and period
 * 
 * @see get_seguimientos_monitor($id_monitor,$id_instance,$fechas_epoch,$periodo)
 * @param $id_monitor --> monitor id
 * @param $id_instance --> instance id
 * @param $fechas_epoch --> range date (starting and ending current semester)
 * @param $period 
 * @return array filled with track information
 */

function get_seguimientos_monitor($id_monitor,$id_instance,$fechas_epoch,$periodo){
    global $DB;

    $semestre_act = get_current_semester();

    $sql_query = "SELECT ROW_NUMBER() OVER(ORDER BY seguimiento.id ASC) AS number_unique,seguimiento.id AS id_seguimiento,
                  seguimiento.tipo,usuario_monitor
                  .id AS id_monitor_creo,usuario_monitor.firstname AS nombre_monitor_creo,nombre_usuario_estudiante.firstname 
                  AS nombre_estudiante,nombre_usuario_estudiante.lastname AS apellido_estudiante,seguimiento.created,seguimiento.fecha,seguimiento.hora_ini,
                  seguimiento.hora_fin,seguimiento.lugar,seguimiento.tema,seguimiento.objetivos,seguimiento.actividades,seguimiento.individual,seguimiento.revisado_profesional AS profesional,
                  seguimiento.revisado_practicante AS practicante,seguimiento.individual_riesgo,seguimiento.familiar_desc,seguimiento.familiar_riesgo,seguimiento.academico,
                  seguimiento.academico_riesgo,seguimiento.economico,seguimiento.economico_riesgo, seguimiento.vida_uni,seguimiento.vida_uni_riesgo,
                  seguimiento.observaciones AS observaciones,seguimiento.id AS status,seguimiento.id AS sede, usuario_estudiante.id_ases_user AS id_estudiante,monitor_actual.id_monitor AS id_monitor_actual,
                  usuario_mon_actual.firstname AS nombre_monitor_actual,usuario_mon_actual.lastname AS apellido_monitor_actual, usuario_monitor.lastname AS apellido_monitor_creo
                  FROM {talentospilos_seg_estudiante} AS s_estudiante INNER JOIN {talentospilos_seguimiento} AS seguimiento ON 
                  (s_estudiante.id_seguimiento=seguimiento.id) 
                  INNER JOIN {user} AS usuario_monitor ON (seguimiento.id_monitor = usuario_monitor.id) 
                  INNER JOIN  {talentospilos_user_extended} as usuario_estudiante  ON (usuario_estudiante.id_ases_user= s_estudiante.id_estudiante) 
                  INNER JOIN {user} as nombre_usuario_estudiante ON 
                  (nombre_usuario_estudiante.id=usuario_estudiante.id_moodle_user)
                   INNER JOIN {talentospilos_monitor_estud} as monitor_actual 
                  ON (CAST(monitor_actual.id_estudiante AS text)=CAST(s_estudiante.id_estudiante AS text)) INNER JOIN {user} AS usuario_mon_actual ON (monitor_actual.id_monitor=usuario_mon_actual.id)
                 WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND seguimiento.status <> 0  AND monitor_actual.id_semestre='$periodo->max' AND
                  (seguimiento.fecha between '$fechas_epoch[0]' and '$fechas_epoch[1]')  AND monitor_actual.id_instancia='$id_instance'  ORDER BY usuario_monitor.firstname;";

    
    $consulta=$DB->get_records_sql($sql_query);
    $array_cantidades =[];
    $array_estudiantes=[];


    foreach($consulta as $estudiante)
    {
      //amount of student records checked by his professional, not checked by himself and total of student records (PARES).
      $sql = consult_counting_tracking(1,"PARES",$id_instance,$fechas_epoch,$estudiante);
      $estudiante->registros_estudiantes_revisados=$DB->get_record_sql($sql)->count;

      $sql = consult_counting_tracking(0,"PARES",$id_instance,$fechas_epoch,$estudiante);
      $estudiante->registros_estudiantes_norevisados=$DB->get_record_sql($sql)->count;

      $estudiante->registros_estudiantes_total=($estudiante->registros_estudiantes_revisados + $estudiante->registros_estudiantes_norevisados);


      
      //amount of student records checked by his professional, not checked by himself and total of student records (GRUPAL).
       $sql = consult_counting_tracking(1,"GRUPAL",$id_instance,$fechas_epoch,$id_monitor);
       $estudiante->registros_estudiantes_revisados_grupal=0;

       $sql = consult_counting_tracking(0,"GRUPAL",$id_instance,$fechas_epoch,$id_monitor);
       $estudiante->registros_estudiantes_norevisados_grupal=0;

       $estudiante->registros_estudiantes_total_grupal=($estudiante->registros_estudiantes_revisados_grupal + $estudiante->registros_estudiantes_norevisados_grupal );
       array_push($array_estudiantes,$estudiante);
    }

    return $array_estudiantes;
}


/**
 * Returns information about all monitor tracks given his id 
 * 
 * @see get_cantidad_seguimientos_monitor($id_monitor,$id_instance)
 * @param $id_monitor --> monitor id
 * @param $id_instance --> instance id
 * @return array with the count of tracks a monitor has done
 */

function get_cantidad_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    $valorRetorno=[];
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='1') and id_monitor='$id_monitor' and id_instancia='$id_instance' and status<>0";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$id_monitor' and id_instancia='$id_instance' and status<>0";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$id_monitor' and id_instancia='$id_instance' and status<>0";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);

    return $valorRetorno;
}


/**
 * Returns information of every monitor assigned to a 'practicante'
 * @see get_monitores_practicante($id_practicante,$id_instancia,$semester)
 * @param $id_practicante --> 'practicante' id
 * @param $id_instancia --> instance id
 * @param $semester --> semester id
 * @return array of arrays with information of each monitor
 */
function get_monitores_practicante($id_practicante,$id_instancia,$semester)
{
    global $DB;
    
    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname,usuario.lastname  
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_practicante' and id_instancia='$id_instancia' and id_semestre='$semester'";

    $consulta=$DB->get_records_sql($sql_query);
    //print_r($consulta);
    
    $arreglo_retornar= array();
    
    //por cada registro retornado se toma la información necesaria, se añade a un arreglo auxiliar y este se agrega al areglo que sera retornado
    //for each returned record, the valuable information is added into other array and this last one is added into the main array
    foreach($consulta as $monitores)
    {
        $array_auxiliar=array();
        //position 0
        array_push($array_auxiliar,$monitores->id_usuario);
        $nombre = $monitores->firstname ;
        $apellido = $monitores->lastname; 
        $unir = $nombre." ".$apellido;
        //position 1
        array_push($array_auxiliar,$unir);
        //n position
        array_push($arreglo_retornar,$array_auxiliar);
    }

    
    //Return the main array
    return $arreglo_retornar;
}

/**
 * Search information of each 'practicante' that has been assigned to a 'profesional'
 * 
 * @see get_practicantes_profesional($id_profesional,$id_instancia,$semester)
 * @param $id_profesional --> profesional id
 * @param $id_instancia --> instance id
 * @param $semester --> semester id
 * @return array 
 */
function get_practicantes_profesional($id_profesional,$id_instancia,$semester)
{
    global $DB;

    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname AS nombre,usuario.lastname AS apellido, usuario_rol.id_semestre AS semestre 
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_profesional' and id_rol<>4 and id_semestre ='$semester'";


    $consulta=$DB->get_records_sql($sql_query);

    $arreglo_retornar= array();
    $arreglo_cantidades= array();
    $total_registros_no=[];




    //The value information is taken for every returned record, added to an auxiliar array and this last added to the return array
    foreach($consulta as $practicantes)
    {
        
    $monitores = get_monitores_practicante($practicantes->id_usuario,$id_instancia,$semester);
    $total_registros[0]=0;
    $total_registros[1]=0;
    $total_registros[2]=0;

    foreach($monitores as $monitor){

    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where revisado_profesional='1' and id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    $total_registros[0] +=$valorRetorno[0]->count;
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    $total_registros[1]+=$valorRetorno[1]->count;
    
    $sql_query= "SELECT count(DISTINCT {talentospilos_seguimiento}.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);
    $total_registros[2] +=$valorRetorno[2]->count;
    }
    
        $array_auxiliar=array();
        //position 0
        array_push($array_auxiliar,$practicantes->id_usuario);

        $nombre = $practicantes->nombre ;
        $apellido = $practicantes->apellido; 
        $unir = $nombre." ".$apellido;
        //position 1
        array_push($array_auxiliar,$unir);
        //array_push($array_auxiliar,$practicantes->semestre);
        array_push($array_auxiliar,$total_registros[0]);
        array_push($array_auxiliar,$total_registros[1]);
        array_push($array_auxiliar,$total_registros[2]);

        array_push($arreglo_retornar,$array_auxiliar);
    }

    //print_r($arreglo_retornar);
    return ($arreglo_retornar);
    
}


/**
 * Search bosses information
 * @see get_profesional_practicante($id,$instanceid)
 * @param $id --> user id
 * @param $instanceid --> instance id
 * @return object  with the bosses id
 */
 
function get_profesional_practicante($id,$instanceid)
{
    global $DB;

    $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario=$id AND id_instancia=$instanceid";
    $consulta=$DB->get_records_sql($sql_query);
    
    foreach($consulta as $tomarId)
    {

        $idretornar=$tomarId->id_jefe;
    }
    // print_r($idretornar);
    return $idretornar;
}


/** 
 * Function to send a message to a monitor who wants to make an observation
 * 
 * @see send_email_to_user($tipoSeg,$codigoEnviarN1,$codigoEnviarN2,$fecha,$nombre,$messageText)
 * @param $tipoSeg --> type of track ('seguimiento')
 * @param $codigoEnviarN1 --> first user id to send an email
 * @param $codigoEnviarN2 --> second user id to send an email
 * @param $codigoEnviarN3 --> Thirt user is to send and email
 * @param $fecha --> track date 
 * @param $nombre --> student name
 * @param $messageText --> message content
 * @param $place --> //
 * @return Array message information
 */
function send_email_to_user( $tipoSeg, $codigoEnviarN1, $codigoEnviarN2, $codigoEnviarN3, $fecha, $nombre, $messageText, $place ){

    global $USER;

    try{

    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;
    $messageHtml="";


    $id_user = $USER->id;

    $sending_user = get_full_user($id_user);
    $receiving_user = get_full_user($codigoEnviarN1);

    $monitor = get_full_user($codigoEnviarN1);


    $name_monitor=$monitor->firstname;
    $name_monitor.=" ";
    $name_monitor.=$monitor->lastname;
    $name_prof = $sending_user->firstname." ".$sending_user->lastname;
    
    $emailToUser->email = $receiving_user->email;
    $emailToUser->firstname = $receiving_user->firstname;
    $emailToUser->lastname = $receiving_user->lastname;
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = $receiving_user->id; 
    $emailToUser->alternatename = '';
    $emailToUser->middlename = '';
    $emailToUser->firstnamephonetic = '';
    $emailToUser->lastnamephonetic = '';


    $emailFromUser->email = $sending_user->email;
    $emailFromUser->firstname = $sending_user->firstname;
    $emailFromUser->lastname = $sending_user->lastname;
    $emailFromUser->maildisplay = false;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = $sending_user->id; 
    $emailFromUser->alternatename = '';
    $emailFromUser->middlename = '';
    $emailFromUser->firstnamephonetic = '';
    $emailFromUser->lastnamephonetic = '';
    
    if($tipoSeg=="individual")
    {
      $subject = "Observaciones seguimiento del dia $fecha del estudiante $nombre, Lugar: $place"; 
    }else
    {
      $subject = "Observaciones seguimiento del dia $fecha de los estudiantes $nombre, Lugar: $place";
    }

    $messageHtml.="<b>OBSERVACION:</b><br><br>";
    $messageHtml.="Estimado monitor $name_monitor<br><br>";
    
    
    if($tipoSeg=="individual")
    {
      $messageHtml.="Revisando el seguimiento realizado al estudiante <b> $nombre </b> el dia <b> $fecha </b>, mis comentarios son los siguientes:<br><br>";
    }else
    {
      $messageHtml.="Revisando el seguimiento realizado a los estudiantes $nombre  el dia $fecha, mis comentarios son los siguientes:<br><br>";
    }
    
    $messageHtml.="<i>".$messageText."</i><br><br>";
    $messageHtml.="Cordialmente<br>";
    $messageHtml.="$name_prof";


    $email_result = email_to_user($emailToUser, $emailFromUser->email, $subject, $messageText, $messageHtml, ", ", true);


       $email_result=0;
      //************************************************************************************************************
      //************************************************************************************************************
      //email will be sent to second user
      //************************************************************************************************************
      //************************************************************************************************************
    
      $receiving_user = get_full_user($codigoEnviarN2);


      $emailToUser->email = $receiving_user->email;
      $emailToUser->firstname = $receiving_user->firstname;
      $emailToUser->lastname = $receiving_user->lastname;
      $emailToUser->maildisplay = true;
      $emailToUser->mailformat = 1;
      $emailToUser->id = $receiving_user->id; 
      $emailToUser->alternatename = '';
      $emailToUser->middlename = '';
      $emailToUser->firstnamephonetic = '';
      $emailToUser->lastnamephonetic = '';


      $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);

        $email_result=0;
        //************************************************************************************************************
        //************************************************************************************************************
        //email will be sent to third user
        //************************************************************************************************************
        //************************************************************************************************************
        
        $receiving_user = get_full_user($codigoEnviarN3);


        $emailToUser->email = $receiving_user->email;
        $emailToUser->firstname = $receiving_user->firstname;
        $emailToUser->lastname = $receiving_user->lastname;
        $emailToUser->maildisplay = true;
        $emailToUser->mailformat = 1;
        $emailToUser->id = $receiving_user->id; 
        $emailToUser->alternatename = '';
        $emailToUser->middlename = '';
        $emailToUser->firstnamephonetic = '';
        $emailToUser->lastnamephonetic = '';


        $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
        
      
      

      
    }catch(Exception $ex){
      return "Error";
    }
  
}

/**
 * Update every risk dimension with the last risk level stored in the database.
 * @see dphpforms_find_records( $form_alias, $pregunta_alias, $ases_student_code, $order )
 * @param $ases_student_code --> ases_student_code
 * @param $record_id --> ID student track
 * @return int 0, this process cannot be interrupted, and his return code is not important.
 */
function update_last_user_risk( $ases_student_code, $record_id ){

    $student_track = null;
    if( $record_id != -1 ){
        $student_track = json_decode( dphpforms_get_record( $record_id, 'fecha', true ) )->record;
        $fields_tracking = $student_track->campos;
        for ($i = 0; $i < count( $fields_tracking ); $i++) {
            if( $fields_tracking[$i]->local_alias == 'id_estudiante' ){
                $ases_student_code = $fields_tracking[$i]->respuesta;
                break;
            };
        };
    };

    global $DB;

    $geo_risk_lvl = '0';
    $vida_universitaria_risk_lvl = '0';
    $economico_risk_lvl = '0';
    $academico_risk_lvl = '0';
    $familiar_risk_lvl = '0';
    $individual_risk_lvl = '0';
    //$ases_user_id = get_ases_user_by_code($student_code)->id;
    $ases_user_id = $ases_student_code;
    

    //Get all the records of the forms that contain the risk levels
    $trackings = dphpforms_find_records( 'seguimiento_pares', 'seguimiento_pares_id_estudiante', $ases_student_code, 'DESC' );
    $trackings = json_decode( $trackings )->results;
    $geo_tracking = dphpforms_find_records( 'seguimiento_geografico', 'seg_geo_id_estudiante', $ases_student_code, 'DESC' );
    $geo_tracking = json_decode( $geo_tracking )->results;

    /* 
        Start get last risk lvl geo_tracking.
        At it is only a single record, we just only need bring the last one.
    */
    $full_geo_tracking = null;
    if( count( $geo_tracking ) > 0 ){
        $full_geo_tracking = json_decode( dphpforms_get_record( array_values( $geo_tracking )[0]->id_registro, 'fecha' ) )->record;
    }

    if( $full_geo_tracking ){

        $fields =  $full_geo_tracking->campos;
        foreach ($fields as $key => $field) {
            if( $field->local_alias == 'seg_geo_nivel_riesgo' ){
                if( $field->respuesta == '-#$%-' ){
                    $geo_risk_lvl = '0';
                }else{
                    $geo_risk_lvl = $field->respuesta;
                }
            }
        }
    }
    // End of get last risk geographic risk level.

    $dates = array();
    $full_trackings = array();
    foreach ($trackings as $key => $tracking) {

        $full_tracking = json_decode( dphpforms_get_record( $tracking->id_registro, 'fecha' ) )->record;
        $track_date = strtotime( $full_tracking->alias_key->respuesta );
        array_push( $dates, $track_date );
        array_push( $full_trackings, $full_tracking );  
        
    };

    rsort( $dates );
    $toFile = "";
    
    for( $i = 0; $i < count( $dates ); $i++ ){

        $tracking_tmp = new stdClass();
        $tmp_date = "";
        for ($x = 0; $x < count( $full_trackings ); $x++) {
            
            $tmp_date = $full_trackings[$x]->alias_key->respuesta;
            if( $dates[$i] == strtotime( $tmp_date ) ){
                $tracking_tmp->id = $full_trackings[$x]->id_registro;
                $tracking_tmp->fecha = strtotime( $full_trackings[$x]->alias_key->respuesta );
                $tracking_tmp->json_full_tracking = json_encode( $full_trackings[$x] );
                //unset( $full_trackings[$x] );
                $full_trackings[$x]->alias_key->respuesta = -1;
                break;
            };
        };

        $fields_tracking = json_decode( $tracking_tmp->json_full_tracking )->campos;
        foreach ($fields_tracking as $key => $field) {
            
            if( $field->local_alias == 'puntuacion_vida_uni' ){
                if( $field->respuesta == '-#$%-' ){
                    //$vida_universitaria_risk_lvl = '0';
                }else{
                    if( $vida_universitaria_risk_lvl == '0' ){
                        $vida_universitaria_risk_lvl = $field->respuesta;
                    };
                };
            };
            if( $field->local_alias == 'puntuacion_riesgo_economico' ){
                if( $field->respuesta == '-#$%-' ){
                    //$economico_risk_lvl = '0';
                }else{
                    if( $economico_risk_lvl == '0' ){
                        $economico_risk_lvl = $field->respuesta;
                    };
                };
            };
            if( $field->local_alias == 'puntuacion_riesgo_academico' ){
                if( $field->respuesta == '-#$%-' ){
                    //$academico_risk_lvl = '0';
                }else{
                    if( $academico_risk_lvl == '0' ){
                        $academico_risk_lvl = $field->respuesta;
                    };
                };
            };
            if( $field->local_alias == 'puntuacion_riesgo_familiar' ){
                if( $field->respuesta == '-#$%-' ){
                    //$familiar_risk_lvl = '0';
                }else{
                    if( $familiar_risk_lvl == '0' ){
                        $familiar_risk_lvl = $field->respuesta;
                    };
                };
            };
            
            if( $field->local_alias == 'puntuacion_riesgo_individual' ){
                if( $field->respuesta == '-#$%-' ){
                    //$individual_risk_lvl = '0';
                }else{
                    if( $individual_risk_lvl == '0' ){
                        $individual_risk_lvl = $field->respuesta;
                    };
                };
            };
        };
    };

    $risks = $DB->get_records_sql( "SELECT * FROM {talentospilos_riesgos_ases}" );
    foreach( $risks as $key => $risk ){

        if ( $risk->nombre == 'individual' ){
            
            $previous_record_risk = $DB->get_record_sql( "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = '$ases_user_id' AND id_riesgo = '$risk->id'" );
            if( $previous_record_risk ){
                $previous_record_risk->calificacion_riesgo = $individual_risk_lvl;
                $DB->update_record( 'talentospilos_riesg_usuario', $previous_record_risk, $bulk=false );
            }else{
                $new_user_risk = new stdClass();
                $new_user_risk->id_usuario = $ases_user_id;
                $new_user_risk->id_riesgo = $risk->id;
                $new_user_risk->calificacion_riesgo = $individual_risk_lvl;
                $DB->insert_record( 'talentospilos_riesg_usuario', $new_user_risk, $returnid=false, $bulk=false );
            }

        }elseif ( $risk->nombre == 'familiar' ){

            $previous_record_risk = $DB->get_record_sql( "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = '$ases_user_id' AND id_riesgo = '$risk->id'" );
            if( $previous_record_risk ){
                $previous_record_risk->calificacion_riesgo = $familiar_risk_lvl;
                $DB->update_record( 'talentospilos_riesg_usuario', $previous_record_risk, $bulk=false );
            }else{
                $new_user_risk = new stdClass();
                $new_user_risk->id_usuario = $ases_user_id;
                $new_user_risk->id_riesgo = $risk->id;
                $new_user_risk->calificacion_riesgo = $familiar_risk_lvl;
                $DB->insert_record( 'talentospilos_riesg_usuario', $new_user_risk, $returnid=false, $bulk=false );
            }

        }elseif ( $risk->nombre == 'academico' ){

            $previous_record_risk = $DB->get_record_sql( "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = '$ases_user_id' AND id_riesgo = '$risk->id'" );
            if( $previous_record_risk ){
                $previous_record_risk->calificacion_riesgo = $academico_risk_lvl;
                $DB->update_record( 'talentospilos_riesg_usuario', $previous_record_risk, $bulk=false );
            }else{
                $new_user_risk = new stdClass();
                $new_user_risk->id_usuario = $ases_user_id;
                $new_user_risk->id_riesgo = $risk->id;
                $new_user_risk->calificacion_riesgo = $academico_risk_lvl;
                $DB->insert_record( 'talentospilos_riesg_usuario', $new_user_risk, $returnid=false, $bulk=false );
            }

        }elseif ( $risk->nombre == 'economico' ) {
            
            $previous_record_risk = $DB->get_record_sql( "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = '$ases_user_id' AND id_riesgo = '$risk->id'" );
            if( $previous_record_risk ){
                $previous_record_risk->calificacion_riesgo = $economico_risk_lvl;
                $DB->update_record( 'talentospilos_riesg_usuario', $previous_record_risk, $bulk=false );
            }else{
                $new_user_risk = new stdClass();
                $new_user_risk->id_usuario = $ases_user_id;
                $new_user_risk->id_riesgo = $risk->id;
                $new_user_risk->calificacion_riesgo = $economico_risk_lvl;
                $DB->insert_record( 'talentospilos_riesg_usuario', $new_user_risk, $returnid=false, $bulk=false );
            }

        }elseif ( $risk->nombre == 'vida_universitaria' ) {
            
            $previous_record_risk = $DB->get_record_sql( "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = '$ases_user_id' AND id_riesgo = '$risk->id'" );
            if( $previous_record_risk ){
                $previous_record_risk->calificacion_riesgo = $vida_universitaria_risk_lvl;
                $DB->update_record( 'talentospilos_riesg_usuario', $previous_record_risk, $bulk=false );
            }else{
                
                $new_user_risk = new stdClass();
                $new_user_risk->id_usuario = $ases_user_id;
                $new_user_risk->id_riesgo = $risk->id;
                $new_user_risk->calificacion_riesgo = $vida_universitaria_risk_lvl;
                $DB->insert_record( 'talentospilos_riesg_usuario', $new_user_risk, $returnid=false, $bulk=false );
            }

        }elseif ( $risk->nombre == 'geografico' ){

            $previous_record_risk = $DB->get_record_sql( "SELECT * FROM {talentospilos_riesg_usuario} WHERE id_usuario = '$ases_user_id' AND id_riesgo = '$risk->id'" );
            if( $previous_record_risk ){
                $previous_record_risk->calificacion_riesgo = $geo_risk_lvl;
                $DB->update_record( 'talentospilos_riesg_usuario', $previous_record_risk, $bulk=false );
            }else{
                
                $new_user_risk = new stdClass();
                $new_user_risk->id_usuario = $ases_user_id;
                $new_user_risk->id_riesgo = $risk->id;
                $new_user_risk->calificacion_riesgo = $geo_risk_lvl;
                $DB->insert_record( 'talentospilos_riesg_usuario', $new_user_risk, $returnid=false, $bulk=false );
            } 
        }
    }
    return 0;
};




?>