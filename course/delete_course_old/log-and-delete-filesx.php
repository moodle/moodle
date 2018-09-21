<?php
/**
 * 
 * @author Hernán Darío Arango C. <hernan.arango@yahoo.com>
 **/
require("../../config.php");

$idCurso   = optional_param('id', 0, PARAM_INT);

//realizamos la copia de los nombres de los archivos a nuestra tabla temporal
/*$sql="select mdl_files.filename as filename, mdl_files.contenthash,mdl_files.filesize
	FROM 
	  public.mdl_course, 
	  public.mdl_files, 
	  public.mdl_context, 
	  public.mdl_course_modules
	WHERE 
	  mdl_context.id = mdl_files.contextid AND
	  mdl_context.instanceid = mdl_course_modules.id AND
	  mdl_course_modules.course = mdl_course.id and mdl_course.id='$idCurso' and mdl_files.filename!='.'";
$result=$DB->get_records_sql($sql);

foreach ($result as $key => $file) {

	$sql="INSERT INTO mdl_log_file_course_deleted(filename, contenthash, filesize) VALUES (?,?,?)";
	$params=array($file->filename, $file->contenthash, $file->filesize);
	$DB->execute($sql,$params);
	
}*/


//Eliminar el curso
$course = $DB->get_record('course', array('id' => $idCurso), '*', MUST_EXIST);

$categorycontext = context_coursecat::instance($course->category);
$PAGE->set_url('/course/delete.php', array('id' => $idCurso));
$PAGE->set_context($categorycontext);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url(new moodle_url('/course/management.php', array('categoryid'=>$course->category)));

$courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
$coursefullname = format_string($course->fullname, true, array('context' => $coursecontext));

    
$strdeletingcourse = get_string("deletingcourse", "", $courseshortname);

$PAGE->navbar->add($strdeletingcourse);
$PAGE->set_title("$SITE->shortname: $strdeletingcourse");
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($strdeletingcourse);

// We do this here because it spits out feedback as it goes.
delete_course($course);

echo $OUTPUT->heading( get_string("deletedcourse", "", $courseshortname) );
// Update course count in categories.
fix_course_sortorder();
echo $OUTPUT->continue_button(new moodle_url('/course/delete_course_old'));
echo $OUTPUT->footer();

