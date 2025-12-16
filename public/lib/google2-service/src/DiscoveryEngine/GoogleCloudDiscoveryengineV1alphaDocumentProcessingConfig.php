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

class GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig extends \Google\Model
{
  protected $chunkingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig::class;
  protected $chunkingConfigDataType = '';
  protected $defaultParsingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig::class;
  protected $defaultParsingConfigDataType = '';
  /**
   * The full resource name of the Document Processing Config. Format:
   * `projects/locations/collections/dataStores/documentProcessingConfig`.
   *
   * @var string
   */
  public $name;
  protected $parsingConfigOverridesType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig::class;
  protected $parsingConfigOverridesDataType = 'map';

  /**
   * Whether chunking mode is enabled.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig $chunkingConfig
   */
  public function setChunkingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig $chunkingConfig)
  {
    $this->chunkingConfig = $chunkingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig
   */
  public function getChunkingConfig()
  {
    return $this->chunkingConfig;
  }
  /**
   * Configurations for default Document parser. If not specified, we will
   * configure it as default DigitalParsingConfig, and the default parsing
   * config will be applied to all file types for Document parsing.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig $defaultParsingConfig
   */
  public function setDefaultParsingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig $defaultParsingConfig)
  {
    $this->defaultParsingConfig = $defaultParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig
   */
  public function getDefaultParsingConfig()
  {
    return $this->defaultParsingConfig;
  }
  /**
   * The full resource name of the Document Processing Config. Format:
   * `projects/locations/collections/dataStores/documentProcessingConfig`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Map from file type to override the default parsing configuration based on
   * the file type. Supported keys: * `pdf`: Override parsing config for PDF
   * files, either digital parsing, ocr parsing or layout parsing is supported.
   * * `html`: Override parsing config for HTML files, only digital parsing and
   * layout parsing are supported. * `docx`: Override parsing config for DOCX
   * files, only digital parsing and layout parsing are supported. * `pptx`:
   * Override parsing config for PPTX files, only digital parsing and layout
   * parsing are supported. * `xlsm`: Override parsing config for XLSM files,
   * only digital parsing and layout parsing are supported. * `xlsx`: Override
   * parsing config for XLSX files, only digital parsing and layout parsing are
   * supported.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig[] $parsingConfigOverrides
   */
  public function setParsingConfigOverrides($parsingConfigOverrides)
  {
    $this->parsingConfigOverrides = $parsingConfigOverrides;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig[]
   */
  public function getParsingConfigOverrides()
  {
    return $this->parsingConfigOverrides;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig');
