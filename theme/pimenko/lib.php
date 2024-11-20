<?php
// This file is part of the Pimenko theme for Moodle
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
 * Theme Pimenko lib file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We will add callbacks here as we add features to our theme.

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_pimenko_get_main_scss_content($theme): string {
    global $CFG;

    // File storage API.
    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

    } else {
        // This preset file was fetched from the file area for theme_pimenko and not theme_boost (see the line above).
        // Fall back.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
        // If we have a custom files.
        $fs = get_file_storage();
        $filename = '/'.$theme->settings->preset;
        $context = context_system::instance();
        $presetfile = $fs->get_file($context->id, 'theme_pimenko', 'preset', 0, '/', $filename);
        if ($presetfile) {
            $scss .= $presetfile->get_content();
        }
    }

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_pimenko_get_extra_scss($theme) {
    $content = '';
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $content .= '@media (min-width: 768px) {';
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;";
        $content .= ' } }';
    }

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? $theme->settings->scss . ' ' . $content : $content;
}

/**
 * Parses CSS before it is cached.
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_pimenko_process_css($css, $theme) {
    // Define the default settings for the theme incase they've not been set.
    $defaults = [
        'brandcolor' => '#000',
        'brandcolorbutton' => '#000',
        'brandcolortextbutton' => '#FFF',
        'loginbgimage' => '',
        'loginbgstyle' => '',
        'loginbgopacity1' => '',
        'loginbgopacity2' => '',
        'loginbgopacity3' => '',
        'navbarcolor' => '#FFF',
        'navbartextcolor' => '#343B3F',
        'footercolor' => '#343B3F',
        'footertextcolor' => '#FFF',
        'hooverfootercolor' => '',
        'hoovernavbarcolor' => '',
        'blockregionrowbackgroundcolor1' => '',
        'blockregionrowbackgroundcolor2' => '',
        'blockregionrowbackgroundcolor3' => '',
        'blockregionrowbackgroundcolor4' => '',
        'blockregionrowbackgroundcolor5' => '',
        'blockregionrowbackgroundcolor6' => '',
        'blockregionrowbackgroundcolor7' => '',
        'blockregionrowbackgroundcolor8' => '',
        'blockregionrowtextcolor1' => '',
        'blockregionrowtextcolor2' => '',
        'blockregionrowtextcolor3' => '',
        'blockregionrowtextcolor4' => '',
        'blockregionrowtextcolor5' => '',
        'blockregionrowtextcolor6' => '',
        'blockregionrowtextcolor7' => '',
        'blockregionrowtextcolor8' => '',
        'blockregionrowlinkcolor1' => '',
        'blockregionrowlinkcolor2' => '',
        'blockregionrowlinkcolor3' => '',
        'blockregionrowlinkcolor4' => '',
        'blockregionrowlinkcolor5' => '',
        'blockregionrowlinkcolor6' => '',
        'blockregionrowlinkcolor7' => '',
        'blockregionrowlinkcolor8' => '',
        'blockregionrowlinkhovercolor1' => '',
        'blockregionrowlinkhovercolor2' => '',
        'blockregionrowlinkhovercolor3' => '',
        'blockregionrowlinkhovercolor4' => '',
        'blockregionrowlinkhovercolor5' => '',
        'blockregionrowlinkhovercolor6' => '',
        'blockregionrowlinkhovercolor7' => '',
        'blockregionrowlinkhovercolor8' => '',
        'gradienttextcolor' => '#000',
        'gradientcovercolor' => '',
        'googlefont' => 'Verdana',
        'tooglercolor' => 'rgb(255, 255, 255)'
    ];

    // Get all the defined settings for the theme and replace defaults.
    foreach ($theme->settings as $key => $val) {
        if (array_key_exists($key, $defaults) && !empty($val)) {
            $defaults[$key] = $val;
        }
    }

    // For login bg img.
    $loginbgimage = '';
    if (!empty($theme->settings->loginbgimage)) {
        $loginbgimage = $theme->setting_file_url('loginbgimage', 'loginbgimage');
        $loginbgimage = 'url("' . $loginbgimage . '") no-repeat center center fixed';
    }
    $defaults['loginbgimage'] = $loginbgimage;

    // For login bg style.
    $loginbgstyle = '';
    if (!empty($theme->settings->loginbgstyle)) {
        $replacementstyle = 'cover';
        if ($theme->settings->loginbgstyle === 'stretch') {
            $replacementstyle = '100% 100%';
        }
        $loginbgstyle = $replacementstyle;
    }
    $defaults['loginbgstyle'] = $loginbgstyle;

    // Darken color for link in navbar.
    $color = $defaults['navbartextcolor'];
    if ($defaults['hoovernavbarcolor']) {
        $defaults['darkennavcolor'] = $defaults['hoovernavbarcolor'];
    } else {
        $defaults['darkennavcolor'] = theme_pimenko_colorbrightness($color, -0.5);
    }

    // Footer darkencolor.
    $color = $defaults['footertextcolor'];
    if ($defaults['hooverfootercolor']) {
        $defaults['darkenfootercolor'] = $defaults['hooverfootercolor'];
    } else {
        $defaults['darkenfootercolor'] = theme_pimenko_colorbrightness($color, -0.5);
    }

    // Hoover button.
    $color = $defaults['brandcolorbutton'];
    $defaults['darkenbrandcolorbutton'] = theme_pimenko_colorbrightness($color, 0.5);
    $color = $defaults['brandcolortextbutton'];
    $defaults['darkenbrandcolortextbutton'] = theme_pimenko_colorbrightness($color, 0.5);

    // Set up svg toggler color.
    $defaults['tooglercolor'] = 'rgba(' . implode(",", theme_pimenko_hex2rgb($defaults['navbartextcolor'])) . ')';

    // Set up gradient for cover.
    $defaults['gradientcovercolor'] = theme_pimenko_hex2rgba($defaults['gradientcovercolor'], '.6');

    // Get all the defined settings for the theme and replace defaults.

    return strtr($css, $defaults);
}

/**
 * Returns the RGBA for the given hex and alpha.
 *
 * @param string $hex
 * @param string $alpha
 * @return string
 */
