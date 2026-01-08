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

namespace aiprovider_gemini;

use core_ai\ai_image;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class process image generation.
 *
 * @package    aiprovider_gemini
 * @copyright  2025 University of Ferrara, Italy
 * @author     Andrea Bertelli <andrea.bertelli@unife.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_image extends abstract_processor {
    /** @var int The number of images to generate. */
    private int $numberimages = 1;

    #[\Override]
    /**
     * Create the request object for the Google Gemini API.
     *
     * API reference:
     * (https://cloud.google.com/vertex-ai/generative-ai/docs/model-reference/imagen-api)
     *
     * @param string $userid User identifier.
     * @return RequestInterface The request object to send to the Gemini API.
     */
    protected function create_request_object(string $userid): RequestInterface {
        // Create the request object.
        $requestobj = new \stdClass();

        $requestobj->instances = [
            (object) [
                'prompt' => $this->action->get_configuration('prompttext'),
            ],
        ];

        $requestobj->parameters = (object) [
            'sampleCount' => $this->numberimages,
            'aspectRatio' => $this->calculate_aspect_ratio($this->action->get_configuration('aspectratio')),
            'imageSize' => $this->calculate_image_quality($this->action->get_configuration('quality')),
            'languageCode' => 'en', // Force English for best results.
        ];

        return new Request(
            method: 'POST',
            uri: '',
            body: json_encode($requestobj),
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
    }

    #[\Override]
    protected function handle_api_success(ResponseInterface $response): array {
        $responsebody = $response->getBody();
        $bodyobj = json_decode($responsebody);

        $predictions = $bodyobj->predictions;

        // I have only one image.
        $imagebase64 = $predictions[0]->bytesBase64Encoded;
        $model = $this->get_model();

        return [
            'success' => true,
            'model' => $model,
            'draftfile' => $this->base64_to_file(
                $this->action->get_configuration('userid'),
                $imagebase64,
            ),
        ];
    }

    /**
     * Convert a base64 image to a stored_file object.
     * Google Gemini returns images as base64 encoded strings.
     *
     * @param int $userid The user id.
     * @param string $base64image The base64 of the image.
     * @return \stored_file The file object.
     */
    private function base64_to_file(int $userid, string $base64image): \stored_file {
        global $CFG;

        require_once("{$CFG->libdir}/filelib.php");

        // Decode the base64 image into a binary format we can use.
        $binarydata = base64_decode($base64image);

        // Construct a filename for the image, because we don't get one explicitly.
        $imageinfo = getimagesizefromstring($binarydata);
        $fileext = image_type_to_extension($imageinfo[2]);
        $filename = substr(hash('sha512', ($base64image)), 0, 16) . $fileext;

        // Save the image to a temp location and add the watermark.
        $tempdst = make_request_directory() . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($tempdst, $binarydata);
        $image = new ai_image($tempdst);
        $image->add_watermark()->save();

        // We put the file in the user draft area initially.
        // Placements (on behalf of the user) can then move it to the correct location.
        $fileinfo = new \stdClass();
        $fileinfo->contextid = \context_user::instance($userid)->id;
        $fileinfo->filearea = 'draft';
        $fileinfo->component = 'user';
        $fileinfo->itemid = file_get_unused_draft_itemid();
        $fileinfo->filepath = '/';
        $fileinfo->filename = $filename;

        $fs = get_file_storage();
        return $fs->create_file_from_string($fileinfo, file_get_contents($tempdst));
    }

    /**
     * Convert the given quality to an image size that is compatible with the Gemini Imagen API.
     *
     * @param string $quality The quality of the image (supported values are "standard" and "hd").
     * @return string The size of the image.
     */
    private function calculate_image_quality(string $quality): string {
        switch ($quality) {
            case 'standard':
                return '1k';
            case 'hd':
                return '2k';
            default:
                throw new \coding_exception('Invalid image quality: ' . $quality);
        }
    }

    /**
     * Convert the given orientation to a ratio.
     *
     * @param string $orientation The orientation of the image.
     * @return string The aspect ratio of the image.
     */
    private function calculate_aspect_ratio(string $orientation): string {
        if ($orientation === 'square') {
            $ratio = '1:1';
        } else if ($orientation === 'landscape') {
            $ratio = '16:9';
        } else if ($orientation === 'portrait') {
            $ratio = '9:16';
        } else {
            throw new \coding_exception('Invalid orientation: ' . $orientation);
        }
        return $ratio;
    }
}
