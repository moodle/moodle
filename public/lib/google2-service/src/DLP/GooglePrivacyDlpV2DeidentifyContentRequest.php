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

class GooglePrivacyDlpV2DeidentifyContentRequest extends \Google\Model
{
  protected $deidentifyConfigType = GooglePrivacyDlpV2DeidentifyConfig::class;
  protected $deidentifyConfigDataType = '';
  /**
   * Template to use. Any configuration directly specified in deidentify_config
   * will override those set in the template. Singular fields that are set in
   * this request will replace their corresponding fields in the template.
   * Repeated fields are appended. Singular sub-messages and groups are
   * recursively merged.
   *
   * @var string
   */
  public $deidentifyTemplateName;
  protected $inspectConfigType = GooglePrivacyDlpV2InspectConfig::class;
  protected $inspectConfigDataType = '';
  /**
   * Template to use. Any configuration directly specified in inspect_config
   * will override those set in the template. Singular fields that are set in
   * this request will replace their corresponding fields in the template.
   * Repeated fields are appended. Singular sub-messages and groups are
   * recursively merged.
   *
   * @var string
   */
  public $inspectTemplateName;
  protected $itemType = GooglePrivacyDlpV2ContentItem::class;
  protected $itemDataType = '';
  /**
   * Deprecated. This field has no effect.
   *
   * @var string
   */
  public $locationId;

  /**
   * Configuration for the de-identification of the content item. Items
   * specified here will override the template referenced by the
   * deidentify_template_name argument.
   *
   * @param GooglePrivacyDlpV2DeidentifyConfig $deidentifyConfig
   */
  public function setDeidentifyConfig(GooglePrivacyDlpV2DeidentifyConfig $deidentifyConfig)
  {
    $this->deidentifyConfig = $deidentifyConfig;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyConfig
   */
  public function getDeidentifyConfig()
  {
    return $this->deidentifyConfig;
  }
  /**
   * Template to use. Any configuration directly specified in deidentify_config
   * will override those set in the template. Singular fields that are set in
   * this request will replace their corresponding fields in the template.
   * Repeated fields are appended. Singular sub-messages and groups are
   * recursively merged.
   *
   * @param string $deidentifyTemplateName
   */
  public function setDeidentifyTemplateName($deidentifyTemplateName)
  {
    $this->deidentifyTemplateName = $deidentifyTemplateName;
  }
  /**
   * @return string
   */
  public function getDeidentifyTemplateName()
  {
    return $this->deidentifyTemplateName;
  }
  /**
   * Configuration for the inspector. Items specified here will override the
   * template referenced by the inspect_template_name argument.
   *
   * @param GooglePrivacyDlpV2InspectConfig $inspectConfig
   */
  public function setInspectConfig(GooglePrivacyDlpV2InspectConfig $inspectConfig)
  {
    $this->inspectConfig = $inspectConfig;
  }
  /**
   * @return GooglePrivacyDlpV2InspectConfig
   */
  public function getInspectConfig()
  {
    return $this->inspectConfig;
  }
  /**
   * Template to use. Any configuration directly specified in inspect_config
   * will override those set in the template. Singular fields that are set in
   * this request will replace their corresponding fields in the template.
   * Repeated fields are appended. Singular sub-messages and groups are
   * recursively merged.
   *
   * @param string $inspectTemplateName
   */
  public function setInspectTemplateName($inspectTemplateName)
  {
    $this->inspectTemplateName = $inspectTemplateName;
  }
  /**
   * @return string
   */
  public function getInspectTemplateName()
  {
    return $this->inspectTemplateName;
  }
  /**
   * The item to de-identify. Will be treated as text. This value must be of
   * type Table if your deidentify_config is a RecordTransformations object.
   *
   * @param GooglePrivacyDlpV2ContentItem $item
   */
  public function setItem(GooglePrivacyDlpV2ContentItem $item)
  {
    $this->item = $item;
  }
  /**
   * @return GooglePrivacyDlpV2ContentItem
   */
  public function getItem()
  {
    return $this->item;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DeidentifyContentRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2DeidentifyContentRequest');
