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

class GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig extends \Google\Model
{
  protected $digitalParsingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigDigitalParsingConfig::class;
  protected $digitalParsingConfigDataType = '';
  protected $layoutParsingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig::class;
  protected $layoutParsingConfigDataType = '';
  protected $ocrParsingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigOcrParsingConfig::class;
  protected $ocrParsingConfigDataType = '';

  /**
   * Configurations applied to digital parser.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigDigitalParsingConfig $digitalParsingConfig
   */
  public function setDigitalParsingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigDigitalParsingConfig $digitalParsingConfig)
  {
    $this->digitalParsingConfig = $digitalParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigDigitalParsingConfig
   */
  public function getDigitalParsingConfig()
  {
    return $this->digitalParsingConfig;
  }
  /**
   * Configurations applied to layout parser.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig $layoutParsingConfig
   */
  public function setLayoutParsingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig $layoutParsingConfig)
  {
    $this->layoutParsingConfig = $layoutParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig
   */
  public function getLayoutParsingConfig()
  {
    return $this->layoutParsingConfig;
  }
  /**
   * Configurations applied to OCR parser. Currently it only applies to PDFs.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigOcrParsingConfig $ocrParsingConfig
   */
  public function setOcrParsingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigOcrParsingConfig $ocrParsingConfig)
  {
    $this->ocrParsingConfig = $ocrParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigOcrParsingConfig
   */
  public function getOcrParsingConfig()
  {
    return $this->ocrParsingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig');
