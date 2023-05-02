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
 * Parent theme: Bootstrapbase by Bas Brands
 * Built on: Essential by Julian Ridden
 *
 * @package   theme_lambda
 * @copyright 2020 redPIthemes
 *
 */

/* Core */
$string['configtitle'] = 'lambda';
$string['pluginname'] = 'lambda';
$string['choosereadme'] = '
<div class="clearfix">
<div style="margin-bottom:20px;">
<p style="text-align:center;"><img class="img-polaroid" src="lambda/pix/screenshot.jpg" /></p>
</div>
<hr />
<h2>Lambda - Responsive Moodle Theme</h2>
<div class="divider line-01"></div>
<div style="color: #888; text-transform: uppercase; margin-bottom:20px;">
<p>creado por RedPiThemes<br />Documentación en línea: <a href="http://redpithemes.com/Documentation/assets/index.html" target="_blank">http://redpithemes.com/Documentation/assets/index.html</a><br />El soporte se proporciona mediante un ticket en el foro de soporte: <a href="https://redpithemes.ticksy.com" target="_blank">https://redpithemes.ticksy.com</a></p>
</div>
<hr />
<p style="text-align:center;"><img class="img-polaroid" src="lambda/pix/redPIthemes.jpg" /></p>';

/* Settings - General */
$string['settings_general'] = 'General';
$string['logo'] = 'Logo';
$string['logodesc'] = 'Personaliza aqu&iacute; tu logo. El logo que subas se mostrar&aacute; en el encabezado.';
$string['logo_res'] = 'Dimensión estándar del logotipo';
$string['logo_res_desc'] = 'Establece la dimensión de su logo a una altura máxima de 90px. Con esta configuración, su logo se adaptará a diferentes resoluciones de pantalla y también puede usar una versión @2x para pantallas de alta resolución.';
$string['favicon'] = 'Favivon';
$string['favicon_desc'] = 'Cambia el favicon por Lambda. Las imágenes con un fondo transparente y una altura de 32 píxeles funcionarán mejor. Tipos permitidos: PNG, JPG, ICO';
$string['pagewidth'] = 'Ancho de p&aacute;gina';
$string['pagewidthdesc'] = 'Seleccionar de la lista de dise&ntilde;os de p&aacute;gina disponibles.';
$string['boxed_wide'] = 'Caja - ancho fijo amplio';
$string['boxed_narrow'] = 'Caja - ancho fijo estrecho';
$string['boxed_variable'] = 'Caja - ancho variable';
$string['full_wide'] = 'Ancho completo';
$string['layout'] = 'Usar dise&ntilde;o de bloques est&aacute;ndar';
$string['layoutdesc'] = 'Este tema est&aacute; dise&ntilde;ado para poner ambas columnas de bloques a un lado. Si prefieres la disposici&oacute; est&aacute;ndar de Moodle, selecciona el dise&ntilde;o est&aacute;ndar de tres columnas.';
$string['footnote'] = 'Pi&eacute; de p&aacute;gina';
$string['footnotedesc'] = 'Cualquier contenido que a&ntilde;adas a esta &aacute;rea se mostrar&aacute; en el pi&eacute; de p&aacute;gina en todo tu sitio de Moodle. P.ej. el copyright y el nombre de tu organizaci&oacute;n.';
$string['customcss'] = 'CSS personalizado';
$string['customcssdesc'] = 'Cualquier regla CSS que a&ntilde;adas a esta &aacute;rea tendr&aacute; efecto en todas las p&aacute;ginas de tu sitio, facilitando la personalizaci&oacute;n de este tema.';

/* Settings - Background */
$string['settings_background'] = 'Fondo de p&aacute;gina';
$string['list_bg'] = 'Seleccionar de la lista';
$string['list_bg_desc'] = 'Selecciona el fondo de p&aacute;gina de la lista de im&aacute;genes de fondo por defecto. <br /><strong>Nota: </strong>Si subes tu propia imagen de fondo, tu selecci&oacute;n de esta lista ser&aacute; descartada.';
$string['pagebackground'] = 'Subir imagen de fondo';
$string['pagebackgrounddesc'] = 'Sube tu imagen de fondo personalizada. Si no lo haces, se mostrar&aacute; una imagen de la lista anterior.';
$string['page_bg_repeat'] = 'Imagen en mosaico';
$string['page_bg_repeat_desc'] = 'Si has subido un fondo en mosaico (de im&aacute;genes que se repiten), marca esta casilla para que se repitan las im&aacute;genes por todo el fondo de la p&aacute;gina.<br />En caso contrario, si no la marcas, la imagen se usar&aacute; como un fondo de pantalla completa que rellenará todo el espacio de la ventana del navegador.';

