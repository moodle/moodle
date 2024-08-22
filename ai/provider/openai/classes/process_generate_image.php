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

use core_ai\ai_image;
use core_ai\aiactions\responses\response_base;
use core_ai\aiactions\responses\response_generate_image;
use curl;
use Psr\Http\Message\ResponseInterface;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Class process image generation.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_image extends process_generate_text {
    /** @var string The API endpoint to make requests against. */
    private string $aiendpoint = 'https://api.openai.com/v1/images/generations';

    /** @var string The API model to use. */
    private string $model = 'dall-e-3';

    /** @var int The number of images to generate dall-e-3 only supports 1. */
    private int $numberimages = 1;

    /** @var string Response format: url or b64_json. */
    private string $responseformat = 'url';

    /**
     * Process the AI request.
     *
     * @return response_base The result of the action.
     */
    public function process(): response_base {
        // Check the rate limiter.
        $ratelimitcheck = $this->provider->is_request_allowed($this->action);
        if ($ratelimitcheck !== true) {
            return new response_generate_image(
                success: false,
                actionname: 'generate_image',
                errorcode: $ratelimitcheck['errorcode'],
                errormessage: $ratelimitcheck['errormessage'],
            );
        }

        $userid = $this->provider->generate_userid($this->action->get_configuration('userid'));
        $client = $this->provider->create_http_client($this->aiendpoint);

        // Create the request object.
        $requestobj = $this->create_request_object($this->action, $userid);

        // Make the request to the OpenAI API.
        $response = $this->query_ai_api($client, $requestobj);

        // If the request was successful, save the URL to a file.
        if ($response['success']) {
            $fileobj = $this->url_to_file(
                $this->action->get_configuration('userid'),
                $response['sourceurl']
            );
            // Add the file to the response, so the calling placement can do whatever they want with it.
            $response['draftfile'] = $fileobj;
        }

        // Format the action response object.
        return $this->prepare_response($response);
    }

    /**
     * Convert the given aspect ratio to an image size
     * that is compatible with the OpenAI API.
     *
     * @param string $ratio The aspect ratio of the image.
     * @return string The size of the image.
     */
    private function calculate_size(string $ratio): string {
        if ($ratio === 'square') {
            $size = '1024x1024';
        } else if ($ratio === 'landscape') {
            $size = '1792x1024';
        } else if ($ratio === 'portrait') {
            $size = '1024x1792';
        } else {
            throw new \coding_exception('Invalid aspect ratio: ' . $ratio);
        }
        return $size;
    }

    /**
     * Create the request object to send to the OpenAI API.
     *
     * This object contains all the required parameters for the request.
     *
     * @param \core_ai\aiactions\base $action The action to process.
     * @param string $userid The user id.
     * @return \stdClass The request object to send to the OpenAI API.
     */
    private function create_request_object(\core_ai\aiactions\base $action, string $userid): \stdClass {
        $requestobj = new \stdClass();
        $requestobj->prompt = $action->get_configuration('prompttext');
        $requestobj->model = $this->model;
        $requestobj->n = $this->numberimages;
        $requestobj->quality = $action->get_configuration('quality');
        $requestobj->response_format = $this->responseformat;
        $requestobj->size = $this->calculate_size($action->get_configuration('aspectratio'));
        $requestobj->style = $action->get_configuration('style');
        $requestobj->user = $userid;

        return $requestobj;
    }

    /**
     * Handle a successful response from the external AI api.
     *
     * @param ResponseInterface $response The response object.
     * @return array The response.
     */
    protected function handle_api_success(ResponseInterface $response): array {
        $responsebody = $response->getBody();
        $bodyobj = json_decode($responsebody->getContents());

        return [
            'success' => true,
            'sourceurl' => $bodyobj->data[0]->url,
            'revisedprompt' => $bodyobj->data[0]->revised_prompt,
        ];
    }

    /**
     * Prepare the response object.
     *
     * @param array $response The response object.
     * @return response_generate_image The action response object.
     */
    private function prepare_response(array $response): response_generate_image {
        if ($response['success']) {
            $generatedimage = new response_generate_image(
                success: true,
                actionname: 'generate_image',
            );
            $generatedimage->set_response_data($response);
            return $generatedimage;
        } else {
            return new response_generate_image(
                success: false,
                actionname: 'generate_image',
                errorcode: $response['errorcode'],
                errormessage: $response['errormessage'],
            );
        }
    }

    /**
     * Convert the url for the image  to a file.
     *
     * Placements can't interact with the provider AI directly,
     * therefore we need to provide the image file in a format that can
     * be used by placements. So we use the file API.
     *
     * @param int $userid The user id.
     * @param string $url The URL to the image.
     * @return \stored_file The file object.
     */
    private function url_to_file(int $userid, string $url): \stored_file {
        global $CFG;
        $parsedurl = parse_url($url, PHP_URL_PATH); // Parse the URL to get the path.
        $filename = basename($parsedurl); // Get the basename of the path.

        // Download the image and add the watermark.
        $downloadtmpdir = make_request_directory();
        $tempdst = $downloadtmpdir . $filename;
        $c = new curl;
        $result = $c->download_one(
            $url,
            null,
            ['filepath' => $tempdst, 'timeout' => $CFG->repositorygetfiletimeout],
        );
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
