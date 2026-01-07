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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ReplicatedVoiceConfig extends \Google\Model
{
  /**
   * Optional. The mimetype of the voice sample. The only currently supported
   * value is `audio/wav`. This represents 16-bit signed little-endian wav data,
   * with a 24kHz sampling rate. `mime_type` will default to `audio/wav` if not
   * set.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Optional. The sample of the custom voice.
   *
   * @var string
   */
  public $voiceSampleAudio;

  /**
   * Optional. The mimetype of the voice sample. The only currently supported
   * value is `audio/wav`. This represents 16-bit signed little-endian wav data,
   * with a 24kHz sampling rate. `mime_type` will default to `audio/wav` if not
   * set.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Optional. The sample of the custom voice.
   *
   * @param string $voiceSampleAudio
   */
  public function setVoiceSampleAudio($voiceSampleAudio)
  {
    $this->voiceSampleAudio = $voiceSampleAudio;
  }
  /**
   * @return string
   */
  public function getVoiceSampleAudio()
  {
    return $this->voiceSampleAudio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReplicatedVoiceConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReplicatedVoiceConfig');
