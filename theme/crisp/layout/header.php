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

//$PAGE->requires->jquery();
//$PAGE->requires->jquery_plugin('ui');
//$PAGE->requires->jquery_plugin('ui-css');
//$PAGE->requires->js('/theme/'.$CFG->theme.'/javascript/font.js');
//$PAGE->requires->js('/theme/'.$CFG->theme.'/lemmon-Lemmon-Slider/lemmon-slider.js');
$PAGE->requires->js_call_amd('theme_crisp/crispy','init');
//$PAGE->requires->js('/theme/'.$CFG->theme.'/javascript/crispy.js');

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
<?php if(!isloggedin()) { ?>
<header role="banner" class="navbar navbar-fixed-top<?php echo $html->navbarclass ?> moodle-has-zindex">
<?php }else{ ?>  
<header id="header-principal"role="banner" class="navbar navbar-fixed-top<?php echo $html->navbarclass ?> moodle-has-zindex">
<?php } ?>
  <nav role="navigation" class="navbar-inner">
      
      
                     
            <div id="header-univalle" class="container">
                  <div class="row-fluid">        
                    <div id="div-img-logo" class="span6">
                        <a href="http://www.univalle.edu.co" target="_blank"><img class="logo-img" src="<?php echo $CFG->wwwroot. '/theme/crisp/img/LogoUnivalle.png';?>"/></a>
                    </div>
                    <div class="span6">
                        <div id="menu-header">
                          <ul>
                            <li><a href="http://www.univalle.edu.co/directorio-univalle" target="_blank">Directorio</a></li>
                            <li><a href="http://www.univalle.edu.co/index.php/correo-electronico-institucional" target="_blank">Correo</a></li>
                            <li><a href="http://biblioteca.univalle.edu.co/" target="_blank">Biblioteca</a></li>
                            <li><a href="http://atencionalciudadano.univalle.edu.co/" target="_blank">Atención al ciudadano</a></li>
                            <li><a href="http://www.univalle.edu.co/index.php/mapa-del-sitio" target="_blank">Mapa del sitio</a></li>
                          </ul>
                        </div>
                        <div class="row-fluid">
                        <?php if(!isloggedin()) { ?>

                              <div id="content-options">
                              <button style="color: white;" class="btn" onClick="location.href='<?php echo $CFG->wwwroot;?>/login'">Ingresar</button>
                              <?php /*idioma*/echo $OUTPUT->custom_menu(); ?>
                              </div>

                        
                        <?php } else {?>
                   
                                  <div class="span5 offset2">
                                      <?php /*idioma*/echo $OUTPUT->custom_menu(); ?>
                                      
                                  </div>
                                  <div id="bug-login" class="span4">
                                    
                                          <?php require('profileblock.php');?>
                                        
                                  </div>

                            
                       <?php } ?>
                          </div>
                     </div>
                   </div>
                  </div>
                    
               
            </div>
          
  </nav>

</header> 
<div id="page" class="container-fluid">

  <header id="page-header" class="clearfix">
    <div class="head-details">
      <div class="row-fluid" style="margin: 0 auto;">
        <div class="span3">
          <h1>
          	<a class="brand" href="<?php echo $CFG->wwwroot;?>"><?php echo $SITE->fullname; ?></a>
          </h1>
        </div> <!-- end of span3 -->
        <div class="span9">
          <div class="shadow" role="navigation">
            <ul id="main-navigation" class="menulist">
              <li id="1" class="list">
	<h6><a id= "children1" class="main" href="<?php echo $CFG->wwwroot."?redirect=0";?>">INICIO</a></h6>
              </li>
              <!--<li id="2" class="list">
               <h6> <a id= "children3" class="main" href="<?php echo $CFG->wwwroot.'/mod/forum/user.php?id='.$USER->id;?>">FOROS</a></h6>
              </li>-->
              <li id="3" class="list">
<h6><a id= "children3" class="main" href="<?php echo $CFG->wwwroot.'/info-dintev/soporte.php';?>">SOPORTE</a></h6>

              </li>
              <li id="4" class="list">
                <h6><a id= "children4" class="main" href="<?php echo $CFG->wwwroot.'/course/index.php';?>">CURSOS</a></h6>
                <?php global $USER; if ($USER->id!=0){?>
                <ul id="vistachild4" class="dropdown">
                <h6><li><a href="<?php echo $CFG->wwwroot.'/course/index.php';?>">Categorías</a></li></h6>
                <h6><li><a href="<?php echo $CFG->wwwroot.'/my';?>">Mis Cursos</a></li></h6>
                <?php
                 global $DB;
                 //añadido para mostrar eliminar cursos
                  $result=$DB->get_records_sql("SELECT 
                    mdl_course.id
                  FROM 
                    public.mdl_user, 
                    public.mdl_role_assignments, 
                    public.mdl_context, 
                    public.mdl_course
                  WHERE 
                    mdl_role_assignments.userid = mdl_user.id AND
                    mdl_role_assignments.contextid = mdl_context.id AND
                    mdl_context.instanceid = mdl_course.id And mdl_role_assignments.roleid='3' And mdl_user.id='$USER->id' limit 1");
                  $totalCursos=count($result);
                  if ($totalCursos!=0){?>
                      <h6><li><a href="<?php echo $CFG->wwwroot.'/course/delete_course_old';?>">Eliminar Cursos</a></li></h6>    
                 <?php } ?>
                </ul>
                <?php } ?>
              </li>
            </ul>
          </div>
        </div> <!-- end of span9 -->
      </div> <!-- end of row-fluid -->
    </div> <!-- end of head-details -->
    <div id="course-header">
        <?php echo $OUTPUT->course_header(); ?>
    </div>
  </header>
   <?php /*} else{*/?>
      <!--<div id="header-espaciado"></div>-->
   <?php /*}*/?>

  <div id="page-navbar" class="clearfix">
    <nav id="pageheader-nav" class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
    <div class="breadcrum-button"> <?php echo $PAGE->button; ?></div>
  </div>
</div>