/* Settings - Colors */
$string['settings_colors'] = 'Color';
$string['maincolor'] = 'Colores del tema';
$string['maincolordesc'] = 'El color principal del tema - se cambiar&aacute;n varios componentes para producir la combinación de colores que desees en todo el sitio de Moodle.';
$string['linkcolor'] = 'Color de enlace';
$string['linkcolordesc'] = 'El color de los enlaces. Puedes usar el color principal del tema aqu&iacute; tambi&eacute;n, pero algunos colores claros quiz&aacute;s se lean dificultosamente con este ajuste. En ese caso, selecciona un color m&aacute;s oscuro aqu&iacute;.';
$string['mainhovercolor'] = 'Color al pasar el cursor';
$string['mainhovercolordesc'] = 'El color del efecto hover - Usado en enlaces, men&uacute;s, etc.';
$string['def_buttoncolor'] = 'Bot&oacute;n por defecto';
$string['def_buttoncolordesc'] = 'Color del botón usado por defecto en Moodle';
$string['def_buttonhovercolor'] = 'Color del bot&oacute;n al pasar cursor';
$string['def_buttonhovercolordesc'] = 'El color del efecto hover en el bot&oacute;n por defecto.';
$string['menufirstlevelcolor'] = 'Men&uacute;, nivel 1';
$string['menufirstlevelcolordesc'] = 'Color de la barra de navegaci&oacute;n';
$string['menufirstlevel_linkcolor'] = 'Men&uacute;, nivel 1 - Enlaces';
$string['menufirstlevel_linkcolordesc'] = 'Color de los enlaces en la barra de navegaci&oacute;n';
$string['menusecondlevelcolor'] = 'Men&uacute;, nivel 2';
$string['menusecondlevelcolordesc'] = 'Color del men&uacute; desplegable dentro de la barra de navegaci&oacute;n.';
$string['menusecondlevel_linkcolor'] = 'Men&uacute;, nivel 1 - Enlaces';
$string['menusecondlevel_linkcolordesc'] = 'Color de los enlaces el men&uacute; desplegable de la barra de navegaci&oacute;n.';
$string['footercolor'] = 'Color de fondo del pie de p&aacute;gina';
$string['footercolordesc'] = 'Color de la caja contenedora del pie de p&aacute;gina.';
$string['footerheadingcolor'] = 'Color del encabezado del pie de p&aacute;gina';
$string['footerheadingcolordesc'] = 'Color de los bloques de encabezado del pie de p&aacute;gina.';
$string['footertextcolor'] = 'Color del texto del pie de p&aacute;gina';
$string['footertextcolordesc'] = 'Color del texto que aparece en el pie de p&aacute;gina.';
$string['copyrightcolor'] = 'Color del copyright del pie de p&aacute;gina';
$string['copyrightcolordesc'] = 'Color de fondo de la caja de copyright en el pie de p&aacute;gina.';
$string['copyright_textcolor'] = 'Color del texto del copyright';
$string['copyright_textcolordesc'] = 'Color del texto que aparece en la caja del copyright.';

/* Settings - Socials */
$string['settings_socials'] = 'Enlaces sociales';
$string['socialsheadingsub'] = 'Aumenta tus usuarios con las redes sociales';
$string['socialsdesc'] = 'Establece enlaces directos a las principales redes sociales para promocionar tu marca.';
$string['facebook'] = 'URL de Facebook';
$string['facebookdesc'] = 'Introduce la URL de tu p&aacute;gina de Facebook. (p.ej. https://www.facebook.com/miescuela)';
$string['twitter'] = 'URL de Twitter';
$string['twitterdesc'] = 'Introduce la URL de tu cuenta de Twitter. (p.ej. https://www.twitter.com/miescuela)';
$string['googleplus'] = 'URL de Google+';
$string['googleplusdesc'] = 'Introduce la URL de tu perfil de Google+. (p.ej. https://plus.google.com/+miescuela)';
$string['youtube'] = 'URL de YouTube';
$string['youtubedesc'] = 'Introduce la URL de tu canal de YouTube. (p.ej. https://www.youtube.com/user/miescuela)';
$string['flickr'] = 'URL de Flickr';
$string['flickrdesc'] = 'Introduce la URL de tu p&aacute;gina de Flickr. (p.ej. http://www.flickr.com/photos/miescuela)';
$string['pinterest'] = 'URL de Pinterest';
$string['pinterestdesc'] = 'Introduce la URL de tu p&aacute;gina de Pinterest. (p.ej. http://pinterest.com/mycollege/mypinboard)';
$string['instagram'] = 'URL de Instagram';
$string['instagramdesc'] = 'Introduce la URL de tu p&aacute;gina de Instagram. (p.ej. http://instagram.com/miescuela)';
$string['website'] = 'URL del sitio web';
$string['websitedesc'] = 'Introduce la URL de tu sitio web. (p.ej. http://www.miescuela.com)';
$string['socials_mail'] = 'Direcci&oacute;n de email';
$string['socials_mail_desc'] = 'Introduce tu direcci&oacute;n de correo electr&oacute;nico. (p.ej. info@miescuela.com)';
$string['socials_color'] = 'Color de los enlaces sociales';
$string['socials_color_desc'] = 'Color del icono de los enlaces sociales.';
$string['socials_position'] = 'Posición de los iconos';
$string['socials_position_desc'] = 'Selecciona d&oacute;nde colocar los iconos de los enlaces sociales: abajo (en el pie de p&aacute;gina) o arriba (en el encabezado).';


