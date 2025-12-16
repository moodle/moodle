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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CreateInspectTemplateRequest extends \Google\Model
{
  protected $inspectTemplateType = GooglePrivacyDlpV2InspectTemplate::class;
  protected $inspectTemplateDataType = '';
  /**
   * Deprecated. This field has no effect.
   *
   * @var string
   */
  public $locationId;
  /**
   * The template id can contain uppercase and lowercase letters, numbers, and
   * hyphens; that is, it must match the regular expression: `[a-zA-Z\d-_]+`.
   * The maximum length is 100 characters. Can be empty to allow the system to
   * generate one.
   *
   * @var string
   */
  public $templateId;

  /**
   * Required. The InspectTemplate to create.
   *
   * @param GooglePrivacyDlpV2InspectTemplate $inspectTemplate
   */
  public function setInspectTemplate(GooglePrivacyDlpV2InspectTemplate $inspectTemplate)
  {
    $this->inspectTemplate = $inspectTemplate;
  }
  /**
   * @return GooglePrivacyDlpV2InspectTemplate
   */
  public function getInspectTemplate()
  {
    return $this->inspectTemplate;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * The template id can contain uppercase and lowercase letters, numbers, and
   * hyphens; that is, it must match the regular expression: `[a-zA-Z\d-_]+`.
   * The maximum length is 100 characters. Can be empty to allow the system to
   * generate one.
   *
   * @param string $templateId
   */
  public function setTemplateId($templateId)
  {
    $this->templateId = $templateId;
  }
  /**
   * @return string
   */
  public function getTemplateId()
  {
    return $this->templateId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CreateInspectTemplateRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2CreateInspectTemplateRequest');
