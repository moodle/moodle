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
<p>erstellt von RedPiThemes<br />Theme Dokumentation: <a href="http://redpithemes.com/Documentation/assets/index.html" target="_blank">http://redpithemes.com/Documentation/assets/index.html</a><br />Der Support erfolgt per Ticket im Support-Forum: <a href="https://redpithemes.ticksy.com" target="_blank">https://redpithemes.ticksy.com</a></p>
</div>
<hr />
<p style="text-align:center;"><img class="img-polaroid" src="lambda/pix/redPIthemes.jpg" /></p>';

/* Settings - General */
$string['settings_general'] = 'Allgemein';
$string['logo'] = 'Logo';
$string['logodesc'] = 'Bitte laden Sie hier Ihr individuelles Logo hoch. Wenn Sie ein Logo hochladen, erscheint es in der Kopfzeile.';
$string['logo_res'] = 'Standard-Logo-Dimension';
$string['logo_res_desc'] = 'Setzt die Dimension Ihres Logos auf eine maximale Höhe von 90px. Mit dieser Einstellung passt sich Ihr Logo an verschiedene Bildschirmauflösungen an, und Sie können auch eine @2x-Version für hochauflösende Bildschirme verwenden.';
$string['favicon'] = 'Favicon';
$string['favicon_desc'] = 'Ändern Sie das Favicon für Lambda. Bilder mit einem transparenten Hintergrund und 32px Höhe funktionieren am besten. Erlaubte Typen: PNG, JPG, ICO';
$string['pagewidth'] = 'Seitenbreite festlegen';
$string['pagewidthdesc'] = 'Wählen Sie aus der Liste der verfügbaren Seitenlayouts.';
$string['boxed_wide'] = 'Boxed - feste Breite';
$string['boxed_narrow'] = 'Boxed - feste Breite schmal';
$string['boxed_variable'] = 'Boxed - variable Breite';
$string['full_wide'] = 'Volle Breite';
$string['page_centered_logo'] = 'Kopfzeile mit zentriertem Logo';
$string['page_centered_logo_desc'] = 'Markieren Sie die Checkbox, um eine Variation für die Kopfzeile mit einem zentrierten Logo zu verwenden.';
$string['category_layout'] = 'Ansicht der Kurskategorie';
$string['category_layout_desc'] = 'Wählen Sie in der Kurskategorieansicht ein Layout für die Kurse aus. Sie können wählen, ob Sie Ihre Kurse in einer Liste oder in einer Grid-Ansicht anzeigen möchten.';
$string['category_layout_list'] = 'Kurs-Liste';
$string['category_layout_grid'] = 'Kurs-Grid';
$string['footnote'] = 'Fußnote';
$string['footnotedesc'] = 'Alles, was Sie zu diesem Textbereich hinzufügen, wird in der Fußzeile Ihrer Moodle-Site angezeigt, z.B. Copyright und der Name Ihrer Organisation.';
$string['customcss'] = 'Benutzerdefiniertes CSS';
$string['customcssdesc'] = 'Welche CSS-Regeln Sie auch immer zu diesem Textbereich hinzufügen, sie werden auf jeder Seite reflektiert, was die Anpassung dieses Themas erleichtert.';