function theme_pimenko_hex2rgba(string $hex, string $alpha): string {
    if ($hex && $alpha) {
        $rgba = theme_pimenko_hex2rgb($hex);
        $rgba[] = $alpha;
        $hexrgba = 'rgba(' . implode(", ", $rgba) . ')'; // Returns the rgba values separated by commas.
    } else {
        $hexrgba = 'transparent';
    }

    return $hexrgba;
}

/**
 * Returns the RGB for the given hex.
 *
 * @param string $hex
 * @return array
 */
function theme_pimenko_hex2rgb($hex) {
    // From: http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/.
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array('r' => $r, 'g' => $g, 'b' => $b);
    return $rgb; // Returns the rgb as an array.
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_pimenko_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;

    if (empty($theme)) {
        $theme = theme_config::load('pimenko');
    }

    if ($context->contextlevel == CONTEXT_SYSTEM || $filearea == 'coverimage') {
        // By default, theme files must be cache-able by both browsers and proxies.  From 'More' theme.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        switch ($filearea) {
            case 'coverimage':
                $revision = array_shift($args);
                if ($revision < 0) {
                    $lifetime = 0;
                } else {
                    $lifetime = DAYSECS * 60;
                }

                $filename = end($args);
                $contextid = $context->id;
                $fullpath = "/$contextid/theme_pimenko/$filearea/0/$filename";
                $fs = get_file_storage();
                $file = $fs->get_file_by_hash(sha1($fullpath));
                if ($file) {
                    send_stored_file(
                        $file, $lifetime, 0, $forcedownload, $options
                    );
                    return true;
                }
                break;
            case 'loginbgimage':
            case 'favicon':
            case 'h5pcss':
            case 'sitelogo':
            case 'navbarpicture':
            case 'pimenkoimages':
            case 'backgroundimage':
            case strstr($filearea, 'slideimage'):
                return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
            default:
                send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/** Function to darker css */
function theme_pimenko_colorbrightness($hex, $percent) {
    // Work out if hash given.
    $hash = '';
    if (stristr($hex, '#')) {
        $hex = str_replace('#', '', $hex);
        $hash = '#';
    }

    // This function can't handle case if extrem white or extrem black
    // We do this to prevent strange color.
    if (strtoupper($hex) === "FFF" || strtoupper($hex) === "FFFFFF") {
        $hex = "FFFFFE";
    } else if ($hex === "000" || $hex === "000000") {
        $hex = "010101";
    }

    // HEX TO RGB.
    $rgb = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    // CALCULATE.
    for ($i = 0; $i < 3; $i++) {
        // See if brighter or darker.
        if ($percent > 0) {
            // Lighter.
            $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1 - $percent));
        } else {
            // Darker.
            $positivepercent = $percent - ($percent * 2);
            $rgb[$i] = round($rgb[$i] * (1 - $positivepercent));
        }
        // In case rounding up causes us to go to 256.
        if ($rgb[$i] > 255) {
            $rgb[$i] = 255;
        }
    }
    // RBG to Hex.
    $hex = '';
    for ($i = 0; $i < 3; $i++) {
        // Convert the decimal digit to hex.
        $hexdigit = dechex($rgb[$i]);
        // Add a leading zero if necessary.
        if (strlen($hexdigit) == 1) {
            $hexdigit = "0" . $hexdigit;
        }
        // Append to the hex string.
        $hex .= $hexdigit;
    }
    return sprintf("%s%s", $hash, $hex);
}

/**
 * Get icon mapping for font-awesome.
 */
function theme_pimenko_get_fontawesome_icon_map() {
    return [
        'theme_pimenko:t/check' => 'fa-check',
    ];
}

/**
 * @return array
 */
function theme_pimenko_regions() {
    $regions = [
        'side-pre',
        'side-post'
    ];
    foreach (range(
        'a', 'u'
    ) as $reg) {
        $regions[] = 'theme-front-' . $reg;
    }
    return $regions;
}
