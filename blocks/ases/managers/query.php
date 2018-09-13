<?php
require_once(dirname(__FILE__). '/../../../config.php');
require_once('MyException.php');
require_once $CFG->libdir.'/gradelib.php';
require('../../../grade/querylib.php');
require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php'; 
require_once $CFG->dirroot.'/grade/report/grader/lib.php'; 
require_once $CFG->dirroot.'/grade/lib.php'; 
require_once ('lib/student_lib.php');


/**
 * get_user_by_username()
 *
 * @param  $username Moodle username 
 * @return Array user
 */
function get_user_by_username($username){
    global $DB;
    
    $sql_query = "SELECT * FROM {user} WHERE username = '".$username."'";
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}


// function get_userById($column, $id){
//     global $DB;
    
//     $columns_str= "";
//     for($i = 0; $i < count($column); $i++){
        
//         $columns_str = $columns_str.$column[$i].",";
//     }
    
//     if(strlen($id) > 7){
//         $id = substr ($id, 0 , -5);
//     }
    
//     $columns_str = trim($columns_str,",");
//     $sql_query = "SELECT ".$columns_str.", (now() - fecha_nac)/365 AS age  FROM (SELECT *, idnumber as idn, name as namech FROM {cohort}) AS ch INNER JOIN (SELECT * FROM {cohort_members} AS chm INNER JOIN ((SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT userid, CAST(d.data as int) as data FROM {user_info_data} d WHERE d.data <> '' and fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')) AS field ON userm. id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON usermoodle.data = usuario.id) AS infouser ON infouser.id_user = chm.userid) AS userchm ON ch.id = userchm.cohortid WHERE userchm.id_user in (SELECT userid FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='estado' AND d.data ='ACTIVO') AND substr(userchm.username,1,7) = '".$id."';";
    
//     $result_query = $DB->get_record_sql($sql_query);
//     //se formatea el codigo  para eliminar la info del programa
//     if($result_query) {
//         if(property_exists($result_query,'username'))  $result_query->username = substr ($result_query->username, 0 , -5);
//     }
//     //print_r($result_query);
//     return $result_query;
// }

// function getPrograma($id){
//     global $DB;
    
//     return $DB -> get_record_sql("SELECT * FROM  {talentospilos_programa} WHERE id=".$id.";"); 
// }

// function getSchool($id){
//     global $DB;
    
//     $sql_query = "SELECT * FROM {talentospilos_facultad} WHERE id=".$id;
//     $result = $DB->get_record_sql($sql_query);
    
//     return $result;
// }

function getEnfasisFinal($idtalentos){
    global $DB;
    return $DB -> get_record_sql("SELECT * FROM (SELECT nombre AS nom_enfasis, * FROM {talentospilos_enfasis}) enf INNER JOIN {talentospilos_vocacional} voc ON enf.id = voc.final_enfasis  WHERE id_estudiante=".$idtalentos.";"); 
}

function getRiskString($val){
    if($val ==0){
        return '<span ><span style="color: red;">Sin Contacto</span></span>';
    }else if($val>0 && $val<2){
        return "Bajo";
    }else if($val>=2 && $val<3){
        return "Medio Bajo";
    }else if($val>=3 && $val<4){
        return "Medio";
    }else if($val>=4 && $val<5){
        return "Medio Alto";
    }else if($val == 5){
        return "Alto";
    }
}

function update_talentosusuario($column,$values,$id){
    global $DB;
    try{
        
        //se obtiene el id de  la tabla usario talentos
        $iduser = get_userById(array('idtalentos'),$id);
        //se define un arreglo que va a contener la info a actualizar
        $obj_updatable = array();
        //se inserta la info
        for($i = 0; $i < count($column); $i++){
            $obj_updatable[$column[$i]] = $values[$i];
        }
        $obj_updatable = (object) $obj_updatable;
        //se le asigna el id del usario a actualizar
        $obj_updatable->id = $iduser->idtalentos;
        
        return $DB->update_record('talentospilos_usuario', $obj_updatable);
    }catch(Exception $e){
       return false;
    }
}

/**
 * Función que genera el reporte de cantidad de estudiantes por cohorte por estado
 *
 * @see count_by_state($cohort){
 * @return array
 */
function count_by_state($cohort){
    
    global $DB;
    
    if($cohort == "TODOS"){
        $sql_query = "SELECT estado, COUNT(id) FROM {talentospilos_usuario} GROUP BY estado";
        $result = $DB->get_records_sql($sql_query);
    }else{
        $sql_query = "";
        $result = "";
    }
    
    print_r($result);
    die();
}
/*******
 Testing
 *******/
// count_by_state('TODOS');


/**
 * Update notes from a student
 *
 * @param   $userid: id of student
 *          $items: Array of item's id
 *          $old_n: Array of old notes
 *          $new_n: Array of new notes
 *          ---old_n[i] and new_n[i] are notes from items[i]        
 * @return A query result if is succesful and false if not.
 */
 
function update_notas($user_id, $items, $old_n, $new_n){
    global $DB;
    try{
        $sql_query = "";
        for($i = 0; $i < count($old_n); $i++){
            if(($old_n[$i] != $new_n[$i])){

                    if(are_register($user_id, $items[$i])){
                        $sql_query = "UPDATE {grade_grades} SET finalgrade = {$new_n[$i]} WHERE userid = {$user_id} AND itemid = {$items[$i]}";
                        //echo $sql_query;
                        // print_r("hola");
                        $succes = $DB->execute($sql_query);
                        // print_r($succes);
                    }else{
                        // $sql_query = "INSERT INTO {grade_grades}(userid, itemid, finalgrade) VALUES($new_n[$i], $user_id, $items[$i])";
                        $succes = $DB->insert_record('grade_grades',array('userid'=> $user_id, 'itemid' => $items[$i], 'finalgrade' => $new_n[$i]));
                        //  print_r($sql_query);
                        // print_r("hola");
                    }
                    // if(!$succes){
                    //     return $succes;
                    // }
                }
        }
        
        return $succes;
        
    }catch(Exception $e){
           
      return false;
    }
}

function are_register($userid, $item){

    global $DB;
    $sql_query = "SELECT id FROM {grade_grades} WHERE itemid = $item AND userid = $userid;";
    $succes = $DB->get_record_sql( $sql_query);
    
    
    if(isset($succes->id)){
        // print_r("def");
        $bool = true;
    }else{
        // print_r("no def");
        $bool =  false;
    }
    // print($bool);
    return $bool;
}
    
// are_register(171,455);

/** 
 **************************************
 Funciones reculcular notas finales
 **************************************
**/

function make_categories($notas, $categs, $porcentajes){
    
    $categorias = array();
    $categoria = new stdClass();
    $ultimo = count($notas)-1;
    //Se llena el arreglo de categorias
    //Cada categoria tiene un id y un arreglo de items
    for($i = 0; $i < count($notas); $i++){
        
        if($i>=1 && $categs[$i] != $categs[$i - 1]){
            array_push($categorias, $categoria);
            $categoria->id = "";
            $categoria->items = array();
            $categoria->id = $categs[$i];
            $item = new stdClass();
            $item->nota = $notas[i];
            $item->porcentaje = $porcentajes[i];
            array_push($categoria->items, $item);    
        }else if($i != $ultimo){
            $categoria->id = $categs[$i];
            $item = new stdClass();
            $item->nota = $notas[i];
            $item->porcentaje = $porcentajes[i];
            array_push($categoria->items, $item);
            
        }
        
    }
    
    
    
    return $categorias;
}

function extract_notes($categorias){
    $notas = array();
    
    for($i = 0; $i < count($categorias); $i++){
        for($j = 0; $j < count($categorias[$i]->items);$j++){
            array_push($notas, $categorias[$i]->items[$j]->nota);
        }
    }
    
    return $notas;
}

function recalculate_percentages(&$categorias){
    //se recorren las categorias
    for($i = 0; $i < count($categorias); $i++){
        //numero de items calificados
        $items_calif = 0;
        //porcentaje de items que estan sin calificar
        $porcentaje_sin_nota = 0;
        //porcentaje que se debe repartir a cada item: $porcentaje_a_repart = $porcentaje_sin_nota / $items_calif
        $porcentaje_a_repart = 0;
        
        //se recorren los items para obtener los datos
        for($j = 0; $j < count($categorias[$i]->items)-1; $j++){
            if($categorias[$i]->items[$j]->nota == '-'){
               $porcentaje_sin_nota += $categorias[$i]->items[$j]->porcentaje;
               $categorias[$i]->items[$j]->porcentaje = 0;
            }else{
               $items_calif++; 
            }
        }
        
        $porcentaje_a_repart = $porcentaje_sin_nota / $items_calif;
        
        //se recorren los items modificando los porcentajes
        for($k = 0; $k < count($categorias[$i]->items); $k++){
           if($categorias[$i]->items[$k]->nota != '-'){
                $categorias[$i]->items[$k]->porcentaje += $porcentaje_a_repart;
           }
        }
    }
}

function recalculate_totals($notas, $categs, $porcentajes){
    
    $categorias = make_categories($items, $notas, $categs, $porcentajes);
    
    recalculate_percentages($categorias);//REVISAR SI ES REALMENTE NECESARIO
    
    for($i = 0; $i < count($categorias); $i++){
        $total = 0;
        $ultimo = count($categorias[$i]->items);
        for($j = 0; $j < count($categorias[$i]->items); $j++){
            
            if($j == $ultimo){
                $categorias[$i]->items[$j]->nota = total;
            }else{
                if($categorias[$i]->items[$j]->nota != '-'){
                    $total += ($categorias[$i]->items[$j]->nota*$categorias[$i]->items[$j]->porcentaje/100);
                }
            }
        }
    }
    
    $new_notas = extract_notes($categorias);
    
    return $new_notas;
}
//PRUEBA
// $notas_prueba = array();
//$categorias_prueba = array();
//$porcentajes_prueba = array();

/** 
 *****************************
 Funciones gestión de usuarios
 *****************************
**/

function get_role_user($id_moodle, $idinstancia)
{
    global $DB;
    $current_semester = get_current_semester(); 
    $sql_query = "select nombre_rol, rol.id as rolid from {talentospilos_user_rol} as ur inner join {talentospilos_rol} as rol on rol.id = ur.id_rol where  ur.estado = 1 AND ur.id_semestre =".$current_semester->max."  AND id_usuario = ".$id_moodle." AND id_instancia =".$idinstancia.";";
    return $DB->get_record_sql($sql_query);
}

function get_permisos_role($idrol,$page){
    global $DB;
    
    $fun_str ="";
    switch ($page) {
        case "ficha":
            $fun_str = " AND  substr(fun.nombre_func,1,2) = 'f_';";
            break;
        case 'archivos':
            $fun_str = " AND fun.nombre_func = 'carga_csv';";
            break;
        case 'index':
            $fun_str = " AND fun.nombre_func = 'reporte_general';";
            break;
        case 'gestion_roles':
            $fun_str = " AND fun.nombre_func = 'gestion_roles';";
            break;
        case 'v_seguimiento_pilos':
            $fun_str = "AND fun.nombre_func = 'v_seguimiento_pilos';";
            break;
            case 'v_general_reports':
            $fun_str = "AND fun.nombre_func = 'v_general_reports';";
            break;
        default:
            // code...
            break;
    }
    
    
    $sql_query = "select pr.id as prid , fun.id as funid,* from {talentospilos_permisos_rol} as pr inner join {talentospilos_funcionalidad} as fun on id_funcionalidad = fun.id inner join {talentospilos_permisos} p  on id_permiso = p.id inner join {talentospilos_rol} r on r.id = id_rol   where id_rol=".$idrol.$fun_str;
    //print_r($sql_query);
    $result_query = $DB->get_records_sql($sql_query);
    //print_r(json_encode($result_query));
    
    return $result_query;
}
//get_permisos_role(221,'role');

/**
 * Función que asigna un rol a un usuario
 *
 * @see assign_role_user($username, $id_role, $state, $semester, $username_boss){
 * @return Integer
 */
 
 function assign_role_user($username, $role, $state, $semester,$idinstancia, $username_boss = null){
     
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username='$username'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
     
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
    $id_role = $DB->get_record_sql($sql_query);
    
    $id_semester = get_current_semester();
    
    if($role == "monitor_ps")
    {
        $sql_query = "SELECT * FROM {user} WHERE username='$username_boss'";
        $id_boss = $DB->get_record_sql($sql_query);    
    }
    else{
        $id_boss = null;
    }
        
    $array = new stdClass;
    $array->id_rol = $id_role->id;
    $array->id_usuario = $id_user_moodle->id;
    $array->estado = $state;
    $array->id_semestre = $id_semester->max;
    $array->id_jefe = $id_boss;
    $array->id_instancia= $idinstancia;
    
    //print_r($array);
    
    $insert_user_rol = $DB->insert_record('talentospilos_user_rol', $array, false);
        
    if($insert_user_rol){
        return 1;
    }
    else{
        return 2;
    }
}

/**
 * Función que elimina un registro grupal tanto en 
 * las tablas {talentospilos_seg_estudiante}
 * {talentospilos_seguimientos} dado un id de seguimiento.
 * @see delete_seguimiento_grupal($id)
 * @return 0 o 1
 */
function delete_seguimiento_grupal($id){
    
    global $DB;

    $sql_query = "DELETE FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id'";
    $success = $DB->execute($sql_query);
    $sql_query = "DELETE FROM {talentospilos_seguimiento} WHERE id = $id";
    $success = $DB->execute($sql_query);
    return $success;
}


/**
 * Función que revisa si un usuario tiene un rol asignado
 *
 * @see checking_role($username)
 * @return Boolean
 */
 
// function checking_role($username, $idinstancia){
     
//     global $DB;
     
//     $sql_query = "SELECT id FROM {user} WHERE username = '$username'";
//     $id_moodle_user = $DB->get_record_sql($sql_query);
    
//     $semestre =  get_current_semester();
    
//     $sql_query = "SELECT ur.id_rol as id_rol , r.nombre_rol as nombre_rol, ur.id as id, ur.id_usuario, ur.estado FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol WHERE ur.id_usuario = ".$id_moodle_user->id." and ur.id_semestre = ".$semestre->max." and ur.id_instancia=".$idinstancia.";";
//     $role_check = $DB->get_record_sql($sql_query); 
    
//     return $role_check;
// }

/**
 * Función que actualiza el rol de un usuario en particular
 *
 * @see update_role_user($id_moodle_user, $id_role, $state, $id_semester, $username_boss){
 * @return Entero
 */
// function update_role_user($username, $role, $idinstancia, $state = 1, $semester = null, $username_boss = null){
    
//     global $DB;
    
//     $sql_query = "SELECT id FROM {user} WHERE username='$username'";
//     $id_user_moodle = $DB->get_record_sql($sql_query);
     
//     $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
//     $id_role = $DB->get_record_sql($sql_query);
    
//     $sql_query ="select max(id) as id from {talentospilos_semestre};";
//     $id_semester = $DB->get_record_sql($sql_query);
    
//     $array = new stdClass;
//     $id_boss = null;
//     if($username_boss != null){
//         $sql_query = "SELECT * FROM {user} WHERE username='$username_boss'";
//         $result = $DB->get_record_sql($sql_query);
//         $id_boss =  $result->id;
//     }
    
//     $array->id_rol = $id_role->id;
//     $array->id_usuario = $id_user_moodle->id;
//     $array->estado = $state;
//     $array->id_semestre = $id_semester->id;
//     $array->id_jefe = $id_boss;
//     $array->id_instancia = $idinstancia;
    
//     $result = 0;
    
//     if ($checkrole = checking_role($username, $idinstancia)){
        
//         if ($checkrole->nombre_rol == 'monitor_ps'){
//             $whereclause = "id_monitor = ".$id_user_moodle->id;
//             $DB->delete_records_select('talentospilos_monitor_estud',$whereclause);
            
//         }else if($checkrole->nombre_rol == 'profesional_ps'){ 
//             $whereclause = "id_usuario = ".$id_user_moodle->id;
//             $DB->delete_records_select('talentospilos_usuario_prof',$whereclause);
//         } 
        
        
//         $array->id = $checkrole->id;
//         $update_record = $DB->update_record('talentospilos_user_rol', $array);
//         if($update_record){
//             $result = 3;
//         }else{
//             $result = 4;
//         }
//     }else{
//         $insert_record = $DB->insert_record('talentospilos_user_rol', $array);
//         if($insert_record){
//             $result =1;
//         }else{
//             $result = 2;
//         }
//     }

//     return $result;
// }


/**
 * Función que actualiza el rol de un usuario practicante_ps
 *
 * @see actualiza_rol_practicante($id_moodle_user, $id_role, $state, $id_semester, $username_boss){
 * @return Entero
 */
// function actualiza_rol_practicante($username, $role, $idinstancia, $state = 1, $semester = null, $id_boss = null){
    
//     global $DB;
    
//     $sql_query = "SELECT id FROM {user} WHERE username='$username'";
//     $id_user_moodle = $DB->get_record_sql($sql_query);
     
//     $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='$role';";
//     $id_role = $DB->get_record_sql($sql_query);
    
//     $sql_query ="select max(id) as id from {talentospilos_semestre};";
//     $id_semester = $DB->get_record_sql($sql_query);
    
