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

namespace aitool_googlesynthesize;

use core\http_client;
use local_ai_manager\local\prompt_response;
use local_ai_manager\local\request_response;
use local_ai_manager\local\unit;
use local_ai_manager\local\usage;
use Locale;
use Psr\Http\Message\StreamInterface;

/**
 * Connector for Google Synthesize.
 *
 * @package    aitool_googlesynthesize
 * @copyright  ISB Bayern, 2024
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class connector extends \local_ai_manager\base_connector {

    #[\Override]
    public function get_models_by_purpose(): array {
        return [
                'tts' => ['googletts'],
        ];
    }

    #[\Override]
    public function get_unit(): unit {
        return unit::COUNT;
    }

    #[\Override]
    public function make_request(array $data): request_response {
        $client = new http_client([
                'timeout' => get_config('local_ai_manager', 'requesttimeout'),
        ]);

        $options['headers'] = [
                'x-goog-api-key' => $this->get_api_key(),
                'Content-Type' => 'application/json;charset=utf-8',
        ];
        $options['body'] = json_encode($data);

        $response = $client->post($this->get_endpoint_url(), $options);
        if ($response->getStatusCode() === 200) {
            $return = request_response::create_from_result($response->getBody());
        } else {
            $return = request_response::create_from_error($response->getStatusCode(),
                    get_string('error_sendingrequestfailed', 'local_ai_manager'),
                    $response->getBody()->getContents(),
                    $response->getBody()
            );
        }
        return $return;
    }

    #[\Override]
    public function execute_prompt_completion(StreamInterface $result, array $options = []): prompt_response {
        global $USER;

        $content = json_decode($result->getContents(), true);

        $fs = get_file_storage();
        $fileinfo = [
                'contextid' => \context_user::instance($USER->id)->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => $options['itemid'],
                'filepath' => '/',
                'filename' => $options['filename'],
        ];
        $file = $fs->create_file_from_string($fileinfo, base64_decode($content['audioContent']));

        $filepath = \moodle_url::make_draftfile_url(
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
        )->out();

        return prompt_response::create_from_result($this->instance->get_model(), new usage(1.0), $filepath);
    }

    #[\Override]
    public function get_prompt_data(string $prompttext, array $requestoptions): array {
        return [
                'input' => [
                        'text' => $prompttext,
                ],
                'voice' => [
                        'ssmlGender' => $requestoptions['gender'][0],
                        'languageCode' => $requestoptions['languages'][0],
                ],
                'audioConfig' => [
                        'audioEncoding' => 'MP3',
                ],
        ];
    }

    #[\Override]
    public function has_customvalue1(): bool {
        return true;
    }

    #[\Override]
    public function has_customvalue2(): bool {
        return true;
    }

    #[\Override]
    public function get_available_options(): array {
        $voices = $this->retrieve_available_voices();
        $languagekeys = [];
        foreach ($voices as $voice) {
            foreach ($voice['languageCodes'] as $languagecode) {
                if (!in_array($languagecode, $languagekeys)) {
                    $languagekeys[] = $languagecode;
                }
            }
        }
        // The call array_values(...) is needed to re-index the array for later merging.
        $languages = array_values(array_map(
                fn($languagecode) => [
                        'key' => $languagecode,
                        'displayname' => Locale::getDisplayLanguage($languagecode, current_language()) . ' (' . $languagecode . ')',
                ],
                $languagekeys
        ));
        usort($languages, fn($a, $b) => strcmp($a['displayname'], $b['displayname']));
        return [
                'gender' => [
                        ['key' => 'MALE', 'displayname' => get_string('male', 'local_ai_manager')],
                        ['key' => 'FEMALE', 'displayname' => get_string('female', 'local_ai_manager')],
                ],
                'languages' => $languages,
        ];
    }

    /**
     * Function to retrieve all available voices.
     *
     * Fetches all available voices from the Google API.
     * The result will be cached and refreshed if the last result is older than 24 hours.
     *
     * @return array list of available voices
     */
    public function retrieve_available_voices(): array {
        $clock = \core\di::get(\core\clock::class);
        $cache = \cache::make('aitool_googlesynthesize', 'googlesynthesizevoices');
        $voices = $cache->get('voices');
        if ($voices) {
            $lastfetched = $cache->get('lastfetched');
            if ($clock->time() - $lastfetched <= DAYSECS) {
                return $voices;
            }
        }

        $client = new http_client([
                'timeout' => get_config('local_ai_manager', 'requesttimeout'),
        ]);

        $options['headers'] = [
                'x-goog-api-key' => $this->get_api_key(),
                'Content-Type' => 'application/json;charset=utf-8',
        ];

        $response = $client->get('https://texttospeech.googleapis.com/v1/voices', $options);
        if ($response->getStatusCode() !== 200) {
            debugging('Could not retrieve voices from google api endpoint');
            if ($voices) {
                debugging('Still have a cache entry for google voices which is being returned');
                return $voices;
            } else {
                debugging('No cache entry available for google voices, so we cannot return any voices');
                return [];
            }
        }
        $voices = json_decode($response->getBody()->getContents(), true)['voices'];
        $cache->set('voices', $voices);
        $cache->set('lastfetched', $clock->time());
        return $voices;
    }
}
