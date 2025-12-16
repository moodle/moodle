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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings extends \Google\Model
{
  /**
   * Required. A challenge is triggered if the end-user score is below that
   * threshold. Value must be between 0 and 1 (inclusive).
   *
   * @var float
   */
  public $scoreThreshold;

  /**
   * Required. A challenge is triggered if the end-user score is below that
   * threshold. Value must be between 0 and 1 (inclusive).
   *
   * @param float $scoreThreshold
   */
  public function setScoreThreshold($scoreThreshold)
  {
    $this->scoreThreshold = $scoreThreshold;
  }
  /**
   * @return float
   */
  public function getScoreThreshold()
  {
    return $this->scoreThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings');
