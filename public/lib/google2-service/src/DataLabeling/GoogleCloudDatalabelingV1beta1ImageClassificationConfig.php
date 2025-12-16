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

class GoogleCloudDatalabelingV1beta1ImageClassificationConfig extends \Google\Model
{
  public const ANSWER_AGGREGATION_TYPE_STRING_AGGREGATION_TYPE_UNSPECIFIED = 'STRING_AGGREGATION_TYPE_UNSPECIFIED';
  /**
   * Majority vote to aggregate answers.
   */
  public const ANSWER_AGGREGATION_TYPE_MAJORITY_VOTE = 'MAJORITY_VOTE';
  /**
   * Unanimous answers will be adopted.
   */
  public const ANSWER_AGGREGATION_TYPE_UNANIMOUS_VOTE = 'UNANIMOUS_VOTE';
  /**
   * Preserve all answers by crowd compute.
   */
  public const ANSWER_AGGREGATION_TYPE_NO_AGGREGATION = 'NO_AGGREGATION';
  /**
   * Optional. If allow_multi_label is true, contributors are able to choose
   * multiple labels for one image.
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
  /**
   * Optional. The type of how to aggregate answers.
   *
   * @var string
   */
  public $answerAggregationType;

  /**
   * Optional. If allow_multi_label is true, contributors are able to choose
   * multiple labels for one image.
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
   * Optional. The type of how to aggregate answers.
   *
   * Accepted values: STRING_AGGREGATION_TYPE_UNSPECIFIED, MAJORITY_VOTE,
   * UNANIMOUS_VOTE, NO_AGGREGATION
   *
   * @param self::ANSWER_AGGREGATION_TYPE_* $answerAggregationType
   */
  public function setAnswerAggregationType($answerAggregationType)
  {
    $this->answerAggregationType = $answerAggregationType;
  }
  /**
   * @return self::ANSWER_AGGREGATION_TYPE_*
   */
  public function getAnswerAggregationType()
  {
    return $this->answerAggregationType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1ImageClassificationConfig::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1ImageClassificationConfig');
