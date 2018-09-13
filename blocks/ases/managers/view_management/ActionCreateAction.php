<?php
    require_once(dirname(__FILE__). '/../../../config.php');
    global $DB;

    if(isset($_POST['nombre']) && isset($_POST['descripcion'])){
        $record = new stdClass;
        $record->nombre_accion = $_POST['nombre'];
        $record->descripcion = $_POST['descripcion'];
        $record->estado = true;
        
        $sql_query = "SELECT * FROM {talentospilos_accion} WHERE nombre_accion = '".$record->nombre_accion."'";
        $accion = $DB->get_record_sql($sql_query);
        $repetido = false;
        if($accion->nombre_accion){
            $repetido = true;
        }
        
        
        if(!$repetido){
            $id_nueva_accion = $DB->insert_record('talentospilos_accion', $record, true); 
            $nombre_archivo = $record->nombre_accion.".php";
            $file = fopen(dirname(__FILE__)."/".$nombre_archivo, "w") or die("Unable to open file!");
            $content = 
"
<?php
    /**
    * Accion generada por el generador de codigo de moodle para el 
    * programa de talentos pilos de la universidad del valle
    * @author Edgar Mauricio Ceron Florez
    * @author ESCRIBA AQUI SU NOMBRE */
    require_once(dirname(__FILE__). '/../../../config.php');
    require('validate_profile_action.php');
    $"."accion = '".$id_nueva_accion."';
    global $"."USER;
    $"."id_instancia = $"."_POST['instance'];
    $"."moodle_id = $"."USER->id; 
    $"."user_id = get_talentos_id($"."moodle_id);
    $"."perfil = get_perfil_usuario($"."user_id, $"."id_instancia);
    if(validar_permisos($"."perfil, $"."accion)){
        //Escriba aqui su codigo en caso de que el usuario tenga acceso a la función
    }
    else{
        //Escriba aqui su codigo en caso de que el usuario no tenga acceso a la función
    }
";
            fwrite($file, $content);
            fclose($file);
            echo "Accion creada exitosamente";
        }
        else{
            echo "Ya existe una accion con este nombre, escoja otro nombre";
        }
    }
    
    
    
    
    