//     $array = new stdClass;

//     $array->id_rol = $id_role->id;
//     $array->id_usuario = $id_user_moodle->id;
//     $array->estado = $state;
//     $array->id_semestre = $id_semester->id;
//     $array->id_jefe = (int)$id_boss;
//     $array->id_instancia = $idinstancia;
    
//     $result = 0;
    
//     if ($checkrole = checking_role($username, $idinstancia)){
        
//         if ($checkrole->nombre_rol == 'monitor_ps'){
//             $whereclause = "id_monitor = ".$id_user_moodle->id;
//             $DB->delete_records_select('talentospilos_monitor_estud',$whereclause);
            
//         }else if($checkrole->nombre_rol == 'profesional_ps'){ 
//             $whereclause = "id_usuario = ".$id_user_moodle->id;
//             $DB->delete_records_select('talentospilos_usuario_prof',$whereclause);
//         } 
        
        

//         $array->id = $checkrole->id;
//         $update_record = $DB->update_record('talentospilos_user_rol', $array);
//         //echo $update_record;
//         if($update_record){
//             $result = 3;
//         }else{
//             $result = 4;
//         }
//     }else{
//         $insert_record = $DB->insert_record('talentospilos_user_rol', $array);
//         if($insert_record){
//             $result =1;
//         }else{
//             $result = 2;
//         }
//     }



//     return $result;
// }


/*
*********************************************************************************
FUNCIONES RELACIONADAS CON EL ROL PROFESIONAL PSICOEDUCATIVO
*********************************************************************************
*/

/**
 * Función que asigna un tipo de profesional a un usuario con rol profesional psicoeducativo
 *
 * @see assign_professional_user($id_user, $professional)
 * @return Integer
 */
 
 function assign_professional_user($id_user, $professional){
    
    global $DB;
    
    $sql_query = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
    $id_professional = $DB->get_record_sql($sql_query);
    
    $record_professional_type = new stdClass;
    $record_professional_type->id_usuario = $id_user;
    $record_professional_type->id_profesional = $id_professional->id;
    
    $insert_record = $DB->insert_record('talentospilos_usuario_prof', $record_professional_type, true);
    
    return $insert_record;
 }
 
//  assign_professional_user(221, 'psicologo');
 
 /**
 * Función que actualiza en l
 *
 * @see assign_professional_user($id_user, $professional)
 * @return Integer
 */
 
 
 /**
 * Función que actualiza el tipo de profesional a un usuario con rol profesional psicoeducativo
 *
 * @see update_professional_user($id_user, $professional)
 * @return Integer
 */
 
 // function update_professional_user($id_user, $professional){
     
 //    global $DB;
    
 //    $sql_query = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
 //    $id_professional = $DB->get_record_sql($sql_query);
    
 //    if($id_professional){
 //        $sql_query = "SELECT id FROM {talentospilos_usuario_prof} WHERE id_usuario = '$id_user'";
 //        $id_to_update = $DB->get_record_sql($sql_query);
    
 //        $record_professional_type = new stdClass;
 //        $record_professional_type->id = $id_to_update->id;
 //        $record_professional_type->id_profesional = $id_professional->id;
    
 //        $update_record = $DB->update_record('talentospilos_usuario_prof', $record_professional_type);
    
 //        return $update_record;
 //    }else{
 //        return false;
 //    }
    
 // }
 
 // Testing
 // update_professional_user(221, 'trabajador_social');
 
/**
 * Función que administra el rol profesional psicoeducativo
 *
 * @see manage_role_profesional_ps($username, $role, $professional)
 * @return booleano confirmando el éxito de la operación
 */

// function manage_role_profesional_ps($username, $role, $professional, $idinstancia, $state = 1)
// {
//     global $DB;
    
//     try{
//         // Select object user
//         $sql_query = "SELECT * FROM {user} WHERE username ='$username';";
//         $object_user = $DB->get_record_sql($sql_query);

//         // Current role
//         pg_query("BEGIN") or die("Could not start transaction\n");
//         $sql_query = "SELECT id_rol, nombre_rol FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} r ON r.id = ur.id_rol WHERE id_usuario = ".$object_user->id." AND ur.id_instancia=".$idinstancia." AND  id_semestre = (SELECT max(id) FROM {talentospilos_semestre});";
//         $id_current_role = $DB->get_record_sql($sql_query);
//         pg_query("COMMIT") or die("Transaction commit failed\n");

//         $id_current_semester = get_current_semester();

//         if(empty($id_current_role)){

//             // Start db transaction
//             pg_query("BEGIN") or die("Could not start transaction\n");

//             assign_role_user($username, $role, 1, $id_current_semester->max, $idinstancia, null);
            
//             assign_professional_user($object_user->id, $professional);
            
//             // End db transaction
//             pg_query("COMMIT") or die("Transaction commit failed\n");
        
//         }
//         else{
//             //en la consulta se hace tiene en cuenta el semestre concurrente
//             $sql_query = "SELECT * FROM {talentospilos_user_rol} userrol INNER JOIN {talentospilos_usuario_prof} userprof 
//                             ON userrol.id_usuario = userprof.id_usuario INNER JOIN {talentospilos_rol} rol ON rol.id = userrol.id_rol  WHERE userprof.id_usuario = ".$object_user->id." AND userrol.id_semestre=".$id_current_semester->max." AND userrol.id_instancia = ".$idinstancia.";";
//             $object_user_role = $DB->get_record_sql($sql_query);
            
//             if($object_user_role){
//                 // Incluir el estado
                
//                 $sql_query = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = '$professional'";
//                 $new_id_professional_type = $DB->get_records_sql($sql_query);
                
//                 foreach ($new_id_professional_type as $n){
//                     if($object_user_role->id_profesional != $n->id){
//                         update_professional_user($object_user->id, $professional);
//                     }
//                 }
                
//                 //se actualiza el estado en caso de que se hjaya desactivado anteriormente
//                 update_role_user($username,$role,$idinstancia, $state);
//                 if($state == 0){
//                     $whereclause = "id_usuario = ".$object_user->id;
//                     $DB->delete_records_select('talentospilos_usuario_prof',$whereclause);
//                 }
               
//             }else{
                
//                 // caso monitor
                
                
//                 // Start db transaction
//                 pg_query("BEGIN") or die("Could not start transaction\n");
                
//                 if($id_current_role->nombre_rol == 'monitor_ps'){ 
//                     $whereclause = "id_monitor = ".$object_user->id;
//                     $DB->delete_records_select('talentospilos_monitor_estud',$whereclause);
//                 } 
                
//                 update_role_user($username, $role,$idinstancia, $state, $id_current_semester->max, null);
                
//                 assign_professional_user($object_user->id, $professional);
                
//                 // End db transaction
//                 pg_query("COMMIT") or die("Transaction commit failed\n");
//             }
            
//         }
//     //print_r(1);
//     return 1;
        
//     }catch(Exception $e){
//         return "Error al gestionar los permisos profesional ".$e->getMessage();
//     }
    
// }

// Testing
// manage_role_profesional_ps('1124153-3743', 'profesional_ps', 'terapeuta_ocupacional', 534);
//manage_role_profesional_ps('1673003-1008', 'profesional_ps', 'psicologo');

/**
 * Función que asigna el rol profesional psicoeducativo y el tipo de profesional 
 *
 * @see update_role_profesional_ps($username, $role, $professional)
 * @return booleano confirmando el éxito de la operación
 */

function assign_role_professional_ps($username, $role, $state = 1, $semester, $username_boss = null, $professional)
{
    global $DB;
    
    $sql_query = "SELECT id FROM {user} WHERE username ='$username';";
    $id_user = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario = '$id_user->id';";
    $id_current_role = $DB->get_record_sql($sql_query);
    
    if(empty($id_current_role)){
        
        // Start db transaction
        pg_query("BEGIN") or die("Could not start transaction\n");
        
        assign_role_user($username, $role, $state, $semester->max, null);
        
        assign_professional_user($id_user->id, $professional);
        
        // End db transaction
        pg_query("COMMIT") or die("Transaction commit failed\n");
        
        //print_r("funcinoa");
    }
}

function getProfessionals($id = null, $idinstancia){
    global $DB;
    // $sql_query = "SELECT username, firstname, lastname, us.id, prof.nombre_profesional 
    //               FROM {user} us INNER JOIN  {talentospilos_usuario_prof} p 
    //                                     ON p.id_usuario = us.id INNER JOIN {talentospilos_profesional} prof on prof.id = p.id_profesional 
    //                              INNER JOIN {talentospilos_user_rol} ur ON ur.id_usuario = us.id WHERE ur.id_instancia =".$idinstancia;
    
    $sql_query = "SELECT username, firstname, lastname, id 
                  FROM {user} us  WHERE id IN 
                  (SELECT id_usuario FROM {talentospilos_user_rol} ur WHERE id_rol IN (3,7) AND ur.id_instancia =".$idinstancia.")";
    
    if($id != null) $sql_query .= " AND us.id =".$id.";";
    return $DB->get_records_sql($sql_query);
}

// /**
//  * Función que retorna el nombre del profesional asignado a un estudiante
//  *
//  * @see get_assigned_professional($id_student)
//  * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
//  * @return String Nombre completo del profesional asignado
//  */
//  function get_assigned_professional($id_student){
     
//      global $DB;
     
//      $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
//      $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
     
//      if($id_monitor){

//          $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor.";";
//          $id_practicante = $DB->get_record_sql($sql_query)->id_jefe; 
         
//          $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_practicante.";";
//          $id_professional = $DB->get_record_sql($sql_query)->id_jefe;
         
//          $sql_query = "SELECT firstname, lastname FROM {user} WHERE id = ".$id_professional.";";
//          $fullname_professional = $DB->get_record_sql($sql_query)->firstname." ".$DB->get_record_sql($sql_query)->lastname;
         
//          if($fullname_professional == ""){
//                 $fullname_professional = "No registra"; 
//          }
//      }else{
//          $fullname_professional = "No registra";
//      }
     
//      return $fullname_professional;
//  }
//  get_assigned_professional(907);

/**
 * Función que retorna el id del profesional asignado a un estudiante
 *
 * @see get_id_assigned_professional($id_student)
 * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return int Returns professional id or 0 if the student does not have a professional assigned
 */
 
 function get_id_assigned_professional($id_student){
     
    global $DB;
     
    $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
    $id_monitor = $DB->get_record_sql($sql_query);
    
    $id_professional = "";
    
    if($id_monitor){

        $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor->id_monitor.";";
        $id_practicante = $DB->get_record_sql($sql_query)->id_jefe; 
        
        $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_practicante.";";
        $id_professional = $DB->get_record_sql($sql_query)->id_jefe;

        if($id_professional == ""){
            $id_professional = 0;
        }
    }else{
        $id_professional = 0;
    }
    
    return $id_professional;
 }
 
//  /**
//  * Función que retorna el practicante asignado a un estudiante
//  *
//  * @see get_assigned_pract($id_student)
//  * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
//  * @return String Nombre completo del practicante asignado
//  */

// function get_assigned_pract($id_student){
     
//      global $DB;
     
//      $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
//      $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
     
//      if($id_monitor){
//          $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor.";";
//          $id_practicante = $DB->get_record_sql($sql_query)->id_jefe; 
         
//          $sql_query = "SELECT CONCAT(firstname, CONCAT(' ', lastname)) AS fullname FROM {user} WHERE id = ".$id_practicante;
//          $fullname_pract = $DB->get_record_sql($sql_query);
//      }else{
//          $fullname_pract = "No registra";
//      }
//     //  print_r($fullname_pract);
//      return $fullname_pract->fullname;
// }

// get_assigned_pract(710);

/**
 * Función que retorna el id de practicante asignado a un estudiante
 *
 * @see get_id_assigned_pract($id_student)
 * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return String Nombre completo del practicante asignado
 */

 function get_id_assigned_pract($id_student){
     global $DB;
     
     $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
     $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
     
     if($id_monitor){
         $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = ".$id_monitor.";";
         $id_practicante = $DB->get_record_sql($sql_query)->id_jefe; 
         
         if($id_practicante == ""){
             $id_practicante = 0;
         }
         
     }else{
         $id_practicante = 0;
     }
    //  print_r($fullname_pract);
     return $id_practicante;     
 }

//  /**
//  * Función que retorna el practicante asignado a un estudiante
//  *
//  * @see get_assigned_monitor($id_student)
//  * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
//  * @return String Nombre completo del monitor asignado
//  */

// function get_assigned_monitor($id_student){
     
//     global $DB;
    
//     $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =".$id_student.";";
//     $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;
    
//     if($id_monitor){
//         $sql_query = "SELECT CONCAT(firstname, lastname) AS fullname FROM {user} WHERE id = ".$id_monitor;
//         $fullname_monitor = $DB->get_record_sql($sql_query)->fullname;
//     }else{
//         $fullname_monitor = "No registra";
//     }
    
//     return $fullname_monitor;
// }

 /**
 * Función que retorna el monitor asignado a un estudiante
 *
 * @see get_assigned_monitor($id_student)
 * @parameters $id_student int Id relacionado en la tabla {talentospilos_usuario}
 * @return String Nombre completo del practicante asignado
 */
/*
*********************************************************************************
FIN FUNCIONES RELACIONADAS CON EL ROL PROFESIONAL PSICOEDUCATIVO
*********************************************************************************
*/

// function update_role_monitor_ps($username, $role, $array_students, $boss,$idinstancia,$state = 1)
// {
//     global $DB;
    
//     $sql_query = "SELECT id FROM {user} WHERE username ='$username';";
//     $id_moodle = $DB->get_record_sql($sql_query);
    
//     //se consulta el id del semestre actual
//     $sql_query = "select max(id) as id_semestre from {talentospilos_semestre};";
//     $semestre = $DB->get_record_sql($sql_query);
    
//     $sql_query = "SELECT rol.id as id, rol.nombre_rol as nombre_rol, ur.id as id_user_rol, id_usuario FROM {talentospilos_user_rol} ur INNER JOIN {talentospilos_rol} rol ON rol.id = ur.id_rol  WHERE id_usuario = ".$id_moodle->id." and id_semestre =".$semestre->id_semestre." AND ur.id_instancia=".$idinstancia.";";
//     $id_rol_actual = $DB->get_record_sql($sql_query);
    
    
//     //se consulta el id del rol
//     $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol='monitor_ps';";
//     $id_role = $DB->get_record_sql($sql_query);
    
//     //se consulta el jefe
//     $bossid = null;
//     if(intval($boss)){
//         if (getProfessionals($boss, $idinstancia)) $bossid = $boss;
//     }

    

//     $object_role = new stdClass;
//     $object_role->id_rol = $id_role->id;
//     $object_role->id_usuario = $id_moodle->id;
//     $object_role->estado = $state;
//     $object_role->id_semestre = $semestre->id_semestre;
//     $object_role->id_jefe = $bossid;
//     $object_role->id_instancia = $idinstancia;

//     if(empty($id_rol_actual)){
//         $insert_user_rol = $DB->insert_record('talentospilos_user_rol', $object_role, true);
        
//         if($insert_user_rol){
//             //procesar el array de estudiantes
//             $check_assignment = monitor_student_assignment($username, $array_students,$idinstancia);
//             if($check_assignment == 1){
//                 return 1;
//             }else{
//                 return $check_assignment;
//             }
            
//         }
//         else{
            
//             return 2;
//         }
        
//     }else{
//         // if ($id_rol_actual->nombre_rol != 'monitor_ps'){
//         //     $object_role->id = $id_rol_actual->id_user_rol;
//         //     $DB->update_record('talentospilos_user_rol',$object_role);
//         // }
//         //print_r($id_rol_actual);
//         if($id_rol_actual->nombre_rol == 'profesional_ps'){
            
//             $whereclause = "id_usuario = ".$id_rol_actual->id_usuario;
//             $DB->delete_records_select('talentospilos_usuario_prof',$whereclause);
//         } 
        
//         $object_role->id = $id_rol_actual->id_user_rol;
//         $DB->update_record('talentospilos_user_rol',$object_role);
        
//         $check_assignment = monitor_student_assignment($username, $array_students, $idinstancia);
        
//         if($check_assignment ==1){
//             return 3;
//         }else{
//             return $check_assignment;
//         }
        
//     }
// }

/**
 * Función que elimina el ultimo registro de una tabla
 *
 * @see delete_last_register($table_name)
 * @return booleano confirmando el éxito de la operación
 */

function delete_last_register($table_name){
    
    global $DB;
    
    $sql_query = "SELECT MAX(id) FROM {$table_name}";
    $max_id = get_record_sql($sql_query);
    
    $sql_query = "DELETE FROM {$table_name} WHERE id = $max_id->max";
    $success = $DB->execute($sql_query);
    
    return $success;
}


