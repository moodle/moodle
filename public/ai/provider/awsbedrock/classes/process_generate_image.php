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

namespace aiprovider_awsbedrock;

use Aws\Result;
use core_ai\ai_image;

/**
 * Class process image generation.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_image extends abstract_processor {
    /**
     * Create the request object for the Amazon models.
     *
     * @param \stdClass $requestobj The base request object to extend.
     * @param array $modelsettings The model settings to append to the request object.
     * @return \stdClass $requestobj The extended request object.
     */
    private function create_amazon_request(\stdClass $requestobj, array $modelsettings): \stdClass {
        $requestobj->taskType = 'TEXT_IMAGE';
        $aspectratio = $this->action->get_configuration('aspectratio');
        $quality = $this->action->get_configuration('quality') == 'hd' ? 'premium' : 'standard';
        $model = $this->get_model();

        // Calculate the dimensions based on the aspect ratio and quality.
        if (str_contains($model, 'amazon.nova')) {
            $dimensions = [
                'premium' => [
                    'square' => ['width' => 1792, 'height' => 1792],
                    'portrait' => ['width' => 1024, 'height' => 1792],
                    'landscape' => ['width' => 1792, 'height' => 1024],
                ],
                'standard' => [
                    'square' => ['width' => 1024, 'height' => 1024],
                    'portrait' => ['width' => 592, 'height' => 1024],
                    'landscape' => ['width' => 1024, 'height' => 592],
                ],
            ];
        } else {
            // Titan models have different dimensions.
            $dimensions = [
                'premium' => [
                    'square'    => ['width' => 1024, 'height' => 1024],
                    'portrait'  => ['width' => 768, 'height' => 1408],
                    'landscape' => ['width' => 1173, 'height' => 640],
                ],
                'standard' => [
                    'square'    => ['width' => 768, 'height' => 768],
                    'portrait'  => ['width' => 384, 'height' => 704],
                    'landscape' => ['width' => 1173, 'height' => 640],
                ],
            ];
        }

        if (isset($dimensions[$quality][$aspectratio])) {
            $width  = $dimensions[$quality][$aspectratio]['width'];
            $height = $dimensions[$quality][$aspectratio]['height'];
        } else {
            throw new \coding_exception('Unknown aspect ratio or quality.');
        }

        // Create the prompt object.
        $promptobj = new \stdClass();
        $promptobj->text = $this->action->get_configuration('prompttext');
        $requestobj->textToImageParams = $promptobj;

        // Create the image generation config object.
        $imggenconfig = new \stdClass();
        $imggenconfig->numberOfImages = $this->action->get_configuration('numimages');
        $imggenconfig->width = $width;
        $imggenconfig->height = $height;
        $imggenconfig->quality = $quality;

        // Add the model settings to the request object.
        foreach ($modelsettings as $setting => $value) {
            $imggenconfig->$setting = is_numeric($value) ? ($value + 0) : $value;
        }

        $requestobj->imageGenerationConfig = $imggenconfig;

        return $requestobj;
    }

    /**
     * Create the request object for Stability AI models.
     *
     * @param \stdClass $requestobj The base request object to extend.
     * @param array $modelsettings The model settings to append to the request object.
     * @return \stdClass $requestobj The extended request object.
     */
    private function create_stability_request(\stdClass $requestobj, array $modelsettings): \stdClass {
        $requestobj->prompt = $this->action->get_configuration('prompttext');
        $requestobj->aspect_ratio = $this->get_stability_aspect_ratio(
            $this->action->get_configuration('aspectratio'),
            $this->action->get_configuration('quality'),
        );
        // Add the model settings to the request object.
        foreach ($modelsettings as $setting => $value) {
            $requestobj->$setting = is_numeric($value) ? ($value + 0) : $value;
        }

        return $requestobj;
    }

    #[\Override]
    protected function create_request(): array {
        $requestobj = new \stdClass();
        $modelsettings = $this->get_model_settings();
        $model = $this->get_model();

        if (str_contains($model, 'amazon')) {
            $requestobj = $this->create_amazon_request($requestobj, $modelsettings);
        } else if (str_contains($model, 'stability')) {
            $requestobj = $this->create_stability_request($requestobj, $modelsettings);
        } else {
            throw new \coding_exception('Unknown model class type.');
        }

        return [
            'ContentType' => 'application/json',
            'Accept' => 'application/json',
            'modelId' => $model,
            'body' => json_encode($requestobj),
        ];
    }

    #[\Override]
    protected function handle_api_success(Result $result): array {
        $bodyobj = json_decode($result['body']->getContents());
        $responseheaders = $result['@metadata']['headers'] ?? [];
        $statuscode = (int)($result['@metadata']['statusCode'] ?? 500);
        $model = $this->get_model();

        // Base response with common fields.
        $response = [
            'success' => true,
            'fingerprint' => (string)($responseheaders['x-amzn-requestid'] ?? ''),
            'prompttokens' => (string)($responseheaders['x-amzn-bedrock-input-token-count'] ?? '0'),
            'completiontokens' => (string)($responseheaders['x-amzn-bedrock-output-token-count'] ?? '0'),
            'revisedprompt' => $this->action->get_configuration('prompttext'), // No revised prompt in AWS Bedrock.
            'model' => $model,
        ];

        // Handle image generation models.
        if (!str_contains($model, 'amazon') && !str_contains($model, 'stability')) {
            throw new \coding_exception('Unknown model class type.');
        }

        $imagebase64 = $bodyobj->images[0] ?? null;
        if (!is_string($imagebase64) || $imagebase64 === '') {
            return [
                'success' => false,
                'errorcode' => $statuscode,
                'error' => 'InvalidResponseException',
                'errormessage' => 'AWS Bedrock response did not include a valid image payload.',
            ];
        }

        try {
            $response['draftfile'] = $this->base64_to_file($imagebase64);
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'errorcode' => $statuscode,
                'error' => 'InvalidResponseException',
                'errormessage' => 'AWS Bedrock image payload could not be processed.',
            ];
        }

        return $response;
    }

    /**
     * Convert the base64 for the image to a file.
     *
     * Placements can't interact with the provider AI directly,
     * therefore we need to provide the image file in a format that can
     * be used by placements. So we use the file API.
     *
     * @param string $base64 The base64 encoded image.
     * @return \stored_file The file object.
     */
    protected function base64_to_file(string $base64): \stored_file {
        global $CFG, $USER;
        require_once("{$CFG->libdir}/filelib.php");

        $base64 = ltrim($base64, "\xEF\xBB\xBF");
        $base64 = preg_replace('/\s+/', '', $base64) ?? '';

        // Decode the base64 image into a binary format we can use.
        $binarydata = base64_decode($base64, true);
        if ($binarydata === false) {
            throw new \coding_exception('Invalid image data returned by AWS Bedrock.');
        }

        // Construct a filename for the image, because we don't get one explicitly.
        $imageinfo = getimagesizefromstring($binarydata);
        if ($imageinfo === false || empty($imageinfo[2])) {
            throw new \coding_exception('Invalid image binary returned by AWS Bedrock.');
        }
        $fileext = image_type_to_extension($imageinfo[2]);
        if ($fileext === false) {
            throw new \coding_exception('Unsupported image format returned by AWS Bedrock.');
        }
        $filename = substr(hash('sha512', ($base64)), 0, 16) . $fileext;

        // Save the image to a temp location and add the watermark.
        $tempdst = make_request_directory() . DIRECTORY_SEPARATOR . $filename;
        if (file_put_contents($tempdst, $binarydata) === false) {
            throw new \coding_exception('Unable to write temporary image file.');
        }
        $image = new ai_image($tempdst);
        $image->add_watermark()->save();

        // We put the file in the user draft area initially.
        // Placements (on behalf of the user) can then move it to the correct location.
        $fileinfo = new \stdClass();
        $fileinfo->contextid = \context_user::instance($USER->id)->id;
        $fileinfo->filearea = 'draft';
        $fileinfo->component = 'user';
        $fileinfo->itemid = file_get_unused_draft_itemid();
        $fileinfo->filepath = '/';
        $fileinfo->filename = $filename;

        $fs = get_file_storage();
        $filecontent = file_get_contents($tempdst);
        if ($filecontent === false) {
            throw new \coding_exception('Unable to read temporary image file.');
        }
        return $fs->create_file_from_string($fileinfo, $filecontent);
    }

    /**
     * Convert Moodle aspect ratio and quality to Stability AI compatible aspect ratio
     *
     * @param string $aspectratio The aspect ratio from Moodle form ('square', 'landscape', 'portrait')
     * @param string $quality The quality level ('standard', 'high')
     * @return string The aspect ratio string accepted by Stable Image Core
     */
    private function get_stability_aspect_ratio(string $aspectratio, string $quality) {
        $mapping = [
            'square' => [
                'standard' => '1:1',
                'hd' => '1:1',
            ],
            'landscape' => [
                'standard' => '16:9', // Widescreen - good for standard.
                'hd' => '3:2', // Classic photo ratio - better for high quality.
            ],
            'portrait' => [
                'standard' => '9:16', // Standard portrait orientation.
                'hd' => '4:5', // Better composition for high quality portraits.
            ],
        ];

        if (!isset($mapping[$aspectratio][$quality])) {
            return '1:1';
        }

        return $mapping[$aspectratio][$quality];
    }
}
