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

class UpdateSettingsRequest extends \Google\Model
{
  protected $settingsType = FormSettings::class;
  protected $settingsDataType = '';
  /**
   * Required. Only values named in this mask are changed. At least one field
   * must be specified. The root `settings` is implied and should not be
   * specified. A single `"*"` can be used as short-hand for updating every
   * field.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The settings to update with.
   *
   * @param FormSettings $settings
   */
  public function setSettings(FormSettings $settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return FormSettings
   */
  public function getSettings()
  {
    return $this->settings;
  }
  /**
   * Required. Only values named in this mask are changed. At least one field
   * must be specified. The root `settings` is implied and should not be
   * specified. A single `"*"` can be used as short-hand for updating every
   * field.
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
class_alias(UpdateSettingsRequest::class, 'Google_Service_Forms_UpdateSettingsRequest');