/**
 * Función que actualiza el tipo de profesional de un usuario
 *
 * @see update_professional_type()
 * @return booleano confirmando el éxito de la operación
 */
 
 function update_professional_type($id_user, $name_prof)
 {
     global $DB;
     
     $sql_query = "SELECT id FROM {talentospilos_profesional} WHERE nombre_profesional = $name_prof";
     $id_profesional = $DB->get_record_sql($sql_query);
     
     $object = new stdClass();
     $object->id_usuario = $id_user;
     $object->id_profesional = $id_profesional->id;
     
     $update = $DB->update_record('talentospilos_usuario_prof', $object);
     
     return $update;
 }
 
 /**
 * Función que verifica si un registro existe en la tabla usuario_profesional
 *
 * @see record_check_professional($id_user, $id_professional)
 * @return boolean
 */
 
 function record_check_professional($id_user)
 {
     global $DB;
     
     $sql_query = "SELECT id FROM {talentospilos_usuario_prof} WHERE id_usuario = $id_user";
     $check = $DB->get_record_sql($sql_query);
     
     //print_r(empty($check));
 }




/**
 * Función que relaciona a un conjunto de estudiantes con un monitor
 *
 * @see monitor_student_assignment()
 * @return booleano confirmando el éxito de la operación
 */
function monitor_student_assignment($username_monitor, $array_students, $idinstancia)
{
    global $DB;
    
    try{
        $sql_query = "SELECT id FROM {user} WHERE username = '$username_monitor'";
        $idmonitor = $DB->get_record_sql($sql_query);
        
        $first_insertion_sql = "SELECT MAX(id) FROM {talentospilos_monitor_estud};";
        $first_insertion_id = $DB->get_record_sql($first_insertion_sql);
        
        $insert_record = "";
        $array_errors = array();
        $hadErrors = false; 
        
        foreach($array_students as $student)
        {
            
                //$sql_query = "SELECT id FROM {user} WHERE username= '$student'";
                //$studentid = $DB->get_record_sql($sql_query);
                
                //se obtiene el id en la tabla de {talentospilos_usuario} del estudiante
                $studentid = get_userById(array('*'),$student);
                
                if($studentid){
                    //se valida si el estudiante ya tiene asignado un monitor
                    $sql_query = "SELECT u.id as id, username,firstname, lastname FROM {talentospilos_monitor_estud} me INNER JOIN {user} u  ON  u.id = me.id_monitor WHERE me.id_estudiante =".$studentid->idtalentos."";
                    $hasmonitor = $DB->get_record_sql($sql_query);
                
                    if(!$hasmonitor){
                        $object = new stdClass();
                        $object->id_monitor = $idmonitor->id;
                        $object->id_estudiante = $studentid->idtalentos;
                        $object->id_instancia = $idinstancia;
              
                        $insert_record = $DB->insert_record('talentospilos_monitor_estud', $object, true);
                
                        if(!$insert_record){
                            $hadErrors = true; 
                            array_push($array_errors, "Error al asignar el estudiante ".$student." al monitor (monitor_student_assignment). Operaciòn de asignaciòn del estudiante anulada.");
                            
                        }
                
                    }elseif($hasmonitor->id != $idmonitor->id){
                        $hadErrors = true; 
                        array_push($array_errors,"El estudiante con codigo ".$student." ya tiene asigando el monitor: ".$hasmonitor->username."-".$hasmonitor->firstname."-".$hasmonitor->lastname.". Operaciòn de asignaciòn del estudiante anulada.");
                    }
                }else{
                    $hadErrors = true; 
                    array_push($array_errors,"El estudiante con codigo '".$student."' no se encontro en la base de datos. Operaciòn de asignaciòn del estudiante anulada.");
                } 
        }
        
        if(!$hadErrors){
            return 1;
        }else{
            $message = "";
            foreach ($array_errors as $error){
                $message .= "*".$error."<br>";
            }
            throw new MyException("Rol Actualizado con los siguientes inconvenientes:<br><hr>".$message);
        }
        
    
    }
    catch(MyException $ex){
        return $ex->getMessage();
    }
    catch(Exception $e){
        $error = "Error en la base de datos(monitor_student_assignment).".$e->getMessage();
        echo $error;
    }
}

/**
 * dropStudentofMonitor
 * 
 * Elimina de base de datos la relacion monitor - estudiante
 * @param $monitor [string] username en moodle del ususario del monitor 
 * @param $student [string] username en moodle del usuario studiante
 * @return void
 **/
 
// function dropStudentofMonitor($monitor,$student){
//     global $DB;
    
//     //idmonitor
//     $sql_query = "SELECT id FROM {user} WHERE username = '$monitor'";
//     $idmonitor = $DB->get_record_sql($sql_query);
    
//     //se obtiene el id en la tabla de {talentospilos_usuario} del estudiante
//     $studentid = get_userById(array('idtalentos'),$student);

//     //where clause
//     $whereclause = "id_monitor = ".$idmonitor->id." AND id_estudiante =".$studentid->idtalentos;
//     return $DB->delete_records_select('talentospilos_monitor_estud',$whereclause);

// }

// function changeMonitor ($oldMonitor, $newMonitor){
//     global $DB;
    
//     try{
        
//         $sql_query ="SELECT  id from {talentospilos_monitor_estud} where id_monitor =".$oldMonitor;
//         $result = $DB->get_records_sql($sql_query);
        
//         foreach ($result as $row){
//             $newObject = new stdClass();
//             $newObject->id = $row->id;
//             $newObject->id_monitor = $newMonitor;
//             $DB->update_record('talentospilos_monitor_estud', $newObject);
//         }
        
//         return 1;
        
//     }catch(Exception $e){
//         return $e->getMessage();
//     }
    
// }

/**
 * Función que retorna los usuarios en el sistema
 *
 * @see get_users_role()
 * @return Array 
 */
 
// function get_users_role($idinstancia)
// {
//     global $DB;
    
//     $array = Array();
    
//     $sql_query = "SELECT {user}.id, {user}.username, {user}.firstname, {user}.lastname, {talentospilos_rol}.nombre_rol FROM {talentospilos_user_rol} INNER JOIN {user} ON {talentospilos_user_rol}.id_usuario = {user}.id 
//                                 INNER JOIN {talentospilos_rol} ON {talentospilos_user_rol}.id_rol = {talentospilos_rol}.id INNER JOIN {talentospilos_semestre} s ON  s.id = {talentospilos_user_rol}.id_semestre 
//                                 WHERE {talentospilos_user_rol}.estado = 1 AND {talentospilos_user_rol}.id_instancia=".$idinstancia." AND s.id = (SELECT MAX(id) FROM {talentospilos_semestre});";
//     $users_array = $DB->get_records_sql($sql_query);
    
//     foreach ($users_array as $user){
//         $user->button = "<a id = \"delete_user\"  ><span  id=\"".$user->id."\" class=\"red glyphicon glyphicon-remove\"></span></a>";
//         array_push($array, $user);
//     }
//     return $array;
// }

/** 
 ***********************************
 Fin consultas gestión de  usuarios
 ***********************************
**/

/** 
 **********************
 Consultas asistencias 
 **********************
**/

/**
 * Función que retorna un arreglo con las faltas justificadas e injustificadas
 * de cada estudiante del plan Talentos Pilos
 *
 * @see general_attendance()
 * @return array de objetos con las faltas justificas e injustificadas de un estudiante
 */
function general_attendance($programa, $semestre)
{
    global $DB;

    $user_report = array();
    
    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre ='$semestre';";
    $id_semestre = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = '$programa'";
    $id_program = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT SUBSTRING(tuser.username FROM 1 FOR 7) AS codigoestudiante, tuser.lastname AS apellidos, tuser.firstname AS nombres, count(tattendancestatuses.description) AS faltasinjustificadas
                  FROM {user_info_field} AS tinfofield INNER JOIN {user_info_data} AS tinfodata ON tinfodata.fieldid = tinfofield.id
                                                       INNER JOIN {user} AS tuser ON tuser.id = tinfodata.userid
                                                       INNER JOIN {attendance_log} AS tattendancelog ON tuser.id = tattendancelog.studentid
                                                       INNER JOIN {attendance_statuses} AS tattendancestatuses ON tattendancestatuses.id = tattendancelog.statusid
                  WHERE tinfofield.shortname = 'idprograma' AND tinfodata.data = '$id_program->id' 
                                                            AND tattendancestatuses.description = 'Falta injustificada'
                                                            AND to_timestamp(tattendancelog.timetaken) > (SELECT fecha_inicio::DATE - INTERVAL '30 days' FROM {talentospilos_semestre} WHERE id = $id_semestre->id) 
                                                            AND to_timestamp(tattendancelog.timetaken) < (SELECT fecha_fin::DATE FROM {talentospilos_semestre} WHERE id = $id_semestre->id)
                  GROUP BY codigoestudiante, apellidos, nombres;";
                 
    $attendance_report = $DB->get_records_sql($sql_query, null);
    
    // print_r($attendance_report);
    
    $sql_query = "SELECT SUBSTRING(tuser.username FROM 1 FOR 7) AS codigoestudiante, tuser.lastname AS apellidos, tuser.firstname AS nombres, count(tattendancestatuses.description) AS faltasjustificadas
                  FROM {user_info_field} AS tinfofield INNER JOIN {user_info_data} AS tinfodata ON tinfodata.fieldid = tinfofield.id
                                                       INNER JOIN {user} AS tuser ON tuser.id = tinfodata.userid
                                                       INNER JOIN {attendance_log} AS tattendancelog ON tuser.id = tattendancelog.studentid
                                                       INNER JOIN {attendance_statuses} AS tattendancestatuses ON tattendancestatuses.id = tattendancelog.statusid
                  WHERE tinfofield.shortname = 'idprograma' AND tinfodata.data = '$id_program->id' 
                                                            AND tattendancestatuses.description = 'Falta justificada'
                                                            AND to_timestamp(tattendancelog.timetaken) > (SELECT fecha_inicio::DATE - INTERVAL '30 days' FROM {talentospilos_semestre} WHERE id = $id_semestre->id) 
                                                            AND to_timestamp(tattendancelog.timetaken) < (SELECT fecha_fin::DATE FROM {talentospilos_semestre} WHERE id = $id_semestre->id)
                  GROUP BY codigoestudiante, apellidos, nombres;";
                
    $attendance_report_justified = $DB->get_records_sql($sql_query, null);
    
    foreach ($attendance_report as $report)
    {
        $count = 0;
        foreach($attendance_report_justified as $justified)
        {
            if($report->codigoestudiante == $justified->codigoestudiante)
            {
                $report->faltasjustificadas = $justified->faltasjustificadas;
                unset($attendance_report_justified[$justified->codigoestudiante]);
                $count = $count + 1;
                break;
            }
        }
        if($count == 0)
        {
            $report->faltasjustificadas = 0;
        }
        
    }
    foreach($attendance_report_justified as $justified)
    {
        $justified->faltasinjustificadas = 0;
    }

    $result = array_merge($attendance_report, $attendance_report_justified);
    
    foreach($result as $val)
    {
        $val->totalfaltas = (int) $val->faltasjustificadas + (int)$val->faltasinjustificadas;
    }
    
    $array_result = array();
    
    foreach($result as $object){
        $array_result[] = $object;
    }
    
    
    //print_r($result);
    return $array_result;
}
//general_attendance('1008', '2016B');
//general_attendance('1008', '2017A');
// general_attendance('1008', '2016B');

/**
 * Función que retorna las faltas de cada en estudiante en cada curso 
 * monitoreado desde el Plan Talentos Pilos
 *
 * @see attendance_by_course()
 * @return array de objetos con las faltas justificas e injustificadas de un estudiante por curso matriculado
 */
function attendance_by_course($code_student)
{
    global $DB;
    
    $user_report = array();
    
    $sql_query = "SELECT id FROM {user} WHERE username LIKE '$code_student%'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre ='".get_current_semester()->nombre."';";
    $id_current_semester = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT fecha_inicio::DATE FROM {talentospilos_semestre} WHERE id = $id_current_semester->id";
    $start_date = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT fecha_fin FROM {talentospilos_semestre} WHERE id = $id_current_semester->id";
    $end_date = $DB->get_record_sql($sql_query);
    
    
    // $sql_query = "SELECT courses.timecreated AS tcreated
    //               FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
    //                                                       INNER JOIN {course} AS courses ON enrols.courseid = courses.id 
    //               WHERE userEnrolments.userid = $id_user_moodle->id";
                          
    // $courses = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT courses.id AS idcourse, courses.fullname AS coursename, COUNT(attendancestatuses.description) AS injustifiedabsence
                  FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id
                                              INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                              INNER JOIN {talentospilos_semestre} AS semesters ON  (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                    AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE))
                                              INNER JOIN {attendance_log} AS attendancelog ON userEnrolments.userid = attendancelog.studentid
                                              INNER JOIN {attendance_statuses} AS attendancestatuses ON attendancelog.statusid = attendancestatuses.id
                                              INNER JOIN {attendance} AS att ON attendancestatuses.attendanceid = att.id 
                  WHERE userEnrolments.userid = $id_user_moodle->id AND semesters.id = $id_current_semester->id
                                     AND attendancestatuses.description = 'Falta injustificada'
                                     AND courses.id = att.course
                  GROUP BY idcourse, coursename";
                    
    $attendance_report_injustified = $DB->get_records_sql($sql_query, null);

    $sql_query = "SELECT courses.id AS idcourse, courses.fullname AS coursename, COUNT(attendancestatuses.description) AS justifiedabsence
                  FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id
                                              INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                              INNER JOIN {talentospilos_semestre} AS semesters ON  (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                    AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE))
                                              INNER JOIN {attendance_log} AS attendancelog ON userEnrolments.userid = attendancelog.studentid
                                              INNER JOIN {attendance_statuses} AS attendancestatuses ON attendancelog.statusid = attendancestatuses.id
                                              INNER JOIN {attendance} AS att ON attendancestatuses.attendanceid = att.id 
                  WHERE userEnrolments.userid = $id_user_moodle->id AND semesters.id = $id_current_semester->id
                                     AND attendancestatuses.description = 'Falta justificada'
                                     AND courses.id = att.course
                  GROUP BY idcourse, coursename";

    $attendance_report_justified = $DB->get_records_sql($sql_query, null);
    
    foreach ($attendance_report_injustified as $report)
    {
        $count = 0;
        foreach($attendance_report_justified as $justified)
        {
            if($report->coursename == $justified->coursename)
            {
                $report->justifiedabsence = $justified->justifiedabsence;
                unset($attendance_report_justified[$justified->idcourse]);
                $count = $count + 1;
                break;
            }
        }
        if($count == 0)
        {
            $report->justifiedabsence = 0;
        }
        
    }
    foreach($attendance_report_justified as $justified)
    {
        $justified->injustifiedabsence = 0;
    }
    
    $result = array_merge($attendance_report_injustified, $attendance_report_justified);
    
    foreach($result as $val)
    {
        $val->total = (int)$val->justifiedabsence + (int)$val->injustifiedabsence;
    }
    
    // print_r($result);
    
    return $result;
}

// Testing
// attendance_by_course('1673003');