/* Settings - Background images */
$string['settings_background'] = 'Hintergrundbilder';
$string['list_bg'] = 'Seitenhintergrund';
$string['list_bg_desc'] = 'Wählen Sie den Seitenhintergrund aus einer Liste der enthaltenen Hintergrundbilder aus.<br /><strong>Hinweis:</strong> Wenn Sie unten ein Bild hochladen, wird Ihre Auswahl hier in der Liste verworfen.';
$string['pagebackground'] = 'Eigenes Hintergrundbild hochladen';
$string['pagebackgrounddesc'] = 'Laden Sie Ihr eigenes Hintergrundbild hoch. Wenn keines hochgeladen wird, wird ein Standardbild aus der obigen Liste verwendet.';
$string['page_bg_repeat'] = 'Hochgeladenes Bild wiederholen?';
$string['page_bg_repeat_desc'] = 'Wenn Sie einen gekachelten Hintergrund (wie ein Muster) hochgeladen haben, sollten Sie das Kontrollkästchen markieren, um das Bild über den Seitenhintergrund zu wiederholen.<br /> Andernfalls, wenn Sie das Kästchen nicht markiert lassen, wird das Bild als ganzseitiges Hintergrundbild verwendet, dass das gesamte Browserfenster abdeckt.';
$string['header_background'] = 'Eigenes Header-Bild hochladen';
$string['header_background_desc'] = 'Laden Sie Ihr eigenes Header-Bild hoch. Wenn kein Bild hochgeladen wird, wird ein weißer Standardhintergrund für den Header verwendet.';
$string['header_bg_repeat'] = 'Header-Bild wiederholen?';
$string['header_bg_repeat_desc'] = 'Wenn Sie einen gekachelten Hintergrund (wie ein Muster) hochgeladen haben, sollten Sie das Kontrollkästchen ankreuzen, um das Bild im Header über dem Hintergrund zu wiederholen.<br />Anderenfalls wird das Bild so groß wie möglich skaliert, so dass der Header-Bereich vollständig vom Hintergrundbild bedeckt wird.';
$string['category_background'] = 'Hintergrundbanner der Kurskategorie';
$string['category_background_desc'] = 'Laden Sie Ihr eigenes Hintergrundbanner-Bild für die Moodle-Kurskategorieansicht hoch. Wenn keines hochgeladen wird, wird ein Standardbild verwendet.';
$string['banner_font_color'] = 'Schriftfarbe für das Banner';
$string['banner_font_color_desc'] = 'Das Standard-Banner-Hintergrundbild für die Moodle-Kurskategorie-Ansicht ist abgeblendet. Daher wird dort weiße Schriftfarbe verwendet. Wenn Sie ein eigenes Bannerbild hochladen, kann es sinnvoll sein, eine andere Schriftfarbe zu verwenden.';
$string['banner_font_color_opt0'] = 'weiß (Standard)';
$string['banner_font_color_opt1'] = 'dunkel';
$string['banner_font_color_opt2'] = 'Hauptthema Farbe';
$string['hide_category_background'] = 'Das Hintergrundbanner der Kategorie ausblenden?';
$string['hide_category_background_desc'] = 'Markieren Sie die Checkbox, wenn Sie das Kategorie-Hintergrundbanner vollständig ausblenden möchten.';

/* Settings - Colors */
$string['settings_colors'] = 'Farben';
$string['maincolor'] = 'Theme Farbe';
$string['maincolordesc'] = 'Die Hauptfarbe Ihres Themes - dies wird mehrere Komponenten ändern, um die gewünschte Farbe auf der Moodle-Website zu erzeugen.';
$string['linkcolor'] = 'Link-Farbe';
$string['linkcolordesc'] = 'Die Farbe der Links. Sie können auch hier die Hauptfarbe Ihres Themes verwenden, aber einige helle Farben sind mit dieser Einstellung möglicherweise schwer zu lesen. In diesem Fall können Sie hier eine dunklere Farbe wählen.';
$string['mainhovercolor'] = 'Theme Hover Farbe';
$string['mainhovercolordesc'] = 'Farbe für Hover-Effekte - dies wird für Links, Menüs usw. verwendet.';
$string['def_buttoncolor'] = 'Standard-Button';
$string['def_buttoncolordesc'] = 'Farbe für die in Moodle verwendete Standardschaltfläche';
$string['def_buttonhovercolor'] = 'Standard Button (Hover)';
$string['def_buttonhovercolordesc'] = 'Farbe für den Hover-Effekt auf der Standardschaltfläche';
$string['headercolor'] = 'Header-Farbe';
$string['headercolor_desc'] = 'Farbe für den Kopfbereich';
$string['menufirstlevelcolor'] = 'Menü 1. Level';
$string['menufirstlevelcolordesc'] = 'Farbe für die Navigationsleiste';
$string['menufirstlevel_linkcolor'] = 'Menü 1. Level - Links';
$string['menufirstlevel_linkcolordesc'] = 'Farbe für die Links in der Navigationsleiste';
$string['menusecondlevelcolor'] = 'Menü 2. Level';
$string['menusecondlevelcolordesc'] = 'Farbe für das Dropdown-Menü in der Navigationsleiste';
$string['menusecondlevel_linkcolor'] = 'Menü 2. Level - Links';
$string['menusecondlevel_linkcolordesc'] = 'Farbe für die Links im Dropdown-Menü';
$string['footercolor'] = 'Footer Background Color';
$string['footercolordesc'] = 'Set what color the background of the footer box should be';
$string['footerheadingcolor'] = 'Farbe der Fußzeilen-Überschriften';
$string['footerheadingcolordesc'] = 'Legen Sie die Farbe für Blocküberschriften in der Fußzeile fest';
$string['footertextcolor'] = 'Farbe des Fußzeilentextes';
$string['footertextcolordesc'] = 'Legen Sie die Farbe fest, in der Ihr Text in der Fußzeile erscheinen soll';
$string['copyrightcolor'] = 'Fußzeile Copyright Hintergrundfarbe';
$string['copyrightcolordesc'] = 'Legen Sie fest, welche Farbe der Hintergrund des Copyright-Feldes in der Fußzeile haben soll';
$string['copyright_textcolor'] = 'Copyright Textfarbe';
$string['copyright_textcolordesc'] = 'Legen Sie die Farbe fest, in der Ihr Text im Copyright-Feld erscheinen soll';

