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

namespace Google\Service\Translate;

class AdaptiveMtTranslateRequest extends \Google\Collection
{
  protected $collection_key = 'content';
  /**
   * Required. The content of the input in string format.
   *
   * @var string[]
   */
  public $content;
  /**
   * Required. The resource name for the dataset to use for adaptive MT.
   * `projects/{project}/locations/{location-id}/adaptiveMtDatasets/{dataset}`
   *
   * @var string
   */
  public $dataset;
  protected $glossaryConfigType = GlossaryConfig::class;
  protected $glossaryConfigDataType = '';
  protected $referenceSentenceConfigType = ReferenceSentenceConfig::class;
  protected $referenceSentenceConfigDataType = '';

  /**
   * Required. The content of the input in string format.
   *
   * @param string[] $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string[]
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Required. The resource name for the dataset to use for adaptive MT.
   * `projects/{project}/locations/{location-id}/adaptiveMtDatasets/{dataset}`
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Optional. Glossary to be applied. The glossary must be within the same
   * region (have the same location-id) as the model, otherwise an
   * INVALID_ARGUMENT (400) error is returned.
   *
   * @param GlossaryConfig $glossaryConfig
   */
  public function setGlossaryConfig(GlossaryConfig $glossaryConfig)
  {
    $this->glossaryConfig = $glossaryConfig;
  }
  /**
   * @return GlossaryConfig
   */
  public function getGlossaryConfig()
  {
    return $this->glossaryConfig;
  }
  /**
   * Configuration for caller provided reference sentences.
   *
   * @param ReferenceSentenceConfig $referenceSentenceConfig
   */
  public function setReferenceSentenceConfig(ReferenceSentenceConfig $referenceSentenceConfig)
  {
    $this->referenceSentenceConfig = $referenceSentenceConfig;
  }
  /**
   * @return ReferenceSentenceConfig
   */
  public function getReferenceSentenceConfig()
  {
    return $this->referenceSentenceConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdaptiveMtTranslateRequest::class, 'Google_Service_Translate_AdaptiveMtTranslateRequest');
