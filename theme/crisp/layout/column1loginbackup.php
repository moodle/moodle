<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
// Get the HTML for the settings bits.
/**
 * Moodle's crisp theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_crisp
 * @copyright 2014 dualcube {@link http://dualcube.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $USER;
$html = theme_crisp_get_html_for_settings($OUTPUT, $PAGE);
echo $OUTPUT->doctype()
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
  <title><?php echo $OUTPUT->page_title(); ?></title>
  <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
  <?php echo $OUTPUT->standard_head_html() ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<header id="header-login"role="banner" class="navbar <?php echo $html->navbarclass ?> moodle-has-zindex">

  <nav>

       <div class="row-fluid">
           <div class="span12">
              <a href="https://campusvirtual.univalle.edu.co/moodle"><img class="center-in-span"src="<?php echo $CFG->wwwroot. '/theme/crisp/img/Encabezado_IngresoCampus.png';?>"/></a>
           </div>
        </div>
  </nav>

</header>




<div id="page" class="">
  <div id="page-content" class="row-fluid">

      <div class="container">
        <div id="content-login" class="container">
          <div class="row">
            <div id="msj-login" class="span6">
              <p>Si desea más informaci&oacuten ó  requiere
                asesoría adicional, por favor escríbanos a
                campusvirtual@correounivalle.edu.co ó comuniquese al 3182649 ó 3182653</p>
<a href="https://campusvirtual.univalle.edu.co/moodle/info-dintev/CVUV_usuarios_2015.swf">
<center> <h6><b>Manual de ingreso al Campus Virtual</b></a></h6></p></center>      
 </div>
            <div class="span6">
              <center><h5>Ingreso al Campus Virtual Univalle</h5></center>
              <?php echo $OUTPUT->main_content();?>
              <!-- <div class="forgetpass2"> 
              <a href="forgot_password.php">¿Olvidó su contraseña?</a>
              </div>-->
            <?php echo $OUTPUT->course_content_footer(); ?>
          </div>
        </div>

      </div>

  </div>
</div>
<script>
  
     

  //función para ajustar el tamaño del login cuando aparece mensaje de error
  var error = document.getElementById("loginerrormessage");
  if (error != null) { 
   		//altura del content-login
      var height_content_login = document.getElementById('content-login').offsetHeight; 		

      //aumentamos la altura
      document.getElementById('content-login').style.height = (height_content_login+34)+'px';
      //acomodamos el recuadro con el mensaje
   		document.getElementById('msj-login').style.marginTop ='5.5%';
  }
 </script>
<?php require('footer.php');