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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ProcessOptions extends \Google\Model
{
  /**
   * Only process certain pages from the end, same as above.
   *
   * @var int
   */
  public $fromEnd;
  /**
   * Only process certain pages from the start. Process all if the document has
   * fewer pages.
   *
   * @var int
   */
  public $fromStart;
  protected $individualPageSelectorType = GoogleCloudDocumentaiV1ProcessOptionsIndividualPageSelector::class;
  protected $individualPageSelectorDataType = '';
  protected $layoutConfigType = GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig::class;
  protected $layoutConfigDataType = '';
  protected $ocrConfigType = GoogleCloudDocumentaiV1OcrConfig::class;
  protected $ocrConfigDataType = '';
  protected $schemaOverrideType = GoogleCloudDocumentaiV1DocumentSchema::class;
  protected $schemaOverrideDataType = '';

  /**
   * Only process certain pages from the end, same as above.
   *
   * @param int $fromEnd
   */
  public function setFromEnd($fromEnd)
  {
    $this->fromEnd = $fromEnd;
  }
  /**
   * @return int
   */
  public function getFromEnd()
  {
    return $this->fromEnd;
  }
  /**
   * Only process certain pages from the start. Process all if the document has
   * fewer pages.
   *
   * @param int $fromStart
   */
  public function setFromStart($fromStart)
  {
    $this->fromStart = $fromStart;
  }
  /**
   * @return int
   */
  public function getFromStart()
  {
    return $this->fromStart;
  }
  /**
   * Which pages to process (1-indexed).
   *
   * @param GoogleCloudDocumentaiV1ProcessOptionsIndividualPageSelector $individualPageSelector
   */
  public function setIndividualPageSelector(GoogleCloudDocumentaiV1ProcessOptionsIndividualPageSelector $individualPageSelector)
  {
    $this->individualPageSelector = $individualPageSelector;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessOptionsIndividualPageSelector
   */
  public function getIndividualPageSelector()
  {
    return $this->individualPageSelector;
  }
  /**
   * Optional. Only applicable to `LAYOUT_PARSER_PROCESSOR`. Returns error if
   * set on other processor types.
   *
   * @param GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig $layoutConfig
   */
  public function setLayoutConfig(GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig $layoutConfig)
  {
    $this->layoutConfig = $layoutConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1ProcessOptionsLayoutConfig
   */
  public function getLayoutConfig()
  {
    return $this->layoutConfig;
  }
  /**
   * Only applicable to `OCR_PROCESSOR` and `FORM_PARSER_PROCESSOR`. Returns
   * error if set on other processor types.
   *
   * @param GoogleCloudDocumentaiV1OcrConfig $ocrConfig
   */
  public function setOcrConfig(GoogleCloudDocumentaiV1OcrConfig $ocrConfig)
  {
    $this->ocrConfig = $ocrConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1OcrConfig
   */
  public function getOcrConfig()
  {
    return $this->ocrConfig;
  }
  /**
   * Optional. Override the schema of the ProcessorVersion. Will return an
   * Invalid Argument error if this field is set when the underlying
   * ProcessorVersion doesn't support schema override.
   *
   * @param GoogleCloudDocumentaiV1DocumentSchema $schemaOverride
   */
  public function setSchemaOverride(GoogleCloudDocumentaiV1DocumentSchema $schemaOverride)
  {
    $this->schemaOverride = $schemaOverride;
  }
  /**
   * @return GoogleCloudDocumentaiV1DocumentSchema
   */
  public function getSchemaOverride()
  {
    return $this->schemaOverride;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ProcessOptions::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ProcessOptions');
