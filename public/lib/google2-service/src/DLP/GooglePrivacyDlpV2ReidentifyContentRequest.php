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

class GooglePrivacyDlpV2ReidentifyContentRequest extends \Google\Model
{
  protected $inspectConfigType = GooglePrivacyDlpV2InspectConfig::class;
  protected $inspectConfigDataType = '';
  /**
   * Template to use. Any configuration directly specified in `inspect_config`
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
  protected $reidentifyConfigType = GooglePrivacyDlpV2DeidentifyConfig::class;
  protected $reidentifyConfigDataType = '';
  /**
   * Template to use. References an instance of `DeidentifyTemplate`. Any
   * configuration directly specified in `reidentify_config` or `inspect_config`
   * will override those set in the template. The `DeidentifyTemplate` used must
   * include only reversible transformations. Singular fields that are set in
   * this request will replace their corresponding fields in the template.
   * Repeated fields are appended. Singular sub-messages and groups are
   * recursively merged.
   *
   * @var string
   */
  public $reidentifyTemplateName;

  /**
   * Configuration for the inspector.
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
   * Template to use. Any configuration directly specified in `inspect_config`
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
   * The item to re-identify. Will be treated as text.
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
  /**
   * Configuration for the re-identification of the content item. This field
   * shares the same proto message type that is used for de-identification,
   * however its usage here is for the reversal of the previous de-
   * identification. Re-identification is performed by examining the
   * transformations used to de-identify the items and executing the reverse.
   * This requires that only reversible transformations be provided here. The
   * reversible transformations are: - `CryptoDeterministicConfig` -
   * `CryptoReplaceFfxFpeConfig`
   *
   * @param GooglePrivacyDlpV2DeidentifyConfig $reidentifyConfig
   */
  public function setReidentifyConfig(GooglePrivacyDlpV2DeidentifyConfig $reidentifyConfig)
  {
    $this->reidentifyConfig = $reidentifyConfig;
  }
  /**
   * @return GooglePrivacyDlpV2DeidentifyConfig
   */
  public function getReidentifyConfig()
  {
    return $this->reidentifyConfig;
  }
  /**
   * Template to use. References an instance of `DeidentifyTemplate`. Any
   * configuration directly specified in `reidentify_config` or `inspect_config`
   * will override those set in the template. The `DeidentifyTemplate` used must
   * include only reversible transformations. Singular fields that are set in
   * this request will replace their corresponding fields in the template.
   * Repeated fields are appended. Singular sub-messages and groups are
   * recursively merged.
   *
   * @param string $reidentifyTemplateName
   */
  public function setReidentifyTemplateName($reidentifyTemplateName)
  {
    $this->reidentifyTemplateName = $reidentifyTemplateName;
  }
  /**
   * @return string
   */
  public function getReidentifyTemplateName()
  {
    return $this->reidentifyTemplateName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ReidentifyContentRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2ReidentifyContentRequest');
