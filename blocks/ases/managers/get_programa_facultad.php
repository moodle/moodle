<?php

require_once('query.php');

if(isset($_POST['fun'])){
    if($_POST['fun'] == 'get_program'){
        get_program($_POST['idStudent']);    
    }else if($_POST['fun'] == 'get_school'){
        get_school($_POST['idStudent']);
    }
    
}

function get_program($id_student){

    $array_aditional_fields = get_additional_fields($id_student);
    $academic_program = getPrograma((int)$array_aditional_fields[1]->data);
    
    echo $academic_program->nombre;
}

function get_school($id_student){
    
    $array_aditional_fields = get_additional_fields($id_student);
    $academic_program = getPrograma((int)$array_aditional_fields[1]->data);
    $school = getSchool($academic_program->id_facultad);
    
    echo $school->nombre;
}

