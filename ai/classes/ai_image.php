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
    /** @var false|\GdImage|resource Image object. */
    private $imgobject;
    /** @var int Image's width. */
    private int $width;
    /** @var int Image's height. */
    private int $height;

    public function __construct(
        /** @var string Image path. */
        private string $imagepath,
    ) {
        ini_set('gd.jpeg_ignore_warning', 1);
        if (!function_exists('imagecreatefrompng') && !function_exists('imagecreatefromjpeg')) {
            throw new moodle_exception('gdnotexist');
        }

        if (!file_exists($imagepath) || !is_readable($imagepath)) {
            throw new moodle_exception('invalidfile');
        }

        $this->imageinfo = getimagesize($imagepath);
        if (empty($this->imageinfo)) {
            throw new moodle_exception('invalidfile');
        }

        switch ($this->imageinfo['mime']) {
            case 'image/jpeg':
                $this->imgobject = imagecreatefromjpeg($imagepath);
                break;
            case 'image/png':
                $this->imgobject = imagecreatefrompng($imagepath);
                break;
            case 'image/gif':
                $this->imgobject = imagecreatefromgif($imagepath);
                break;
            default:
                break;
        }
        $this->width = imagesx($this->imgobject);
        $this->height = imagesy($this->imgobject);
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
            $watermark = get_string('imagewatermark', 'core_ai');
        }
        if (empty($options)) {
            $options = [
                'font' => $CFG->libdir . '/default.ttf',
                'fontsize' => '20',
                'angle' => 0,
                'ttf' => true,
            ];
        }
        $text = iconv(
            from_encoding: 'ISO-8859-8',
            to_encoding: 'UTF-8',
            string: $watermark
        );
        $clr = imagecolorallocate(
            image: $this->imgobject,
            red: 255,
            green: 255,
            blue: 255,
        );
        if (!empty($options['ttf'])) {
            imagettftext(
                image: $this->imgobject,
                size: $options['fontsize'],
                angle: $options['angle'],
                x: $pos[0],
                y: $this->height - ($pos[1] + $options['fontsize']),
                color: $clr,
                font_filename: $options['font'],
                text: $text,
            );
        } else {
            imagestring(
                image: $this->imgobject,
                font: $options['fontsize'],
                x: $pos[0],
                y: $pos[1],
                string: $text,
                color: $clr,
            );
        }

        return $this;
    }

    /**
     * Destroy image object.
     * @return bool
     */
    private function destroy(): bool {
        return imagedestroy($this->imgobject);
    }

    /**
     * Save image.
     * @param string $newpath New path to save image.
     * @return bool
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
                break;
        }
        return $this->destroy();
    }
}
