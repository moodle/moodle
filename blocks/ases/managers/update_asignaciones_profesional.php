<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;

    echo "asignacion profesional";
    echo $DB->insert_record('talentospilos_user_rol', array('id_rol'=>3,'id_usuario'=>90597,'estado'=>1,'id_semestre'=>5,'id_jefe'=>110954,'id_instancia' => 450299));
    $update1="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 89004";
    $update2="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 85742";
    $update3="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 93989";
    $update4="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 85729";
    $update5="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 99546";
    $update6="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 99732";
    $update7="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 110773";
    $update8="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 95832";
    $update9="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 85764";
    $update10="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 85735";
    $update11="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 64029";
    $update12="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 85766";
    $update13="UPDATE {talentospilos_user_rol} SET id_jefe = 90597 WHERE id_usuario = 92190";
    
    
    echo $DB->execute($update1);
    echo $DB->execute($update2);
    echo $DB->execute($update3);
    echo $DB->execute($update4);
    echo $DB->execute($update5);
    echo $DB->execute($update6);
    echo $DB->execute($update7);
    echo $DB->execute($update8);
    echo $DB->execute($update9);
    echo $DB->execute($update10);
    echo $DB->execute($update11);
    echo $DB->execute($update12);
    echo $DB->execute($update13);