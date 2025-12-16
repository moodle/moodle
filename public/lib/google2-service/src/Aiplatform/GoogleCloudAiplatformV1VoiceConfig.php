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

class GoogleCloudAiplatformV1VoiceConfig extends \Google\Model
{
  protected $prebuiltVoiceConfigType = GoogleCloudAiplatformV1PrebuiltVoiceConfig::class;
  protected $prebuiltVoiceConfigDataType = '';
  protected $replicatedVoiceConfigType = GoogleCloudAiplatformV1ReplicatedVoiceConfig::class;
  protected $replicatedVoiceConfigDataType = '';

  /**
   * The configuration for a prebuilt voice.
   *
   * @param GoogleCloudAiplatformV1PrebuiltVoiceConfig $prebuiltVoiceConfig
   */
  public function setPrebuiltVoiceConfig(GoogleCloudAiplatformV1PrebuiltVoiceConfig $prebuiltVoiceConfig)
  {
    $this->prebuiltVoiceConfig = $prebuiltVoiceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1PrebuiltVoiceConfig
   */
  public function getPrebuiltVoiceConfig()
  {
    return $this->prebuiltVoiceConfig;
  }
  /**
   * Optional. The configuration for a replicated voice. This enables users to
   * replicate a voice from an audio sample.
   *
   * @param GoogleCloudAiplatformV1ReplicatedVoiceConfig $replicatedVoiceConfig
   */
  public function setReplicatedVoiceConfig(GoogleCloudAiplatformV1ReplicatedVoiceConfig $replicatedVoiceConfig)
  {
    $this->replicatedVoiceConfig = $replicatedVoiceConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ReplicatedVoiceConfig
   */
  public function getReplicatedVoiceConfig()
  {
    return $this->replicatedVoiceConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1VoiceConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1VoiceConfig');
