<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Texttospeech\Resource;

use Google\Service\Texttospeech\Operation;
use Google\Service\Texttospeech\SynthesizeLongAudioRequest;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $texttospeechService = new Google\Service\Texttospeech(...);
 *   $locations = $texttospeechService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Synthesizes long form text asynchronously. (locations.synthesizeLongAudio)
   *
   * @param string $parent The resource states of the request in the form of
   * `projects/locations`.
   * @param SynthesizeLongAudioRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function synthesizeLongAudio($parent, SynthesizeLongAudioRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('synthesizeLongAudio', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Texttospeech_Resource_ProjectsLocations');
