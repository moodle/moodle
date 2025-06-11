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


namespace theme_snap;

/**
 * Functions to calculate color contrast.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class color_contrast {

    /**
     * Calculates the luminosity of an given RGB color.
     * the color code must be in the format of RRGGBB.
     * the luminosity equations are from the WCAG 2 requirements.
     * http://www.w3.org/TR/WCAG20/#relativeluminancedef
     *
     * @param string $color
     * @return float
     */
    public static function calculate_luminosity($color) {
        $r = hexdec(substr($color, 1, 2)) / 255; // Red value.
        $g = hexdec(substr($color, 3, 2)) / 255; // Green value.
        $b = hexdec(substr($color, 5, 2)) / 255; // Blue value.
        if ($r <= 0.03928) {
            $r = $r / 12.92;
        } else {
            $r = pow((($r + 0.055) / 1.055), 2.4);
        }
        if ($g <= 0.03928) {
            $g = $g / 12.92;
        } else {
            $g = pow((($g + 0.055) / 1.055), 2.4);
        }
        if ($b <= 0.03928) {
            $b = $b / 12.92;
        } else {
            $b = pow((($b + 0.055) / 1.055), 2.4);
        }
        $luminosity = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        return $luminosity;
    }

    /**
     * calculates the luminosity ratio of two colors.
     * the luminosity ratio equations are from the WCAG 2 requirements.
     * http://www.w3.org/TR/WCAG20/#contrast-ratiodef
     *
     * @param string $color1
     * @param string $color2
     * @return float
     */
    public static function calculate_luminosity_ratio($color1, $color2) {
        $l1 = self::calculate_luminosity($color1);
        $l2 = self::calculate_luminosity($color2);
        if ($l1 > $l2) {
            $ratio = (($l1 + 0.05) / ($l2 + 0.05));
        } else {
            $ratio = (($l2 + 0.05) / ($l1 + 0.05));
        }
        return $ratio;
    }

    /**
     * Returns the calculated contrast ratio.
     * the ratio levels are from the WCAG 2 requirements.
     * http://www.w3.org/TR/WCAG20/#visual-audio-contrast (1.4.3)
     * http://www.w3.org/TR/WCAG20/#larger-scaledef
     *
     * @param string $color1
     * @param string $color2
     * @return float
     */
    public static function evaluate_color_contrast($color1, $color2) {
        $ratio = self::calculate_luminosity_ratio($color1, $color2);
        return $ratio;
    }

    /**
     * Compares category colors, if they exist, to different setup colors.
     * @return array Category ids for each comparison against setup colors.
     */
    public static function compare_cat_colors() {
        $failed = ['white' => [], 'custombar' => [], 'customnav' => []];

        $catcolors = get_config('theme_snap', 'category_color');
        $catcolorelements = json_decode($catcolors);
        if ($catcolorelements === false || $catcolorelements == null) {
            return;
        }
        $iscustomnavbaron = get_config('theme_snap', 'customisenavbar');
        $iscustomnavbuttonon = get_config('theme_snap', 'customisenavbutton');
        $navbarcolorbk = get_config('theme_snap', 'navbarbg');
        $navbarbutcolorbk = get_config('theme_snap', 'navbarbuttoncolor');
        foreach ($catcolorelements as $key => $catcolorelement) {
            if ($iscustomnavbaron) {
                $contrast = self::evaluate_color_contrast($catcolorelement, $navbarcolorbk);
                if ($contrast < 4.5) {
                    array_push($failed['custombar'], $key);
                }
            }
            if ($iscustomnavbuttonon) {
                $contrast = self::evaluate_color_contrast($catcolorelement, $navbarbutcolorbk);
                if ($contrast < 4.5) {
                    array_push($failed['customnav'], $key);
                }
            }
            $contrast = self::evaluate_color_contrast($catcolorelement, "#FFFFFF");
            if ($contrast < 4.5) {
                array_push($failed['white'], $key);
            }
        }
        return $failed;
    }

    /**
     * Compares colors depending on the setup.
     * @param $identifier
     * @return array|float
     */
    public static function compare_colors($identifier) {
        if ($identifier == admin_setting_configcolorwithcontrast::BASICS) {
            $ratio = self::compare_basics_color();
        } else if ($identifier == admin_setting_configcolorwithcontrast::NAVIGATION_BAR) {
            $ratio = self::compare_navbar_color();
        } else if ($identifier == admin_setting_configcolorwithcontrast::NAVIGATION_BAR_BUTTON) {
            $ratio = self::compare_navbarbutton_color();
        } else if ($identifier == admin_setting_configcolorwithcontrast::FEATURESPOT_BACK) {
            $ratio = [self::compare_featurespot_title_color(), self::compare_featurespot_content_color()];
        } else if ($identifier == admin_setting_configcolorwithcontrast::FOOTER) {
            $ratio = self::compare_footer_txt_color();
        }
        return $ratio;
    }

    /**
     * Compares the theme color with white color.
     * @return float
     */
    public static function compare_basics_color() {
        $basiccolor = get_config('theme_snap', 'themecolor');
        $contrast = self::evaluate_color_contrast($basiccolor, "#FFFFFF");
        return $contrast;
    }

    /**
     * Compares the colors of the nav bar background and link.
     * @return float
     */
    public static function compare_navbar_color() {
        $navbarcolor1 = get_config('theme_snap', 'navbarbg');
        $navbarcolor2 = get_config('theme_snap', 'navbarlink');
        $contrast = self::evaluate_color_contrast($navbarcolor1, $navbarcolor2);
        return $contrast;
    }

    /**
     * Compare nav bar button colors to nav bar button link
     * @return array
     */
    public static function compare_navbarbutton_color() {
        $navbarbutcolor1 = get_config('theme_snap', 'navbarbuttoncolor');
        $navbarbutcolor2 = get_config('theme_snap', 'navbarbuttonlink');
        $contrast = self::evaluate_color_contrast($navbarbutcolor1, $navbarbutcolor2);
        return $contrast;
    }

    /**
     * Compare the footer text color with the footer background color.
     * @return float
     */
    public static function compare_footer_txt_color() {
        $footertxtcolor = get_config('theme_snap', 'footertxt');
        $footerbgcolor = get_config('theme_snap', 'footerbg');
        $contrast = self::evaluate_color_contrast($footertxtcolor, $footerbgcolor);
        return $contrast;
    }

    /**
     * Gets the pixel average colour in the third top left part of the image.
     * @param stored_file $originalfile Image file to be processed.
     * @param array $fileinfo Image info.
     * @return hex color.
     */
    public static function calculate_image_main_color(\stored_file $originalfile, array $fileinfo) {
        // Copy file to temp directory to avoid messing up the original file.
        $tmpimage = tempnam(sys_get_temp_dir(), 'tmpimg');
        \file_put_contents($tmpimage, $originalfile->get_content());

        // Create resource depending on mime type.
        $mimetype = $fileinfo['mimetype'];
        if ($mimetype == 'image/jpeg') {
            $resource = imagecreatefromjpeg($tmpimage);
        } else if ($mimetype == 'image/png') {
            $resource = imagecreatefrompng($tmpimage);
        } else if ($mimetype == 'image/gif') {
            $resource = imagecreatefromgif($tmpimage);
        }

        // Calculate the average pixel colour for the third left part of the image.
        $widththird = $fileinfo['width'] / 3;
        $height = $fileinfo['height'] / 2;
        $totalvalue = 0;
        $pixelcount = 0;

        for ($i = 0; $i < $widththird; $i++) {
            for ($j = 0; $j < $height; $j++) {
                $pixelint = imagecolorat($resource, $i, $j);
                $totalvalue += $pixelint;
                $pixelcount++;
            }
        }

        $average = intval($totalvalue / $pixelcount);

        $cols = imagecolorsforindex($resource, $average);
        $redhex = substr("00" . dechex($cols['red']), -2);
        $greenhex = substr("00" . dechex($cols['green']), -2);
        $bluehex = substr("00" . dechex($cols['blue']), -2);
        $pixelhex = "#" . $redhex . $greenhex . $bluehex;

        return $pixelhex;
    }

    /**
     * Compare feature spots background color and title text color
     * @return array
     */
    public static function compare_featurespot_title_color() {
        $text = get_config('theme_snap', 'feature_spot_title_color');
        $background = get_config('theme_snap', 'feature_spot_background_color');
        $contrast = self::evaluate_color_contrast($background, $text);
        return $contrast;
    }

    /**
     * Compare feature spots background color and description text color
     * @return array
     */
    public static function compare_featurespot_content_color() {
        $text = get_config('theme_snap', 'feature_spot_description_color');
        $background = get_config('theme_snap', 'feature_spot_background_color');
        $contrast = self::evaluate_color_contrast($background, $text);
        return $contrast;
    }
}