/* Settings - blocks */
$string['settings_blocks'] = 'Moodle Blocks';
$string['block_layout'] = 'Blocklayout wählen';
$string['block_layout_opt0'] = 'Standard-Lambda-Block-Layout';
$string['block_layout_opt1'] = 'Standard-Moodle-Block-Layout';
$string['block_layout_opt2'] = 'Zusammenklappbare linke Blockregion';
$string['block_layout_desc'] = 'Sie können wählen zwischen:<br /><ul><li>Standard-Lambda-Blocklayout: beide Blockspalten links und rechts neben dem Hauptinhaltsbereich</li><li>Standard-Moodle-Blocklayout: Blockbereiche links und rechts vom Hauptinhaltsbereich</li><li>Zusammenklappbarer linker Blockbereich: Sie können eine einklappbare Seitenleiste für den linken Blockbereich verwenden: </li></ul><strong>Bitte beachten Sie:</strong>Das Moodle-Dock für die Blöcke kann nur mit dem <em>Standard-Lambda-Blocklayout</em> oder dem <em>Standard-Moodle-Blocklayout</em> verwendet werden.';
$string['sidebar_frontpage'] = 'Klappbare Seitenleiste für die Titelseite aktivieren';
$string['sidebar_frontpage_desc'] = 'Wenn Sie die einklappbare Seitenleiste für das Blocklayout aus der obigen Dropdown-Liste ausgewählt haben, können Sie wählen, ob diese Seitenleiste auch für die Moodle-Titelseite aktiviert werden soll oder nicht. Die Titelseite bietet einen zusätzlichen Blockbereich für Admins, so dass die Seitenleiste dort möglicherweise nicht erforderlich ist.<br /><strong>Bitte beachten Sie: </strong>Wenn Sie ein anderes Blocklayout als die einklappbare Seitenleiste gewählt haben, dann hat diese Einstellung keine Auswirkung.';
$string['block_style'] = 'Blockstil wählen';
$string['block_style_opt0'] = 'Blockstil 01';
$string['block_style_opt1'] = 'Blockstil 02';
$string['block_style_opt2'] = 'Blockstil 03';
$string['block_style_desc'] = 'Sie können zwischen den folgenden Blockstilvarianten wählen:<div class="row-fluid"><div class="span4"><p><img class="img-responsive img-polaroid" src="https://redpithemes.com/Documentation/assets/img/options-blocks-1.jpg" /><p>Blockstil 01</div><div class="span4"><p><img class="img-responsive img-polaroid" src="https://redpithemes.com/Documentation/assets/img/options-blocks-2.jpg" /><p>Blockstil 02</div><div class="span4"><p><img class="img-responsive img-polaroid" src="https://redpithemes.com/Documentation/assets/img/options-blocks-3.jpg" /><p>Blockstil 03</div></div>';
$string['block_icons'] = 'Theme Lambda Blocksymbole';
$string['block_icons_opt0'] = 'farbig (Standard)';
$string['block_icons_opt1'] = 'monochrom';
$string['block_icons_opt2'] = 'keine (Blocksymbole ausblenden)';
$string['block_icons_desc'] = 'Wählen Sie einen Stil für die Blocksymbole.';

