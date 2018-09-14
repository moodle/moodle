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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param context $context.
 * @param string $filearea.
 * @param array $args.
 * @param bool $forcedownload.
 * @param array $options.
 * @return bool.
 */
function theme_essential_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('essential');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        // By default, theme files must be cache-able by both browsers and proxies.  From 'More' theme.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'style') {
            theme_essential_serve_css($args[1]);
        } else if ($filearea === 'headerbackground') {
            return $theme->setting_file_serve('headerbackground', $args, $forcedownload, $options);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if ($filearea === 'favicon') {
            return $theme->setting_file_serve('favicon', $args, $forcedownload, $options);
        } else if (preg_match("/^fontfile(eot|otf|svg|ttf|woff|woff2)(heading|body)$/", $filearea)) {
            // Ref: http://www.regexr.com/.
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^(marketing|slide|categoryct)[1-9][0-9]*image$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'defaultcategoryimage') {
            return $theme->setting_file_serve('defaultcategoryimage', $args, $forcedownload, $options);
        } else if (preg_match("/^categoryimage[1-9][0-9]*$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'iphoneicon') {
            return $theme->setting_file_serve('iphoneicon', $args, $forcedownload, $options);
        } else if ($filearea === 'iphoneretinaicon') {
            return $theme->setting_file_serve('iphoneretinaicon', $args, $forcedownload, $options);
        } else if ($filearea === 'ipadicon') {
            return $theme->setting_file_serve('ipadicon', $args, $forcedownload, $options);
        } else if ($filearea === 'ipadretinaicon') {
            return $theme->setting_file_serve('ipadretinaicon', $args, $forcedownload, $options);
        } else if ($filearea === 'loginbackground') {
            return $theme->setting_file_serve('loginbackground', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

function theme_essential_serve_css($filename) {
    global $CFG;

    if (file_exists("{$CFG->dirroot}/theme/essential/style/")) {
        $thestylepath = $CFG->dirroot . '/theme/essential/style/';
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/style/")) {
        $thestylepath = $CFG->themedir . '/essential/style/';
    } else {
        header('HTTP/1.0 404 Not Found');
        die('Essential style folder not found, check $CFG->themedir is correct.');
    }
    $thesheet = $thestylepath . $filename;

    /* http://css-tricks.com/snippets/php/intelligent-php-cache-control/ - rather than /lib/csslib.php as it is a static file who's
      contents should only change if it is rebuilt.  But! There should be no difference with TDM on so will see for the moment if
      that decision is a factor. */

    $etagfile = md5_file($thesheet);
    // File.
    $lastmodified = filemtime($thesheet);
    // Header.
    $ifmodifiedsince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
    $etagheader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

    if ((($ifmodifiedsince) && (strtotime($ifmodifiedsince) == $lastmodified)) || $etagheader == $etagfile) {
        theme_essential_send_unmodified($lastmodified, $etagfile);
    }
    theme_essential_send_cached_css($thestylepath, $filename, $lastmodified, $etagfile);
}

function theme_essential_send_unmodified($lastmodified, $etag) {
    $lifetime = 60 * 60 * 24 * 60;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}

function theme_essential_send_cached_css($path, $filename, $lastmodified, $etag) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php'); // For min_enable_zlib_compression().
    // 60 days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($path . $filename));
    }

    readfile($path . $filename);
    die;
}

function theme_essential_process_css($css, $theme) {
    global $PAGE;
    $outputus = $PAGE->get_renderer('theme_essential', 'core');
    \theme_essential\toolbox::set_core_renderer($outputus);

    // Set the theme width.
    $pagewidth = \theme_essential\toolbox::get_setting('pagewidth');
    $css = \theme_essential\toolbox::set_pagewidth($css, $pagewidth);

    // Set the theme font.
    $css = \theme_essential\toolbox::set_font($css, 'heading', \theme_essential\toolbox::get_setting('fontnameheading'));
    $css = \theme_essential\toolbox::set_font($css, 'body', \theme_essential\toolbox::get_setting('fontnamebody'));

    // Set the theme colour.
    $themecolor = \theme_essential\toolbox::get_setting('themecolor');
    $css = \theme_essential\toolbox::set_color($css, $themecolor, '[[setting:themecolor]]', '#30add1');

    // Input focus colour.
    $css = \theme_essential\toolbox::set_color($css, $themecolor, '[[setting:inputfocusbordercolor]]', '#30add1', '0.8');
    $css = \theme_essential\toolbox::set_color($css, $themecolor, '[[setting:inputfocusshadowcolor]]', '#30add1', '0.6');

    // Set the theme text colour.
    $themetextcolor = \theme_essential\toolbox::get_setting('themetextcolor');
    $css = \theme_essential\toolbox::set_color($css, $themetextcolor, '[[setting:themetextcolor]]', '#047797');

    // Set the theme url colour.
    $themeurlcolor = \theme_essential\toolbox::get_setting('themeurlcolor');
    $css = \theme_essential\toolbox::set_color($css, $themeurlcolor, '[[setting:themeurlcolor]]', '#FF5034');

    // Set the theme hover colour.
    $themehovercolor = \theme_essential\toolbox::get_setting('themehovercolor');
    $css = \theme_essential\toolbox::set_color($css, $themehovercolor, '[[setting:themehovercolor]]', '#F32100');

    // Set the theme header text colour.
    $themetextcolor = \theme_essential\toolbox::get_setting('headertextcolor');
    $css = \theme_essential\toolbox::set_color($css, $themetextcolor, '[[setting:headertextcolor]]', '#217a94');

    // Set the theme icon colour.
    $themeiconcolor = \theme_essential\toolbox::get_setting('themeiconcolor');
    $css = \theme_essential\toolbox::set_color($css, $themeiconcolor, '[[setting:themeiconcolor]]', '#30add1');

    // Set the theme side-pre block background colour.
    $themesidepreblockbackgroundcolour = \theme_essential\toolbox::get_setting('themesidepreblockbackgroundcolour');
    $css = \theme_essential\toolbox::set_color($css, $themesidepreblockbackgroundcolour, '[[setting:themesidepreblockbackgroundcolour]]', '#ffffff');

    // Set the theme side-pre block text colour.
    $themesidepreblocktextcolour = \theme_essential\toolbox::get_setting('themesidepreblocktextcolour');
    $css = \theme_essential\toolbox::set_color($css, $themesidepreblocktextcolour, '[[setting:themesidepreblocktextcolour]]', '#217a94');

    // Set the theme side-pre block url colour.
    $themesidepreblockurlcolour = \theme_essential\toolbox::get_setting('themesidepreblockurlcolour');
    $css = \theme_essential\toolbox::set_color($css, $themesidepreblockurlcolour, '[[setting:themesidepreblockurlcolour]]', '#943b21');

    // Set the theme side-pre block url hover colour.
    $themesidepreblockhovercolour = \theme_essential\toolbox::get_setting('themesidepreblockhovercolour');
    $css = \theme_essential\toolbox::set_color($css, $themesidepreblockhovercolour, '[[setting:themesidepreblockhovercolour]]', '#6a2a18');

    // Set the theme default button text colour.
    $themedefaultbuttontextcolour = \theme_essential\toolbox::get_setting('themedefaultbuttontextcolour');
    $css = \theme_essential\toolbox::set_color($css, $themedefaultbuttontextcolour,
        '[[setting:themedefaultbuttontextcolour]]', '#ffffff');

    // Set the theme default button text hover colour.
    $themedefaultbuttontexthovercolour = \theme_essential\toolbox::get_setting('themedefaultbuttontexthovercolour');
    $css = \theme_essential\toolbox::set_color($css, $themedefaultbuttontexthovercolour,
        '[[setting:themedefaultbuttontexthovercolour]]', '#ffffff');

    // Set the theme default button background colour.
    $themedefaultbuttonbackgroundcolour = \theme_essential\toolbox::get_setting('themedefaultbuttonbackgroundcolour');
    $css = \theme_essential\toolbox::set_color($css, $themedefaultbuttonbackgroundcolour,
        '[[setting:themedefaultbuttonbackgroundcolour]]', '#30add1');
    $css = \theme_essential\toolbox::set_color($css, $themedefaultbuttonbackgroundcolour,
        '[[setting:themedefaultbuttonbackgroundcolourimage]]', '#30add1');
    $css = \theme_essential\toolbox::set_color($css,
        \theme_essential\toolbox::hexadjust($themedefaultbuttonbackgroundcolour, 10),
        '[[setting:themedefaultbuttonbackgroundcolourrgba]]', '#30add1', '0.25');

    // Set the theme default button background hover colour.
    $themedefaultbuttonbackgroundhovercolour = \theme_essential\toolbox::get_setting('themedefaultbuttonbackgroundhovercolour');
    $css = \theme_essential\toolbox::set_color($css, $themedefaultbuttonbackgroundhovercolour,
        '[[setting:themedefaultbuttonbackgroundhovercolour]]', '#3ad4ff');
    $css = \theme_essential\toolbox::set_color($css, $themedefaultbuttonbackgroundhovercolour,
        '[[setting:themedefaultbuttonbackgroundhovercolourimage]]', '#3ad4ff');
    $css = \theme_essential\toolbox::set_color($css,
        \theme_essential\toolbox::hexadjust($themedefaultbuttonbackgroundhovercolour, 10),
        '[[setting:themedefaultbuttonbackgroundhovercolourrgba]]', '#3ad4ff', '0.25');

    // Set the theme navigation colour.
    $themenavcolor = \theme_essential\toolbox::get_setting('themenavcolor');
    $css = \theme_essential\toolbox::set_color($css, $themenavcolor, '[[setting:themenavcolor]]', '#ffffff');

    // Set the theme stripe text colour.
    $themestripetextcolour = \theme_essential\toolbox::get_setting('themestripetextcolour');
    $css = \theme_essential\toolbox::set_color($css, $themestripetextcolour, '[[setting:themestripetextcolour]]', '#ffffff');

    // Set the theme stripe background colour.
    $themestripebackgroundcolour = \theme_essential\toolbox::get_setting('themestripebackgroundcolour');
    $css = \theme_essential\toolbox::set_color($css, $themestripebackgroundcolour, '[[setting:themestripebackgroundcolour]]', '#ff9a34');

    $themestripeurlcolour = \theme_essential\toolbox::get_setting('themestripeurlcolour');
    $css = \theme_essential\toolbox::set_color($css, $themestripeurlcolour, '[[setting:themestripeurlcolour]]', '#25849F');

    // Set the theme Quiz 'Submit all and finish' colours.
    $themequizsubmittextcolour = \theme_essential\toolbox::get_setting('themequizsubmittextcolour');
    $css = \theme_essential\toolbox::set_color($css, $themequizsubmittextcolour,
        '[[setting:themequizsubmittextcolour]]', '#ffffff');

    $themequizsubmittexthovercolour = \theme_essential\toolbox::get_setting('themequizsubmittexthovercolour');
    $css = \theme_essential\toolbox::set_color($css, $themequizsubmittexthovercolour,
        '[[setting:themequizsubmittexthovercolour]]', '#ffffff');

    $themequizsubmitbackgroundcolour = \theme_essential\toolbox::get_setting('themequizsubmitbackgroundcolour');
    $css = \theme_essential\toolbox::set_color($css, $themequizsubmitbackgroundcolour,
        '[[setting:themequizsubmitbackgroundcolour]]', '#ff9a34');
    $css = \theme_essential\toolbox::set_color($css, $themequizsubmitbackgroundcolour,
        '[[setting:themequizsubmitbackgroundcolourimage]]', '#ff9a34');
    $css = \theme_essential\toolbox::set_color($css,
        \theme_essential\toolbox::hexadjust($themequizsubmitbackgroundcolour, 10),
        '[[setting:themequizsubmitbackgroundcolourrgba]]', '#ff9a34', '0.25');

    $themequizsubmitbackgroundhovercolour = \theme_essential\toolbox::get_setting('themequizsubmitbackgroundhovercolour');
    $css = \theme_essential\toolbox::set_color($css, $themequizsubmitbackgroundhovercolour,
        '[[setting:themequizsubmitbackgroundhovercolour]]', '#ffaf60');
    $css = \theme_essential\toolbox::set_color($css, $themequizsubmitbackgroundhovercolour,
        '[[setting:themequizsubmitbackgroundhovercolourimage]]', '#ffaf60');
    $css = \theme_essential\toolbox::set_color($css,
        \theme_essential\toolbox::hexadjust($themequizsubmitbackgroundhovercolour, 10),
        '[[setting:themequizsubmitbackgroundhovercolourrgba]]', '#ffaf60', '0.25');

    // Enrolled and not accessed course background colour.
    $mycoursesorderenrolbackcolour = \theme_essential\toolbox::get_setting('mycoursesorderenrolbackcolour');
    $css = \theme_essential\toolbox::set_color($css, $mycoursesorderenrolbackcolour,
        '[[setting:mycoursesorderenrolbackcolour]]', '#a3ebff');

    // Set the footer colour.
    $footercolor = \theme_essential\toolbox::get_setting('footercolor');
    $css = \theme_essential\toolbox::set_color($css, $footercolor, '[[setting:footercolor]]', '#30add1', '0.95');

    // Set the footer text colour.
    $footertextcolor = \theme_essential\toolbox::get_setting('footertextcolor');
    $css = \theme_essential\toolbox::set_color($css, $footertextcolor, '[[setting:footertextcolor]]', '#ffffff');

    // Set the footer block background colour.
    $footerheadingcolor = \theme_essential\toolbox::get_setting('footerblockbackgroundcolour');
    $css = \theme_essential\toolbox::set_color($css, $footerheadingcolor, '[[setting:footerblockbackgroundcolour]]',
                    '#cccccc');

    // Set the footer block heading colour.
    $footerheadingcolor = \theme_essential\toolbox::get_setting('footerheadingcolor');
    $css = \theme_essential\toolbox::set_color($css, $footerheadingcolor, '[[setting:footerheadingcolor]]', '#cccccc');

    // Set the footer text colour.
    $footertextcolor = \theme_essential\toolbox::get_setting('footerblocktextcolour');
    $css = \theme_essential\toolbox::set_color($css, $footertextcolor, '[[setting:footerblocktextcolour]]', '#000000');

    // Set the footer block URL colour.
    $footerurlcolor = \theme_essential\toolbox::get_setting('footerblockurlcolour');
    $css = \theme_essential\toolbox::set_color($css, $footerurlcolor, '[[setting:footerblockurlcolour]]', '#000000');

    // Set the footer block hover colour.
    $footerhovercolor = \theme_essential\toolbox::get_setting('footerblockhovercolour');
    $css = \theme_essential\toolbox::set_color($css, $footerhovercolor, '[[setting:footerblockhovercolour]]', '#555555');

    // Set the footer separator colour.
    $footersepcolor = \theme_essential\toolbox::get_setting('footersepcolor');
    $css = \theme_essential\toolbox::set_color($css, $footersepcolor, '[[setting:footersepcolor]]', '#313131');

    // Set the footer URL colour.
    $footerurlcolor = \theme_essential\toolbox::get_setting('footerurlcolor');
    $css = \theme_essential\toolbox::set_color($css, $footerurlcolor, '[[setting:footerurlcolor]]', '#cccccc');

    // Set the footer hover colour.
    $footerhovercolor = \theme_essential\toolbox::get_setting('footerhovercolor');
    $css = \theme_essential\toolbox::set_color($css, $footerhovercolor, '[[setting:footerhovercolor]]', '#bbbbbb');

    // Set the slide header colour.
    $slideshowcolor = \theme_essential\toolbox::get_setting('slideshowcolor');
    $css = \theme_essential\toolbox::set_color($css, $slideshowcolor, '[[setting:slideshowcolor]]', '#30add1');

    // Set the slide header colour.
    $slideheadercolor = \theme_essential\toolbox::get_setting('slideheadercolor');
    $css = \theme_essential\toolbox::set_color($css, $slideheadercolor, '[[setting:slideheadercolor]]', '#30add1');

    // Set the slide caption text colour.
    $slidecaptiontextcolor = \theme_essential\toolbox::get_setting('slidecaptiontextcolor');
    $css = \theme_essential\toolbox::set_color($css, $slidecaptiontextcolor, '[[setting:slidecaptiontextcolor]]',
                    '#ffffff');

    // Set the slide caption background colour.
    $slidecaptionbackgroundcolor = \theme_essential\toolbox::get_setting('slidecaptionbackgroundcolor');
    $css = \theme_essential\toolbox::set_color($css, $slidecaptionbackgroundcolor,
                    '[[setting:slidecaptionbackgroundcolor]]', '#30add1');

    // Set the slide button colour.
    $slidebuttoncolor = \theme_essential\toolbox::get_setting('slidebuttoncolor');
    $css = \theme_essential\toolbox::set_color($css, $slidebuttoncolor, '[[setting:slidebuttoncolor]]', '#30add1');

    // Set the slide button hover colour.
    $slidebuttonhcolor = \theme_essential\toolbox::get_setting('slidebuttonhovercolor');
    $css = \theme_essential\toolbox::set_color($css, $slidebuttonhcolor, '[[setting:slidebuttonhovercolor]]', '#217a94');

    if ((\theme_essential\toolbox::get_setting('enablealternativethemecolors1')) ||
            (\theme_essential\toolbox::get_setting('enablealternativethemecolors2')) ||
            (\theme_essential\toolbox::get_setting('enablealternativethemecolors3')) ||
            (\theme_essential\toolbox::get_setting('enablealternativethemecolors4'))
    ) {
        // Set theme alternative colours.
        $defaultcolors = array('#a430d1', '#d15430', '#5dd130', '#006b94');
        $defaulthovercolors = array('#9929c4', '#c44c29', '#53c429', '#4090af');
        $defaultstripetextcolors = array('#bdfdb7', '#c3fdd0', '#9f5bfb', '#ff1ebd');
        $defaultstripebackgroundcolors = array('#c1009f', '#bc2800', '#b4b2fd', '#0336b4');
        $defaultstripeurlcolors = array('#bef500', '#30af67', '#ffe9a6', '#ffab00');

        foreach (range(1, 4) as $alternative) {
            $default = $defaultcolors[$alternative - 1];
            $defaulthover = $defaulthovercolors[$alternative - 1];
            $defaultstripetext = $defaultstripetextcolors[$alternative - 1];
            $defaultstripebackground = $defaultstripebackgroundcolors[$alternative - 1];
            $defaultstripeurl = $defaultstripeurlcolors[$alternative - 1];
            $alternativethemecolour = \theme_essential\toolbox::get_setting('alternativethemecolor'.$alternative);
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'color'.$alternative,
                $alternativethemecolour, $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'inputfocusbordercolor'.$alternative,
                $alternativethemecolour, $default, '0.8');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'inputfocusshadowcolor'.$alternative,
                $alternativethemecolour, $default, '0.6');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'textcolor'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemetextcolor'.$alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'urlcolor'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemeurlcolor'.$alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttontextcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemedefaultbuttontextcolour'.$alternative),
                '#ffffff');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttontexthovercolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemedefaultbuttontexthovercolour'.$alternative),
                '#ffffff');

            $alternativethemedefaultbuttonbackgroundcolour = \theme_essential\toolbox::get_setting(
                'alternativethemedefaultbuttonbackgroundcolour'.$alternative);
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttonbackgroundcolour'.$alternative,
                $alternativethemedefaultbuttonbackgroundcolour,
                '#30add1');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttonbackgroundcolourimage'.$alternative,
                $alternativethemedefaultbuttonbackgroundcolour,
                '#30add1');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttonbackgroundcolourrgba'.$alternative,
                \theme_essential\toolbox::hexadjust($alternativethemedefaultbuttonbackgroundcolour, 10),
                '#30add1', '0.25');

            $alternativethemedefaultbuttonbackgroundhovercolour = \theme_essential\toolbox::get_setting(
                'alternativethemedefbuttonbackgroundhvrcolour'.$alternative);
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttonbackgroundhovercolour'.$alternative,
                $alternativethemedefaultbuttonbackgroundhovercolour,
                '#3ad4ff');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttonbackgroundhovercolourimage'.$alternative,
                $alternativethemedefaultbuttonbackgroundhovercolour,
                '#3ad4ff');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'defaultbuttonbackgroundhovercolourrgba'.$alternative,
                \theme_essential\toolbox::hexadjust($alternativethemedefaultbuttonbackgroundhovercolour, 10),
                '#3ad4ff', '0.25');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'iconcolor'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemeiconcolor'.$alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'sidepreblockbackgroundcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemesidepreblockbackgroundcolour'.$alternative), '#ffffff');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'sidepreblocktextcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemesidepreblocktextcolour'.$alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'sidepreblockurlcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemesidepreblockurlcolour'.$alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'sidepreblockhovercolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemesidepreblockhovercolour'.$alternative), $defaulthover);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'navcolor'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemenavcolor'.$alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'hovercolor'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemehovercolor'.$alternative), $defaulthover);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'stripetextcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemestripetextcolour'.$alternative), $defaultstripetext);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'stripebackgroundcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemestripebackgroundcolour'.$alternative), $defaultstripebackground);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'stripeurlcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemestripeurlcolour'.$alternative), $defaultstripeurl);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmittextcolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemequizsubmittextcolour'.$alternative),
                '#ffffff');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmittexthovercolour'.$alternative,
                \theme_essential\toolbox::get_setting('alternativethemequizsubmittexthovercolour'.$alternative),
                '#ffffff');

            $alternativethemequizsubmitbackgroundcolour = \theme_essential\toolbox::get_setting(
                'alternativethemequizsubmitbackgroundcolour'.$alternative);
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmitbackgroundcolour'.$alternative,
                $alternativethemequizsubmitbackgroundcolour,
                '#ff9a34');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmitbackgroundcolourimage'.$alternative,
                $alternativethemequizsubmitbackgroundcolour,
                '#ff9a34');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmitbackgroundcolourrgba'.$alternative,
                \theme_essential\toolbox::hexadjust($alternativethemequizsubmitbackgroundcolour, 10),
                '#ff9a34', '0.25');

            $alternativethemequizsubmitbackgroundhovercolour = \theme_essential\toolbox::get_setting(
                'alternativethemequizsubmitbackgroundhovercolour'.$alternative);
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmitbackgroundhovercolour'.$alternative,
                $alternativethemequizsubmitbackgroundhovercolour,
                '#ffaf60');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmitbackgroundhovercolourimage'.$alternative,
                $alternativethemequizsubmitbackgroundhovercolour,
                '#ffaf60');
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'quizsubmitbackgroundhovercolourrgba'.$alternative,
                \theme_essential\toolbox::hexadjust($alternativethemequizsubmitbackgroundhovercolour, 10),
                '#ffaf60', '0.25');

            $alternativethememycoursesorderenrolbackcolour = \theme_essential\toolbox::get_setting(
                'alternativethememycoursesorderenrolbackcolour'.$alternative);
            $css = \theme_essential\toolbox::set_alternativecolor($css, 'mycoursesorderenrolbackcolour'.$alternative,
                $alternativethememycoursesorderenrolbackcolour, '#a3ebff');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footercolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefootercolor' . $alternative), '#30add1');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footertextcolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefootertextcolor' . $alternative), '#30add1');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerblockbackgroundcolour' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterblockbackgroundcolour' . $alternative), '#cccccc');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerblocktextcolour' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterblocktextcolour' . $alternative), '#000000');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerblockurlcolour' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterblockurlcolour' . $alternative), '#000000');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerblockhovercolour' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterblockhovercolour' . $alternative), '#555555');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerheadingcolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterheadingcolor' . $alternative), '#cccccc');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footersepcolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefootersepcolor' . $alternative), '#313131');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerurlcolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterurlcolor' . $alternative), '#cccccc');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'footerhovercolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemefooterhovercolor' . $alternative), '#bbbbbb');

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'slidecaptiontextcolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemeslidecaptiontextcolor' . $alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'slidecaptionbackgroundcolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemeslidecaptionbackgroundcolor' . $alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'slidebuttoncolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemeslidebuttoncolor' . $alternative), $default);

            $css = \theme_essential\toolbox::set_alternativecolor($css, 'slidebuttonhovercolor' . $alternative,
                \theme_essential\toolbox::get_setting('alternativethemeslidebuttonhovercolor' . $alternative), $defaulthover);
        }
    }

    // Set the logo desktop and mobile width.
    $logodesktopwidth = \theme_essential\toolbox::get_setting('logodesktopwidth');
    $logomobilewidth = \theme_essential\toolbox::get_setting('logomobilewidth');
    $css = \theme_essential\toolbox::set_integer($css, 'logodesktopwidth', $logodesktopwidth, 25);
    $css = \theme_essential\toolbox::set_integer($css, 'logomobilewidth', $logomobilewidth, 10);

    // Set the dropdown menu maximum height.
    $dropdownmenumaxheight = \theme_essential\toolbox::get_setting('dropdownmenumaxheight');
    $css = \theme_essential\toolbox::set_integer($css, 'dropdownmenumaxheight', $dropdownmenumaxheight, 384);

    // Set the background image for the header.
    $headerbackground = \theme_essential\toolbox::setting_file_url('headerbackground', 'headerbackground');
    $css = \theme_essential\toolbox::set_headerbackground($css, $headerbackground);

    // Set the background image for the page.
    $pagebackground = \theme_essential\toolbox::setting_file_url('pagebackground', 'pagebackground');
    $css = \theme_essential\toolbox::set_pagebackground($css, $pagebackground);

    // Set the background style for the page.
    $pagebgstyle = \theme_essential\toolbox::get_setting('pagebackgroundstyle');
    $css = \theme_essential\toolbox::set_pagebackgroundstyle($css, $pagebgstyle);

    // Set the background image for the login page.
    $loginbackground = \theme_essential\toolbox::setting_file_url('loginbackground', 'loginbackground');
    $css = \theme_essential\toolbox::set_loginbackground($css, $loginbackground);

    // Set the background style for the login page.
    $loginbgstyle = \theme_essential\toolbox::get_setting('loginbackgroundstyle');
    $loginbgopacity = \theme_essential\toolbox::get_setting('loginbackgroundopacity');
    $css = \theme_essential\toolbox::set_loginbackgroundstyle($css, $loginbgstyle, $loginbgopacity);

    // Set the user image border radius.
    $userimageborderradius = \theme_essential\toolbox::get_setting('userimageborderradius');
    $css = \theme_essential\toolbox::set_integer($css, 'userimageborderradius', $userimageborderradius, 90);

    // Set the user menu user image border radius.
    $usermenuuserimageborderradius = \theme_essential\toolbox::get_setting('usermenuuserimageborderradius');
    $css = \theme_essential\toolbox::set_integer($css, 'usermenuuserimageborderradius', $usermenuuserimageborderradius, 4);

    // Set marketing height.
    $marketingheight = \theme_essential\toolbox::get_setting('marketingheight');
    $marketingimageheight = \theme_essential\toolbox::get_setting('marketingimageheight');
    $css = \theme_essential\toolbox::set_marketingheight($css, $marketingheight, $marketingimageheight);

    // Set marketing images.
    $setting = 'marketing1image';
    $marketingimage = \theme_essential\toolbox::setting_file_url($setting, $setting);
    $css = \theme_essential\toolbox::set_marketingimage($css, $marketingimage, $setting);

    $setting = 'marketing2image';
    $marketingimage = \theme_essential\toolbox::setting_file_url($setting, $setting);
    $css = \theme_essential\toolbox::set_marketingimage($css, $marketingimage, $setting);

    $setting = 'marketing3image';
    $marketingimage = \theme_essential\toolbox::setting_file_url($setting, $setting);
    $css = \theme_essential\toolbox::set_marketingimage($css, $marketingimage, $setting);

    // Category course title images.
    $css = \theme_essential\toolbox::set_categorycoursetitleimages($css);

    // Set custom CSS.
    $customcss = \theme_essential\toolbox::get_setting('customcss');
    $css = \theme_essential\toolbox::set_customcss($css, $customcss);

    // Finally return processed CSS.
    return $css;
}
