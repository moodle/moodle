<?php
/**
 * 
 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
 **/
define('CLI_SCRIPT', true);

require("../../config.php");





//Eliminar el curso
$sql="select idcourse from {list_courses_delete}";
$result = $DB->get_records_sql($sql);






foreach ($result as $obj) {
	echo "CURSO ID: ".$obj->idcourse;
	delete_course($obj->idcourse);
	//$course = $DB->get_record('course', array('id' => $obj->idcourse), 'id');
	//var_export($course);
}


$sql="DELETE from {list_courses_delete}";
$result = $DB->execute($sql);


// We do this here because it spits out feedback as it goes.
//delete_course($course);




// Update course count in categories.
fix_course_sortorder();