/* Settings - Socials */
$string['settings_socials'] = 'Social Media';
$string['socialsheadingsub'] = 'Begeistern Sie Ihre Benutzer mit Social Networking';
$string['socialsdesc'] = 'Bieten Sie direkte Links zu den wichtigsten sozialen Netzwerken, die Ihre Marke fördern.';
$string['facebook'] = 'Facebook URL';
$string['facebookdesc'] = 'Geben Sie die URL Ihrer Facebook-Seite ein. (z.B. https://www.facebook.com/mycollege)';
$string['twitter'] = 'Twitter URL';
$string['twitterdesc'] = 'Geben Sie die URL Ihres Twitter-Feeds ein. (z.B. https://www.twitter.com/mycollege)';
$string['googleplus'] = 'Google+ URL';
$string['googleplusdesc'] = 'Geben Sie die URL Ihres Google+-Profils ein. (z.B. https://plus.google.com/+MyCollege)';
$string['youtube'] = 'YouTube URL';
$string['youtubedesc'] = 'Geben Sie die URL Ihres YouTube-Kanals ein. (z.B. https://www.youtube.com/user/mycollege)';
$string['flickr'] = 'Flickr URL';
$string['flickrdesc'] = 'Geben Sie die URL Ihrer Flickr-Seite ein. (z.B. http://www.flickr.com/photos/mycollege)';
$string['pinterest'] = 'Pinterest URL';
$string['pinterestdesc'] = 'Geben Sie die URL Ihrer Pinterest-Seite ein. (z.B. http://pinterest.com/mycollege/mypinboard)';
$string['instagram'] = 'Instagram URL';
$string['instagramdesc'] = 'Geben Sie die URL Ihrer Instagram-Seite ein. (z.B. http://instagram.com/mycollege)';
$string['linkedin'] = 'LinkedIn URL';
$string['linkedindesc'] = 'Geben Sie die URL Ihrer LinkedIn-Seite ein. (z.B. http://www.linkedin.com/company/mycollege)';
$string['website'] = 'Website URL';
$string['websitedesc'] = 'Geben Sie die URL Ihrer eigenen Website ein. (z.B. https://www.mycollege.com)';
$string['socials_mail'] = 'Email-Adresse';
$string['socials_mail_desc'] = 'Geben Sie den Hyperlink-Code für die HTML-E-Mail-Adresse ein. (z.B. info@mycollege.com)';
$string['socials_color'] = 'Social Icons Farbe';
$string['socials_color_desc'] = 'Legen Sie die Farbe für Ihre Social Media-Symbole fest.';
$string['socials_position'] = 'Icons Position';
$string['socials_position_desc'] = 'Wählen Sie, wo die Social-Media-Symbole platziert werden sollen: unten auf der Seite (Fußzeile) oder oben (Kopfzeile).';
$string['socials_header_bg'] = 'Social Icons Header Background';
$string['socials_header_bg_desc'] = 'Hier können Sie auswählen, wie Sie die Hintergrundfarbe für die Social Icons in der Kopfzeile trennen möchten.';
$string['socials_header_bg_0'] = 'vollständig transparent (Kopfzeilenhintergrund verwenden)';
$string['socials_header_bg_1'] = 'leicht gedimmt';
$string['socials_header_bg_2'] = 'verdunkelt';
$string['socials_header_bg_3'] = 'Farbe des Hauptthemes verwenden';
$string['socials_header_bg_4'] = 'Fußzeile Copyright-Hintergrund verwenden';

