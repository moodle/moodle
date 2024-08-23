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

namespace core_ai;

use core\exception\moodle_exception;

/**
 * AI Image.
 *
 * @package    core_ai
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_image {
    /** @var array Image information. */
    private array $imageinfo;

    /** @var false|\GdImage Image object. */
    private false|\GDImage $imgobject;

    /**
     * Constructor for the image processing class.
     *
     * Initializes the class with the provided image path, setting up the image object and its properties.
     * The constructor checks if the GD library functions for PNG and JPEG are available, ensures the image file
     * exists and is readable, and then creates an image resource object based on the file type (JPEG, PNG, or GIF).
     *
     * @param string $imagepath The path to the image file.
     */
    public function __construct(
        /** @var string Image path. */
        private string $imagepath,
    ) {
        ini_set('gd.jpeg_ignore_warning', 1);

        if (!file_exists($imagepath) || !is_readable($imagepath)) {
            throw new moodle_exception('invalidfile', debuginfo: $imagepath);
        }

        $imageinfo = getimagesize($imagepath);
        if (empty($imageinfo)) {
            throw new moodle_exception('invalidfile', debuginfo: $imagepath);
        }
        $this->imageinfo = $imageinfo;

        switch ($this->imageinfo['mime']) {
            case 'image/jpeg':
                if (!function_exists('imagecreatefromjpeg')) {
                    throw new moodle_exception('gdfeaturenotsupported', a: 'jpeg');
                }
                $this->imgobject = imagecreatefromjpeg($imagepath);
                break;
            case 'image/png':
                if (!function_exists('imagecreatefrompng')) {
                    throw new moodle_exception('gdfeaturenotsupported', a: 'png');
                }
                $this->imgobject = imagecreatefrompng($imagepath);
                break;
            case 'image/gif':
                if (!function_exists('imagecreatefromgif')) {
                    throw new moodle_exception('gdfeaturenotsupported', a: 'gif');
                }
                $this->imgobject = imagecreatefromgif($imagepath);
                break;
            default:
                throw new moodle_exception('gdmimetypenotsupported', debuginfo: $this->imageinfo['mime']);
        }
    }

    /**
     * Get the predominant color of a specific area of the image.
     *
     * This method analyzes a rectangular area of the image, calculating the
     * average color by summing up the red, green, and blue components of all pixels
     * within the area, and then dividing by the total number of pixels.
     *
     * @param int $x X coordinate of the top-left corner of the area.
     * @param int $y Y coordinate of the top-left corner of the area.
     * @param int $width Width of the area.
     * @param int $height Height of the area.
     * @return array RGB array of the predominant color, with keys 'red', 'green', and 'blue'.
     */
    private function get_predominant_color(int $x, int $y, int $width, int $height): array {
        // If the width or height is smaller than 10 pixels, sample the entire image.
        if (imagesx($this->imgobject) < 10 || imagesy($this->imgobject) < 10) {
            $x = 0;
            $y = 0;
            $width = imagesx($this->imgobject);
            $height = imagesy($this->imgobject);
        }

        // Initialize variables to accumulate the total red, green, and blue values.
        $redtotal = $greentotal = $bluetotal = 0;
        // Initialize a counter for the number of pixels processed.
        $pixelcount = 0;

        // Iterate over each pixel within the specified area of the image.
        for ($i = $x; $i < $x + $width; $i++) {
            for ($j = $y; $j < $y + $height; $j++) {
                // Retrieve the color index of the current pixel.
                $rgb = imagecolorat(
                    image: $this->imgobject,
                    x: $i,
                    y: $j);
                // Extract the red component (shift the bits 16 places to the right and mask the rest).
                $red = ($rgb >> 16) & 0xFF;
                // Extract the green component (shift the bits 8 places to the right and mask the rest).
                $green = ($rgb >> 8) & 0xFF;
                // Extract the blue component (mask directly to get the blue value).
                $blue = $rgb & 0xFF;

                // Accumulate the red, green, and blue values.
                $redtotal += $red;
                $greentotal += $green;
                $bluetotal += $blue;
                // Increment the pixel counter.
                $pixelcount++;
            }
        }

        // Calculate the average red, green, and blue values by dividing the total by the number of pixels.
        return [
            'red' => $redtotal / $pixelcount,
            'green' => $greentotal / $pixelcount,
            'blue' => $bluetotal / $pixelcount,
        ];
    }

    /**
     * Determine if the color is dark based on its RGB values.
     *
     * This method uses a formula to calculate the luminance of a color.
     * Luminance is a weighted sum of the red, green, and blue components, with green having the highest weight
     * because the human eye is more sensitive to green.
     * A luminance value below 128 is generally considered dark.
     *
     * @param array $color RGB array with keys 'red', 'green', and 'blue'.
     * @return bool True if the color is dark, false if it is light.
     */
    private function is_color_dark(array $color): bool {
        // Calculate the luminance using the standard formula.
        // Luminance = 0.299 * Red + 0.587 * Green + 0.114 * Blue.
        // The coefficients correspond to the human eye's sensitivity to these colors.
        $luminance = (0.299 * $color['red'] + 0.587 * $color['green'] + 0.114 * $color['blue']);

        // Return true if the luminance is below 128 (dark), otherwise return false (light).
        return $luminance < 128;
    }

    /**
     * Draw a pill-shaped rounded rectangle.
     * The pill is composed of two half circles and a single rectangle.
     *
     * @param int $x1 Top-left X coordinate of the rectangle.
     * @param int $y1 Top-left Y coordinate of the rectangle.
     * @param int $x2 Bottom-right X coordinate of the rectangle.
     * @param int $y2 Bottom-right Y coordinate of the rectangle.
     * @param int $radius Radius of the rounded corners (half the pill height).
     * @param int $color Color for the pill background.
     */
    private function draw_rounded_rectangle(
        int $x1,
        int $y1,
        int $x2,
        int $y2,
        int $radius,
        int $color
    ): void {
        // Draw two half circles at the ends of the pill.
        // Left half circle.
        imagefilledarc(
            image: $this->imgobject,
            center_x: $x1 + $radius, // Center X coordinate.
            center_y: ($y1 + $y2) / 2, // Center Y coordinate.
            width: $radius * 2, // Width of the circle (diameter).
            height: $radius * 2, // Height of the circle (diameter).
            start_angle: 90,
            end_angle: 270,
            color: $color,
            style: IMG_ARC_PIE
        );

        // Right half circle.
        imagefilledarc(
            image: $this->imgobject,
            center_x: $x2 - $radius,
            center_y: ($y1 + $y2) / 2,
            width: $radius * 2,
            height: $radius * 2,
            start_angle: 270,
            end_angle: 90,
            color: $color,
            style: IMG_ARC_PIE
        );

        // Draw the rectangle joining the two half circles.
        imagefilledrectangle(
            image: $this->imgobject,
            x1: $x1 + $radius, // Start after the left half circle.
            y1: $y1, // Top of the rectangle.
            x2: $x2 - $radius, // End before the right half circle.
            y2: $y2, // Bottom of the rectangle.
            color: $color
        );
    }

    /**
     * Add watermark to image.
     *
     * @param string $watermark Watermark text.
     * @param array $options Watermark options.
     * @param array $pos Watermark position.
     * @return $this
     */
    public function add_watermark(
        string $watermark = '',
        array $options = [],
        array $pos = [10, 10],
    ): static {
        global $CFG;
        if (empty($watermark)) {
            $watermark = get_string('contentwatermark', 'core_ai');
        }
        if (empty($options)) {
            $options = [
                'font' => $CFG->libdir . '/default.ttf',
                'fontsize' => '20',
                'angle' => 0,
                'ttf' => true,
            ];
        }

        $imagewidth = imagesx($this->imgobject);
        $imageheight = imagesy($this->imgobject);

        // Determine the size of the area to analyze: 10% of the image width and height.
        $areawidth = (int)($imagewidth * 0.1);
        $areaheight = (int)($imageheight * 0.1);

        // Dynamically calculate the bottom-left corner coordinates.
        $bottomleftcolor = $this->get_predominant_color(
            x: 0,
            y: $imageheight - $areaheight,
            width: $areawidth,
            height: $areaheight
        );

        // Set text color based on the background color.
        if ($this->is_color_dark($bottomleftcolor)) {
            $clr = imagecolorallocate( // White for dark background.
                image: $this->imgobject,
                red: 255,
                green: 255,
                blue: 255
            );
            $bgclr = imagecolorallocatealpha( // Black (80% transparent).
                image: $this->imgobject,
                red: 0,
                green: 0,
                blue: 0,
                alpha: (int)(127 * 0.2)
            );
        } else {
            $clr = imagecolorallocate( // Black for light background.
                image: $this->imgobject,
                red: 0,
                green: 0,
                blue: 0
            );
            $bgclr = imagecolorallocatealpha( // White (80% transparent).
                image: $this->imgobject,
                red: 255,
                green: 255,
                blue: 255,
                alpha: (int)(127 * 0.2)
            );
        }

        // Encode the text properly.
        $text = iconv(
            from_encoding: 'ISO-8859-8',
            to_encoding: 'UTF-8',
            string: $watermark
        );

        // Calculate text bounding box for determining pill siz), different for TTF and non-TTF fonts.
        if (!empty($options['ttf'])) {
            // For TTF fonts, use imagettfbbox to get the text's bounding box.
            $bbox = imagettfbbox($options['fontsize'], $options['angle'], $options['font'], $text);
            $textwidth = abs($bbox[4] - $bbox[0]);
            $textheight = abs($bbox[5] - $bbox[1]);
        } else {
            // For non-TTF fonts, use imagefontwidth and imagefontheight.
            $textwidth = strlen($text) * imagefontwidth($options['fontsize']);
            $textheight = imagefontheight($options['fontsize']);
        }

        // Pill background dimensions.
        $padding = 10;
        $pillwidth = $textwidth + $padding * 2;
        $pillheight = $textheight + $padding * 2;

        // Position for the pill background.
        $x = $pos[0];
        $y = $imageheight - ($pos[1] + $pillheight); // Adjust Y based on the pill height.

        // Draw the pill background.
        $this->draw_rounded_rectangle(
            x1: $x,
            y1: $y,
            x2: $x + $pillwidth,
            y2: $y + $pillheight,
            radius: $pillheight / 2,
            color: $bgclr
        );

        // Correct the position of the text to center it inside the pill.
        $textx = $x + (($pillwidth - $textwidth) / 2); // Center text horizontally in the pill.
        $texty = $y + ((($pillheight - $textheight) / 2) * .75) + $textheight; // Center vertically, adjusting for baseline.

        // Draw the text on top of the pill background.
        if (!empty($options['ttf'])) {
            imagettftext(
                image: $this->imgobject,
                size: $options['fontsize'],
                angle: $options['angle'],
                x: (int)$textx,
                y: (int)$texty,
                color: $clr,
                font_filename: $options['font'],
                text: $text,
            );
        } else {
            imagestring(
                image: $this->imgobject,
                font: $options['fontsize'],
                x: (int)$textx,
                y: (int)$texty,
                string: $text,
                color: $clr,
            );
        }

        return $this;
    }

    /**
     * Save image.
     *
     * @param string $newpath New path to save image.
     * @return bool Whether the save was successful
     */
    public function save(string $newpath = ''): bool {
        if (empty($newpath)) {
            $newpath = $this->imagepath;
        }
        switch($this->imageinfo['mime']) {
            case 'image/jpeg':
                return imagejpeg(image: $this->imgobject, file: $newpath);
            case 'image/png':
                return imagepng(image: $this->imgobject, file: $newpath);
            case 'image/gif':
                return imagegif(image: $this->imgobject, file: $newpath);
            default:
                return false;
        }
    }
}
