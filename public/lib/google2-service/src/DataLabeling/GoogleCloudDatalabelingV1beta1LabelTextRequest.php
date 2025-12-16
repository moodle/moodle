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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1LabelTextRequest extends \Google\Model
{
  public const FEATURE_FEATURE_UNSPECIFIED = 'FEATURE_UNSPECIFIED';
  /**
   * Label text content to one of more labels.
   */
  public const FEATURE_TEXT_CLASSIFICATION = 'TEXT_CLASSIFICATION';
  /**
   * Label entities and their span in text.
   */
  public const FEATURE_TEXT_ENTITY_EXTRACTION = 'TEXT_ENTITY_EXTRACTION';
  protected $basicConfigType = GoogleCloudDatalabelingV1beta1HumanAnnotationConfig::class;
  protected $basicConfigDataType = '';
  /**
   * Required. The type of text labeling task.
   *
   * @var string
   */
  public $feature;
  protected $textClassificationConfigType = GoogleCloudDatalabelingV1beta1TextClassificationConfig::class;
  protected $textClassificationConfigDataType = '';
  protected $textEntityExtractionConfigType = GoogleCloudDatalabelingV1beta1TextEntityExtractionConfig::class;
  protected $textEntityExtractionConfigDataType = '';

  /**
   * Required. Basic human annotation config.
   *
   * @param GoogleCloudDatalabelingV1beta1HumanAnnotationConfig $basicConfig
   */
  public function setBasicConfig(GoogleCloudDatalabelingV1beta1HumanAnnotationConfig $basicConfig)
  {
    $this->basicConfig = $basicConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1HumanAnnotationConfig
   */
  public function getBasicConfig()
  {
    return $this->basicConfig;
  }
  /**
   * Required. The type of text labeling task.
   *
   * Accepted values: FEATURE_UNSPECIFIED, TEXT_CLASSIFICATION,
   * TEXT_ENTITY_EXTRACTION
   *
   * @param self::FEATURE_* $feature
   */
  public function setFeature($feature)
  {
    $this->feature = $feature;
  }
  /**
   * @return self::FEATURE_*
   */
  public function getFeature()
  {
    return $this->feature;
  }
  /**
   * Configuration for text classification task. One of
   * text_classification_config and text_entity_extraction_config is required.
   *
   * @param GoogleCloudDatalabelingV1beta1TextClassificationConfig $textClassificationConfig
   */
  public function setTextClassificationConfig(GoogleCloudDatalabelingV1beta1TextClassificationConfig $textClassificationConfig)
  {
    $this->textClassificationConfig = $textClassificationConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TextClassificationConfig
   */
  public function getTextClassificationConfig()
  {
    return $this->textClassificationConfig;
  }
  /**
   * Configuration for entity extraction task. One of text_classification_config
   * and text_entity_extraction_config is required.
   *
   * @param GoogleCloudDatalabelingV1beta1TextEntityExtractionConfig $textEntityExtractionConfig
   */
  public function setTextEntityExtractionConfig(GoogleCloudDatalabelingV1beta1TextEntityExtractionConfig $textEntityExtractionConfig)
  {
    $this->textEntityExtractionConfig = $textEntityExtractionConfig;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1TextEntityExtractionConfig
   */
  public function getTextEntityExtractionConfig()
  {
    return $this->textEntityExtractionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1LabelTextRequest::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1LabelTextRequest');
