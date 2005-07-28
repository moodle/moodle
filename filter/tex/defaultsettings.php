<?php
    // defaultsettings.php
    // deafault settings are done here, saves doing all this twice in
    // both the rendering routine and the config screen

    function tex_defaultsettings() {

	global $CFG;

        if (!isset($CFG->filter_tex_latexpreamble)) {
            set_config( 'filter_tex_latexpreamble', " \\usepackage[latin1]{inputenc}\n \\usepackage{amsmath}\n \\usepackage{amsfonts}\n");
        }

        if (!isset($CFG->filter_tex_latexbackground)) {
            set_config( 'filter_tex_latexbackground', '#FFFFFF' );
        }

        if (!isset($CFG->filter_tex_density)) {
            set_config( 'filter_tex_density', '120' );
        }

        // defaults for paths - if one not set assume all not set
        if (!isset($CFG->filter_tex_pathlatex)) {
            // load the paths for the appropriate OS
            // it would be nice to expand this
            if (PHP_OS=='Linux') {
                $binpath = '/usr/bin/';
                set_config( 'filter_tex_pathlatex',"{$binpath}latex" );
                set_config( 'filter_tex_pathdvips',"{$binpath}dvips" );
                set_config( 'filter_tex_pathconvert',"{$binpath}convert" );
            }
            elseif (PHP_OS=='Darwin') {
                $binpath = '/sw/bin/'; // most likely needs a fink install (fink.sf.net)
                set_config( 'filter_tex_pathlatex',"{$binpath}latex" );
                set_config( 'filter_tex_pathdvips',"{$binpath}dvips" );
                set_config( 'filter_tex_pathconvert',"{$binpath}convert" );
            }
            elseif (PHP_OS=='WINNT' or PHP_OS=='WIN32' or PHP_OS=='Windows') {
	        // note: you need Ghostscript installed (standard), miktex (standard)
	        // and ImageMagick (install at c:\ImageMagick)
	        set_config( 'filter_tex_pathlatex',"\"c:\\texmf\\miktex\\bin\\latex.exe\" " );
                set_config( 'filter_tex_pathdvips',"\"c:\\texmf\\miktex\\bin\\dvips.exe\" " );
                set_config( 'filter_tex_pathconvert',"\"c:\\imagemagick\\convert.exe\" " );
            }    
            else {
                set_config( 'filter_tex_pathlatex','' );
                set_config( 'filter_tex_pathdvips','' );
                set_config( 'filter_tex_pathconvert','' );
            }
        }

    } 
?>
