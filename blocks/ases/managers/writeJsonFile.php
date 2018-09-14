<?php

require_once(dirname(__FILE__). '/../../../config.php');
require_once('query.php');
global $DB;

if(isset($_POST['block'])){
    
    $infoinstancia = consultInstance($_POST['block']);
    $asescohorts = "";
    if($infoinstancia->cod_univalle == 1008){
        $asescohorts = "OR ch.idnumber LIKE 'SP%'";
    }
    
    
   $sql_query = "select usuarios.username, usuarios.firstname, usuarios.lastname, usuarios.num_doc, usuarios.id_user, usuarios.tipo_doc from (select * from (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE f.shortname ='idtalentos' and d.data <> '') AS field ON userm.id_user = field.userid ) AS usermoodle INNER JOIN (SELECT *,id AS idtalentos FROM {talentospilos_usuario}) AS usuario ON  usermoodle.data = CAST (usuario.id  AS VARCHAR (3))) AS usuarios INNER JOIN
    (select pr.cod_univalle, usermoodle.id_user from (SELECT * FROM (SELECT *, id AS id_user FROM {user}) AS userm INNER JOIN (SELECT * FROM {user_info_data} as d INNER JOIN {user_info_field} as f ON d.fieldid = f.id WHERE d.data <> '' AND f.shortname='idprograma') AS field ON userm.id_user = field.userid ) AS usermoodle  INNER JOIN  {talentospilos_programa} as pr ON usermoodle.data  = CAST(pr.id AS VARCHAR(4))) as programa ON usuarios.id_user = programa.id_user  INNER JOIN {cohort_members}  cm ON cm.userid = usuarios.userid  INNER JOIN {cohort} ch ON ch.id =cm.cohortid WHERE ch.idnumber LIKE '".$infoinstancia->cod_univalle."%' ".$asescohorts." ;";
    
    
    
    $talentos = $DB->get_records_sql($sql_query);
    
    //se setea la url de la imagen
    
    foreach ($talentos as $value) {
        $value->image = "../../../user/pix.php/".$value->id_user."/f1.jpg";
    }
    
    
    $result = new stdClass();
    
    $result->students = $talentos;
    
    header('Content-Type: application/json');
    file_put_contents('../files/infostudents'.$infoinstancia->cod_univalle.'.json', json_encode($result));
    echo "hecho";  
}else{
    echo "no entro";
}


?>