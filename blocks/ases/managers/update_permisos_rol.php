<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    
global $DB;
if(isset($_POST['funciones']) && isset($_POST['rol'])){
    $funciones = $_POST['funciones'];
    foreach($funciones as $permiso){
        
    }   
}