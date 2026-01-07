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

class GoogleCloudAiplatformV1MultiSpeakerVoiceConfig extends \Google\Collection
{
  protected $collection_key = 'speakerVoiceConfigs';
  protected $speakerVoiceConfigsType = GoogleCloudAiplatformV1SpeakerVoiceConfig::class;
  protected $speakerVoiceConfigsDataType = 'array';

  /**
   * Required. A list of configurations for the voices of the speakers. Exactly
   * two speaker voice configurations must be provided.
   *
   * @param GoogleCloudAiplatformV1SpeakerVoiceConfig[] $speakerVoiceConfigs
   */
  public function setSpeakerVoiceConfigs($speakerVoiceConfigs)
  {
    $this->speakerVoiceConfigs = $speakerVoiceConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1SpeakerVoiceConfig[]
   */
  public function getSpeakerVoiceConfigs()
  {
    return $this->speakerVoiceConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MultiSpeakerVoiceConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MultiSpeakerVoiceConfig');
