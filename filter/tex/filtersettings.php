<?php  //$Id$

require_once($CFG->dirroot.'/filter/tex/lib.php');

$items = array();
$items[] = new admin_setting_heading('filter_tex_latexheading', get_string('latexsettings', 'admin'), '');
$items[] = new admin_setting_configtextarea('filter_tex_latexpreamble', get_string('latexpreamble','admin'),
               '', " \\usepackage[latin1]{inputenc}\n \\usepackage{amsmath}\n \\usepackage{amsfonts}\n \\RequirePackage{amsmath,amssymb,latexsym}\n");
$items[] = new admin_setting_configtext('filter_tex_latexbackground', get_string('backgroundcolour', 'admin'), '', '#FFFFFF');
$items[] = new admin_setting_configtext('filter_tex_density', get_string('density', 'admin'), '', '120', PARAM_INT);
$items[] = new admin_setting_configtext('filter_tex_density', get_string('density', 'admin'), '', '120', PARAM_INT);

if (PHP_OS=='Linux') {
    $default_filter_tex_pathlatex   = "/usr/bin/latex";
    $default_filter_tex_pathdvips   = "/usr/bin/dvips";
    $default_filter_tex_pathconvert = "/usr/bin/convert";

} else if (PHP_OS=='Darwin') {
    // most likely needs a fink install (fink.sf.net)
    $default_filter_tex_pathlatex   = "/sw/bin/latex";
    $default_filter_tex_pathdvips   = "/sw/bin/dvips";
    $default_filter_tex_pathconvert = "/sw/bin/convert";

} else if (PHP_OS=='WINNT' or PHP_OS=='WIN32' or PHP_OS=='Windows') {
    // note: you need Ghostscript installed (standard), miktex (standard)
    // and ImageMagick (install at c:\ImageMagick)
    $default_filter_tex_pathlatex   = "\"c:\\texmf\\miktex\\bin\\latex.exe\" ";
    $default_filter_tex_pathdvips   = "\"c:\\texmf\\miktex\\bin\\dvips.exe\" ";
    $default_filter_tex_pathconvert = "\"c:\\imagemagick\\convert.exe\" ";

} else {
    $default_filter_tex_pathlatex   = '';
    $default_filter_tex_pathdvips   = '';
    $default_filter_tex_pathconvert = '';
}

$items[] = new admin_setting_configexecutable('filter_tex_pathlatex', get_string('pathlatex', 'admin'), '', $default_filter_tex_pathlatex);
$items[] = new admin_setting_configexecutable('filter_tex_pathdvips', get_string('pathdvips', 'admin'), '', $default_filter_tex_pathdvips);
$items[] = new admin_setting_configexecutable('filter_tex_pathconvert', get_string('pathconvert', 'admin'), '', $default_filter_tex_pathconvert);

foreach ($items as $item) {
    $item->set_updatedcallback('filter_tex_updatedcallback');
    $settings->add($item);
}
?>