/* Settings - Fonts */
$string['settings_fonts'] = 'Fonts';
$string['fontselect_heading'] = 'Schriftartauswahl - Überschriften';
$string['fontselectdesc_heading'] = 'Wählen Sie aus der Liste der verfügbaren Schriftarten.';
$string['fontselect_body'] = 'Schriftartauswahl - Body';
$string['fontselectdesc_body'] = 'Wählen Sie aus der Liste der verfügbaren Schriftarten.';
$string['font_body_size'] = 'Body Textgröße';
$string['font_body_size_desc'] = 'Passen Sie die globale Schriftgröße für den Fließtext an.';
$string['font_languages'] = 'Zusätzliche Zeichensätze';
$string['font_languages_desc'] = 'Einige der Schriftarten im Google-Schriftartenverzeichnis unterstützen zusätzliche Zeichensätze für verschiedene Sprachen. Die Verwendung vieler Zeichensätze kann Ihr Moodle verlangsamen. Wählen Sie daher nur die Zeichensätze aus, die Sie tatsächlich benötigen.<br /><strong>Bitte beachten Sie: </strong>Das Google-Schriftartenverzeichnis stellt nicht für jede Schriftart jeden zusätzlichen Zeichensatz zur Verfügung. Im Zweifelsfall sollten Sie <i>Open Sans</i> wählen.';
$string['font_languages_latinext'] = 'Latein Erweitert';
$string['font_languages_cyrillic'] = 'Kyrillisch';
$string['font_languages_cyrillicext'] = 'Kyrillisch Erweitert';
$string['font_languages_greek'] = 'Griechisch';
$string['font_languages_greekext'] = 'Griechisch Erweitert';
$string['use_fa5'] = 'Font Awesome 5';
$string['use_fa5_desc'] = 'Verwenden Sie die neuen Font Awesome 5 Web Font Icons.<br /><strong>Bitte beachten Sie:</strong> Font Awesome Version 5 wurde komplett neu geschrieben und von Grund auf neu gestaltet. Es sind also einige Schritte notwendig, wenn Sie die Symbole bereits mit einer früheren Version von Theme Lambda verwendet haben. Es wird notwendig sein, alle Icons, die zwischen Version 4 und 5 unterschiedliche Namen haben, zu finden und zu ersetzen. Stellen Sie sicher, dass Sie diese <a href="https://fontawesome.com/how-to-use/upgrading-from-4#icon-name-changes" target="_blank">Liste mit Namensänderungen </a>.<br /> Wenn Sie Lambda zum ersten Mal auf Ihrer Moodle-Seite installieren, wird die neue Version 5 empfohlen.';
$string['fonts_source'] = 'Schriftartauswahl';
$string['fonts_source_desc'] = 'Wählen Sie, ob Sie eine Google-Webschriftart verwenden möchten oder ob Sie Ihre eigene benutzerdefinierte Schriftartdatei hochladen möchten.<br /><strong>Bitte beachten Sie:</strong> Sie müssen <em>Änderungen speichern</em> zuerst aufrufen, um die neuen Optionen für Ihre Wahl anzuzeigen.';
$string['fonts_source_google'] = 'Google Fonts';
$string['fonts_source_file'] = 'Benutzerdefinierte Schriftdatei';
$string['fonts_file_body'] = 'Body Schriftart-Datei';
$string['fonts_file_body_desc'] = 'Laden Sie hier Ihre Body Font-Datei hoch. Für beste Kompatibilität sollten Sie ein True Type oder Web Open Font Format verwenden.';
$string['fonts_file_headings'] = 'Überschriften Schriftart-Datei';
$string['fonts_file_headings_desc'] = 'Laden Sie hier Ihre Überschriften Schriftdatei hoch. Für beste Kompatibilität sollten Sie ein True Type oder Web Open Font Format verwenden.';
$string['font_headings_weight'] = 'Überschriften Schriftstärke';
$string['font_headings_weight_desc'] = 'Sie können eine geeignete Schriftstärke für Ihre Überschriftenschrift wählen. Definiert von dicken bis dünnen Zeichen: 700 ist dasselbe wie fett, 400 ist dasselbe wie normal und 300 ist für Schriften mit leichteren Zeichen.';