/**
 * Función que retorna las faltas de cada en estudiante en cada semestre cursado
 * exceptuando el semestre actual
 *
 * @see attendance_by_semester()
 * @return array de objetos con las faltas justificas e injustificadas de un estudiante por semestre cursado exceptuando el actual
 * 
 */
 function attendance_by_semester($code_student) 
 {
    global $DB;
    
    $user_report = array();
    
    $sql_query = "SELECT id FROM {user} WHERE username LIKE '$code_student%'";
    $id_user_moodle = $DB->get_record_sql($sql_query);
    
    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre='".get_current_semester()->nombre."';";
    $id_current_semester = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT coursesSemester.semesterid AS idsemester, coursesSemester.semestersname AS semestername, COUNT({attendance_statuses}.description) AS injustifiedabsence 
                  FROM ({attendance} INNER JOIN {attendance_sessions} ON {attendance}.id = {attendance_sessions}.attendanceid)
                                    INNER JOIN {attendance_log} ON {attendance_sessions}.id = {attendance_log}.sessionid INNER JOIN {attendance_statuses} ON {attendance_log}.statusid = {attendance_statuses}.id
                                    INNER JOIN {user} ON {attendance_log}.studentid = {user}.id
                                    INNER JOIN {course} ON {course}.id = {attendance}.course
                                    INNER JOIN (SELECT {user_info_data}.userid, {user_info_data}.data  
                                                FROM {user_info_data} INNER JOIN {user_info_field} ON {user_info_data}.fieldid = {user_info_field}.id 
                                                WHERE {user_info_field}.shortname = 'idtalentos') AS fieldsadd
                                        ON fieldsadd.userid = {user}.id
                                    INNER JOIN (SELECT courses.id AS idcourse, courses.timecreated AS tcreated, semesters.id AS semesterid, semesters.nombre AS semestersname
                                                FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
                                                                                         INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                                                                         INNER JOIN {talentospilos_semestre} AS semesters ON (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                                                              AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE)) 
                                                WHERE userEnrolments.userid = $id_user_moodle->id) AS coursesSemester ON coursesSemester.idcourse = {course}.id
                  WHERE {attendance_statuses}.description = 'Falta injustificada' AND coursesSemester.semesterid <> $id_current_semester->id 
                  GROUP BY idsemester, semestername;";
                    
    $attendance_report_injustified = $DB->get_records_sql($sql_query, null);
    
    $sql_query = "SELECT coursesSemester.semesterid AS idsemester, coursesSemester.semestersname AS semestername, COUNT({attendance_statuses}.description) AS justifiedabsence 
                  FROM ({attendance} INNER JOIN {attendance_sessions} ON {attendance}.id = {attendance_sessions}.attendanceid)
                                    INNER JOIN {attendance_log} ON {attendance_sessions}.id = {attendance_log}.sessionid INNER JOIN {attendance_statuses} ON {attendance_log}.statusid = {attendance_statuses}.id
                                    INNER JOIN {user} ON {attendance_log}.studentid = {user}.id
                                    INNER JOIN {course} ON {course}.id = {attendance}.course
                                    INNER JOIN (SELECT {user_info_data}.userid, {user_info_data}.data  
                                                FROM {user_info_data} INNER JOIN {user_info_field} ON {user_info_data}.fieldid = {user_info_field}.id 
                                                WHERE {user_info_field}.shortname = 'idtalentos') AS fieldsadd
                                        ON fieldsadd.userid = {user}.id
                                    INNER JOIN (SELECT courses.id AS idcourse, courses.timecreated AS tcreated, semesters.id AS semesterid, semesters.nombre AS semestersname
                                                FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
                                                                                         INNER JOIN {course} AS courses ON enrols.courseid = courses.id
                                                                                         INNER JOIN {talentospilos_semestre} AS semesters ON (to_timestamp(courses.timecreated) > semesters.fecha_inicio::DATE - INTERVAL '30 days'
                                                                                                                                              AND (to_timestamp(courses.timecreated) < semesters.fecha_fin::DATE)) 
                                                WHERE userEnrolments.userid = $id_user_moodle->id) AS coursesSemester ON coursesSemester.idcourse = {course}.id
                  WHERE {attendance_statuses}.description = 'Falta justificada' AND coursesSemester.semesterid <> $id_current_semester->id
                  GROUP BY idsemester, semestername;";
                    
    $attendance_report_justified = $DB->get_records_sql($sql_query, null);
    
    foreach ($attendance_report_injustified as $report)
    {
        $count = 0;
        foreach($attendance_report_justified as $justified)
        {
            if($report->idsemester == $justified->idsemester)
            {
                $report->justifiedabsence = $justified->justifiedabsence;
                unset($attendance_report_justified[$justified->idsemester]);
                $count = $count + 1;
                break;
            }
        }
        if($count == 0)
        {
            $report->justifiedabsence = 0;
        }
        
    }
    foreach($attendance_report_justified as $justified)
    {
        $justified->injustifiedabsence = 0;
    }
    
    $result = array_merge($attendance_report_injustified, $attendance_report_justified);
    
    foreach($result as $val)
    {
        $val->total = (int)$val->justifiedabsence + (int)$val->injustifiedabsence;
    }
    // /($result);
    return $result;
}

//Testing
// attendance_by_semester('1673003'); 

 /**
 * Función que retorna el semestre actual a partir de la fecha del sistema
 *
 * @see get_current_semester()
 * @return cadena de texto que representa el semestre actual
 */
function get_current_semester_by_date()
{
  $time = time();
  $current_mont = date("m", $time);
  $current_year = date("Y", $time);
  
  if($current_mont > 1 && $current_mont < 7)
  {
      $current_semester = $current_year."A";
  }
  else if($current_mont > 6 && $current_mont <= 12)
  {
      $current_semester = $current_year."B";
  }
  else
  {
      $current_semester = "Error al calcular el semestre actual";
  }
  
  return $current_semester;
}

//  /**
//  * Función que retorna el semestre actual 
//  *
//  * @see get_current_semester()
//  * @return cadena de texto que representa el semestre actual
//  */
 
//  function get_current_semester(){
     
//      global $DB;
     
//      $sql_query = "SELECT id AS max, nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
     
//      $current_semester = $DB->get_record_sql($sql_query);
     
//      return $current_semester;
//  }
/** 
 **********************
 Fin consultas asistencias 
 **********************
**/

function getConcurrentCohortsSPP($idinstancia){
    global $DB;
    $infoinstancia = consultInstance($idinstancia);
    $asescohorts = "";
    if($infoinstancia->cod_univalle == 1008){
        $asescohorts = "OR idnumber LIKE 'SP%'";
    }
    
    $sql_query="SELECT idnumber, name, timecreated FROM {cohort} WHERE idnumber LIKE '".$infoinstancia->cod_univalle."%' ".$asescohorts." ORDER BY timecreated DESC;";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

function getConcurrentEnfasisSPP(){
    global $DB;
    $sql_query="SELECT  nombre FROM {talentospilos_enfasis};";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

function insertSeguimiento($object, $id_est){
    global $DB;
    $id_seg = $DB->insert_record('talentospilos_seguimiento', $object,true);
    
    //se relaciona el seguimiento con el estudiant
    insertSegEst($id_seg, $id_est);
    
    //se actualiza el riesgo
    if($object->tipo == 'PARES'){
        foreach ($id_est as $idStudent) {
            updateRisks($object, $idStudent);
        }
    }
    
    return true;
}

function updateRisks($segObject, $idStudent){
    global $DB;
    
    //se crea un arraglo que contien la informacion a actualizar
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

function update_array_risk(&$array_student_risks, $name_risk, $calificacion, $idstudent){
    global $DB;
    //Se obtienen los riegos disponible
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


function insertSegEst($id_seg, $id_est){
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

function getSeguimiento($id_est, $id_seg, $tipo, $idinstancia){
    global $DB;
    
    // print_r($id_est);
    // print_r($id_seg);
    // print_r($tipo);
    // print_r($idinstancia);
    
    $sql_query="SELECT *, seg.id as id_seg from {talentospilos_seguimiento} seg INNER JOIN {talentospilos_seg_estudiante} seges  on seg.id = seges.id_seguimiento  where seg.tipo ='".$tipo."' ;";
    
    if($idinstancia != null ){
        $sql_query =  trim($sql_query,";");    
        $sql_query .= " AND seg.id_instancia=".$idinstancia." ;";
    }
    
    if($id_est != null){
        $sql_query = trim($sql_query,";");
        $sql_query .= " AND seges.id_estudiante =".$id_est.";";
    }
    
    // if($id_est != null){
    //     $sql_query = trim($sql_query,";");
    //     $sql_query .= " AND seges.id_estudiante =".$id_est.";";
    // }
    
    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";
   
    }
    
    // var_dump($DB->get_records_sql($sql_query));
    //print_r($sql_query);
    //print_r($DB->get_records_sql($sql_query));
    
   return $DB->get_records_sql($sql_query);
}

//getSeguimiento(169);

function getSeguimientoOrderBySemester($id_est = null, $tipo,$idsemester = null, $idinstancia = null){
    global $DB;
    $result = getSeguimiento($id_est, null,$tipo, $idinstancia );
    
    $seguimientos = array();
    foreach ($result as $r){
        array_push($seguimientos, $r);
    }
    
    $lastsemestre = false;
    $firstsemester=false;
    
    $sql_query = "select * from {talentospilos_semestre} ";
    if($idsemester != null){
        $sql_query .= " WHERE id = ".$idsemester;
    }else{
        $userid = $DB->get_record_sql("select userid from {user_info_data} d inner join {user_info_field} f on d.fieldid = f.id where f.shortname='idtalentos' and d.data='$id_est';");
        $firstsemester = getIdFirstSemester($userid->userid);
        $lastsemestre = getIdLastSemester($userid->userid);
        //print_r($firstsemester."-last:".$lastsemestre);
        
        $sql_query .= " WHERE id >=".$firstsemester;
        
    }
    $sql_query.=" order by fecha_inicio DESC";

    $array_semesters_seguimientos =  array();
    
    if($lastsemestre && $firstsemester){
        
        $semesters = $DB->get_records_sql($sql_query);
        $counter = 0;

        $sql_query ="select * from {talentospilos_semestre} where id = ".$lastsemestre;
        $lastsemestreinfo = $DB->get_record_sql($sql_query);
        
        foreach ($semesters as $semester){
            
            if($lastsemestreinfo && (strtotime($semester->fecha_inicio) <= strtotime($lastsemestreinfo->fecha_inicio))){ //se valida que solo se obtenga la info de los semestres en que se encutra matriculado el estudiante
            
                $semester_object = new stdClass;
                
                $semester_object->id_semester = $semester->id;
                $semester_object->name_semester = $semester->nombre;
                $array_segumietos = array();
                
                while(compare_dates(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin),$seguimientos[$counter]->created)){
                    
                    array_push($array_segumietos, $seguimientos[$counter]);
                    $counter+=1;
                    
                    if ($counter == count($seguimientos)){
                        break;
                    }
                    
                }
                
                foreach($array_segumietos as $r){
                    $r->fecha = date('d-m-Y', $r->fecha);
                    $r->created = date('d-m-Y', $r->created);
                }

                // $semester_object->promedio = getPormStatus($id_est,$semester->id)->promedio;
                $semester_object->result = $array_segumietos;
                $semester_object->rows = count($array_segumietos);
                array_push($array_semesters_seguimientos, $semester_object);
            }
        }
        
    }
    
    $object_seguimientos =  new stdClass();
    
    $promedio = getPormStatus($id_est);
    $object_seguimientos->promedio = $promedio->promedio;
    $object_seguimientos->semesters_segumientos = $array_semesters_seguimientos;
    
    //print_r($object_seguimientos);
    return $object_seguimientos;
}

//getSeguimientoOrderBySemester(169,'PARES');




function getSegumientoByMonitor($id_monitor, $id_seg= null, $tipo, $idinstancia){
    global $DB;
    $sql_query= "";
        $sql_query="SELECT seg.id as id_seg, to_timestamp(fecha) as fecha_formato,*  from {talentospilos_seguimiento} seg  where seg.id_monitor = ".$id_monitor." AND seg.tipo = '".$tipo."' AND seg.id_instancia=".$idinstancia." ORDER BY fecha_formato DESC;";

    if($id_seg != null){
      $sql_query = trim($sql_query,";");
      $sql_query.= " AND seg.id =".$id_seg.";";

   
    }
   return $DB->get_records_sql($sql_query);
}

// getSegumientoByMonitor(120, null, 'PARES', 19);

function getPormStatus($id, $idsemester = null){
    global $DB;
    
    $seguimientos_pares = array();

    $sql_query ="select seg.id,  status, seg.created from {talentospilos_seguimiento} seg INNER JOIN {talentospilos_seg_estudiante}  seg_es on seg_es.id_seguimiento = seg.id where seg.tipo='PARES' AND seg_es.id_estudiante=$id;";
    
    $semester_result= null;
    if($idsemester){
        $semester_query = "SELECT * from {talentospilos_semestre} where id=".$idsemester;
        $semester_result = $DB->get_record_sql($semester_query);
        $seguimientos =  $DB->get_records_sql($sql_query);
        foreach($seguimientos as $seg){
            if(compare_dates(strtotime($semester_result->fecha_inicio), strtotime($semester_result->fecha_fin),$seg->created)){
                array_push($seguimientos_pares, $seg); 
            }
        }
    }
    
    $operadores_pares = new stdClass();
    $operadores_pares->counts = count($seguimientos_pares);
    
    $sum = 0;
    foreach($seguimientos_pares as $seg){
        $sum += $seg->status;
    }
    
    $operadores_pares->sum = $sum;
    
    $seguimientos_soc = array();
    //print_r($operadores_pares);
    $sql_query = "select id,  status, created from {talentospilos_seg_soc_educ} where id_estudiante =".$id.";";
    
    if($semester_result){
        $seguimientos =  $DB->get_records_sql($sql_query);
        foreach($seguimientos as $seg){
            if(compare_dates(strtotime($semester_result->fecha_inicio), strtotime($semester_result->fecha_fin),$seg->created)){
                array_push($seguimientos_soc, $seg); 
            }
        }
    }
    
    $operadores_socio = new stdClass();
    $operadores_socio->counts = count($seguimientos_soc);
    
    $sum = 0;
    foreach($seguimientos_soc as $seg){
        $sum += $seg->status;
    }
    
    $operadores_socio->sum = $sum;
    
    
    //print_r($operadores_socio);
    $result_pares = new stdClass();
    $result_socio = new stdClass();
    $total_promedio = new stdClass();
    $ponde_pares = 0.5;
    $ponde_socio = 0.5;
        
    if($operadores_pares->counts == 0){
        $operadores_pares->promedio = 1;
        $ponde_socio = 1;
        $ponde_pares = 0;
    }else{
        $promedio = $operadores_pares->sum / $operadores_pares->counts;
        $operadores_pares->promedio =  number_format($promedio,1);
    }
    
    
    if($operadores_socio->counts == 0){
        $operadores_socio->promedio = 1;
        $ponde_socio = 0;
        $ponde_pares = 1;
    }else{
        $promedio = $operadores_socio->sum / $operadores_socio->counts;
        $operadores_socio->promedio =  number_format($promedio,1);
    }    
        
        
    $promedio = $operadores_pares->promedio*$ponde_pares + $operadores_socio->promedio*$ponde_socio;
    $total_promedio->promedio =  number_format($promedio,1);
    

    
    if($operadores_socio->counts == 0 && $operadores_pares->counts == 0 ) $total_promedio->promedio = 0;
   
    //print_r($total_promedio);
    return $total_promedio;
}
//getPormStatus(169, 6);

// function getStudentsGrupal($id_monitor, $idinstancia){
//     global $DB;
//     $sql_query = "SELECT * FROM (SELECT * FROM 
//                     (SELECT *, id AS id_user FROM {user}) AS userm 
//                             INNER JOIN 
//                             (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' AND data <> '') AS field 
//                             ON userm. id_user = field.userid ) AS usermoodle 
//                         INNER JOIN 
//                         (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario 
//                         ON usermoodle.data = CAST(usuario.id AS TEXT)
//                     where  idtalentos in (select id_estudiante from {talentospilos_monitor_estud} where id_monitor =".$id_monitor." AND id_instancia=".$idinstancia.");";
    
//    $result = $DB->get_records_sql($sql_query);
//    return $result;
// }


function getEstudiantesSegGrupal($id_seg){
    global $DB;
    $sql_query = "SELECT id_estudiante FROM {talentospilos_seg_estudiante} WHERE id_seguimiento ='$id_seg'";
    return $DB->get_records_sql($sql_query);
}

function insertPrimerAcerca($object){
    global $DB;
    return $DB->insert_record('talentospilos_primer_acerca',$object);
}

function updatePrimerAcerca($object){
    global $DB;
    return $DB->update_record('talentospilos_primer_acerca', $object);
}

function getPrimerAcerca($idtalentos,$idinstancia){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_primer_acerca} WHERE id_estudiante =".$idtalentos." AND id_instancia=".$idinstancia;
    return $DB->get_records_sql($sql_query);
}

function dropTalentosFromSeg($idSeg,$id_est){
    global $DB;
    $whereclause = "id_seguimiento =".$idSeg." AND id_estudiante=".$id_est;
    return $DB->delete_records_select('talentospilos_seg_estudiante',$whereclause);
}


function insertnewAcompaSocio($record){
    global $DB;
    return $DB->insert_record('talentospilos_socioeducativo',$record);
}

function insertInfoEconomica($infoEconomica){
    global $DB;
    $result = false;
    foreach ($infoEconomica as $object){
        $result = $DB->insert_record('talentospilos_economia', $object);
    }
    
    return $result; 
}
function insertInfoFamilia($infoFamilia){
    global $DB;
     $result = false;
    foreach ($infoFamilia as $object){
        $result = $DB->insert_record('talentospilos_familia', $object);
    }
    
    return $result; 
}

function getAcompaSocio($idtalentos,$idinstancia){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_socioeducativo} WHERE id_estudiante =".$idtalentos." AND id_instancia=".$idinstancia.";";
    return $DB->get_records_sql($sql_query);
}

function getEconomia($idtalentos,$tipo ){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_economia} WHERE id_estudiante =".$idtalentos." AND tipo='".$tipo."';";
    return $DB->get_records_sql($sql_query);
}

function getFamilia($idtalentos){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_familia} WHERE id_estudiante =".$idtalentos.";";
    return $DB->get_records_sql($sql_query);
}

function updateAcompaSocio($object){
    global $DB;
    return $DB->update_record('talentospilos_socioeducativo', $object);
}

function updateInfoEconomica($object){
    global $DB;
    return $DB->update_record('talentospilos_economia', $object);
}

function updateInfoFamilia($object){
    global $DB;
    return $DB->update_record('talentospilos_familia', $object);
}

function dropInfoEconomica($idInfo){
    global $DB;
    $whereclause = "id =".$idInfo;
    //print_r($DB->delete_records_select('talentospilos_economia',$whereclause));
    return $DB->delete_records_select('talentospilos_economia',$whereclause);
}

function dropFamilia($idInfo){
    global $DB;
    $whereclause = "id =".$idInfo;
    //print_r($DB->delete_records_select('talentospilos_economia',$whereclause));
    return $DB->delete_records_select('talentospilos_familia',$whereclause);
}

function insertSegSocio($object){
    global $DB;
    return $DB->insert_record('talentospilos_seg_soc_educ',$object);
}

function updateSegSocio($object){
    global $DB;
    return $DB->update_record('talentospilos_seg_soc_educ', $object);
}

