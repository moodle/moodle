<?php
require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

echo $DB->insert_record('talentospilos_semestre', array('nombre'=>'2017B','fecha_inicio'=>'2017-08-01','fecha_fin'=>'2017-12-31'));