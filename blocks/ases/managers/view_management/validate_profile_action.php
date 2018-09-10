<?php
    require_once(__DIR__ . '/../../../../config.php');
    // print_r(__DIR__);
    global $user;
    $accion = $_POST["id_accion"];
    $blockid = $_POST['block'];
    
    
    //Hace falta validar que el perfil este en el periodo actual
    function get_perfil_usuario($id_moodle, $id_instancia){
        global $DB;
        $current_semester = get_current_semester(); 
       
        $sql_query = "SELECT * FROM {talentospilos_perfil} WHERE id IN (
                        SELECT id_perfil FROM {talentospilos_usuario_perfil} 
                            WHERE estado = 1 
                            AND id_semestre =".$current_semester->max."
                            AND id_usuario = ".$id_moodle." 
                            AND id_instancia =".$id_instancia."
                        );";
        $perfil_usuario = $DB->get_record_sql($sql_query);            
        return $perfil_usuario->id;
    }
    
    
    /**
     * Verifica si un rol tiene acceso a la acción especificada
     * @param $id_perfil ID del perfil a evaluar 
     * @param $id_accion ID de la accion a evaluar
     * @return booelan True si tiene permisos para realizar la acción, false si nos tiene
     * @author Edgar Mauricio Ceron Florez
     */ 
    
    function validar_permisos($id_perfil, $id_accion){
        global $DB;
        $sql_query = "SELECT * FROM {talentospilos_perfil_accion} AS pa
        WHERE id_perfil =".$id_perfil." AND id_accion = ".$id_accion.";";
        $permiso = $DB->get_record_sql($sql_query);
        return $permiso->habilitado;
    }
    
     /**
     * Función que retorna el identificador del semestre actual 
     * @see get_current_semester()
     * @return cadena de texto que representa el semestre actual
     */
    function get_current_semester(){
        global $DB;
        $sql_query = "SELECT id AS max, nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
        $current_semester = $DB->get_record_sql($sql_query);
        return $current_semester;
    }
    
    /**
     * Retorna el id de talentos a partir del id de moodle
     * @param $id_moodle int con el di de moodle
     * @return int con el id de talentos
     */ 
    
    function get_talentos_id($id_moodle){
        global $DB;
        $sql_query = "SELECT field.shortname, data.data 
                   FROM {user_info_data} AS data INNER JOIN {user_info_field} AS field ON data.fieldid = field.id 
                   WHERE data.userid = $id_moodle";
        
        $result = $DB->get_records_sql($sql_query);
        
        return $result['idtalentos']->data;
    }
    
