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

class GoogleCloudDatalabelingV1beta1TextClassificationConfig extends \Google\Model
{
  /**
   * Optional. If allow_multi_label is true, contributors are able to choose
   * multiple labels for one text segment.
   *
   * @var bool
   */
  public $allowMultiLabel;
  /**
   * Required. Annotation spec set resource name.
   *
   * @var string
   */
  public $annotationSpecSet;
  protected $sentimentConfigType = GoogleCloudDatalabelingV1beta1SentimentConfig::class;
  protected $sentimentConfigDataType = '';

  /**
   * Optional. If allow_multi_label is true, contributors are able to choose
   * multiple labels for one text segment.
   *
   * @param bool $allowMultiLabel
   */
  public function setAllowMultiLabel($allowMultiLabel)
  {
    $this->allowMultiLabel = $allowMultiLabel;
  }
  /**
   * @return bool
   */
  public function getAllowMultiLabel()
  {
    return $this->allowMultiLabel;
  }
  /**
   * Required. Annotation spec set resource name.
   *
   * @param string $annotationSpecSet
   */
  public function setAnnotationSpecSet($annotationSpecSet)
  {
    $this->annotationSpecSet = $annotationSpecSet;
  }
  /**
   * @return string
   */
  public function getAnnotationSpecSet()
  {
    return $this->annotationSpecSet;
  }
  /**
   * Optional. Configs for sentiment selection. We deprecate sentiment analysis
   * in data labeling side as it is incompatible with uCAIP.
   *
   * @deprecated
   * @param GoogleCloudDatalabelingV1beta1SentimentConfig $sentimentConfig
   */
  public function setSentimentConfig(GoogleCloudDatalabelingV1beta1SentimentConfig $sentimentConfig)
  {
    $this->sentimentConfig = $sentimentConfig;
  }
  /**
   * @deprecated
   * @return GoogleCloudDatalabelingV1beta1SentimentConfig
   */
  public function getSentimentConfig()
  {
    return $this->sentimentConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1TextClassificationConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1TextClassificationConfig');