function getSegSocio($idtalentos,$idinstancia, $idseg = null){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_seg_soc_educ} WHERE id_estudiante =".$idtalentos." AND id_instancia=".$idinstancia.";";
    if( $idseg != null){
        $sql_query = trim($sql_query,";");
        $sql_query.= " AND id =".$idseg.";";
    }
    //print_r($sql_query);
    return $DB->get_records_sql($sql_query);
}

function getSegSocioOrderBySemester($idtalentos,$idinstancia, $idseg = null, $idsemester= null){
    global $DB;
    $result = getSegSocio($idtalentos,$idinstancia, null);
    
    $seguimientos = array();
    
    foreach ($result as $r){
        array_push($seguimientos, $r);
    }
    
    foreach($seguimientos as $r){
        $r->fecha = date('d-m-Y', $r->fecha);
    }
    
    //print_r($seguimientos);
    
    $sql_query = "select * from {talentospilos_semestre} ";
    if($idsemester != null){
        $sql_query .= " WHERE id = ".$idsemester;
    }else{
        $userid = $DB->get_record_sql("select userid from {user_info_data} d inner join {user_info_field} f on d.fieldid = f.id where f.shortname='idtalentos' and d.data='$idtalentos';");
        $firstsemester = getIdFirstSemester($userid->userid);
        $sql_query .= " WHERE id >=".$firstsemester;
    }
    
    $sql_query.=" order by fecha_inicio DESC";
    
    $semesters = $DB->get_records_sql($sql_query);
    
    $object_seguimientos =  new stdClass();
    
    $array_semesters_seguimientos =  array();

    $counter = 0;
    foreach ($semesters as $semester){
        
        $semester_object = new stdClass;
        
        $semester_object->id_semester = $semester->id;
        $semester_object->name_semester = $semester->nombre;
        $array_segumietos = array();
        
        while(compare_dates(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin),$seguimientos[$counter]->created)){
            //print_r("fecha segumiento:".$seguimientos[$counter].)
            array_push($array_segumietos, $seguimientos[$counter]);
            $counter+=1;
            
            if ($counter == count($seguimientos)){
                break;
            }
            
        }
        
        $semester_object->result = $array_segumietos;
        $semester_object->rows = count($array_segumietos);
        array_push($array_semesters_seguimientos, $semester_object);
    }
    
    $promedio = getPormStatus($idtalentos);
    
    $object_seguimientos->promedio = $promedio->promedio;
    $object_seguimientos->semesters_segumientos = $array_semesters_seguimientos;
    //print_r("adaf<br>");
    //print_r($object_seguimientos);
    return $object_seguimientos;
}

//getSegSocioOrderBySemester(169);

function getUserMoodleByid($id){
    global $DB;
    $sql_query = "SELECT * FROM {user} WHERE id =".$id.";";
    return $DB->get_record_sql($sql_query);
}

/**
 * Return final grade of a course for a single student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing courses and grades for single student
 */

function get_grades_courses_student_semester($id_student, $coursedescripctions){
    //print_r("<br><hr>".$id_student."<hr><br>");
    global $DB;
    
    // var_dump($id_student);
    
    $id_first_semester = getIdFirstSemester($id_student);
    
    // var_dump($id_first_semester);
    
    $semesters = get_semesters_student($id_first_semester);
    
    // var_dump($semesters);
    
    // print_r($semesters);
    
    $courses = get_courses_by_student($id_student, $coursedescripctions);
    $array_semesters_courses =  array();
   
    $counter = 0;
    foreach ($semesters as $semester){
        
        $semester_object = new stdClass;
        
        $semester_object->id_semester = $semester->id;
        $semester_object->name_semester = $semester->nombre;
        $array_courses = array();
        
        $coincide =false;
        
        if ($courses){
            while($coincide = compare_dates(strtotime($semester->fecha_inicio), strtotime($semester->fecha_fin), strtotime( $courses[$counter]->time_created))){
                array_push($array_courses, $courses[$counter]);
                $counter+=1;
                
                if ($counter == count($courses)){
                    break;
                }
                
            }
        }
        if($coincide || $counter != 0){
            $semester_object->courses = $array_courses;
            array_push($array_semesters_courses, $semester_object);
        }
    }
    // print_r($array_semesters_courses);
    return $array_semesters_courses; 
}
 
// Test
// get_grades_courses_student_semester(10304);

function compare_dates($fecha_inicio, $fecha_fin, $fecha_comparar){
    
    $fecha_inicio = new DateTime(date('Y-m-d',$fecha_inicio));
    date_add($fecha_inicio, date_interval_create_from_date_string('-30 days'));
    
    // var_dump(strtotime($fecha_inicio->format('Y-m-d')));
    // var_dump($fecha_fin);
    // var_dump($fecha_comparar);
    //print_r(($fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ($fecha_comparar <= $fecha_fin));
    return (((int)$fecha_comparar >= strtotime($fecha_inicio->format('Y-m-d'))) && ((int)$fecha_comparar <= (int)$fecha_fin));
}

/**
 * Return array of semesters of a student
 *
 * @param string $username_student Is te username of moodlesite 
 * @return array() of stdClass object representing semesters of a student
 */
 function get_semesters_student($id_first_semester){
     
     global $DB;
     
     $sql_query = "SELECT id, nombre, fecha_inicio::DATE, fecha_fin::DATE FROM {talentospilos_semestre} WHERE id >= $id_first_semester ORDER BY {talentospilos_semestre}.fecha_inicio DESC";
     
     $result_query = $DB->get_records_sql($sql_query);
     
     $semesters_array = array();
     
     foreach ($result_query as $result){
       array_push($semesters_array, $result);
     }
    //print_r($semesters_array);
    return $semesters_array;
}

// Test
//get_semesters_student(getIdFirstSemester(169));

/**
 * Return courses of a student
 *
 * @param username moodle site
 * @return array of courses 
 */

// function get_courses_by_student($id_student, $coursedescripction = false){
//     //print_r("<br><br>id: ".$id_student."<br>");
//     global $DB;
    
//     $sql_query = "SELECT subcourses.id_course, name_course, tgcategories.fullname, to_timestamp(subcourses.time_created)::DATE AS time_created
//                   FROM {grade_categories} as tgcategories INNER JOIN
//                      (SELECT tcourse.id AS id_course, tcourse.fullname AS name_course, tcourse.timecreated AS time_created 
//                      FROM {user}  AS tuser INNER JOIN {user_enrolments}  AS tenrolments ON tuser.id = tenrolments.userid
//                           INNER JOIN {enrol}  AS tenrol ON  tenrolments.enrolid = tenrol.id
//                           INNER JOIN {course}  AS tcourse ON tcourse.id = tenrol.courseid
//                      WHERE tuser.id = $id_student) AS subcourses
//                      ON subcourses.id_course = tgcategories.courseid
//                   ORDER BY subcourses.time_created DESC;";
//     $result_query = $DB->get_records_sql($sql_query);
    
//     if($coursedescripction){
        
//         $courses_array = array();
//         foreach ($result_query as $result){
            
//             $result->grade = number_format (grade_get_course_grade($id_student, $result->id_course)->grade,2);
//             $result->descriptions = getCoursegradelib($result->id_course, $id_student);
//             array_push($courses_array, $result);
//         }
//         return $courses_array;
        
//     }else{
//         //print_r($result_query);
//         return $result_query;
//     }
// }

// //Test
// //get_courses_by_student(3);

 /**
 * Return total of semesters 
 *
 * @param null
 * @return integer representing the total number of semesters registered in db
 */
 
 function get_total_numbers_semesters(){
     
     global $DB;
     
     $sql_query = "SELECT COUNT(id) FROM {talentospilos_semestre}";
     $total_semesters = $DB->get_record_sql($sql_query);

     return $total_semesters->count;
}

/**
 * Return id of first semester of a student
 *
 * @param int --- id student 
 * @return int --- id first semester
 */
function getIdFirstSemester($id){
    try {
        global $DB;
        
        $sql_query = "SELECT username, timecreated from {user} where id = ".$id;
        $result = $DB->get_record_sql($sql_query);
        
        $year_string = substr($result->username, 0, 2);
        $date_start = strtotime('01-01-20'.$year_string);

        if(!$result) throw new Exception('error al consultar fecha de creación');
        
        $timecreated = $result->timecreated;
        
        if($timecreated <= 0){
            
            $sql_query = "SELECT MIN(courses.timecreated)
                          FROM {user_enrolments} AS userEnrolments INNER JOIN {enrol} AS enrols ON userEnrolments.enrolid = enrols.id 
                                                                   INNER JOIN {course} AS courses ON enrols.courseid = courses.id 
                          WHERE userEnrolments.userid = $id AND courses.timecreated >= ".$date_start;
                          
            $courses = $DB->get_record_sql($sql_query);

            $timecreated = $courses->min;
        }

        $sql_query = "select id, nombre ,fecha_inicio::DATE, fecha_fin::DATE from {talentospilos_semestre} ORDER BY fecha_fin ASC;";
        
        $semesters = $DB->get_records_sql($sql_query);
        
        $id_first_semester = 0; 

        foreach ($semesters as $semester){
            $fecha_inicio = new DateTime($semester->fecha_inicio);

            date_add($fecha_inicio, date_interval_create_from_date_string('-60 days'));
            
            if((strtotime($fecha_inicio->format('Y-m-d')) <= $timecreated) && ($timecreated <= strtotime($semester->fecha_fin))){
                
                return $semester->id;
            }
        }

    }catch(Exeption $e){
        return "Error en la consulta primer semestre";
    }
}

// Testing
// getIdFirstSemester(103304);
// getIdFirstSemester(103268);
// print_r(getIdFirstSemester(171));
// getIdFirstSemester(171);
// getIdFirstSemester(10);

function getIdLastSemester($idmoodle){
    
    // print_r($idmoodle);
    // print_r("***");
    
    $id_first_semester = getIdFirstSemester($idmoodle);
    // print_r($id_first_semester);
    // print_r("*****************");
    $semesters = get_semesters_student($id_first_semester);
    // print_r($semesters);
    //$result = get_grades_courses_student_semester($idmoodle);
    if($semesters){
       return  $semesters[0]->id;
    }else{
        return false;
    }
    
}
// print_r(getIdLastSemester(171));


/**
 * Genera el reporte de estudiantes activos o inactivos por semestra de Ser pilo paga 1, 2 y 3
 * @param $semestre Id del semestre a buscar, si es nulo busca todos los semestres
 * @retrun $html Table html con el reporte de estudiantes activos e inactivos
 * @author Edgar Mauricio Ceron Florez
 */
  
// function getStudentState($semestre){
//     $sql_query = "SELECT id_semestre, 
//                         (SELECT COUNT(*) FROM {talentospilos_academica} 
//                             WHERE id_semestre = ".$semestre."
//                             AND semestre_act = 1) AS activos,
//                         (SELECT COUNT(*) FROM {talentospilos_academica} 
//                             WHERE id_semestre = ".$semestre."
//                             AND semestre_act = 0) AS inactivos,
//                         (SELECT COUNT(*) FROM {talentospilos_academica} 
//                             WHERE id_semestre = ".$semestre."
//                         ) AS total";
// }

/**
 * Return all course info(items and categories with grades) of a student
 *
 * @param $courseid, $userid
 * @return html table
 */

// function getCoursegradelib($courseid, $userid){
//     /// return tracking object
//     //$courseid = 98;
//     //$userid = 5;
    
//     $context = context_course::instance($courseid);
    
//     $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));
//     $report = new grade_report_user($courseid, $gpr, $context, $userid);
//     reduce_table($report);
//     //echo "si";
//     //print_grade_page_head($courseid, 'report', 'user', get_string('pluginname', 'gradereport_user'). ' - '.fullname($report->user));

//      if ($report->fill_table()) {
//         // print_r($report->gtree->top_element['object']->courseid);
//         //return $report->print_table(true);
//         return input_print_table($report);
//     }
//     return null;
// }
//  print_r(getCoursegradelib(110, 3));


// /**
//  * Reduce course information to display 
//  *
//  * @param &$report
//  * @return null
//  */
//  function reduce_table(&$report) {
	
// 	$report->showpercentage = false;
// 	$report->showrange = false; 
// 	$report->showfeedback = false;
// 	$report->showcontributiontocoursetotal = false;
// // 	$report->showgrade = false;	
// 	$report->setup_table();
// }



/**
 * Generate the html table with de information of a grade_report_user, making input the grades
 *
 * @param $report
 * @return html
//  */
//  function input_print_table($report) {
//          $maxspan = $report->maxdepth;
//          $id_c = $report->gtree->top_element['object']->courseid ;
//          $id_usuario = $report->user->id; 
//            /// Build table structure
//            $html = "
//                <table id = '$id_c-$id_usuario'  cellspacing='0'
//                       cellpadding='0'
//                       summary='" . s($report->get_lang_string('tablesummary', 'gradereport_user')) . "'
//                       class='boxaligncenter generaltable user-grade'>
//                <thead>
//                    <tr>
//                        <th id='".$report->tablecolumns[0]."' class=\"header column-{$report->tablecolumns[0]}\" colspan='$maxspan'>".$report->tableheaders[0]."</th>\n";
   
//            for ($i = 1; $i < count($report->tableheaders); $i++) {
//                $html .= "<th id='".$report->tablecolumns[$i]."' class=\"header column-{$report->tablecolumns[$i]}\">".$report->tableheaders[$i]."</th>\n";
//            }
   
//            $html .= "
//                    </tr>
//                </thead>
//                <tbody>\n";
   
//            /// Print out the table data
//            for ($i = 0; $i < count($report->tabledata); $i++) {
//                $html .= "<tr>\n";
//                if (isset($report->tabledata[$i]['leader'])) {
//                    $rowspan = $report->tabledata[$i]['leader']['rowspan'];
//                    $class = $report->tabledata[$i]['leader']['class'];
//                    $html .= "<td class='$class' rowspan='$rowspan'></td>\n";
//                }
//                for ($j = 0; $j < count($report->tablecolumns); $j++) {
//                    $name = $report->tablecolumns[$j];
// 				   if($name == 'grade'){
// 					   $class = (isset($report->tabledata[$i][$name]['class'])) ? $report->tabledata[$i][$name]['class'] : '';
// 					   $colspan = (isset($report->tabledata[$i][$name]['colspan'])) ? "colspan='".$report->tabledata[$i][$name]['colspan']."'" : '2';
// 					   $content = (isset($report->tabledata[$i][$name]['content'])) ? $report->tabledata[$i][$name]['content'] : null;
// 					   $celltype = (isset($report->tabledata[$i][$name]['celltype'])) ? $report->tabledata[$i][$name]['celltype'] : 'td';
// 					   $id_item = explode("_", ($report->tabledata[$i]['itemname']['id']))[1];
// 					   $weight = getweightofItem($id_item);
// 					   $id1 = "id = '" . $id_item ."-$weight'";
					   
					  
// 					   $headers = (isset($report->tabledata[$i][$name]['headers'])) ? "headers='{$report->tabledata[$i][$name]['headers']}'" : '';
					   
// 			    		   if (isset($content)) {
					       
//                             if (!isTotal($report->tabledata[$i]['itemname']['content'])) {
// 					          $aggregation = getAggregationofItem($id_item,$id_c);
// 					          $id2 = "id = '" . $aggregation ."'";
//     						  $html .= "<$celltype $id2 $headers class='$class' $colspan> <input  $id1 onkeypress='return pulsar(event)' class='item' value=$content readonly/></$celltype>\n";//INPUT
//     						}else{
//     						  $aggregation = getAggregationofTotal($id_item,$id_c);
//     						  $id2 = "id = '" . $aggregation ."'";
//     						  $html .= "<$celltype $id2 $headers class='$class' $colspan> <input  $id1 onkeypress='return pulsar(event)' class='total' value=$content readonly/></$celltype>\n";//INPUT
//     						   //$html .= "<$celltype $id2 $headers class='$class' $colspan >$content</$celltype>\n";//INPUT
// 						}}
// 				   }else{
// 					   $class = (isset($report->tabledata[$i][$name]['class'])) ? $report->tabledata[$i][$name]['class'] : '';
// 					   $colspan = (isset($report->tabledata[$i][$name]['colspan'])) ? "colspan='".$report->tabledata[$i][$name]['colspan']."'" : '';
// 					   $content = (isset($report->tabledata[$i][$name]['content'])) ? $report->tabledata[$i][$name]['content'] : null;
// 					   $celltype = (isset($report->tabledata[$i][$name]['celltype'])) ? $report->tabledata[$i][$name]['celltype'] : 'td';
// 					   $id = (isset($report->tabledata[$i][$name]['id'])) ? "id='{$report->tabledata[$i][$name]['id']}'" : '';
// 					   $headers = (isset($report->tabledata[$i][$name]['headers'])) ? "headers='{$report->tabledata[$i][$name]['headers']}'" : '';
// 					   if (isset($content)) {
// 						   $html .= "<$celltype $id $headers class='$class' $colspan>$content</$celltype>\n"; 
// 						}
// 				   }
//                }
//                $html .= "</tr>\n";
//            }
   
//            $html .= "</tbody></table>";
   
       
//                return $html;
           
//        }


// function isTotal($string){
//     if(stripos($string, "Total") === false){
//         return false;
//     }else{
//         return true;
//     }
    
