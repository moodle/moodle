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

class GooglePrivacyDlpV2RedactImageRequest extends \Google\Collection
{
  protected $collection_key = 'imageRedactionConfigs';
  protected $byteItemType = GooglePrivacyDlpV2ByteContentItem::class;
  protected $byteItemDataType = '';
  /**
   * The full resource name of the de-identification template to use. Settings
   * in the main `image_redaction_configs` field override the corresponding
   * settings in this de-identification template. The request fails if the type
   * of the template's deidentify_config is not image_transformations.
   *
   * @var string
   */
  public $deidentifyTemplate;
  protected $imageRedactionConfigsType = GooglePrivacyDlpV2ImageRedactionConfig::class;
  protected $imageRedactionConfigsDataType = 'array';
  /**
   * Whether the response should include findings along with the redacted image.
   *
   * @var bool
   */
  public $includeFindings;
  protected $inspectConfigType = GooglePrivacyDlpV2InspectConfig::class;
  protected $inspectConfigDataType = '';
  /**
   * The full resource name of the inspection template to use. Settings in the
   * main `inspect_config` field override the corresponding settings in this
   * inspection template. The merge behavior is as follows: - Singular field:
   * The main field's value replaces the value of the corresponding field in the
   * template. - Repeated fields: The field values are appended to the list
   * defined in the template. - Sub-messages and groups: The fields are
   * recursively merged.
   *
   * @var string
   */
  public $inspectTemplate;
  /**
   * Deprecated. This field has no effect.
   *
   * @var string
   */
  public $locationId;

  /**
   * The content must be PNG, JPEG, SVG or BMP.
   *
   * @param GooglePrivacyDlpV2ByteContentItem $byteItem
   */
  public function setByteItem(GooglePrivacyDlpV2ByteContentItem $byteItem)
  {
    $this->byteItem = $byteItem;
  }
  /**
   * @return GooglePrivacyDlpV2ByteContentItem
   */
  public function getByteItem()
  {
    return $this->byteItem;
  }
  /**
   * The full resource name of the de-identification template to use. Settings
   * in the main `image_redaction_configs` field override the corresponding
   * settings in this de-identification template. The request fails if the type
   * of the template's deidentify_config is not image_transformations.
   *
   * @param string $deidentifyTemplate
   */
  public function setDeidentifyTemplate($deidentifyTemplate)
  {
    $this->deidentifyTemplate = $deidentifyTemplate;
  }
  /**
   * @return string
   */
  public function getDeidentifyTemplate()
  {
    return $this->deidentifyTemplate;
  }
  /**
   * The configuration for specifying what content to redact from images.
   *
   * @param GooglePrivacyDlpV2ImageRedactionConfig[] $imageRedactionConfigs
   */
  public function setImageRedactionConfigs($imageRedactionConfigs)
  {
    $this->imageRedactionConfigs = $imageRedactionConfigs;
  }
  /**
   * @return GooglePrivacyDlpV2ImageRedactionConfig[]
   */
  public function getImageRedactionConfigs()
  {
    return $this->imageRedactionConfigs;
  }
  /**
   * Whether the response should include findings along with the redacted image.
   *
   * @param bool $includeFindings
   */
  public function setIncludeFindings($includeFindings)
  {
    $this->includeFindings = $includeFindings;
  }
  /**
   * @return bool
   */
  public function getIncludeFindings()
  {
    return $this->includeFindings;
  }
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
   * The full resource name of the inspection template to use. Settings in the
   * main `inspect_config` field override the corresponding settings in this
   * inspection template. The merge behavior is as follows: - Singular field:
   * The main field's value replaces the value of the corresponding field in the
   * template. - Repeated fields: The field values are appended to the list
   * defined in the template. - Sub-messages and groups: The fields are
   * recursively merged.
   *
   * @param string $inspectTemplate
   */
  public function setInspectTemplate($inspectTemplate)
  {
    $this->inspectTemplate = $inspectTemplate;
  }
  /**
   * @return string
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2RedactImageRequest::class, 'Google_Service_DLP_GooglePrivacyDlpV2RedactImageRequest');
