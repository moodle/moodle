<?php
	require('../config.php');

	global $DB;

    $num_usuarios = $DB->count_records('user'); //Contar las filas de la tabla de usuarios

    echo 'Total usuarios '.$num_usuarios.'</br>'; //COntar el número de usuarios en la BD

	$usuarios_sql = "SELECT id,username, department, SPLIT_PART(username, '-', 2) as programa
				     FROM {user}";
	$usuarios = $DB->get_records_sql($usuarios_sql);

    $conta = 0;
    foreach ($usuarios as $usuario) {
    	$conta++; //Contador para comparar que se hayan actualizado la mayor cantidad de usuarios
    	if($usuario->programa != "" && is_numeric($usuario->programa)){//Verificar si es estudiante, que el username tenga el plan
			$sql_programas = "SELECT prg.*, fac.nombre as facultad
                              FROM {talentospilos_programa} prg, {talentospilos_facultad} fac 
                              WHERE prg.id_facultad = fac.id AND prg.cod_univalle = $usuario->programa";

        	$programas = $DB->get_records_sql($sql_programas);
        	
        	foreach ($programas as $programa) {
        		$str_strtolower = mb_strtolower($programa->facultad,'UTF-8'); //Convertir a minúsculas
        		$str_ucwords = ucwords($str_strtolower, " "); //Primera mayúscula

        		$update_user = new StdClass;
    			$update_user->id = $usuario->id;
    			$update_user->department = ' Facultad de '.$str_ucwords;

    			if ($DB->update_record('user', $update_user)) {
					echo $conta.' El usuario '.$usuario->username.' se ha actualizado el departamento a Facultad de '.$str_ucwords.'</br>';
				}
	        	break;
        	}
        }
    }
    echo 'Actualizado realizada correctamente';
