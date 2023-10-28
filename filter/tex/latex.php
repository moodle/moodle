<?php
    // latex.php
    // render TeX stuff using latex - this will not work on all platforms
    // or configurations. Only works on Linux and Mac with appropriate
    // software installed.
    // Much of this inspired/copied from Benjamin Zeiss' work

    class latex {

        var $temp_dir;
        var $error;

        /**
         * Constructor - create temporary directories and build paths to
         * external 'helper' binaries.
         * Other platforms could/should be added
         */
        public function __construct() {
            // Construct directory structure.
            $this->temp_dir = make_request_directory();
        }

        /**
         * Old syntax of class constructor. Deprecated in PHP7.
         *
         * @deprecated since Moodle 3.1
         */
        public function latex() {
            debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
            self::__construct();
        }

        /**
         * Accessor function for support_platform field.
         * @return boolean value of supported_platform
         */
        function supported() {
            return $this->supported_platform;
        }

        /**
         * Turn the bit of TeX into a valid latex document
         * @param string $forumula the TeX formula
         * @param int $fontsize the font size
         * @return string the latex document
         */
        function construct_latex_document($formula, $fontsize = 12) {
            // $fontsize don't affects to formula's size. $density can change size
            $doc = "\\documentclass[{$fontsize}pt]{article}\n";
            $doc .= get_config('filter_tex', 'latexpreamble');
            $doc .= "\\pagestyle{empty}\n";
            $doc .= "\\begin{document}\n";
            if (preg_match("/^[[:space:]]*\\\\begin\\{(gather|align|alignat|multline).?\\}/i", $formula)) {
               $doc .= "$formula\n";
            } else {
               $doc .= "$ {$formula} $\n";
            }
            $doc .= "\\end{document}\n";

            // Sanitize the whole document (rather than just the formula) to make sure no one can bypass sanitization
            // by using \newcommand in preamble to give an alias to a blocked command.
            $doc = filter_tex_sanitize_formula($doc);

            return $doc;
        }

        /**
         * execute an external command, with optional logging
         * @param string $command command to execute
         * @param file $log valid open file handle - log info will be written to this file
         * @return return code from execution of command
         */
        function execute( $command, $log=null ) {
            $output = array();
            exec( $command, $output, $return_code );
            if ($log) {
                fwrite( $log, "COMMAND: $command \n" );
                $outputs = implode( "\n", $output );
                fwrite( $log, "OUTPUT: $outputs \n" );
                fwrite( $log, "RETURN_CODE: $return_code\n " );
            }
            return $return_code;
        }

        /**
         * Render TeX string into gif/png
         * @param string $formula TeX formula
         * @param string $filename filename for output (including extension)
         * @param int $fontsize font size
         * @param int $density density value for .ps to .gif/.png conversion
         * @param string $background background color (e.g, #FFFFFF).
         * @param file $log valid open file handle for optional logging (debugging only)
         * @return bool true if successful
         */
        function render( $formula, $filename, $fontsize=12, $density=240, $background='', $log=null ) {

            global $CFG;

            // quick check - will this work?
            $pathlatex = get_config('filter_tex', 'pathlatex');
            if (empty($pathlatex)) {
                return false;
            }
            $pathlatex = escapeshellarg(trim($pathlatex, " '\""));

            $doc = $this->construct_latex_document( $formula, $fontsize );

            // construct some file paths
            $convertformat = get_config('filter_tex', 'convertformat');
            if (!strpos($filename, ".{$convertformat}")) {
                $convertformat = 'png';
            }
            $filename = str_replace(".{$convertformat}", '', $filename);
            $tex = "$filename.tex"; // Absolute paths won't work with openin_any = p setting.
            $dvi = "{$this->temp_dir}/$filename.dvi";
            $ps  = "{$this->temp_dir}/$filename.ps";
            $img = "{$this->temp_dir}/$filename.{$convertformat}";

            // Change directory to temp dir so that we can work with relative paths.
            chdir($this->temp_dir);

            // turn the latex doc into a .tex file in the temp area
            $fh = fopen( $tex, 'w' );
            fputs( $fh, $doc );
            fclose( $fh );

            // run latex on document
            $command = "$pathlatex --interaction=nonstopmode --halt-on-error $tex";

            if ($this->execute($command, $log)) { // It allways False on Windows
//                return false;
            }

            // run dvips (.dvi to .ps)
            $pathdvips = escapeshellarg(trim(get_config('filter_tex', 'pathdvips'), " '\""));
            $command = "$pathdvips -q -E $dvi -o $ps";
            if ($this->execute($command, $log )) {
                return false;
            }

            // Run convert on document (.ps to .gif/.png) or run dvisvgm (.ps to .svg).
            if ($background) {
                $bg_opt = "-transparent \"$background\""; // Makes transparent background
            } else {
                $bg_opt = "";
            }
            if ($convertformat == 'svg') {
                $pathdvisvgm = escapeshellarg(trim(get_config('filter_tex', 'pathdvisvgm'), " '\""));
                $command = "$pathdvisvgm -E $ps -o $img";
            } else {
                $pathconvert = escapeshellarg(trim(get_config('filter_tex', 'pathconvert'), " '\""));
                $command = "$pathconvert -density $density -trim $bg_opt $ps $img";
            }
            if ($this->execute($command, $log )) {
                return false;
            }

            return $img;
        }
    }
