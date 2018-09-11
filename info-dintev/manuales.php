<?php
require('../config.php');
$PAGE->set_title('Modulo Manuales');
        $PAGE->set_heading(" ");
        $PAGE->navbar->add("Modulo Manuales");
        echo $OUTPUT->header();
?>
<br><br><br>
<div class="container">

<h1><center>Manuales</center></h1>
<br>

<a href="https://campusvirtual.univalle.edu.co/moodle/info-dintev/CVUV_usuarios_2015.html"><p align="justify"> <h5><b>Manual de ingreso al Campus Virtual</b></h5></p></a>

 <a target="blank" href="https://docs.moodle.org/all/es/Guía_rápida_del_profesor"><p align="justify"> <h5><b>Manual de Moodle 3.3 para profesores</b></h5></p></a>

<a href="https://campusvirtual.univalle.edu.co/moodle/mod/forum/discuss.php?d=51318"><p align="justify"> <h5><b>Persistencia de cursos en el Campus Virtual</b></h5></p></a>

<a href="https://campusvirtual.univalle.edu.co/moodle/info-dintev/instruccion-inscripciones-cursos.php">
 <p align="justify"><h5><b>Instrucciones para la inscripci&oacuten de los cursos en el Campus Virtual</b></h5></p></a>

<a href="http://dintev.univalle.edu.co/acceda-al-campus-virtual-desde-su-dispositivo-movil">
 <p align="justify"><h5><b>Instrucciones para el ingreso al Campus Virtual usando la Aplicación Movil</b></h5></p></a>

<?php
	echo $OUTPUT->footer();
?>