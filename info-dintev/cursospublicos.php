<?php
require('../config.php');
$PAGE->set_title('Cursos Demo');
        $PAGE->set_heading(" ");
        $PAGE->navbar->add("Cursos Demo");
        echo $OUTPUT->header();
?>
<br><br><br>
<div class="container">

<h1><center>Cursos P&uacuteblicos</center></h1>
<br>
		<div id="cursos_publicos">
<ul>
<h5><p align="justify"> 
	
	
<!-- <li class="libro_rojo"><a href="https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=5670" title="UNESCO: Comprensión y producción de textos">Comprensión y Producción de Textos - UNESCO</a></li> -->
<li class="libro_rojo"><a title="Recordando las matemáticas del colegio" href="https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=34259">Recordando las matemáticas del colegio</a></li>

<li class="libro_rojo"><a title="Fortaleciendo las matemáticas en la universidad" href="https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=34258">Fortaleciendo las matemáticas en la universidad</a></li>

<li class="libro_rojo"><a title="Comprensión producción de textos cátedra UNESCO" href="https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=29266">Comprensión producción de textos cátedra UNESCO</a></li>



</ul>
</div></p></h5>
		
	</div>
<?php
	echo $OUTPUT->footer();
?>




