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

	//require_once($CFG->dirroot.'/calendar/lib.php');
	$html = theme_crisp_get_html_for_settings($OUTPUT, $PAGE);
	global $DB, $USER;
	if (right_to_left()) {
	    $regionbsid = 'region-bs-main-and-post';
	} else {
	    $regionbsid = 'region-bs-main-and-pre';
	}
	$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
	?>
	<?php require('header-principal-site.php'); ?>    

	<div id="show-admin">
		<a class="admin-sets" href="#">
			<span></span>
		</a>
		<div class="adminset">  
		<?php if ($hassidepre) { ?>
			<?php echo $OUTPUT->blocks_for_region('side-pre') ?>
		<?php 
	}
	?>
		</div>
	</div>
		
	<div id="page-content" class="container">
		<div class="row-fluid">
			<section id="region-main" class="span12 pull-right">
				<!-- Necessary HTML -->
				<div class="bodydetails">
					
					<!--primer slider e inicio de secion-->
						<div id='box-slide-login' class='row-fluid'>
							
							<!-- login -->
							<div id='login-box' class='span4'>
								<div id="login-header">
									<center><h2>Campus Virtual </h2>
										<?php
										if (!isloggedin()) {?>
										<i class="fa fa-user" aria-hidden="true"></i>ENTRAR
										<?php } ?>
									</center>


								</div>
								<div id="login-body">
									<?php
											if (isloggedin() && !isguestuser()) {?>
											<div id="info-user-login">
												<?php
												echo $OUTPUT->user_picture($USER, array('size'=>75));?><br><?php
												echo $USER->firstname.$USER->lastname;?><br><?php
												echo $USER->email;?><br><br>
												<a href="<?php echo $CFG->wwwroot?>/my/" id="btn-info-user-login">Mis Cursos</a>
												<a href="<?php echo $CFG->wwwroot?>/user/profile.php?id=<?php echo $USER->id;?>" id="btn-info-user-login">Perfil</a>
												<a href="<?php echo $CFG->wwwroot?>/login/logout.php?sesskey=<?php echo $USER->sesskey;?>" id="btn-info-user-login">Salir</a>
											</div>
											<?php
											}
											else{
												?>
											<form action="<?php echo $CFG->wwwroot.'/login/index.php';?>" method="post" id="login">
								                <div class="loginform-principal">
								                    <div class="form-label">
								                        <label for="username">
								                                <?php echo get_string('username');?>
								                        </label>
								                    </div>
								                    <div class="form-input">
								                        <input name="username" id="username" size="15" value="" type="text">
								                    </div>
								                    <div class="clearer"><!-- --></div>
								                    <div class="form-label">
								                        <label for="password"><?php echo get_string('password');?></label>
								                    </div>
								                    <div class="form-input">
								                        <input name="password" id="password" size="15" value="" type="password">
								                    </div>
								                </div>

								                <div class="clearer"><!-- --></div>
								                    <div class="rememberpass">
								                        <input name="rememberusername" id="rememberusername" value="1" type="checkbox">
								                        <label for="rememberusername">Recordar Usuario</label>
								                    </div>
								                <div class="clearer"><!-- --></div>
								                <input id="anchor" name="anchor" value="" type="hidden">
								                <input id="loginbtn" value="Acceder" type="submit">
								                
								            </form><?php } ?>
								</div>

								<div id="login-footer">
										<div class="forgetpass">
						                    <a href="<?php echo $CFG->wwwroot; ?>/login/forgot_password.php">¿Olvidaste tú usuario o contraseña?</a>
						                </div>
								</div>

							</div>
						
						<!-- fin inicio de sesion-->

						<!-- carousel -->  
						<div id='slider-box' class='span8'>
						    <div id="owl-demo" class="owl-carousel owl-theme">
									<?php
										$numberofslides = theme_crisp_get_setting('numberofslides');
										for($i = 1; $i <= $numberofslides; $i++){
											$slideimg = theme_crisp_render_slideimg($i, 'slide'.$i.'image');
											$url = theme_crisp_get_setting('slide'.$i.'caption');
									?>
										<a href="<?php echo $url; ?>" target="_blank"><div class="item"><img src="<?php echo $slideimg; ?>" alt=""/></div></a>				
									<?php } ?>
						    </div>							
						</div>
					</div>
					<!--FIN-->
					<div class="row-fluid">
						<div class="span12 text_uv_home">
						<p ALIGN="justify">El Campus Virtual de la Universidad del Valle es nuestro espacio virtual interactivo donde los profesores y estudiantes desarrollan actividades académicas y de comunicación en los cursos, a través de contenidos multimedia y recursos digitales, así como de actividades individuales, colaborativas y de participación. También hace posible realizar evaluaciones en cada etapa del proceso formativo permitiendo que el estudiante reciba retroalimentación y esté informado sobre su progreso.</p>
						</div>
					</div>

					<div id="contenedor2" class="container">
						<?php 
							//MENSAJES PARA MOSTRAR EN LA PÁGINA PRINCIPAL (MANTENIMIENTO DEL CAMPUS,MENSAJES IMPORTANTES ETC)
							$pluginname = 'theme_crisp';
							$fieldname = 'textinformation';
							$body = $DB->get_record_sql('select mcp.value from {config_plugins} mcp
							  where mcp.plugin = ? and mcp.name = ?', array($pluginname, $fieldname));
						
						
							
								if (!empty($body->value)) {
						?>
										<div style="margin: 0px 0px 0px 110px;">
											<?php	echo format_text($body->value, "", $crispformatoptions); ?>
										</div>
						<?php				
								}
						?>							

						<div class="bodytexts row-fluid">
							<div class="forsupport span4">
								<div class="icons">
									<a target="_blank" href="<?php echo $CFG->wwwroot.'/info-dintev/manuales.php';?>">
										<div id="ico-1" class="container"></div>
									</a>
									<div class="heads">
										<p><b>Soporte</b></p>
									</div>
									<div class="texts">
										<ul class='lista-iconos-principales'>
											<li><i class="fa fa-plus" aria-hidden="true"></i><a href='<?php echo $CFG->wwwroot.'/login/forgot_password.php';?>' target="_blank">Recuperar la contraseña</a></li>
											<li><i class="fa fa-plus" aria-hidden="true"></i><a href='<?php echo $CFG->wwwroot.'/info-dintev/instruccion-inscripciones-cursos.php';?>' target="_blank">Inscribir cursos</a></li>
											<li><i class="fa fa-plus" aria-hidden="true"></i><a href='https://docs.moodle.org/all/es/Gu%C3%ADa_r%C3%A1pida_del_profesor' target="_blank">Guía rápida de Moodle 3.3</a></li>
                                                                                        <li><i class="fa fa-plus" aria-hidden="true"></i><a href='http://cuse.univalle.edu.co/tutoriales' target="_blank">Tutoriales realizados por Fac. Admón.</a></li>										
                                                                                </ul>
										<hr id="hr-uv">
										<p id="info-cita-uv">Solicite una cita personalizada<br>
											<span style='color: #d51b23'>campusvirtual@correounivalle.edu.co</span>
											<br><strong>Horario de atención:</strong><br>
											Lunes,Miércoles y Viernes de 10 a.m a 12m.<br>
											Martes y Jueves de 2 a 4pm.
										</p>										
									</div>
								</div> <!-- end of forsupport -->
							</div>
							<div class="forcourses span4">
								<div class="icons">
									<a href="<?php echo $CFG->wwwroot.'/info-dintev/cursospublicos.php';?>">
										<div id="ico-2" class="container"></div>
									</a>	
								</div>
								<div class="heads">
									<p><b>Cursos abiertos</b></p>
								</div>
								<div class="texts">
                                    <ul class='lista-iconos-principales'>
                                        <li><i class="fa fa-plus" aria-hidden="true"></i><a href='https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=34259' target="_blank">Recordando las matemáticas del colegio</a></li>
                                        <li><i class="fa fa-plus" aria-hidden="true"></i><a href='https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=34258' target="_blank">Fortaleciendo las matemáticas en la Universidad</a></li>
                                        <li><i class="fa fa-plus" aria-hidden="true"></i><a href='https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=29266' target="_blank">Comprensión producción de textos cátedra UNESCO</a></li>
                                        <li><i class="fa fa-plus" aria-hidden="true"></i><a href='https://campusvirtual.univalle.edu.co/moodle/course/view.php?id=34855' target="_blank">Fortaleciendo la lectura crítica en la Universidad</a></li>
                                    </ul>
                                </div>
							</div>  <!-- end of forcourses -->
							<div class="forforum span4">
								<div class="icons">
									<a href="<?php echo $CFG->wwwroot.'/info-dintev/cursos-demo.php';?>">
										<div id="ico-3" class="container"></div>
									</a>
								</div>
								<div class="heads">
                    				<p><b>¿Sabías que...?</b></p>
								</div>
								<div class="texts">
										 <div id="owl-slide2">

										    	<div class="item">
										    		<a class="video" href="https://www.youtube.com/watch?v=3lzc2WMQz48"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/sabias1.png"></a>
										    	</div>
										    	<div class="item">
										    		<a class="video" href="https://www.youtube.com/watch?v=gkkLl7mhicI"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/sabias2.png"></a>
										    	</div>
										    	<div class="item">
										    		<a class="video" href="https://www.youtube.com/watch?v=_gccI-ij0_k"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/sabias3.png"></a>
										    	</div>
										    	<div class="item">
										    		<a class="video" href="https://www.youtube.com/watch?v=TojD-rGmzbE"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/sabias4.png"></a>
										    	</div>

										  </div>
								</div>
							</div> <!-- end of forforum -->
						</div>
					</div>
					<!--
					<div class="row-fluid">
						<div class="span12" id="box-slide2">
							<div class="span3 hidden-phone text-second-slide">						
								<p>¿Sabías<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;qué...?</p>
							</div>
							
						    <div id="owl-slide2" class="span9">

						    	<div class="item">
						    		<a class="video" href="https://www.youtube.com/watch?v=0rEsVp5tiDQ"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/Sabiasque_foros.png"></a>
						    	</div>
						    	<div class="item">
						    		<a class="video" href="https://www.youtube.com/watch?v=FEtbPI9Pu6I"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/Sabiasque_mobile.png"></a>
						    	</div>
						    	<div class="item">
						    		<a class="video" href="https://www.youtube.com/watch?v=0rEsVp5tiDQ"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/Sabiasque_tema.png"></a>
						    	</div>
						    	<div class="item">
						    		<a class="video" href="https://www.youtube.com/watch?v=0rEsVp5tiDQ"><img src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/Sabiasque_foros.png"></a>
						    	</div>


						    </div>
						</div> 
					</div> -->
					<div id="contenedor3" class="row-fluid">
						<div class="span12" id='banner-pestanas'>
							<ul class="nav nav-tabs ul_uv">
								<li class="tab_uv_title">
									<a href="#main" class="ref_uv_main" data-toggle="tab">Licencias y Software Especializado</a>
									<div id="ico-tabs" class="container"></div></li>
								<li class="tab_uv"><a style="color:#121D41;" class="ref_uv" href="#profile" data-toggle="tab">ClarityEnglish</a></li>
								<li class="tab_uv"><a style="color:#121D41;" class="ref_uv" href="#messages" data-toggle="tab">WOLFRAM 11</a></li>
								<li class="tab_uv"><a style="color:#121D41;" class="ref_uv" href="#settings" data-toggle="tab">Adobe Cloud</a></li>
							</ul>
							<div class="tab-content tabcontent-uv">
								<div class="tab-pane active" id="main">
                                    <a target="blank" href='https://sites.google.com/a/correounivalle.edu.co/clarityenglish/'><img class="img_uv" src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/Licencias_Slider1.png"/></a>
                                </div>
                                <div class="tab-pane" id="profile">
                                    <a target="blank" href='https://sites.google.com/a/correounivalle.edu.co/clarityenglish/'><img class="img_uv" src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/clarity.png"/></a>
                                </div>
                                <div class="tab-pane" id="messages">
                                    <a target="blank" href='https://sites.google.com/a/correounivalle.edu.co/mathematica/mathematica'><img class="img_uv" src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/wolfram.png"/></a>
                                </div>
                                <div class="tab-pane" id="settings">
                                    <a target="blank" href='http://dintev.univalle.edu.co/2-uncategorised/83-licencia-adobe.html'><img class="img_uv" src="<?php echo $CFG->wwwroot?>/theme/<?php echo $CFG->theme?>/pix/adobe.png"/></a>
                                </div>
                            </div>
						</div>
					</div>

				<?php
					
					$PAGE->requires->js('/lib/jquery/jquery-3.1.0.min.js');
					$PAGE->requires->js('/theme/'.$CFG->theme.'/javascript/owl.carousel.min.js');
					$PAGE->requires->js_call_amd('theme_crisp/slide','init');
					$PAGE->requires->js('/theme/'.$CFG->theme.'/amd/src/tabs.js','init');
					$PAGE->requires->js_call_amd('theme_crisp/popupvideo','init');

				?>


							
				
			</div> <!-- end of bodydetails -->
		</div> <!-- end of span12 -->
	</div>  <!-- end of row-fluid -->
	

				<div id="bodymaincontent" class="row-fluid">
				<?php
					echo $OUTPUT->main_content();
				?>
				</div>
			</section>
		</div>
	</div>

	<?php 
	require('footer.php');
	?>
