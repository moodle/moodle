<?php
require('../config.php');
$PAGE->set_title('Cursos Demo');
        $PAGE->set_heading(" ");
        $PAGE->navbar->add("Cursos Demo");
        echo $OUTPUT->header();
?>
<br><br><br>
<div class="container">

<h1><center>Cursos Demos para Estudiantes y Profesores</center></h1>
<h3><center>Los cursos DEMO sobre la Plataforma Moodle ofrecen una oportunidad para explorar y experimentar el Campus Virtual sin temores.</center></h3>
<br>



		<div>

                	<p align="justify"> <a href="https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=23260">
			<b>Curso DEMO para estudiantes</b></a></div>
			<div>Este curso permite a los estudiantes practicar con el Campus Virtual, realizar actividades y utilizar los recursos disponibles.</div>
			<div>Para ingresar al curso y realizar las prácticas, se debe ingresar al Campus Virtual con los siguientes datos:</div>
			<div><b>Nombre de usuario</b>: estudiante<br><b>Contraseña</b>: demoestudiante<br></div><div><br></div><div><div>
			<a target="blank" href="https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=23266"><b>Curso DEMO para profesores</b></a></div>
			<div>Este curso permite a los profesores conocer los recursos y herramientas disponibles en Moodle versión3.3.</div>
			<div>Para ingresar al curso y realizar las prácticas, se debe ingresar al Campus Virtual con los siguientes datos:</div>
			<div><b>Nombre de usuario</b>: profesor<br><b>Contraseña</b>: demoprofesor</div></div><br><p></p</p>

	</div>
<?php
	echo $OUTPUT->footer();
?>
