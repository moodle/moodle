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
?>
<footer id="page-footer">
	<div class="home-footer-contacto">
            <div class="container">
                <div class="footer-logo">
                    <a href="http://www.univalle.edu.co" title="Universidad del Valle" target="_blank">
                            <img class="logo-img" src="<?php echo $CFG->wwwroot. '/theme/crisp/pix/logo-footer.png';?>" alt="Universidad del Valle" height="159" width="112">
                    </a>
                </div>
                <div class="nombre-contacto">
                    <h5>Universidad del Valle</h5>
                    <ul>
                        <li>Cali - Colombia</li>
                    </ul>
                </div>
                <div class="address">
                    <h5> </h5>
                    <ul>
                        <li><strong>Dirección:</strong></li>
                        <li>Ciudad Universitaria Meléndez</li>
                        <li>Calle 13 # 100-00</li>
                        <li>Sede San Fernando</li>
                        <li>Calle 4B # 36-00</li>
                        <!--
                        <li>Dirección De Nuevas Tecnologías y Educación Virtual-Dintev</li>
                        <li><a target="_blank" href="mailto:campusvirtual@correounivalle.edu.co">campusvirtual@correounivalle.edu.co</a></li>
                        <li>Telefonos: +57 2 318 2649 ó 321 2100 Ext. 2649</li>
                        <li>Edificio 317-CREE Ciudadela Universitaria Meléndez</li>
                        <li>Universidad Del Valle</li>
                        <li>Cali-Colombia</li>-->
                    </ul>
                </div>
                
                <div class="phone">
                    <h5> </h5>
                    <ul>
                        <li><strong>PBX:</strong></li>
                        <li>+57 2 3212100</li>
                        <li>A.A.25360</li>
                        <br>
                        <li>Línea Gratuita PQRSD:</li>
                        <li>018000 22 00 21</li>
                    </ul>
                </div>
                   
                <div class="social">
                   <h5></h5>
                    <h5>Redes Sociales:</h5>
                    <ul>
                        <li><a href="https://www.facebook.com/universidaddelvalle" target="_blank"><img src="<?php echo $CFG->wwwroot. '/theme/crisp/pix/facebook.png';?>"></a></li>
                        <li><a href="https://www.youtube.com/user/universidaddelvalle1" target="_blank"><img src="<?php echo $CFG->wwwroot. '/theme/crisp/pix/youtube.png';?>"></a></li>
                        <li><a href="https://twitter.com/univallecol" target="_blank"><img src="<?php echo $CFG->wwwroot. '/theme/crisp/pix/twitter.png';?>"></a></li>
                    </ul>
                </div> 
            </div>
        </div>
</footer>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
