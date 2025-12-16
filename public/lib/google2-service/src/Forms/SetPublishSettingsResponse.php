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

class SetPublishSettingsResponse extends \Google\Model
{
  /**
   * Required. The ID of the Form. This is same as the Form.form_id field.
   *
   * @var string
   */
  public $formId;
  protected $publishSettingsType = PublishSettings::class;
  protected $publishSettingsDataType = '';

  /**
   * Required. The ID of the Form. This is same as the Form.form_id field.
   *
   * @param string $formId
   */
  public function setFormId($formId)
  {
    $this->formId = $formId;
  }
  /**
   * @return string
   */
  public function getFormId()
  {
    return $this->formId;
  }
  /**
   * The publish settings of the form.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetPublishSettingsResponse::class, 'Google_Service_Forms_SetPublishSettingsResponse');