/* Settings - Slider */
$string['settings_slider'] = 'Slideshow';
$string['slideshowheading'] = 'Startseite Slideshow';
$string['slideshowheadingsub'] = 'Dynamische Slideshow für die Startseite';
$string['slideshowdesc'] = 'Dadurch wird eine dynamische Slideshow mit bis zu 5 Slides für Sie erstellt, um wichtige Elemente Ihrer Website zu promoten.<br /><b>HINWEIS: </b>Sie müssen mindestens ein Bild hochladen, damit die Slideshow erscheint. Überschrift, Bildunterschrift und URL sind optional.';
$string['slideshow_slide1'] = 'Slideshow - Slide 1';
$string['slideshow_slide2'] = 'Slideshow - Slide 2';
$string['slideshow_slide3'] = 'Slideshow - Slide 3';
$string['slideshow_slide4'] = 'Slideshow - Slide 4';
$string['slideshow_slide5'] = 'Slideshow - Slide 5';
$string['slideshow_options'] = 'Slideshow - Optionen';
$string['slidetitle'] = 'Slide Überschrift';
$string['slidetitledesc'] = 'Geben Sie eine beschreibende Überschrift für Ihren Slide ein.';
$string['slideimage'] = 'Slide Bild';
$string['slideimagedesc'] = 'Laden Sie ein Bild hoch.';
$string['slidecaption'] = 'Slide Bildunterschrift';
$string['slidecaptiondesc'] = 'Geben Sie den Beschriftungstext ein, der für das Slide verwendet werden soll.';
$string['slide_url'] = 'Slide URL';
$string['slide_url_desc'] = 'Wenn Sie eine URL eingeben, wird in Ihrem Slide ein "Weiterlesen"-Button angezeigt.';
$string['slideshow_height'] = 'Höhe der Slideshow';
$string['slideshow_height_desc'] = 'Wählen Sie eine Höhe für die Slideshow, die für Desktop-Auflösungen verwendet werden soll. Diese Höhe wird für Tablets und Handys angepasst und verringert.';
$string['slideshow_hide_captions'] = 'Beschriftungen auf mobilen Geräten ausblenden';
$string['slideshow_hide_captions_desc'] = 'Wenn Sie für die Slideshow eine geringere Höhe verwenden oder wenn Sie die Einstellung <em>responsive</em> gewählt haben, kann es notwendig sein, die Überschriften und Bildunterschriften für mobile Geräte auszublenden. Andernfalls passen die Bildunterschriften möglicherweise nicht auf die angepasste Bildhöhe für mobile Geräte.';
$string['slideshowpattern'] = 'Pattern/Überlagerung';
$string['slideshowpatterndesc'] = 'Wählen Sie ein Pattern als transparente Überlagerung auf Ihren Bildern';
$string['pattern1'] = 'keine';
$string['pattern2'] = 'gepunktet - schmal';
$string['pattern3'] = 'gepunktet - breit';
$string['pattern4'] = 'Linien - horizontal';
$string['pattern5'] = 'Linien - vertikal';
$string['slideshow_advance'] ='AutoAdvance';
$string['slideshow_advance_desc'] ='Wählen Sie, ob ein Slide nach einer bestimmten Zeit automatisch vorwärts bewegt werden soll.';
$string['slideshow_nav'] ='Navigation Hover';
$string['slideshow_nav_desc'] ='Wenn true, werden die Navigationsschaltflächen (prev, next und Play/Stopp-Schaltflächen) nur im Hover-Status sichtbar sein, wenn false, werden sie immer sichtbar sein.';
$string['slideshow_loader'] ='Slideshow Loader';
$string['slideshow_loader_desc'] ='Wählen Sie pie, bar, keine (selbst wenn Sie "pie" wählen, können alte Browser wie IE8- es nicht anzeigen... sie werden immer einen Ladebalken anzeigen)';
$string['slideshow_imgfx'] ='Image Effekte';
$string['slideshow_imgfx_desc'] ='Wählen Sie einen Übergangseffekt für Ihre Bilder:<br /><i>random, simpleFade, curtainTopLeft, curtainTopRight, curtainBottomLeft, curtainBottomRight, curtainSliceLeft, curtainSliceRight, blindCurtainTopLeft, blindCurtainTopRight, blindCurtainBottomLeft, blindCurtainBottomRight, blindCurtainSliceBottom, blindCurtainSliceTop, stampede, mosaic, mosaicReverse, mosaicRandom, mosaicSpiral, mosaicSpiralReverse, topLeftBottomRight, bottomRightTopLeft, bottomLeftTopRight, bottomLeftTopRight, scrollLeft, scrollRight, scrollHorz, scrollBottom, scrollTop</i>';
$string['slideshow_txtfx'] ='Text Effekte';
$string['slideshow_txtfx_desc'] ='Wählen Sie einen Übergangseffekt-Text in Ihre Slides aus:<br /><i>moveFromLeft, moveFromRight, moveFromTop, moveFromBottom, fadeIn, fadeFromLeft, fadeFromRight, fadeFromTop, fadeFromBottom</i>';

