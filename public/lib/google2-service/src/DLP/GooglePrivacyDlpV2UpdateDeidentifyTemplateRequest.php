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

class GooglePrivacyDlpV2UpdateDeidentifyTemplateRequest extends \Google\Model
{
  protected $deidentifyTemplateType = GooglePrivacyDlpV2DeidentifyTemplate::class;
  protected $deidentifyTemplateDataType = '';
  /**
   * Mask to control which fields get updated.
   *
   * @var string
   */
  public $updateMask;

  /**
   * New DeidentifyTemplate value.
   *
   * @param GooglePrivacyDlpV2DeidentifyTemplate $deidentifyTemplate
   */
  public function setDeidentifyTemplate(GooglePrivacyDlpV2DeidentifyTemplate $deidentifyTemplate)
  {
    $this->deidentifyTemplate = $deidentifyTemplate;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyTemplate
   */
  public function getDeidentifyTemplate()
  {
    return $this->deidentifyTemplate;
  }
  /**
   * Mask to control which fields get updated.
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
class_alias(GooglePrivacyDlpV2UpdateDeidentifyTemplateRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2UpdateDeidentifyTemplateRequest');
