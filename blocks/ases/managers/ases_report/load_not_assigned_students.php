
<?php
require_once ('asesreport_lib.php');

require_once ('asesreport_functions.php');

require_once ('../validate_profile_action.php');

require_once (dirname(__FILE__) . '/../role_management/role_management_lib.php');

$columns = array();
$conditions = array(); // Condiciones para la consulta
$query_fields = array();
$risk_fields = array();
$academic_fields = array();
$name_columns = new stdClass();
$practicants = array();
$monitors = array();
global $USER;

$fields_format = array(
    'student_code' => 'user_moodle.username',
    'firstname' => 'user_moodle.firstname',
    'lastname' => 'user_moodle.lastname',
    'document_id' => 'tp_user.num_doc',
    'cohort'=>'all_students_cht.cohorts_student',
    'email' => 'tp_user.emailpilos',
    'cellphone' => 'tp_user.celular',
    'address' => 'tp_user.direccion_res',
    'program_code' => 'acad_program.cod_univalle AS cod_univalle',
    'name_program' => 'acad_program.nombre AS nombre_programa',
    'faculty' => 'faculty.nombre AS nombre_facultad'
);
$columns_format = array(
    'student_code' => 'Código estudiante',
    'firstname' => 'Nombre(s)',
    'lastname' => 'Apellido(s)',
    'document_id' => 'Número de documento',
    'cohort'=>'Cohorte',
    'email' => 'Correo electrónico',
    'cellphone' => 'Celular',
    'address' => 'Dirección residencia',
    'program_code' => 'Código programa',
    'name_program' => 'Programa académico',
    'faculty' => 'Facultad'
);

if (isset($_POST['conditions']))
    {

    foreach($_POST['conditions'] as $condition)
        {
        array_push($conditions, $condition);
        }
    }


if (isset($_POST['fields']))
    {
    foreach($_POST['fields'] as $field)
        {
        array_push($query_fields, $fields_format[$field]);
        array_push($columns, array(
            "title" => $columns_format[$field],
            "name" => explode('.', $fields_format[$field]) [1],
            "data" => explode('.', $fields_format[$field]) [1]
        ));
        }
    }



if (isset($_POST['academic_fields']))
    {
    foreach($_POST['academic_fields'] as $academic_field)
        {

        array_push($academic_fields, $fields_format[$academic_field]);
        array_push($columns, array(
            "title" => $columns_format[$academic_field],
            "name" => explode(' ', $fields_format[$academic_field]) [2],
            "data" => explode(' ', $fields_format[$academic_field]) [2]
        ));
        }
    }




if(isset($_POST['instance_id'])){

$instancia =$_POST['instance_id'];
$current_role = get_id_rol($USER->id,$instancia);
$role_name = get_name_role($current_role);
$monitors=[];

if ($role_name == 'profesional_ps')
    {
    $practicants = get_pract_of_prof($USER->id, $_POST['instance_id']);
   // $monitors = get_monitors_of_pract(array_values($practicants) [0]->id_usuario, $_POST['instance_id']);
    }
}

array_push($columns, array(
    "title" => "Escoger practicante",
    "name" => "practicantes",
    "data" => "practicante"
));
array_push($columns, array(
    "title" => "Escoger monitor",
    "name" => "monitores",
    "data" => "monitor"
));

array_push($columns, array(
    "title" => "Asignar",
    "name" => "student_assign",
    "data" => "assign"
));

if (isset($_POST['instance_id']))
    {
    $counter = 0;
    $result = get_not_assign_students($query_fields, $conditions, $academic_fields, $_POST['instance_id']);
    $data = array(
        "bsort" => false,
        "data" => get_assign($result, $practicants, $monitors) ,
        "columns" => $columns,
        "select" => "false",
        "scrollX" => true,
        "language" => array(
            "search" => "Buscar:",
            "oPaginate" => array(
                "sFirst" => "Primero",
                "sLast" => "Último",
                "sNext" => "Siguiente",
                "sPrevious" => "Anterior"
            ) ,
            "sProcessing" => "Procesando...",
            "sLengthMenu" => "Mostrar _MENU_ registros",
            "sZeroRecords" => "No se encontraron resultados",
            "sEmptyTable" => "Ningún dato disponible en esta tabla",
            "sInfo" => "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty" => "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered" => "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix" => "",
            "sSearch" => "Buscar:",
            "sUrl" => "",
            "sInfoThousands" => ",",
            "sLoadingRecords" => "Cargando...",
            "oAria" => array(
                "sSortAscending" => ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending" => ": Activar para ordenar la columna de manera descendente"
            )
        ) ,
        "autoFill" => "true",
        "dom" => "lfrtBip",
        "buttons" => array(
            array(
                "extend" => "pdf",
                "message" => "Generando PDF"
            ) ,
            "csv",
            "excel"
        )
    );
    header('Content-Type: application/json');
    echo json_encode($data);
    }

?>