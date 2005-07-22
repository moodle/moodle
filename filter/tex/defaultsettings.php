<?php
    // defaultsettings.php
    // deafault settings are done here, saves doing all this twice in
    // both the rendering routine and the config screen

    function tex_defaultsettings() {

	global $CFG;

        if (!isset($CFG->filter_tex_latexpreamble)) {
            set_config( 'filter_text_latexpreamble', " \\usepackage[latin1]{inputenc}\n \\usepackage{amsmath}\n \\usepackage{amsfonts}\n");
        }

        if (!isset($CFG->filter_tex_latexbackground)) {
            set_config( 'filter_tex_latexbackground', '#FFFFFF' );
        }

        if (!isset($CFG->filter_tex_density)) {
            set_config( 'filter_tex_density', '120' );
        }

    } 
?>