/* Settings - Carousel */
$string['settings_carousel'] = 'Carousel';
$string['carouselheadingsub'] = 'Einstellungen für das Frontpage Carousel';
$string['carouseldesc'] = 'Hier können Sie einen Karussell-Schieberegler für Ihre Frontpage einrichten.<br /><strong>Bitte beachten Sie: </strong>Sie müssen mindestens die Bilder hochladen, damit der Schieberegler erscheint. Die Untertitel-Einstellungen werden als Hover-Effekt für die Bilder angezeigt und sind optional.';
$string['carousel_position'] = 'Carousel Position';
$string['carousel_positiondesc'] = 'Wählen Sie eine Position für den Karussell-Schieberegler.<br />Sie können wählen, ob der Schieberegler oben oder unten im Inhaltsbereich platziert werden soll.';
$string['carousel_h'] = 'Überschrift';
$string['carousel_h_desc'] = 'Eine Überschrift für das Frontpage Carousel.';
$string['carousel_hi'] = 'Überschrift-Tag';
$string['carousel_hi_desc'] = 'Definieren Sie Ihre Überschrift: &lt;h1&gt; definiert die wichtigste Überschrift. &lt;h6&gt; definiert die am wenigsten wichtige Überschrift.';
$string['carousel_add_html'] = 'Zusätzlicher HTML-Inhalt';
$string['carousel_add_html_desc'] = 'Jeglicher Inhalt, den Sie hier eingeben, wird links vom Karussell der Titelseite platziert.<br /><strong>Hinweis: </strong>Sie müssen HTML-Formatierungselemente verwenden, um Ihren Text zu formatieren.';
$string['carousel_slides'] = 'Anzahl der Slides';
$string['carousel_slides_desc'] = 'Wählen Sie die Anzahl der Slides für Ihr Karussell.';
$string['carousel_image'] = 'Bild';
$string['carousel_imagedesc'] = 'Laden Sie das Bild hoch, das im Slide erscheinen soll.';
$string['carousel_heading'] = 'Bildunterschrift - Überschrift';
$string['carousel_heading_desc'] = 'Geben Sie eine Überschrift für Ihr Bild ein - dies erzeugt eine Beschriftung mit einem Hover-Effekt.<br /><strong>Hinweis: </strong>Sie müssen mindestens die Überschrift eingeben, damit die Überschrift erscheint.';
$string['carousel_caption'] = 'Bildunterschrift - Text';
$string['carousel_caption_desc'] = 'Geben Sie den Beschriftungstext ein, der für den Hover-Effekt verwendet werden soll.';
$string['carousel_url'] = 'Bildunterschrift - URL';
$string['carousel_urldesc'] = 'Dadurch wird eine Schaltfläche für Ihre Bildunterschrift mit einem Link zu der eingegebenen URL erstellt.';
$string['carousel_btntext'] = 'Bildunterschrift - Link Text';
$string['carousel_btntextdesc'] = 'Geben Sie einen Linktext für die URL ein.';
$string['carousel_color'] = 'Bildunterschrift - Farbe';
$string['carousel_colordesc'] = 'Wählen Sie eine Farbe für die Beschriftung aus.';
$string['carousel_img_dim'] = 'Abmessungen des Karussellbildes';
$string['carousel_img_dim_desc'] = 'Stellen Sie die Breite für die Karussellbilder ein.';

