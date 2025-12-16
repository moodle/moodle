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

namespace Google\Service\Forms;

class SetPublishSettingsRequest extends \Google\Model
{
  protected $publishSettingsType = PublishSettings::class;
  protected $publishSettingsDataType = '';
  /**
   * Optional. The `publish_settings` fields to update. This field mask accepts
   * the following values: * `publish_state`: Updates or replaces all
   * `publish_state` settings. * `"*"`: Updates or replaces all
   * `publish_settings` fields.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The desired publish settings to apply to the form.
   *
   * @param PublishSettings $publishSettings
   */
  public function setPublishSettings(PublishSettings $publishSettings)
  {
    $this->publishSettings = $publishSettings;
  }
  /**
   * @return PublishSettings
   */
  public function getPublishSettings()
  {
    return $this->publishSettings;
  }
  /**
   * Optional. The `publish_settings` fields to update. This field mask accepts
   * the following values: * `publish_state`: Updates or replaces all
   * `publish_state` settings. * `"*"`: Updates or replaces all
   * `publish_settings` fields.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetPublishSettingsRequest::class, 'Google_Service_Forms_SetPublishSettingsRequest');
