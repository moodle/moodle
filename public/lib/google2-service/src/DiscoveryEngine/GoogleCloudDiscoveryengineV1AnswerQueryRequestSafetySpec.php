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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec extends \Google\Collection
{
  protected $collection_key = 'safetySettings';
  /**
   * Enable the safety filtering on the answer response. It is false by default.
   *
   * @var bool
   */
  public $enable;
  protected $safetySettingsType = GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpecSafetySetting::class;
  protected $safetySettingsDataType = 'array';

  /**
   * Enable the safety filtering on the answer response. It is false by default.
   *
   * @param bool $enable
   */
  public function setEnable($enable)
  {
    $this->enable = $enable;
  }
  /**
   * @return bool
   */
  public function getEnable()
  {
    return $this->enable;
  }
  /**
   * Optional. Safety settings. This settings are effective only when the
   * safety_spec.enable is true.
   *
   * @param GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpecSafetySetting[] $safetySettings
   */
  public function setSafetySettings($safetySettings)
  {
    $this->safetySettings = $safetySettings;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpecSafetySetting[]
   */
  public function getSafetySettings()
  {
    return $this->safetySettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestSafetySpec');
