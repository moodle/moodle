
<?php
	//esto es para poder ejecutarlo por linea de comandos
	define('CLI_SCRIPT', true);

	require("../../../config.php");



	$sql="select * from mdl_log_file_course_deleted";
	$result=$DB->get_records_sql($sql);
	$sizeFiles=0;

	foreach ($result as $key => $file) {
		$sizeFiles+=$file->filesize;
		echo $file->filename."   ".$file->contenthash."\n";
		$name_archivo=$file->contenthash;
		$rutaTotal=$CFG->dataroot."/filedir/".substr($name_archivo, 0, 2)."/".substr($name_archivo, 2, 2)."/".$name_archivo;
		unlink($rutaTotal);
		$DB->execute("delete from mdl_log_file_course_deleted where id='$file->id'");
	}
	echo "\n Tamaño total liberado ".ceil($sizeFiles/1048576)." MB \n";
	
?>
