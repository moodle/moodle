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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2GenerativeQuestionConfig extends \Google\Collection
{
  protected $collection_key = 'exampleValues';
  /**
   * Optional. Whether the question is asked at serving time.
   *
   * @var bool
   */
  public $allowedInConversation;
  /**
   * Required. Resource name of the catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   *
   * @var string
   */
  public $catalog;
  /**
   * Output only. Values that can be used to answer the question.
   *
   * @var string[]
   */
  public $exampleValues;
  /**
   * Required. The facet to which the question is associated.
   *
   * @var string
   */
  public $facet;
  /**
   * Optional. The question that will be used at serving time. Question can have
   * a max length of 300 bytes. When not populated, generated_question should be
   * used.
   *
   * @var string
   */
  public $finalQuestion;
  /**
   * Output only. The ratio of how often a question was asked.
   *
   * @var float
   */
  public $frequency;
  /**
   * Output only. The LLM generated question.
   *
   * @var string
   */
  public $generatedQuestion;

  /**
   * Optional. Whether the question is asked at serving time.
   *
   * @param bool $allowedInConversation
   */
  public function setAllowedInConversation($allowedInConversation)
  {
    $this->allowedInConversation = $allowedInConversation;
  }
  /**
   * @return bool
   */
  public function getAllowedInConversation()
  {
    return $this->allowedInConversation;
  }
  /**
   * Required. Resource name of the catalog. Format:
   * projects/{project}/locations/{location}/catalogs/{catalog}
   *
   * @param string $catalog
   */
  public function setCatalog($catalog)
  {
    $this->catalog = $catalog;
  }
  /**
   * @return string
   */
  public function getCatalog()
  {
    return $this->catalog;
  }
  /**
   * Output only. Values that can be used to answer the question.
   *
   * @param string[] $exampleValues
   */
  public function setExampleValues($exampleValues)
  {
    $this->exampleValues = $exampleValues;
  }
  /**
   * @return string[]
   */
  public function getExampleValues()
  {
    return $this->exampleValues;
  }
  /**
   * Required. The facet to which the question is associated.
   *
   * @param string $facet
   */
  public function setFacet($facet)
  {
    $this->facet = $facet;
  }
  /**
   * @return string
   */
  public function getFacet()
  {
    return $this->facet;
  }
  /**
   * Optional. The question that will be used at serving time. Question can have
   * a max length of 300 bytes. When not populated, generated_question should be
   * used.
   *
   * @param string $finalQuestion
   */
  public function setFinalQuestion($finalQuestion)
  {
    $this->finalQuestion = $finalQuestion;
  }
  /**
   * @return string
   */
  public function getFinalQuestion()
  {
    return $this->finalQuestion;
  }
  /**
   * Output only. The ratio of how often a question was asked.
   *
   * @param float $frequency
   */
  public function setFrequency($frequency)
  {
    $this->frequency = $frequency;
  }
  /**
   * @return float
   */
  public function getFrequency()
  {
    return $this->frequency;
  }
  /**
   * Output only. The LLM generated question.
   *
   * @param string $generatedQuestion
   */
  public function setGeneratedQuestion($generatedQuestion)
  {
    $this->generatedQuestion = $generatedQuestion;
  }
  /**
   * @return string
   */
  public function getGeneratedQuestion()
  {
    return $this->generatedQuestion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2GenerativeQuestionConfig::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2GenerativeQuestionConfig');