/* Settings - Fonts */
$string['settings_fonts'] = 'Fuentes';
$string['fontselect_heading'] = 'Selector de fuentes - Encabezado';
$string['fontselectdesc_heading'] = 'Selecciona una fuente de la lista.';
$string['fontselect_body'] = 'Selector de fuentes - Cuerpo';
$string['fontselectdesc_body'] = 'Selecciona una fuente de la lista.';


/* Settings - Slider */
$string['settings_slider'] = 'Pase de diapositivas';
$string['slideshowheading'] = 'Pase de diapositivas  (slideshow) de la p&aacute;gina; de inicio';
$string['slideshowheadingsub'] = 'Pase de diapositivas din&aacute;mico de la p&aacute;gina; de inicio';
$string['slideshowdesc'] = 'Se genera un pase din&aacute;mico de hasta 5 diapositivas para que promociones cosas importantes de tu sitio web.<br /><b>NOTA: </b>Es necesario subir al menos una imagen para que aparezca el pase de diapostivas. Los encabezados, titulares y URLs son opcionales.';
$string['slideshow_slide1'] = 'Diapositiva 1';
$string['slideshow_slide2'] = 'Diapositiva 2';
$string['slideshow_slide3'] = 'Diapositiva 3';
$string['slideshow_slide4'] = 'Diapositiva 4';
$string['slideshow_slide5'] = 'Diapositiva 5';
$string['slideshow_options'] = 'Configuraci&oacute,n';
$string['slidetitle'] = 'Encabezado de la diapositiva';
$string['slidetitledesc'] = 'Introduce un encabezado descriptivo para tu diapositiva.';
$string['slideimage'] = 'Imagen de la diapositiva';
$string['slideimagedesc'] = 'Subir una imagen.';
$string['slidecaption'] = 'Titular de la diapositiva';
$string['slidecaptiondesc'] = 'Introduce el titular de la diapositiva.';
$string['slide_url'] = 'URL de la diapositiva';
$string['slide_url_desc'] = 'Si introduces una URL, se mostrar&aacute; el bot&oacute;n "Leer m&aacute;s" en tu diapositiva.';
$string['slideshowpattern'] = 'Patr&oacute;n/Capa';
$string['slideshowpatterndesc'] = 'Seleccina un patr&oacute;n como capa transparente en tus im&aacute;genes.';
$string['pattern1'] = 'ninguno';
$string['pattern2'] = 'puntos - estrecho';
$string['pattern3'] = 'puntos - ancho';
$string['pattern4'] = 'l&iacute;neas - horizontal';
$string['pattern5'] = 'l&iacute;neas - vertical';
$string['slideshow_advance'] ='Auto reproducci&oacute;n';
$string['slideshow_advance_desc'] ='Selecciona si quieres que una diapositiva avance autom&aacute;ticamente despu&eacute;s de cierto tiempo.';
$string['slideshow_nav'] ='Navegaci&oacute;n al pasar el cursor';
$string['slideshow_nav_desc'] ='Con esta opción activada, los botones de navegación (anterior, siguiente y play/stop) serán visibles solamente al pasar el cursor (hover). Si está desactivada, se mostrarán siempre.';
$string['slideshow_loader'] ='Precarga del pase';
$string['slideshow_loader_desc'] ='Selecciona tarta, barra, ninguno (si seleccionas "tarta" algunos navegadores antiguos como IE8 no podrán mostrarla, siempre se mostrará una barra de carga).';
$string['slideshow_imgfx'] ='Efectos de imagen';
$string['slideshow_imgfx_desc'] ='Selecciona un efecto de transición entre tus imágenes:<br /><i>random, simpleFade, curtainTopLeft, curtainTopRight, curtainBottomLeft, curtainBottomRight, curtainSliceLeft, curtainSliceRight, blindCurtainTopLeft, blindCurtainTopRight, blindCurtainBottomLeft, blindCurtainBottomRight, blindCurtainSliceBottom, blindCurtainSliceTop, stampede, mosaic, mosaicReverse, mosaicRandom, mosaicSpiral, mosaicSpiralReverse, topLeftBottomRight, bottomRightTopLeft, bottomLeftTopRight, bottomLeftTopRight, scrollLeft, scrollRight, scrollHorz, scrollBottom, scrollTop</i>';
$string['slideshow_txtfx'] ='Efectos de texto';
$string['slideshow_txtfx_desc'] ='Selecciona un efecto de transición de texto en tus diapositivas:<br /><i>moveFromLeft, moveFromRight, moveFromTop, moveFromBottom, fadeIn, fadeFromLeft, fadeFromRight, fadeFromTop, fadeFromBottom</i>';

