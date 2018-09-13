<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    
global $DB;
if(isset($_POST['nombre']) && isset($_POST['descripcion'])){
    $record = new stdClass;
    $record->nombre = $_POST['nombre'];
    $record->descripcion = $_POST['descripcion'];
    $DB->insert_record('talentospilos_rol', $record, false);
}