// }
/*
function getweightofItem($itemid){
    global $DB;
    
    $sql_query = "SELECT aggregationcoef as weight 
                  FROM {grade_items}
                  WHERE id = ".$itemid;
                  
    $output = $DB->get_record_sql($sql_query);
    $weight = $output->weight;
    
    return $weight;
}*/

// function getAggregationofItem($itemid,$courseid){
//     global $DB;
    
    
//     $sql_query = "
//         SELECT cat.aggregation as aggregation, cat.id as id
//         FROM {grade_items} as items INNER JOIN {grade_categories} as cat ON (items.categoryid = cat.id)
//         WHERE items.courseid = '$courseid' AND items.id = '$itemid';";

//     $output = $DB->get_record_sql($sql_query);
//     // print_r($output);
//     $aggregation = $output->aggregation ;
//     $id = $output->id;

    
    
//     $respuesta = $aggregation."-".$id;
    
//     return $respuesta;
// }
// // getAggregationofItem('64','100');

// function getAggregationofTotal($itemid,$courseid){
//     global $DB;
    
//     $sql_query = "
//         SELECT cat.aggregation as aggregation, cat.id as id
//         FROM {grade_items} as items INNER JOIN {grade_categories} as cat ON (items.iteminstance = cat.id)
//         WHERE items.courseid = '$courseid' AND items.id = '$itemid';";
//     $output = $DB->get_record_sql($sql_query);
//     // print_r($output);

//     $aggregation = $output->aggregation ;
//     $id = $output->id;

    
    
//     $respuesta = $aggregation."-".$id;
    
//     return $respuesta;
// }
//PRUEBA
// getAggregationofTotal('330','108');

//DATOS BASICOS
function getStudentInformation($idTalentos){
    global $DB;
    
    $sql_query = "SELECT usuario.id, usuario.firstname, infor_data.id, infor_data.data, infor_field.shortname, usuario_talentos.sexo, usuario_talentos.id_ciudad_ini, municipios_ini_talentos.nombre AS municipio_procedencia, departamentos_ini_talentos.nombre AS departamento_procedencia, usuario_talentos.id_ciudad_res, municipios_res_talentos.nombre AS municipio_residencia, departamentos_res_talentos.nombre AS departamento_residencia 
    FROM {user} AS usuario 
    INNER JOIN {user_info_data} AS infor_data 
    ON usuario.id = infor_data.userid 
    INNER JOIN {user_info}_field AS infor_field 
    ON infor_data.fieldid = infor_field.id 
    INNER JOIN {talentospilos_usuario} AS usuario_talentos 
    ON  cast(usuario_talentos.id AS varchar) = infor_data.data 
    INNER JOIN {talentospilos_municipio} AS municipios_res_talentos 
    ON municipios_res_talentos.id = usuario_talentos.id_ciudad_res 
    INNER JOIN (SELECT * FROM {talentospilos_municipio}) AS municipios_ini_talentos
    ON municipios_ini_talentos.id = usuario_talentos.id_ciudad_ini 
    INNER JOIN (SELECT * FROM {talentospilos_departamento}) AS departamentos_ini_talentos
    ON municipios_ini_talentos.cod_depto = departamentos_ini_talentos.id
    INNER JOIN (SELECT * FROM {talentospilos_departamento}) as departamentos_res_talentos
    ON municipios_res_talentos.cod_depto = departamentos_res_talentos.id
    WHERE infor_field.shortname = '"+$idTalentos+"';";
    
    $output = $DB->get_records_sql($sql_query);
    
    return $output;
}

/**
 * Realiza una consulta con el nombre de la categoria y nombre del curso para luego
 * generar un array con los nombres de las categorias
 *
 * @param $idCourse
 * @return Array
 */
function getCategories($idCourse)
{
    global $DB;
    
    $sql_query="SELECT {grade_categories}.id AS id,{grade_categories}.fullname AS nombre_categoria,{course}.fullname AS nombre_curso, {grade_categories}.aggregation AS tipo
                FROM {grade_categories} INNER JOIN {course} ON ({grade_categories}.courseid={course}.id) 
                WHERE courseid=".$idCourse.";";
    $output = $DB->get_records_sql($sql_query);
    
    $newArray=array();
    //print_r($output);
     foreach($output as &$categoria)
     {
         $arrayAuxiliar=array();
         array_push($arrayAuxiliar,$categoria->id);
         if($categoria->nombre_categoria=="?")
         {
             $ingresar=$categoria->nombre_curso;
             $tipoCalificacion=$categoria->tipo;
         }else
         {
            $ingresar= $categoria->nombre_categoria; 
            $tipoCalificacion=$categoria->tipo;
         }
         array_push($arrayAuxiliar,$ingresar);
         array_push($arrayAuxiliar,$tipoCalificacion);
         array_push($newArray,$arrayAuxiliar);
     }
    // print_r($newArray);
    return $newArray;
}

// getCategories(2);


/**
 * Realiza una consulta para encontrar el ultimo indice del elemento sort correspondiente
 * a la categoria que se esta ingresando
 *
 * @param $curso
 * @return int --- proximoIndice
 */
function sortItem($curso)
{
    global $DB;
    $sql_query = "SELECT max(sortorder) FROM {grade_items} WHERE courseid=".$curso.";";
    $output=$DB->get_record_sql($sql_query);
    $proximoIndice=($output->max)+1;
    //print_r($proximoIndice);
    return $proximoIndice;
}

/**
 * Realiza la insercion de una categoria considerando si es de tipo ponderado o no, luego de esto
 * inserta el item que representara a la categoria, este ultimo es necesario para que la categoria tenga un peso
 *
 * @param $curso
 * @param $padre
 * @param $nombre
 * @param $ponderado
 * @param $peso
 * @return String --- ok || error
 */
function insertarCategoria($curso,$padre,$nombre,$ponderado,$peso)
{
     global $DB;
    
    //se instancia un objeto y sus elementos para utilizar el metodo de insercion de moodle
    $object = new stdClass;
    $object ->courseid=$curso;
    $object ->fullname=$nombre;
    $object ->parent =$padre;
    $object ->aggregation=$ponderado;
    $object ->timecreated=time();
    $object ->timemodified=$object ->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    //print_r($object);
    $succes=$DB->insert_record('grade_categories',$object);
    //print_r($succes);
    if($succes)
    {
      if(insertarItem($curso,$succes,$nombre,$peso,false)=="ok")
      {
        return "ok";    
      }else{
          return "error";
      }
    }
    return "error";
}

// insertarCategoria(105,126,'prueba insertar',10,60);



/**
 * Realiza la insercion de item, ya sea item plano o un item relacionado con una categoria,
 * este ultimo es necesario para poder asignarle un peso en caso de que la categoria
 * sea hija de otra categoria con calificacion ponderada
 *
 * @param $curso
 * @param $padre
 * @param $nombre
 * @param $ponderado
 * @param $peso
 * @return String --- ok || error
 */
function insertarItem($curso,$padre,$nombre,$valorEnviado,$item)
{
    global $DB;
    
    //se instancia un objeto y sus elementos para utilizar el metodo de insercion de moodle
    if($item)
    {
    $object = new stdClass;
    $object ->courseid=$curso;
    $object -> categoryid=$padre;
    $object ->itemname=$nombre;
    $object -> itemnumber=0;
    $object -> itemtype='manual';
    $object -> sortorder=sortItem($curso);
    $object -> aggregationcoef=$valorEnviado;
    $object -> grademax=5;
    }else{
    $object = new stdClass;
    $object ->courseid=$curso;
    $object -> itemtype='category';
    $object -> sortorder=sortItem($curso);
    $object -> aggregationcoef=$valorEnviado;
    $object -> iteminstance=$padre;
    $object -> grademax=5;
    }
    
    //print_r($object);
    
    $retorno=$DB->insert_record('grade_items',$object);
    //print_r($retorno);
    if($retorno)
    {
        return "ok";
    }else
    {
        return "error";
    }
    
}

/**
 * Funcion que retorna las categorias de un curso segun el shortname el cual 
 * se arma en funcion de el codigo de la asignatura,grupo,mes y año actual
 *
 * @param $Asignatura
 * @param $Grupo
 * @return Array
 */
function getCategoriesWithShortname($Asignatura,$Grupo)
{
    global $DB;
    $sql_query ="select max(id) FROM {talentospilos_semestre};";
    $maxid = $DB->get_record_sql($sql_query);
    $maxid= $maxid->max;
    
    //se extrae el mes del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(month FROM fecha_inicio) AS mes FROM {talentospilos_semestre} WHERE id=$maxid;";
    $mes = $DB->get_record_sql($sql_query);
    $mes = $mes->mes;
    
    //se extrae el año del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(year FROM fecha_inicio) AS anio FROM {talentospilos_semestre} WHERE id=$maxid;";
    $año = $DB->get_record_sql($sql_query);
    $año = $año->anio;
    
    //se arma el shortname
    $Shortname="'00-".$Asignatura."-".$Grupo."-".$año."0".$mes."041'";
    
    //se realiza la consulta
    $sql_query="SELECT {grade_categories}.id AS id,{course}.id as id_curso,{grade_categories}.fullname AS nombre_categoria,
                {course}.fullname AS nombre_curso,{grade_categories}.aggregation AS tipo,
                {course}.shortname AS shortname FROM {grade_categories} INNER JOIN {course} 
                ON ({grade_categories}.courseid={course}.id) 
                WHERE {course}.shortname=".$Shortname.";";
                
    $output = $DB->get_records_sql($sql_query);
    
    $newArray=array();
    
    //por cada elemento retornado de la consulta el cual es una categoria se 
     foreach($output as &$categoria)
     {
         $arrayAuxiliar=array();
         //se toma el id
         array_push($arrayAuxiliar,$categoria->id);
         //en caso que tenga un nombre de categoria asignado se toma dicho nombre, en caso contrario
         //significa que es el curso como tal y por ello se le da el nombre de este
         if($categoria->nombre_categoria=="?")
         {
             $ingresar=$categoria->nombre_curso;
             $tipoCalificacion=$categoria->tipo;
         }else
         {
            $ingresar= $categoria->nombre_categoria; 
            $tipoCalificacion=$categoria->tipo;
         }
         //se agregan los elementos a un array auxiliar y luego se añaden al array que se retornara
         array_push($arrayAuxiliar,$ingresar);
         array_push($arrayAuxiliar,$tipoCalificacion);
         array_push($arrayAuxiliar,$categoria->id_curso);
         array_push($newArray,$arrayAuxiliar);
     }
    //print_r($newArray);
    return $newArray;
}

//getCategoriesWithShortname(123456,"00");

/**
 * Funcion que extrae los ids de los usuarios perteneciente a un curso segun el codigo de la asignatura y el grupo
 *
 * @param $curso
 * @param $grupo
 * @param $usado
 * @return Array || String "error id"
 */
function idUsuariosCurso($curso,$grupo,$usado)
{   
    global $DB;
    $sql_query ="select max(id) FROM {talentospilos_semestre};";
    $maxid = $DB->get_record_sql($sql_query);
    $maxid= $maxid->max;
    
    //se extrae el mes del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(month FROM fecha_inicio) AS mes FROM {talentospilos_semestre} WHERE id=$maxid;";
    $mes = $DB->get_record_sql($sql_query);
    $mes = $mes->mes;
    
    //se extrae el año del ultimo semestre registrado en la base de datos
    $sql_query ="select EXTRACT(year FROM fecha_inicio) AS anio FROM {talentospilos_semestre} WHERE id=$maxid;";
    $año = $DB->get_record_sql($sql_query);
    $año = $año->anio;
    
    //se arma el shortname
    $Shortname="'00-".$Asignatura."-".$Grupo."-".$año."0".$mes."041'";
    
    //se realiza la consulta
    $sql_query="SELECT u.id AS userid FROM {enrol} AS e INNER JOIN {user_enrolments} 
    ue ON (e.id=ue.enrolid) INNER JOIN {user} AS u ON (ue.userid = u.id) 
    INNER JOIN {course} AS c ON (e.courseid=c.id) WHERE c.shortname=".$Shortname."";
    
        
        //en caso que el metodo este siendo usado por otro se retorna el texto de la consulta
        //en caso contrario se realiza la consulta y se retorna el resultado
        $output=$DB->get_records_sql($sql_query);
        if($usado)
        {
            return $sql_query;
        }else
        {
            $output = $DB->get_records_sql($sql_query);
            if($output)
            {
                return $output;

            }else
            {
                return "error id";    
            }
        }
        
}

/**
 * Funcion que relaciona los cohortes con sus miembros
 * 
 * @param $usado
 * @return Array || String "error cohortes"
 */
function miembrosCohortes($usado)
{
    global $DB;
    //se realiza la consulta
    $sql_query="SELECT cm.userid AS userid FROM {cohort} AS c INNER JOIN {cohort_members} AS cm 
                ON (c.id=cm.cohortid)";
    
    //si esta siendo usado por otro metodo se retorna el texto de la consulta
    //en caso contrario se realiza la consulta y se retorna el resultado
    if($usado)
    {
        return $sql_query;
    }else{
        $output=$DB->get_records_sql($sql_query);
        if($output)
        {
            
            return $output;    
        }else
        {
            return "error cohortes";
        }
    }
}

/*
 * Funcion que verifica si en la asignatura y grupo ingresados existe almenos un miembro perteneciente
 * al programa ser pilo paga
 * 
 * @param $curso
 * @param $grupo
 * @return Array 
 */
function verificarSPPEnGrupo($curso,$grupo)
{
    global $DB;
    //se realizan las consultas auxiliares
    $idUsuarios=idUsuariosCurso($curso,$grupo,true);
    $idCohortes=miembrosCohortes(true);
    $newArray=array();
    
    //si se presento algun error al momento de hacer la consulta del id entonces se retorna el error
    //si no, se verifica algun error al momento de hacer la consulta de los miembros de los cohortes 
    //si es asi se retorna el error, en caso contrario significa que las consultas fueron exitosas
    if($idUsuarios=="error id")
    {
        return $idUsuarios;
    }else if($idCohortes=="error cohortes")
        {
            return $idCohortes;
        }else{
            
            //se realiza una interseccion para ver si existe algun miembro de ser pilo paga entre los estudiantes
            //matriculados en el grupo
            $sql_query="SELECT * FROM (".$idUsuarios.") AS a INTERSECT (".$idCohortes.")";
            
                
                $output= $DB->get_records_sql($sql_query);
                
                //si el resultado de la consulta no fue vacio
                //en caso que sea vacio entonces se retorna el correspondiente error
                if($output)
                {
                    //por cada resultado se añade el id del estudiante a un arreglo
                    foreach($output as &$estudiante)
                    {
                        $arrayAuxiliar=array();
                        array_push($newArray,$estudiante->userid);
                    }
                    
                    //si el conteo del array no es 0 entonces se retorna el primer elemento ya que no se 
                    //necesita mas informacion para confirmar la existencia de un ser pilo paga en el programa
                    //en caso contrario se retorna que no para presentar el correspondiente error
                    if(count($newArray)!=0)
                    {
                        return $newArray[0];
                    }else
                    {
                        return "no";    
                    }
                }else
                {
                    return "error spp"; 
                }
            
      
    }
}



//verificarSPPEnGrupo(123456,"00");

//insertarItem(105,126,'prueba insertar consola 3',0.20);

//select mdl_grade_items.itemname as nombre,mdl_grade_categories.fullname as que_categoria_esta,mdl_grade_items.itemtype,mdl_grade_items.aggregationcoef,mdl_grade_items.aggregationcoef2 from mdl_grade_items inner join mdl_grade_categories on (mdl_grade_items.categoryid=mdl_grade_categories.id) where mdl_grade_items.courseid=5; 
//^^^^^^^ CONSULTA PARA VER LO DE LOS VALORES



//*****************************************************************************

/**
 * Return all course info(items and categories with grades) of a student
 *
 * @param $courseid, $userid
 * @return html table
 */

function getCoursegradelib_grade_categories($curso,$grupo, $userid){
    
    global $DB;
    $sql_query ="select max(id) FROM {talentospilos_semestre};";
    $maxid = $DB->get_record_sql($sql_query);
    $maxid= $maxid->max;
    
    $sql_query ="select EXTRACT(month FROM fecha_inicio) AS mes FROM {talentospilos_semestre} WHERE id=$maxid;";
    $mes = $DB->get_record_sql($sql_query);
    $mes = $mes->mes;
    
    $sql_query ="select EXTRACT(year FROM fecha_inicio) AS anio FROM {talentospilos_semestre} WHERE id=$maxid;";
    $año = $DB->get_record_sql($sql_query);
    $año = $año->anio;
    
    
    $Shortname="'00-".$curso."-".$grupo."-".$año."0".$mes."041'";
    
    $sql_query="SELECT c.id FROM {course} AS c 
    WHERE c.shortname=".$Shortname.";";
    
    $precourseid = $DB->get_record_sql($sql_query);
    $courseid=$precourseid->id;
    $context = context_course::instance($courseid);
    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table_grade_categories($report);
     
     if ($report->fill_table()) {
       return $report->print_table(true);
    }
}
//getCoursegradelib_grade_categories(123456,"00",100);


