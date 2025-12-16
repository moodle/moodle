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

namespace Google\Service\CloudNaturalLanguage;

class AnnotateTextRequestFeatures extends \Google\Model
{
  /**
   * Optional. Classify the full document into categories.
   *
   * @var bool
   */
  public $classifyText;
  /**
   * Optional. Extract document-level sentiment.
   *
   * @var bool
   */
  public $extractDocumentSentiment;
  /**
   * Optional. Extract entities.
   *
   * @var bool
   */
  public $extractEntities;
  /**
   * Optional. Moderate the document for harmful and sensitive categories.
   *
   * @var bool
   */
  public $moderateText;

  /**
   * Optional. Classify the full document into categories.
   *
   * @param bool $classifyText
   */
  public function setClassifyText($classifyText)
  {
    $this->classifyText = $classifyText;
  }
  /**
   * @return bool
   */
  public function getClassifyText()
  {
    return $this->classifyText;
  }
  /**
   * Optional. Extract document-level sentiment.
   *
   * @param bool $extractDocumentSentiment
   */
  public function setExtractDocumentSentiment($extractDocumentSentiment)
  {
    $this->extractDocumentSentiment = $extractDocumentSentiment;
  }
  /**
   * @return bool
   */
  public function getExtractDocumentSentiment()
  {
    return $this->extractDocumentSentiment;
  }
  /**
   * Optional. Extract entities.
   *
   * @param bool $extractEntities
   */
  public function setExtractEntities($extractEntities)
  {
    $this->extractEntities = $extractEntities;
  }
  /**
   * @return bool
   */
  public function getExtractEntities()
  {
    return $this->extractEntities;
  }
  /**
   * Optional. Moderate the document for harmful and sensitive categories.
   *
   * @param bool $moderateText
   */
  public function setModerateText($moderateText)
  {
    $this->moderateText = $moderateText;
  }
  /**
   * @return bool
   */
  public function getModerateText()
  {
    return $this->moderateText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnnotateTextRequestFeatures::class, 'Google_Service_CloudNaturalLanguage_AnnotateTextRequestFeatures');
