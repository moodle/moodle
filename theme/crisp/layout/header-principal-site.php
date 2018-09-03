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
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
  <link rel="stylesheet" type="text/css" href="./theme/crisp/style/magnific-popup.css" />
  <link rel="stylesheet" type="text/css" href="./theme/crisp/style/owl_carousel.css" />
  <link rel="stylesheet" type="text/css" href="./theme/crisp/style/owl_theme.css" />
	<style>
      body{
        font-family: Open Sans;
      }
      #page-footer{
           margin-top: -21px !important;
      }
  </style>
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
                        
                     </div>
                   </div>
                  </div>
                    
               
            </div>
          
  </nav>

</header> 
<div id="page" class="container-fluid">
  
  
	
  
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