/**
 * Reduce course information to display 
 *
 * @param &$report
 * @return null
 */
 function reduce_table_grade_categories(&$report) {
	
	$report->showpercentage = false;
	$report->showrange = false; 
	$report->showfeedback = false;
	$report->showcontributiontocoursetotal = false;
	$report->showgrade=false;
	$report->showweigth=false;
	$report->setup_table();
}


function retornarCantidadAparicionesMonitor($idmonitor)
{
    global $DB;
    $sql_query ="SELECT count(*) AS cantidad from {user} WHERE username LIKE '".$idmonitor."-%%%%';";
    $cantidad = $DB->get_record_sql($sql_query);
    $cantidadApariciones= $cantidad->cantidad;
    
    return $cantidadApariciones;
}


//*****************************************************************************





//Funciones para administración del plugion

// function getInfoSystemDirector($username){
//      global $DB;

//         $sql_query = "SELECT id, firstname, lastname,username FROM {user} WHERE username = '".$username."';";
//         $info_user = $DB->get_record_sql($sql_query);
    
//         if($info_user){
//             $sql_query = "SELECT instancia.id as id_talentosinstancia, id_director, id_programa, id_instancia, prog.cod_univalle, prog.nombre, seg_academico, seg_asistencias, seg_socioeducativo FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  WHERE usr.id = ".$info_user->id.";";
//             $rol_user = $DB->get_record_sql($sql_query);
            
//             if(!$rol_user)
//             {
//                 $info_user->cod_programa = 0;
//                 $info_user->nombre_programa = "Ninguno";
//             }
//             else {
//                 $info_user->cod_programa = $rol_user->cod_univalle;
//                 $info_user->nombre_programa = $rol_user->nombre;
//                 $info_user->id_talentosinstancia = $rol_user->id_talentosinstancia;
//                 $info_user->id_instancia = $rol_user->id_instancia;
//             }
//             return $info_user;
//         }else{
//             $object =  new stdClass();
//             $object->error = "Error al consultar la base de datos. El usuario con codigo ".$username." no se encuentra en la base de datos.";
//             return $object;
//         }
// }

// function loadProgramsForSystemsAdmins(){
//     global $DB;
//     $sql_query = "SELECT cod_univalle, nombre FROM {talentospilos_programa} WHERE id NOT IN (SELECT id_programa from {talentospilos_instancia});";
//     return $DB->get_records_sql($sql_query);
// }

// function updateSystemDirector($username, $codPrograma, $idinstancia, $segAca, $segAsis, $segSoc){
//     global $DB;
//     try{
        
//         $directorinfo = getInfoSystemDirector($username);
        
//         $consultPrograma = consultProgram($codPrograma);
//         $consultIntancia = consultInstance($idinstancia);
        
//         if($directorinfo->cod_programa != 0){ //se elima la instanciade en caso de que ya tenga una
//             $DB->delete_records_select('talentospilos_instancia', 'id= '.$directorinfo->id_talentosinstancia);
//             update_role_user($directorinfo->username, "sistemas",$idinstancia,0);
//         }
        
//         if($codPrograma == 0) return true; //0->ningunprograma - previamente se ha borrado una instancia en caso de que tenga una
        
//         if($consultPrograma || $consultIntancia){//update 1126259 - 1144066653
//             $updateObject= new stdClass();
            
//             if($consultPrograma){  // se consulta si ya existe una einstancia en el tabla instancias
//                 $updateObject->id = $consultPrograma->id_talentosinstancia; //
//             }else {
//                 $updateObject->id = $consultIntancia->id_talentosinstancia;
//                 $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle=".$codPrograma.";";
//                 $programa = $DB->get_record_sql($sql_query);
//                 if(!$programa) throw new Exception("NO se encontró el programa");
//                 $updateObject->id_programa = $programa->id;
//             }
            
            
//             $updateObject->id_instancia = $idinstancia;
//             $updateObject->id_director = $directorinfo->id;
//             $updateObject->estado = 1;
//             $updateObject->seg_academico = $segAca;
//             $updateObject->seg_asistencias = $segAsis;
//             $updateObject->seg_socioeducativo = $segSoc;
//             $DB->update_record('talentospilos_instancia', $updateObject);
//             update_role_user($directorinfo->username, "sistemas", $idinstancia); // se actualiza al rol sistemas
            
//         }else{//insert
//             // se opbtiene el id del programa
//             $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle=".$codPrograma.";";
//             $programa = $DB->get_record_sql($sql_query);
//             if(!$programa) throw new Exception("NO se encontró el programa");
            
//             $record = new stdClass; 
//             $record->id_instancia = $idinstancia;
//             $record->id_director = $directorinfo->id; 
//             $record->id_programa = $programa->id;
//             $record->seg_academico = $segAca;
//             $record->seg_asistencias = $segAsis;
//             $record->seg_socioeducativo = $segSoc;
//             $record->estado = 1;
//             $DB->insert_record('talentospilos_instancia', $record, false);
//             update_role_user($directorinfo->username, "sistemas", $idinstancia); // se actualiza al rol sistemas
//         }
//         return true;
    
//     }catch(Exception $e){
//         $errorSqlServer = pg_last_error();
//         $result = $e->getMessage()." <br>".$errorSqlServer;
//         return $result;
//     }
// }

// function consultProgram($codPrograma){
//     global $DB;
//     $sql_query = "SELECT instancia.id as id_talentosinstancia , id_director, id_programa, prog.cod_univalle, prog.nombre FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  WHERE prog.cod_univalle = ".$codPrograma.";";
//     return $consultPrograma = $DB->get_record_sql($sql_query);
// }

// function consultInstance($instanceid){
//     global $DB;
//     $sql_query = "SELECT instancia.id as id_talentosinstancia ,id_instancia id_director, id_programa, prog.nombre, prog.cod_univalle FROM {talentospilos_instancia} instancia INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa   WHERE id_instancia = ".$instanceid.";";
//     $consult = $DB->get_record_sql($sql_query);
//     // print_r($consult);
//     return $consult;
// }

// function getSystemAdministrators(){
//     global $DB;
//     $sql_query ="SELECT instancia.id , username, firstname, lastname, prog.nombre, prog.cod_univalle, instancia.id_instancia FROM {talentospilos_instancia} instancia INNER JOIN {user} usr ON usr.id = instancia.id_director INNER JOIN  {talentospilos_programa} prog ON prog.id = instancia.id_programa  ;";
//     $result = $DB->get_records_sql($sql_query);
    
//     $array = array();
    
//     foreach ($result as $r){
//         $r->programa = $r->cod_univalle." - ".$r->nombre;
//         $r->button = "<a id = \"delete_user\"  ><span  id=\"".$r->id."\" class=\"red glyphicon glyphicon-remove\"></span></a>";
//         array_push($array,$r );
//     }
//     return $array;
// }

// function deleteSystemAdministrator($username){
//     global $DB;
//     $directorinfo = getInfoSystemDirector($username);
//     //print_r($directorinfo);
//     update_role_user($directorinfo->username, "sistemas",$directorinfo->id_instancia,0);
//     $DB->delete_records_select('talentospilos_instancia', 'id= '.$directorinfo->id_talentosinstancia);
    
//     return true;
// }

//funcion para crear zip

function createZip($patchFolder,$patchStorageZip){
    // Get real path for our folder
    $rootPath = realpath($patchFolder);
    
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($patchStorageZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($files as $name => $file)
    {
        // Skip directories (they would be added automatically)
        if (!$file->isDir())
        {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);
    
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    // Zip archive will be created only after closing object
    $zip->close();
}

// ************************************
// Funciones para la gestión del riesgo
// ************************************

// function getRiskByStudent($idStudent){
    
//     global $DB;
//     // $sql_query = "SELECT id FROM {user} WHERE username LIKE '$idStudent%';";
//     // $idUser = $DB->get_record_sql($sql_query); 
    
//     $sql_query = "SELECT riesgo.nombre, r_usuario.calificacion_riesgo
//                   FROM {talentospilos_riesg_usuario} AS r_usuario INNER JOIN {talentospilos_riesgos_ases} AS riesgo ON r_usuario.id_riesgo = riesgo.id WHERE r_usuario.id_usuario = $idStudent";
//     $array_risk = $DB->get_records_sql($sql_query);
    
//     return $array_risk;
    
//     //print_r($array_risk);
// }

/**
 * Realiza una consulta en la base de datos para traer la lista de riesgos
 * posteriormente sera cargada en el arbol 
 * @return $array_risk la lista de riegos en la tabla talentospilos_riesgos_ases
 * @author Edgar Mauricio Ceron
 * */

function getRiskList(){
    
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_riesgos_ases}";
    $array_risk = $DB->get_records_sql($sql_query);
    
    return $array_risk;
}


//funcion para obtener los motivos de retiro

function getMotivosRetiros(){
    global $DB;
    $sql_query = "SELECT *  FROM {talentospilos_motivos}";
    return $DB->get_records_sql($sql_query);
}

function saveMotivoRetiro($talentosid, $motivoid,$detalle){
    global $DB;
    
    $record = new stdClass();
    $record->id_usuario = $talentosid;
    $record->id_motivo = $motivoid;
    $record->detalle = $detalle;
    
    
    $sql_query = "SELECT id FROM {talentospilos_retiros} WHERE id_usuario=".$talentosid;
    $exists = $DB->get_record_sql($sql_query);
    
    if($exists)
    {
        $record->id = $exists->id;
        return $DB->update_record('talentospilos_retiros', $record);
    }
    else
    {
        return $DB->insert_record('talentospilos_retiros', $record, false);    
    }
}

function getMotivoRetiroEstudiante($talentosid){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_retiros} retiro INNER JOIN {talentospilos_motivos} motivo  ON motivo.id = retiro.id_motivo  WHERE id_usuario=".$talentosid;
    
    return $DB->get_record_sql($sql_query);
}

/*
 * Funcion que consulta informacion de los monitores asignados a un practicante
 * 
 * @param $id_practicante
 * @return Array 
 */
function get_monitores_practicante($id_practicante)
{
    global $DB;
    
    
    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname,usuario.lastname  
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_practicante'";

    $consulta=$DB->get_records_sql($sql_query);
    
    $arreglo_retornar= array();
    
    //por cada registro retornado se toma la informacion necesaria, se añade a un arreglo auxiliar y este se agrega 
    //al areglo que sera retornado
    foreach($consulta as $monitores)
    {
        $array_auxiliar=array();
        //posicion 0
        array_push($array_auxiliar,$monitores->id_usuario);
        $nombre = $monitores->firstname ;
        $apellido = $monitores->lastname; 
        $unir = $nombre." ".$apellido;
        //posicion 1
        array_push($array_auxiliar,$unir);
        // array_push($array_auxiliar,get_estudiantes_monitor($id_practicante));
        //posicion n del arreglo que se retorna
        array_push($arreglo_retornar,$array_auxiliar);
    }
    
    return $arreglo_retornar;
}

/*
 * Funcion que consulta informacion de los practicantes asignados a un profesional
 * 
 * @param $id_profesional
 * @return Array 
 */
function get_practicantes_profesional($id_profesional,$id_instancia)
{
    global $DB;

    $sql_query = "SELECT DISTINCT usuario_rol.id_usuario,usuario.firstname AS nombre,usuario.lastname AS apellido 
                  FROM {talentospilos_user_rol} as usuario_rol INNER JOIN {user} AS usuario ON 
                  (usuario.id=usuario_rol.id_usuario) WHERE id_jefe='$id_profesional' and id_rol<>4";
                  

    $consulta=$DB->get_records_sql($sql_query);

    $arreglo_retornar= array();
    $arreglo_cantidades= array();
    $total_registros_no=[];


    //por cada registro retornado se toma la informacion necesaria, se añade a un arreglo auxiliar y este se agrega 
    //al areglo que sera retornado
    foreach($consulta as $practicantes)
    {
        
    $monitores = get_monitores_practicante($practicantes->id_usuario);

    foreach($monitores as $monitor){

    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where revisado_profesional='1' and id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    $total_registros[0] +=$valorRetorno[0]->count;
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    $total_registros[1]+=$valorRetorno[1]->count;
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$monitor[0]' and id_instancia='$id_instancia'";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);
    $total_registros[2] +=$valorRetorno[2]->count;
    }
    
        $array_auxiliar=array();
        //posicion 0
        array_push($array_auxiliar,$practicantes->id_usuario);

        $nombre = $practicantes->nombre ;
        $apellido = $practicantes->apellido; 
        $unir = $nombre." ".$apellido;
        //posicion 1
        array_push($array_auxiliar,$unir);
        array_push($array_auxiliar,$total_registros[0]);
        array_push($array_auxiliar,$total_registros[1]);
        array_push($array_auxiliar,$total_registros[2]);

        array_push($arreglo_retornar,$array_auxiliar);
    }

    print_r($arreglo_retornar);
    return ($arreglo_retornar);
    
}


//Funcion para eliminar registro de seguimiento deacuerdo a un id.

function eliminar_registro($id){

    global $DB;
    $whereclause = "id_seguimiento =".$id;
    $result= $DB->delete_records_select('talentospilos_seg_estudiante',$whereclause);
    $whereclause = "id =".$id;
    $result= $DB->delete_records_select('talentospilos_seguimiento',$whereclause);

    return $result;
}

function updateSeguimiento_pares($object){
     global $DB;
    $fecha_formato =str_replace( '/' , '-' , $object->fecha);
    date_default_timezone_set('America/Los_Angeles'); 
    $object->fecha=strtotime($fecha_formato);
    //se obtiene el id del estudiante al que pertene el seguimiento
    $sql_query = "select id_estudiante from {talentospilos_seg_estudiante}  where id_seguimiento=".$object->id;
    $seg_estud = $DB->get_record_sql($sql_query);
    
    //se obtiene el ultimo seguimeinto perteneciente al estudiante
    $lastSeg = $DB->get_record_sql('SELECT id_seguimiento,MAX(id) FROM {talentospilos_seg_estudiante} seg_est WHERE seg_est.id_estudiante='.$seg_estud->id_estudiante.'GROUP BY id_seguimiento ORDER BY id_seguimiento DESC limit 1');
   
      if($lastSeg->id_seguimiento == $object->id) updateRisks($object, $seg_estud->id_estudiante );
     $lastinsertid = $DB->update_record('talentospilos_seguimiento', $object);

     if($lastinsertid){
         return '1';
     }else{
         return '0';
     }

}

//Funcion para actualizar registro de seguimiento deacuerdo a un id.

function actualizar_registro($id,$lugar,$tema,$objetivos,$obindividual,$riesgoIndividual,$obfamiliar,$riesgoFamiliar,$obacademico,$riesgoAcademico,
$obeconomico,$riesgoEconomico,$obuniversitario,$riesgoUniversitario,$observacionesGeneral,$practicante,$profesional,$fecha,$h_inicial,$h_final){

    global $DB;

    $fecha_formato =str_replace( '/' , '-' , $fecha);
    $record->id = $id;
    $record->lugar = $lugar;
    $record->tema = $tema;
    $record->objetivos = $objetivos;
    $record->individual = $obindividual;
    $record->individual_riesgo = (int)$riesgoIndividual;
    $record->familiar_desc = $obfamiliar;
    $record->familiar_riesgo=(int)$riesgoFamiliar;
    $record->academico=$obacademico;
    $record->academico_riesgo=(int)$riesgoAcademico;
    $record->economico=$obeconomico;
    $record->economico_riesgo=(int)$riesgoEconomico;
    $record->vida_uni=$obuniversitario;
    $record->vida_uni_riesgo=(int)$riesgoUniversitario;
    $record->observaciones=$observacionesGeneral;
    $record->revisado_profesional=$profesional;
    $record->revisado_practicante=$practicante;
    $record->hora_ini=$h_inicial;
    $record->hora_fin=$h_final;
    date_default_timezone_set('America/Los_Angeles'); 
    $record->fecha = strtotime($fecha_formato);
    $lastinsertid = $DB->update_record('talentospilos_seguimiento', $record);
    if($lastinsertid){
        return '1';
    }else{
        return '0';
    }
}



/*
 * funcion que obtiene el ID dado el shortname de la tabla
 * user_info_field
 *
 * @param $shortname
 * @return number
 */


function get_id_info_field($shortname){
    global $DB;
    
    $sql_query = "select id from {user_info_field}  where shortname='$shortname'";
    $consulta=$DB->get_record_sql($sql_query);
    return $consulta;
    
}


//***************************************************
//***************************************************
//***************************************************

/*
 * funcion que trae la informacion necesaria para los seguimientos considerando el monitor actual, la instancia actual asi como
 * que el monitor este asignado como tal a esta instancia
 *
 * @param $id_monitor
 * @param $id_instance 
 * @return Array 
 */