/* Settings - Login */
$string['settings_login'] = 'Login und Navigation';
$string['custom_login'] = 'Benutzerdefinierte Login-Seite';
$string['custom_login_desc'] = 'Markieren Sie das Kontrollkästchen, um eine angepasste Version der Standard-Anmeldeseite von Moodle anzuzeigen.';
$string['mycourses_dropdown'] = 'MeineKurse Dropdown-Menü';
$string['mycourses_dropdown_desc'] = 'Zeigt die eingeschriebenen Kurse für einen Benutzer als Dropdown-Eintrag im Benutzerdefinierten Menü an.';
$string['hide_breadcrumb'] = 'Hide Breadcrumb';
$string['hide_breadcrumb_desc'] = 'Die Moodle-Breadcrumb-Navigation für nicht angemeldete und Gastbenutzer ausblenden?';
$string['shadow_effect'] = 'Shadow Effect';
$string['shadow_effect_desc'] = 'Verwenden Sie einen Schatteneffekt für die benutzerdefinierte Moodle-Menüleiste und die Slideshow?';
$string['login_link'] = 'Zusätzlicher Login-Link';
$string['login_link_desc'] = 'Zeigt einen zusätzlichen Link im Anmeldeformular des Themes an.';
$string['moodle_login_page'] = 'Moodle Login Page';
$string['custom_login_link_url'] = 'Benutzerdefinierter Login-Link URL';
$string['custom_login_link_url_desc'] = 'Hier können Sie eine benutzerdefinierte URL für Ihren zusätzlichen Link im Anmeldeformular eingeben. Dadurch wird die Einstellung aus der Dropdown-Liste überschrieben.';
$string['custom_login_link_txt'] = 'Benutzerdefinierter Login-Link-Text';
$string['custom_login_link_txt_desc'] = 'Hier können Sie einen benutzerdefinierten Text für Ihren zusätzlichen Link im Anmeldeformular eingeben. Dadurch wird die Einstellung aus der Dropdown-Liste überschrieben.';
$string['auth_googleoauth2'] = 'Oauth2';
$string['auth_googleoauth2_desc'] = 'Verwenden Sie das Moodle Oauth2-Authentifizierungs-Plugin anstelle des Standard-Anmeldeformulars?<br /><strong>Bitte beachten Sie: </strong>Für alle Moodle-Versionen vor 3.3 müssen Sie dieses zusätzliche Plugin zuerst aus dem Moodle-Plugins-Verzeichnis installieren. Dieses Plugin ermöglicht es Ihren Nutzern, sich mit einem Google / Facebook / Github / Linkedin / Windows Live / VK / Battle.net-Konto anzumelden. Wenn sich ein Benutzer zum ersten Mal anmeldet, wird ein neues Konto erstellt.';
$string['home_button'] = 'Home Button';
$string['home_button_desc'] = 'Wählen Sie aus der Liste der verfügbaren Texte für die Schaltfläche "Home" (die erste Schaltfläche im benutzerdefinierten Menü)';
$string['home_button_shortname'] = 'Kurzer Seitenname';
$string['home_button_frontpagedashboard'] = 'Frontpage (für nicht angemeldete Benutzer) / Dashboard (für eingeloggte Benutzer)';
$string['navbar_search_form'] = 'Suchfeld in der Navigationsleiste';
$string['navbar_search_form_desc'] = 'Hier können Sie wählen, ob das Suchfeld in der Navigationsleiste immer sichtbar, für nicht angemeldete Gastbenutzer ausgeblendet oder immer versteckt sein soll.';
$string['navbar_search_form_0'] = 'immer sichtbar';
$string['navbar_search_form_1'] = 'Ausblenden für nicht angemeldete Benutzer und Gastbenutzer';
$string['navbar_search_form_2'] = 'immer versteckt';

/* Theme */
$string['visibleadminonly'] ='Blöcke, die in den Bereich unten verschoben werden, werden nur von Admins gesehen.';
$string['region-side-post'] = 'Rechts';
$string['region-side-pre'] = 'Links';
$string['region-footer-left'] = 'Footer (Links)';
$string['region-footer-middle'] = 'Footer (Mitte)';
$string['region-footer-right'] = 'Footer (Rechts)';
$string['region-hidden-dock'] = 'Vor Benutzern verborgen';
$string['nextsection'] = '';
$string['previoussection'] = '';
$string['backtotop'] = '';
$string['responsive'] = 'responsive';
$string['privacy:metadata:preference:sidebarstat'] = 'Die Präferenz der/des Benutzer(s) für das Ein- oder Ausblenden der Navigation im Schubladenmenü.';
$string['privacy_sidebar_closed'] = 'Die aktuelle Einstellung für die zusammenklappbare Seitenleiste ist geschlossen.';
$string['privacy_sidebar_open'] = 'Die aktuelle Präferenz für die zusammenklappbare Seitenleiste ist offen.';