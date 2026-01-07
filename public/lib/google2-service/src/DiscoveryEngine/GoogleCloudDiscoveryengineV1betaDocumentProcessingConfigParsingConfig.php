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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfig extends \Google\Model
{
  protected $digitalParsingConfigType = GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigDigitalParsingConfig::class;
  protected $digitalParsingConfigDataType = '';
  protected $layoutParsingConfigType = GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigLayoutParsingConfig::class;
  protected $layoutParsingConfigDataType = '';
  protected $ocrParsingConfigType = GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigOcrParsingConfig::class;
  protected $ocrParsingConfigDataType = '';

  /**
   * Configurations applied to digital parser.
   *
   * @param GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigDigitalParsingConfig $digitalParsingConfig
   */
  public function setDigitalParsingConfig(GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigDigitalParsingConfig $digitalParsingConfig)
  {
    $this->digitalParsingConfig = $digitalParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigDigitalParsingConfig
   */
  public function getDigitalParsingConfig()
  {
    return $this->digitalParsingConfig;
  }
  /**
   * Configurations applied to layout parser.
   *
   * @param GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigLayoutParsingConfig $layoutParsingConfig
   */
  public function setLayoutParsingConfig(GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigLayoutParsingConfig $layoutParsingConfig)
  {
    $this->layoutParsingConfig = $layoutParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigLayoutParsingConfig
   */
  public function getLayoutParsingConfig()
  {
    return $this->layoutParsingConfig;
  }
  /**
   * Configurations applied to OCR parser. Currently it only applies to PDFs.
   *
   * @param GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigOcrParsingConfig $ocrParsingConfig
   */
  public function setOcrParsingConfig(GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigOcrParsingConfig $ocrParsingConfig)
  {
    $this->ocrParsingConfig = $ocrParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfigOcrParsingConfig
   */
  public function getOcrParsingConfig()
  {
    return $this->ocrParsingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaDocumentProcessingConfigParsingConfig');
