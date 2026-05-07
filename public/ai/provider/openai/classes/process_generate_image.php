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

namespace aiprovider_openai;

use aiprovider_openai\aimodel\openai_image_base;
use core_ai\ai_image;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class process image generation.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_image extends abstract_processor {
    /** @var int The number of images to generate dall-e-3 only supports 1. */
    private int $numberimages = 1;

    #[\Override]
    protected function query_ai_api(): array {
        $response = parent::query_ai_api();

        // If the request was successful, save the URL to a file.
        if ($response['success']) {
            $fileobj = $this->create_file_from_response(
                $this->action->get_configuration('userid'),
                $response
            );
            // Add the file to the response, so the calling placement can do whatever they want with it.
            $response['draftfile'] = $fileobj;
        }

        return $response;
    }

    /**
     * Convert the given aspect ratio to an image size compatible with the OpenAI API.
     *
     * Delegates to the model class if one is found for the configured model,
     * otherwise falls back to a default mapping: 'square' → '1024x1024',
     * 'landscape' → '1536x1024', 'portrait' → '1024x1536'.
     *
     * @param string $ratio The aspect ratio of the image ('square', 'landscape', or 'portrait').
     * @return string The size string to send in the API request (e.g. '1024x1024').
     */
    private function calculate_size(string $ratio): string {
        // Get model class.
        $modelclass = helper::get_model_class($this->get_model());
        if ($modelclass) {
            return $modelclass->calculate_size($ratio);
        }
        // Fallback.
        if ($ratio === 'square') {
            $size = '1024x1024';
        } else if ($ratio === 'landscape') {
            $size = '1536x1024';
        } else if ($ratio === 'portrait') {
            $size = '1024x1536';
        } else {
            throw new \coding_exception('Invalid aspect ratio: ' . $ratio);
        }
        return $size;
    }

    /**
     * Convert the given quality setting to an API-compatible quality value.
     *
     * Delegates to the model class if one is found for the configured model,
     * otherwise falls back to a default mapping: 'standard' → 'medium', 'hd' → 'high'.
     *
     * @param string $quality The quality setting from the action ('standard' or 'hd').
     * @return string The quality value to send in the API request.
     */
    private function calculate_quality(string $quality): string {
        // Get model class.
        $modelclass = helper::get_model_class($this->get_model());
        if ($modelclass) {
            return $modelclass->calculate_quality($quality);
        }
        // Fallback.
        if ($quality === 'standard') {
            $processedquality = 'medium';
        } else if ($quality === 'hd') {
            $processedquality = 'high';
        } else {
            throw new \coding_exception('Invalid quality: ' . $quality);
        }

        return $processedquality;
    }

    #[\Override]
    protected function create_request_object(string $userid): RequestInterface {
        // Create the request object.
        $requestobj = new \stdClass();
        $requestobj->model = $this->get_model();
        $requestobj->user = $userid;
        $requestobj->prompt = $this->action->get_configuration('prompttext');
        $requestobj->n = $this->numberimages;
        $requestobj->quality = $this->calculate_quality($this->action->get_configuration('quality'));
        $requestobj->size = $this->calculate_size($this->action->get_configuration('aspectratio'));
        // Get model class.
        $modelclass = helper::get_model_class($this->get_model());
        if ($modelclass instanceof openai_image_base) {
            $responseformat = $modelclass->response_format();
            if ($responseformat !== null) {
                $requestobj->response_format = $responseformat;
            }
            $outputformat = $modelclass->get_output_format();
            if ($outputformat !== null) {
                $requestobj->output_format = $outputformat;
            }
        }
        // Append the extra model settings.
        $modelsettings = $this->get_model_settings();
        foreach ($modelsettings as $setting => $value) {
            $requestobj->$setting = $value;
        }
        return new Request(
            method: 'POST',
            uri: '',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode($requestobj),
        );
    }

    #[\Override]
    protected function handle_api_success(ResponseInterface $response): array {
        $responsebody = $response->getBody();
        $bodyobj = json_decode($responsebody->getContents());

        return [
            'success' => true,
            'b64json' => $bodyobj->data[0]->b64_json,
            'output_format' => $bodyobj->output_format ?? 'png',
            'revisedprompt' => $bodyobj->data[0]->revised_prompt ?? '',
            'model' => $this->get_model(), // There is no model in the response, use config.
        ];
    }

    /**
     * Decode the base64-encoded image from the API response, add a watermark,
     * and store it as a draft file for the given user.
     *
     * Placements can't interact with the provider AI directly,
     * therefore we need to provide the image file in a format that can
     * be used by placements. So we use the file API.
     *
     * @param int $userid The user id.
     * @param array $response Response from the AI provider, containing 'b64json' and 'output_format'.
     * @return \stored_file The stored draft file.
     */
    private function create_file_from_response(
        int $userid,
        array $response,
    ): \stored_file {
        global $CFG;

        require_once("{$CFG->libdir}/filelib.php");

        // Decode the image and store in temp dir.
        $b64json = $response['b64json'];
        $imagebytes = base64_decode($b64json);
        $filename = substr(hash('sha512', $b64json), 0, 16) . '.' . $response['output_format'];
        $tempdst = make_request_directory() . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($tempdst, $imagebytes);

        // Add the watermark.
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
}
