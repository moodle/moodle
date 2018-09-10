<?php

require_once(dirname(__FILE__).'/../../../config.php');


function xmldb_block_ases_install_recovery(){
    xmldb_block_ases_install();
}

/**
 * Función que se ejecuta al instalar el módulo Ases
 * 
 * @see xmldb_block_ases_install()
 * @return void
 */
function xmldb_block_ases_install()
{
	global $DB;
	
	$roles_array = array('administrativo', 'reportes', 'profesional_ps', 'monitor_ps', 'estudiante_t', 'sistemas', 'practicante_ps');
	$descripcion_roles_array = array('Actualizar ficha', 'Rol general para directivos y demas personas que tengan permiso de lectura', 'Profesional Psicoeducativo', 'Monitor Psicoeducativo con estudiantes a cargo', 'Estudiante talentos pilos', 'Rol desarrollador','Practicante Psicoeducativo con monitores a cargo');
	$funcionalidades_array = array('carga_csv','reporte_general','f_general','f_academica','f_asistencia','f_socioeducativa_pro','f_socioeducativa_mon', 'gestion_roles');
	$descripcion_funcionalidades_array = array('Carga de información a tablas de la base de datos','Reporte general de estudiantes pilos','Ficha general de un estudiante pilos','Ficha académica de un estudiante pilos','Ficha asistencia de un estudiante pilos','Ficha psicosocial de un estudiante pilos desde un profesional', 'Ficha psicosocial de un estudiante pilos desde un monitor','Gestiona los roles de los usuarios');
	$permisos_array = array('C','R','U','D');
	$descripcion_permisos_array = array('Crear','Leer','Actualizar','Borrar');
	
    for($i = 0; $i < count($roles_array); $i++){
        $record = new stdClass; 
        $record->nombre_rol = $roles_array[$i];
        $record->descripcion = $descripcion_roles_array[$i];
        $DB->insert_record('talentospilos_rol', $record, false);
    }
    
    for($i = 0; $i < count($funcionalidades_array); $i++){
        $record = new stdClass; 
        $record->nombre_func = $funcionalidades_array[$i];
        $record->descripcion = $descripcion_funcionalidades_array[$i];
        $DB->insert_record('talentospilos_funcionalidad', $record, false);
    }
    
    for($i = 0; $i < count($permisos_array); $i++){
        $record = new stdClass; 
        $record->permiso = $permisos_array[$i];
        $record->descripcion = $descripcion_permisos_array[$i];
        $DB->insert_record('talentospilos_permisos', $record, false);
    }
    
    for($i = 1; $i <= 7; $i++){
        for($j = 1; $j <= 4; $j++)
        {
            $record->id_rol = 6;
            $record->id_permiso = $j;
            $record->id_funcionalidad = $i;
            $DB->insert_record('talentospilos_permisos_rol', $record, false);
        }
    }
    
    $record->id_rol = 1;
    $record->id_permiso = 2;
    $record->id_funcionalidad = 2;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 1;
    $record->id_permiso = 2;
    $record->id_funcionalidad = 3;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 1;
    $record->id_permiso = 3;
    $record->id_funcionalidad = 3;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    //permiso para monitor
    $record->id_rol = 4;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 7; //f_socioeducativa_monitor
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 4;
    $record->id_permiso = 1; //crear
    $record->id_funcionalidad = 7; //f_socioeducativa_monitor
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 4;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 7; //f_socioeducativa_monitor
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    // **************************************************
    // ** permisos para rol profesional psicosocial **
    // **************************************************
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 1; //crear
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 2; //reporte_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);

    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 3; //f_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 3; //f_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 4; //f_academica
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    //permisos para rol reportes
    $record->id_rol = 2;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 2; //reporte_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 2;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 3; //f_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 2;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 5; //f_asistencia
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    //permisos para la gestión de monitores y practicantes
    $record->id_rol = 3;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 9; //gestion_monitores_practicantes
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 3;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 9; //gestion_monitores_practicantes
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    // **************************************************
    // ** permisos para rol practicante psicoeducativo **
    // **************************************************
    $record->id_rol = 7;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 2; //reporte_general
    $DB->insert_record('talentospilos_permisos_rol', $record, false);

    $record->id_rol = 7;
    $record->id_permiso = 2; //leer
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 7;
    $record->id_permiso = 1; //crear
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = 7;
    $record->id_permiso = 3; //actualizar
    $record->id_funcionalidad = 6; //f_socioeducativa_profesional
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    // ****************************
    // ** Carga de departamentos **
    // ****************************
    
    if (!($handle = fopen("../files/departamentos.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'departamentos.csv'. Es posible que el archivo se encuentre dañado");
    
    pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");
    
    global $DB;
    // esta tabla no depende de otra
    $record = new stdClass();
    $count = 0;
         
    while($data = fgetcsv($handle, 1000, ",")){
        $record->codigodivipola = $data[0];
        $record->nombre = $data[1];
        
        $DB->insert_record('talentospilos_departamento', $record, false);
        $count+=1;
    }
    
    //se termina la transaccion
    pg_query("COMMIT") or die("La transacción ha fallado\n");
    fclose($handle);
    
    // *************************
    // ** Carga de municipios **
    // *************************
    
    if (!($handle = fopen("../files/municipios.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'departamentos.csv'. Es posible que el archivo se encuentre dañado");

    pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");
    
    global $DB;
    $record = new stdClass();
    $count = 0;
    $array_id = array();
    $array_data = array();
    $line_count =1;
    
    while($data = fgetcsv($handle, 100, ",")){  
        array_push($array_data, $data);
        
        $query = "SELECT id FROM {talentospilos_departamento} WHERE codigodivipola = ".intval($data[1]).";";
        
        $result = $DB->get_record_sql($query);
        
        if(!$result){
           throw new MyException("Por favor Revisa la línea ".$line_count.".<br>El codigo de División Política del departamento ".$data[1]." asociado al  municipio ".$data[2]." no se encuentra en la base de datos");
        }
        array_push($array_id, $result->id);
        $line_count+=1;
    }
    
    foreach ($array_data as $dat){
        $record->codigodivipola = $dat[0];
        $record->cod_depto = $array_id[$count];
        $record->nombre = $dat[2];
        $DB->insert_record('talentospilos_municipio', $record, false);
        $count += 1;
    }
    
    //se termina la transaccion
    pg_query("COMMIT") or die("La transacción ha fallado\n");
    fclose($handle);
    
    // ********************
    // ** Carga de sedes **
    // ********************
    
    if (!($handle = fopen("../files/sede.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'sede.csv'. Es posible que el archivo se encuentre dañado");

    pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");
    
     global $DB;
     $record = new stdClass();
     $count = 0;
     $array_id = array();
     $array_data = array();
     $line_count=1;
     
     while($data = fgetcsv($handle, 100, ",")){
        array_push($array_data, $data);
        
        $query = "SELECT id FROM {talentospilos_municipio} WHERE codigodivipola ='".intval($data[0])."';";
        $result = $DB->get_record_sql($query);
        if(!$result){
           throw new MyException("Por favor revisa la linea".$line_count.".<br>El codigo de División Política de la ciudad ".$data[0]." asociado a la sede".$data[2]." no se encuentra en la base de datos");
        }
        array_push($array_id, $result->id);
        $line_count+=1;
     }
    
     foreach ($array_data as $data){ 
        $record->id_ciudad = $array_id[$count];
        $record->cod_univalle = $data[1];
        $record->nombre = $data[2];
        
        $DB->insert_record('talentospilos_sede', $record, false);
       $count+=1;
     }
    
    //se termina la transaccion
    pg_query("COMMIT") or die("La transacción ha fallado\n");
    fclose($handle);
    
    // *************************
    // ** Carga de facultades **
    // *************************
    
    if (!($handle = fopen("../files/facultad.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'facultad.csv'. Es posible que el archivo se encuentre dañado");

    pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");
    
    
    global $DB;
    //esta tabla no depende de otra
    $record = new stdClass();
    $count = 0;
    
    while($data = fgetcsv($handle, 50, ",")){
        $record->cod_univalle = $data[0];
        $record->nombre = $data[1];
        $DB->insert_record('talentospilos_facultad', $record, false);
        $count += 1;
    }
    
    //se termina la transaccion
    pg_query("COMMIT") or die("La transacción ha fallado\n");
            
    // ************************
    // ** Carga de programas **
    // ************************
    
    if (!($handle = fopen("../files/programa.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'programa.csv'. Es posible que el archivo se encuentre dañado");

    pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");
    
    global $DB;
    $record = new stdClass();
    $count = 0;
    $array_id_sede = array();
    $array_id_fac = array();
    $array_data = array();
    $line_count = 1;
    
    while($data = fgetcsv($handle, 1000, ",")){
    
        array_push($array_data, $data);
        
        $query = "SELECT id FROM {talentospilos_sede} WHERE cod_univalle ='".intval($data[3])."';";
        $result = $DB->get_record_sql($query);
        if(!$result){
           throw new MyException("Por favor revisa la linea ".$line_count.".<br>El codigo Univalle de la sede ".$data[0]." asociado al programa ".$data[2]." no se encuentra en la base de datos");
        }
        array_push($array_id_sede, $result->id);
        
        //se verifica el codigo de l facultad
        $query = "SELECT id FROM {talentospilos_facultad} WHERE cod_univalle ='".$data[4]."';";
        $result = $DB->get_record_sql($query);
        if(!$result){
           throw new MyException("El codigo Univalle de la facultad ".intval($data[4])." asociado al programa ".$data[2]." no se encuentra en la base de datos. linea ".$line_count." ".$data[0]."-".$data[1]."-".$data[2]."-".$data[3]."-".$data[4]."");
        }
        $line_count+=1;
        array_push($array_id_fac, $result->id);
    }
    
    foreach($array_data as $data){ 
       $record->codigosnies = $data[0];
       $record->cod_univalle = $data[1];
       $record->nombre = $data[2];
       $record->id_sede = $array_id_sede[$count];
       $record->id_facultad = $array_id_fac[$count];
       $record->jornada = $data[5];
       
       $DB->insert_record('talentospilos_programa', $record, false);
      $count+=1;
    }
    //se termina la transaccion
    pg_query("COMMIT") or die("La transacción ha fallado\n");
    fclose($handle);
    
    // ***************************
    // ** Carga de discapacidad **
    // ***************************

    if (!($handle = fopen("../files/discapacidad.csv", 'r'))) throw new MyException("Error al cargar el archivo: 'discapacidad.csv'. Es posible que el archivo se encuentre dañado");

    pg_query("BEGIN") or die("No es posible iniciar la transacción en la base de datos\n");
    
    global $DB;
    //no depende de ninguna tabla
    $record = new stdClass();
    $count = 0;
    
    while($data = fgetcsv($handle, 100, ",")){ 
      $record->codigo_men = $data[0];
      $record->nombre = $data[1];
       
      $DB->insert_record('talentospilos_discap_men', $record, false);
      $count+=1;
    }
    
    //se termina la transaccion
    pg_query("COMMIT") or die("La transacción ha fallado\n");
    fclose($handle);
    
    
    set_config('block_ases_late_install', 1);
}
	 


