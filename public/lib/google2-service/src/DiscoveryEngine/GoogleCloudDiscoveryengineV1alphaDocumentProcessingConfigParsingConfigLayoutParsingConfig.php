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

class GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig extends \Google\Collection
{
  protected $collection_key = 'structuredContentTypes';
  /**
   * Optional. If true, the processed document will be made available for the
   * GetProcessedDocument API.
   *
   * @var bool
   */
  public $enableGetProcessedDocument;
  /**
   * Optional. If true, the LLM based annotation is added to the image during
   * parsing.
   *
   * @var bool
   */
  public $enableImageAnnotation;
  /**
   * Optional. If true, the pdf layout will be refined using an LLM.
   *
   * @var bool
   */
  public $enableLlmLayoutParsing;
  /**
   * Optional. If true, the LLM based annotation is added to the table during
   * parsing.
   *
   * @var bool
   */
  public $enableTableAnnotation;
  /**
   * Optional. List of HTML classes to exclude from the parsed content.
   *
   * @var string[]
   */
  public $excludeHtmlClasses;
  /**
   * Optional. List of HTML elements to exclude from the parsed content.
   *
   * @var string[]
   */
  public $excludeHtmlElements;
  /**
   * Optional. List of HTML ids to exclude from the parsed content.
   *
   * @var string[]
   */
  public $excludeHtmlIds;
  /**
   * Optional. Contains the required structure types to extract from the
   * document. Supported values: * `shareholder-structure`
   *
   * @var string[]
   */
  public $structuredContentTypes;

  /**
   * Optional. If true, the processed document will be made available for the
   * GetProcessedDocument API.
   *
   * @param bool $enableGetProcessedDocument
   */
  public function setEnableGetProcessedDocument($enableGetProcessedDocument)
  {
    $this->enableGetProcessedDocument = $enableGetProcessedDocument;
  }
  /**
   * @return bool
   */
  public function getEnableGetProcessedDocument()
  {
    return $this->enableGetProcessedDocument;
  }
  /**
   * Optional. If true, the LLM based annotation is added to the image during
   * parsing.
   *
   * @param bool $enableImageAnnotation
   */
  public function setEnableImageAnnotation($enableImageAnnotation)
  {
    $this->enableImageAnnotation = $enableImageAnnotation;
  }
  /**
   * @return bool
   */
  public function getEnableImageAnnotation()
  {
    return $this->enableImageAnnotation;
  }
  /**
   * Optional. If true, the pdf layout will be refined using an LLM.
   *
   * @param bool $enableLlmLayoutParsing
   */
  public function setEnableLlmLayoutParsing($enableLlmLayoutParsing)
  {
    $this->enableLlmLayoutParsing = $enableLlmLayoutParsing;
  }
  /**
   * @return bool
   */
  public function getEnableLlmLayoutParsing()
  {
    return $this->enableLlmLayoutParsing;
  }
  /**
   * Optional. If true, the LLM based annotation is added to the table during
   * parsing.
   *
   * @param bool $enableTableAnnotation
   */
  public function setEnableTableAnnotation($enableTableAnnotation)
  {
    $this->enableTableAnnotation = $enableTableAnnotation;
  }
  /**
   * @return bool
   */
  public function getEnableTableAnnotation()
  {
    return $this->enableTableAnnotation;
  }
  /**
   * Optional. List of HTML classes to exclude from the parsed content.
   *
   * @param string[] $excludeHtmlClasses
   */
  public function setExcludeHtmlClasses($excludeHtmlClasses)
  {
    $this->excludeHtmlClasses = $excludeHtmlClasses;
  }
  /**
   * @return string[]
   */
  public function getExcludeHtmlClasses()
  {
    return $this->excludeHtmlClasses;
  }
  /**
   * Optional. List of HTML elements to exclude from the parsed content.
   *
   * @param string[] $excludeHtmlElements
   */
  public function setExcludeHtmlElements($excludeHtmlElements)
  {
    $this->excludeHtmlElements = $excludeHtmlElements;
  }
  /**
   * @return string[]
   */
  public function getExcludeHtmlElements()
  {
    return $this->excludeHtmlElements;
  }
  /**
   * Optional. List of HTML ids to exclude from the parsed content.
   *
   * @param string[] $excludeHtmlIds
   */
  public function setExcludeHtmlIds($excludeHtmlIds)
  {
    $this->excludeHtmlIds = $excludeHtmlIds;
  }
  /**
   * @return string[]
   */
  public function getExcludeHtmlIds()
  {
    return $this->excludeHtmlIds;
  }
  /**
   * Optional. Contains the required structure types to extract from the
   * document. Supported values: * `shareholder-structure`
   *
   * @param string[] $structuredContentTypes
   */
  public function setStructuredContentTypes($structuredContentTypes)
  {
    $this->structuredContentTypes = $structuredContentTypes;
  }
  /**
   * @return string[]
   */
  public function getStructuredContentTypes()
  {
    return $this->structuredContentTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfigLayoutParsingConfig');