function get_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    

    $id_info_field=get_id_info_field("idtalentos");
    
    
    $sql_query = "SELECT ROW_NUMBER() OVER(ORDER BY seguimiento.id ASC) AS number_unique,seguimiento.id AS id_seguimiento,
                  seguimiento.tipo,usuario_monitor
                  .id AS id_monitor_creo,usuario_monitor.firstname AS nombre_monitor_creo,nombre_usuario_estudiante.firstname 
                  AS nombre_estudiante,nombre_usuario_estudiante.lastname AS apellido_estudiante,seguimiento.created,seguimiento.fecha,seguimiento.hora_ini,
                  seguimiento.hora_fin,seguimiento.lugar,seguimiento.tema,seguimiento.objetivos,seguimiento.actividades,seguimiento.individual,seguimiento.revisado_profesional AS profesional,
                  seguimiento.revisado_practicante AS practicante,seguimiento.individual_riesgo,seguimiento.familiar_desc,seguimiento.familiar_riesgo,seguimiento.academico,
                  seguimiento.academico_riesgo,seguimiento.economico,seguimiento.economico_riesgo, seguimiento.vida_uni,seguimiento.vida_uni_riesgo,
                  seguimiento.observaciones AS observaciones,seguimiento.id AS status,seguimiento.id AS sede, usuario_estudiante.id_tal AS id_estudiante,monitor_actual.id_monitor,
                  usuario_mon_actual.firstname AS nombre_monitor_actual,usuario_mon_actual.lastname AS apellido_monitor_actual, usuario_monitor.lastname AS apellido_monitor_creo
                  FROM {talentospilos_seg_estudiante} AS s_estudiante INNER JOIN {talentospilos_seguimiento} AS seguimiento ON 
                  (s_estudiante.id_seguimiento=seguimiento.id) INNER JOIN {user} AS usuario_monitor ON (seguimiento.id_monitor = usuario_monitor.id) 
                  INNER JOIN (SELECT DISTINCT MAX(data.userid) AS userid, data.data as id_tal FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN mdl_user_info_data AS data 
                  ON (CAST(usuarios_tal.id AS varchar) = data.data) WHERE data.fieldid ='$id_info_field->id' GROUP BY id_tal) AS usuario_estudiante  ON 
                  (usuario_estudiante.id_tal=CAST(s_estudiante.id_estudiante AS varchar)) INNER JOIN {user} as nombre_usuario_estudiante ON 
                  (nombre_usuario_estudiante.id=usuario_estudiante.userid) INNER JOIN {talentospilos_monitor_estud} as monitor_actual 
                  ON (CAST(monitor_actual.id_estudiante AS text)=CAST(s_estudiante.id_estudiante AS text)) INNER JOIN {user} AS usuario_mon_actual ON (monitor_actual.id_monitor=usuario_mon_actual.id)
                  WHERE monitor_actual.id_monitor='$id_monitor' AND seguimiento.id_instancia='$id_instance' AND monitor_actual.id_instancia='$id_instance' ORDER BY usuario_monitor.firstname;
    ";
    
    $consulta=$DB->get_records_sql($sql_query);
    $array_cantidades =[];
    $array_estudiantes=[];

    foreach($consulta as $estudiante)
    {
      //Crea un nuevo array con los datos obtenidos en la consulta y luego agrega :
      //Número de registros del estudiante revisados por el profesional  no revisados por el mismo,Número total de registros del estudiante cuando son de tipo 'PARES'. 
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=1 and tipo='PARES' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_revisados=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=0 and tipo='PARES' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_norevisados=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where id_estudiante='$estudiante->id_estudiante'and tipo='PARES' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_total=$DB->get_record_sql($sql)->count;
      
      //Número de registros del estudiante revisados por el profesional  no revisados por el mismo,Número total de registros del estudiante cuando son de tipo 'PARES'. 

      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=1 and tipo='GRUPAL' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_revisados_grupal=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where revisado_profesional=0 and tipo='GRUPAL' and id_estudiante='$estudiante->id_estudiante' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_norevisados_grupal=$DB->get_record_sql($sql)->count;
      $sql = "SELECT count(*) FROM {talentospilos_seguimiento}  INNER JOIN {talentospilos_seg_estudiante} ON {talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento where id_estudiante='$estudiante->id_estudiante'and tipo='GRUPAL' and id_instancia='$id_instance'";
      $estudiante->registros_estudiantes_total_grupal=$DB->get_record_sql($sql)->count;
      array_push($array_estudiantes,$estudiante);
    }
    
   return $array_estudiantes;
}
/* Obtiene la cantidad de seguimientos de cada monitor.
*/
function get_cantidad_seguimientos_monitor($id_monitor,$id_instance){
    global $DB;
    $valorRetorno=[];
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where revisado_profesional='1' and id_monitor='$id_monitor' and id_instancia='$id_instance'";
    $valorRetorno[0]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where (revisado_profesional='0')and id_monitor='$id_monitor' and id_instancia='$id_instance'";
    $valorRetorno[1]=$DB->get_record_sql($sql_query);
    
    $sql_query= "SELECT count(DISTINCT mdl_talentospilos_seguimiento.id) FROM {talentospilos_seguimiento} INNER JOIN {talentospilos_seg_estudiante} ON ({talentospilos_seguimiento}.id = {talentospilos_seg_estudiante}.id_seguimiento) where  id_monitor='$id_monitor' and id_instancia='$id_instance'";
    $valorRetorno[2]=$DB->get_record_sql($sql_query);

    return $valorRetorno;
}

/**
 * Función que recupera la información de la tabla de seguimientos grupales (estudiantes
 *  respectivos que asistieron a ella -firstname-lastname-username y id ).
 *
 * @see get_seguimientos($id,$tipo,$instancia)
 * @param id --> id correspondiente a la id del estudiante.
 * @param tipo--> tipo correspondiente a "GRUPAL".
 * @param instancia --> instancia 
 * @return array con información de los nombres de los estudiantes que tuvieron un seguimiento grupal dado un idseguimiento.
 */

function get_estudiantes($id,$tipo,$instancia){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} AS seguimiento INNER JOIN mdl_talentospilos_seg_estudiante AS seguimiento_estudiante ON (seguimiento.id=seguimiento_estudiante.id_seguimiento) where seguimiento.id='$id' and tipo='$tipo' and id_instancia='$instancia'";
    $registros=$DB->get_records_sql($sql_query);
    
    foreach($registros as $registro){
        
        $estudiante->id = get_id_user_moodle($registro->id_estudiante); //obtiene el id del estudiante.
        $nombres_estudiantes = " SELECT id, username,firstname,lastname FROM {user} where id='$estudiante->id'"; //obtiene el nombre y el apellido dado el código del estudiante.
        $registros_nombres=$DB->get_records_sql($nombres_estudiantes);

        foreach($registros_nombres as $registro_nombre){
            
          $estudiante->username=$registro_nombre->username;
          $estudiante->firstname=$registro_nombre->firstname;
          $estudiante->lastname=$registro_nombre->lastname;
          $estudiante->idtalentos =$registro->id_estudiante;
          array_push($estudiantes,(array)$estudiante);
        }
    }
    return $estudiantes;    
}


/**
 * Función que recupera la información de la tabla de seguimientos grupales dado un id.
 *
 * @see get_seguimientos($id,$tipo,$instancia)
 * @param id --> id correspondiente a la id del estudiante.
 * @param tipo--> tipo correspondiente a "GRUPAL".
 * @param instancia --> instancia 
 * @return array con información de seguimiento grupal dado un idseguimiento.
 */

function get_seguimientos($id,$tipo,$instancia){
    global $DB;
    $estudiantes=array();

    $sql_query = " SELECT * FROM {talentospilos_seguimiento} where id='$id' and tipo='$tipo' and id_instancia='$instancia'";
    $registros=$DB->get_record_sql($sql_query);
    
    return $registros;    
}







//***************************************************
//***************************************************
//***************************************************


//funcion que retorna el rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
function get_name_rol($userid,$instanceid)
{
    global $DB;
    
    $sql_query = "SELECT id_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);
    
    foreach($consulta as $tomarId)
    {

        $idretornar=$tomarId->id_rol;
    }
    // print_r($idretornar);
    return $idretornar;
}

//funcion que retorna el nombre de  rol de un usuario con el fin de mostrar al correspondiente interfaz en seguimiento_pilos
function get_name_of_rol($userid,$instanceid)
{
    global $DB;
    
    $sql_query = "SELECT nombre_rol FROM {talentospilos_user_rol} WHERE id_usuario='$userid' AND id_instancia='$instanceid'";
    $consulta=$DB->get_records_sql($sql_query);
    
    foreach($consulta as $tomarId)
    {

        $idretornar=$tomarId->id_rol;
    }
    // print_r($idretornar);
    return $idretornar;
}



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

// get_profesional_practicante(1113,534);
//get_name_rol("2","534");

/**
 * Función que recupera los datos adicionales de un estudiante
 *
 * @see get_additional_fields($id_student)
 * @param id_student --> id correspondiente a la tabla {user}
 * @return Array
 */
 
//  function get_additional_fields($id_student){
     
//      global $DB;
//      $sql_query = "SELECT field.shortname, data.data 
//                   FROM {user_info_data} AS data INNER JOIN {user_info_field} AS field ON data.fieldid = field.id 
//                   WHERE data.userid = $id_student";
    
//      $result = $DB->get_records_sql($sql_query);
     
//      $array_result = array();
//      array_push($array_result, $result['idtalentos']);
//      array_push($array_result, $result['idprograma']);
//      array_push($array_result, $result['estado']);
    
//      return $array_result;
//     //  print_r($array_result);
//  }
 
//  get_additional_fields(254);
 
 //metodo apra borrar archivos de un folder
 
function deleteFilesFromFolder($folderPath){
    $files = glob($folderPath.'/*'); // get all file names
    foreach($files as $file){ // iterate files
          if(is_file($file))  unlink($file); // delete file
    }
}

/*
 * Geographic functions
 */

//Método para consultar las coordenadas d euna estudiante

// function getCoordinates($idases){
//     global $DB;
//     $sql_query = "SELECT * FROM {talentospilos_demografia} demo WHERE demo.id_usuario=".$idases;
//     $result = $DB->get_record_sql($sql_query);
//     if(!$result) return false;
    
//     //se tiene el valor del riesgo
//     $sql_query = "SELECT calificacion_riesgo as riesgo FROM {talentospilos_riesgos_ases} riesg INNER JOIN {talentospilos_riesg_usuario} ru ON ru.id_riesgo = riesg.id WHERE riesg.nombre = 'geografico' AND ru.id_usuario =".$idases;
//     $object =  $DB->get_record_sql($sql_query);
//     if($object){
//         $result->riesgo = $object->riesgo;
//     }else{
//         $result->riesgo = 1;
//     }
    
//     return $result;
// }

/**
 * Función que retorna un arreglo de barrios
 *
 * @see load_neighborhood(){
 * @return array
 */

function get_neighborhood(){
    global $DB;
    $sql_query = "SELECT id, nombre FROM {talentospilos_barrios} ORDER BY nombre";
    $array_neighborhood = $DB->get_records_sql($sql_query);
    
    return $array_neighborhood;
}

/**
 * Función que guarda el riesgo geográfico
 *
 * @see save_geographic_risk(){
 * @return bool
 */


function save_geographic_risk($id_student, $rate_risk){
    
    global $DB;
    
    $table = 'talentospilos_riesg_usuario';
    
    $sql_query = "SELECT id FROM {talentospilos_riesgos_ases} WHERE nombre = 'geografico'";
    $risk_id = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_riesg_usuario} WHERE id_usuario = $id_student AND id_riesgo = $risk_id";
    $id_register = $DB->get_record_sql($sql_query)->id;
    
    if($id_register){
        
        $update_record = new stdClass();
        
        $update_record->id = (int)$id_register;
        $update_record->id_usuario = (int)$id_student;
        $update_record->id_riesgo = (int)$risk_id;
        $update_record->calificacion_riesgo = (int)$rate_risk;
        
        $update_result = $DB->update_record($table, $update_record);

        if($update_result){
            return '1';
        }else{
            return '0';
        }
        
    }else{
        $insert_record = new stdClass();
        
        $insert_record->id_usuario = (int)$id_student;
        $insert_record->id_riesgo = (int)$risk_id;
        $insert_record->calificacion_riesgo = (int)$rate_risk;
        
        $insert_result = $DB->insert_record($table, $insert_record);
        
        if($insert_result){
            return '1';
        }else{
            return '0';
        }
    }
    
}

/**
 * Función para obtener los periodos existentes
 *
 * @see get_periodos_semestrales(){
 * @return bool
 */
 
 function get_periodos_semestrales(){
     global $DB;
     $sql_query = "SELECT * FROM mdl_talentospilos_semestre";
     $result = $DB->get_records_sql($sql_query);
     return $result;
 }



// save_geographic_risk(1047, 3);

/**
 * Función que guarda los cambios sobre la ficha geográfica
 *
 * @see save_geographic_info(){
 * @return bool
 */
 
 function save_geographic_info($latitud, $longitud, $id_barrio, $id_student){
     
     global $DB;

     $sql_query = "SELECT id FROM {talentospilos_demografia} WHERE  id_usuario = $id_student";
     $id_record = $DB->get_record_sql($sql_query)->id;
     
     if($id_record){

        $table_demographic = "talentospilos_demografia";
        $table_risk_student = "talentospilos_riesg_usuario";
     
        $update_record = new stdClass();
        
        $update_record->id = $id_record;
        $update_record->longitud = (float)$longitud;
        $update_record->latitud = floatval($latitud);
        $update_record->id_usuario = $id_student;
        $update_record->barrio = $id_barrio;
         
        $result_demographic = $DB->update_record($table_demographic, $update_record);

        if($result_demographic){
            return '1';
        }else{
            return '0';
        }
     }else{
        $table_demographic = "talentospilos_demografia";
        
        $insert_record = new stdClass();
     
        $insert_record->longitud = $longitud;
        $insert_record->latitud = $latitud;
        $insert_record->id_usuario = $id_student;
        $insert_record->barrio = $id_barrio;
         
        $result = $DB->insert_record($table_demographic, $insert_record);
        
        if($result){
            return '1';
        }else{
            return '0';
        }
     }
 }

/*
 * End geographic functions
 */

// ***************
// Email functions
// ***************

/*function get_full_user($id){
    global $DB;
    
    $sql_query = "SELECT * FROM {user} WHERE id= ".$id;
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}*/

function get_full_user_talentos($id){
    global $DB;
    
    $sql_query = "SELECT * FROM {talentospilos_usuario} WHERE id= ".$id;
    $user = $DB->get_record_sql($sql_query);
    
    return $user;
}

 
 //consulta consulta completa
//select ptu.id,puid.fieldid,puid.data,puif.name from prefix_talentospilos_usuario as ptu inner join prefix_user_info_data as puid on (ptu.id=puid.userid) inner join prefix_user_info_field as puif on (puid.fieldid=puif.id);


//getRiskByStudent('1673003-1008');

    
//proximoIndice(6);

//Test
//getCategories(2)

//SECTOR PRUEBAS 
// $inf = getStudentInformation('idtalentos');
// print_r($inf);
//dropInfoEconomica(8);
// $infoEconomica = array();
// $object = new stdClass();
//             $object->id_estudiante ='169';
//             $object->concepto = 'sadf';
//             $object->monto = '43';
//             $object->tipo = 'EGRESO';
            
//             array_push($infoEconomica, $object);

// insertInfoEconomica($infoEconomica);

//actualizando funcionalidad
// function addpermiso(){
//     global $DB;
//     $record = new stdClass; 
//     $record->nombre_func = 'f_socioeducativa_mon';
//     $record->descripcion = 'Ficha psicosocial de un estudiante pilos desde un monitor';
//     $DB->insert_record('talentospilos_funcionalidad', $record, false);
// }
// addpermiso();

//

//permiso para monitor
// global $DB;
// $record = new stdClass; 
// $record->id_rol = 4;
// $record->id_permiso = 2; //leer
// $record->id_funcionalidad = 8; //f_socioeducativa_monitor
// $DB->insert_record('talentospilos_permisos_rol', $record, false);

// $record->id_rol = 4;
// $record->id_permiso = 1; //crear
// $record->id_funcionalidad = 8; //f_socioeducativa_monitor
// $DB->insert_record('talentospilos_permisos_rol', $record, false);

// $record->id_rol = 4;
// $record->id_permiso = 3; //actualizar
// $record->id_funcionalidad = 8; //f_socioeducativa_monitor
// $DB->insert_record('talentospilos_permisos_rol', $record, false);
//monitor_student_assignment('1430461-3743', array("1673017"));
//$receiver = get_complete_user_data('id', 2);
//print_r($receiver->username);
//getStudentsGrupal(2)
//getSegumientoByM onitor(2,'GRUPAL');
//get_current_semester();
//getPormStatus(169);
//getSeguimiento(null,1,'GRUPAL');
//getConcurrentCohortsSPP();
// general_attendance('2016B');
// attendance_by_course('1674296');
// attendance_by_semester('1674296');
// get_usersByPopulation(array("Código"),array("SPT12016A","TODOS","ACTIVO","TODOS"));
//get_userById(array('idtalentos'),"1673003-1008");
//record_check_professional(162);
//print_r($u->fecha_nac);
//update_talentosusuario(array("estado"), array("ACTIVO"),"167T4296-1008");
//get_permisos_role(6, "role");
// $array_students = array("1673006-1008", "1673013-1008", "1673046-1008");
//monitor_student_assignment("1430461-3743", array('1673017'));
// checking_role('administrador');
// get_users_role();
// assign_role_profesional_ps('1124153-3743', 'profesional_ps'as, 1, null, null, 'psicologo')
//manage_role_profesional_ps('1430461-3743','profesional_ps','socioeducativo');




?>