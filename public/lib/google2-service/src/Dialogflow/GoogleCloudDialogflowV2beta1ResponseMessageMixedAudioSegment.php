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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1ResponseMessageMixedAudioSegment extends \Google\Model
{
  /**
   * Whether the playback of this segment can be interrupted by the end user's
   * speech and the client should then start the next Dialogflow request.
   *
   * @var bool
   */
  public $allowPlaybackInterruption;
  /**
   * Raw audio synthesized from the Dialogflow agent's response using the output
   * config specified in the request.
   *
   * @var string
   */
  public $audio;
  /**
   * Client-specific URI that points to an audio clip accessible to the client.
   *
   * @var string
   */
  public $uri;

  /**
   * Whether the playback of this segment can be interrupted by the end user's
   * speech and the client should then start the next Dialogflow request.
   *
   * @param bool $allowPlaybackInterruption
   */
  public function setAllowPlaybackInterruption($allowPlaybackInterruption)
  {
    $this->allowPlaybackInterruption = $allowPlaybackInterruption;
  }
  /**
   * @return bool
   */
  public function getAllowPlaybackInterruption()
  {
    return $this->allowPlaybackInterruption;
  }
  /**
   * Raw audio synthesized from the Dialogflow agent's response using the output
   * config specified in the request.
   *
   * @param string $audio
   */
  public function setAudio($audio)
  {
    $this->audio = $audio;
  }
  /**
   * @return string
   */
  public function getAudio()
  {
    return $this->audio;
  }
  /**
   * Client-specific URI that points to an audio clip accessible to the client.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1ResponseMessageMixedAudioSegment::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1ResponseMessageMixedAudioSegment');