/* Settings - Carousel */
$string['settings_carousel'] = 'Carrusel';
$string['carouselheadingsub'] = 'Configuración del carrusel de la página de inicio';
$string['carouseldesc'] = 'Configura un carrusel de pases de diapositivas para tu página de inicio.<br /><strong>Nota: </strong>Tienes que subir al menos las imágenes para que aparezca el pase. Los ajustes de titulares aparecerán con efecto hover para las imágenes y serán opcionales.';
$string['carousel_position'] = 'Posición del carrusel';
$string['carousel_positiondesc'] = 'Selecciona una posición para el carrusel.<br />Puedes elegir entre ubicar el carrusel arriba o debajo del área de contenido.';
$string['carousel_h'] = 'Encabezado';
$string['carousel_h_desc'] = 'El encabezado del carrusel de la página de inicio.';
$string['carousel_hi'] = 'Etiqueta de encabezado';
$string['carousel_hi_desc'] = 'Define el encabezado: &lt;h1&gt; Establece el encabezado principal. &lt;h6&gt; Establece el encabezado secundario.';
$string['carousel_add_html'] = 'Contenido HTML adicional';
$string['carousel_add_html_desc'] = 'Cualquier contenido que introduzcas aquí se ubicará a la izquierda del carrusel.<br /><strong>Nota: </strong>Debes usar HTML para darle formato al texto.';
$string['carousel_slides'] = 'Número de diapositivas';
$string['carousel_slides_desc'] = 'Elige el número de diapositivas de tu carrusel.';
$string['carousel_image'] = 'Imagen';
$string['carousel_imagedesc'] = 'Sube la imagen que aparecerá en la diapositiva.';
$string['carousel_heading'] = 'Titular - Encabezado';
$string['carousel_heading_desc'] = 'Introduce un encabezado para tu imagen - con esto se creará un titular con efecto hover.<br /><strong>Nota: </strong>Al menos tienes que crear el encabezado para hacer que aparezca el titular.';
$string['carousel_caption'] = 'Titular - Texto';
$string['carousel_caption_desc'] = 'Introduce el texto para ser usado con el efecto hover.';
$string['carousel_url'] = 'Titular - URL';
$string['carousel_urldesc'] = 'Se creará un botón para el titular con un enlace a la URL introducida.';
$string['carousel_btntext'] = 'Titular - Enlace de texto';
$string['carousel_btntextdesc'] = 'Introduce un enlace de texto (URL).';
$string['carousel_color'] = 'Titular - Color';
$string['carousel_colordesc'] = 'Selecciona un color para el titular.';

/* Theme */
$string['visibleadminonly'] ='Los bloques añadidos a esta área solamente serán visibles para los administradores.';
$string['region-side-post'] = 'Izquierda';
$string['region-side-pre'] = 'Derecha';
$string['region-footer-left'] = 'Pie (izq.)';
$string['region-footer-middle'] = 'Pie (medio)';
$string['region-footer-right'] = 'Pie (dcha.)';
$string['region-hidden-dock'] = 'Oculto para usuarios';
$string['nextsection'] = '';
$string['previoussection'] = '';
$string['backtotop'] = '';