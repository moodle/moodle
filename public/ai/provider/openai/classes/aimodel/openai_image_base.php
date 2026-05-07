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

namespace aiprovider_openai\aimodel;

/**
 * OpenAI base AI image generation model interface.
 *
 * @package    aiprovider_openai
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface openai_image_base {
    /**
     * Get response format.
     *
     * Return null if the model does not support the response_format API parameter.
     *
     * @return string|null response format, or null to omit the parameter from the request.
     */
    public function response_format(): ?string;

    /**
     * Get output image format (file extension) to send as the output_format request parameter.
     *
     * Return null if the model does not support the output_format API parameter.
     *
     * @return string|null Output format (e.g. 'png'), or null to omit the parameter from the request.
     */
    public function get_output_format(): ?string;

    /**
     * Convert the given aspect ratio to an image size compatible with the model's API.
     *
     * @param string $ratio The aspect ratio (square, landscape, portrait).
     * @return string The size string for the API request.
     */
    public function calculate_size(string $ratio): string;

    /**
     * Convert the given quality setting to the value expected by the model's API.
     *
     * @param string $quality The quality setting (standard, hd).
     * @return string The quality value for the API request.
     */
    public function calculate_quality(string $quality): string;
}
