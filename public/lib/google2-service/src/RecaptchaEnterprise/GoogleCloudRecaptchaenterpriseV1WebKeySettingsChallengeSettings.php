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

class GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings extends \Google\Model
{
  protected $actionSettingsType = GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings::class;
  protected $actionSettingsDataType = 'map';
  protected $defaultSettingsType = GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings::class;
  protected $defaultSettingsDataType = '';

  /**
   * Optional. The action to score threshold map. The action name should be the
   * same as the action name passed in the `data-action` attribute (see
   * https://cloud.google.com/recaptcha/docs/actions-website). Action names are
   * case-insensitive. There is a maximum of 100 action settings. An action name
   * has a maximum length of 100.
   *
   * @param GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings[] $actionSettings
   */
  public function setActionSettings($actionSettings)
  {
    $this->actionSettings = $actionSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings[]
   */
  public function getActionSettings()
  {
    return $this->actionSettings;
  }
  /**
   * Required. Defines when a challenge is triggered (unless the default
   * threshold is overridden for the given action, see `action_settings`).
   *
   * @param GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings $defaultSettings
   */
  public function setDefaultSettings(GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings $defaultSettings)
  {
    $this->defaultSettings = $defaultSettings;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1WebKeySettingsActionSettings
   */
  public function getDefaultSettings()
  {
    return $this->defaultSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1WebKeySettingsChallengeSettings');
