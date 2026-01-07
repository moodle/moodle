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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTextSentimentInputs extends \Google\Model
{
  /**
   * A sentiment is expressed as an integer ordinal, where higher value means a
   * more positive sentiment. The range of sentiments that will be used is
   * between 0 and sentimentMax (inclusive on both ends), and all the values in
   * the range must be represented in the dataset before a model can be created.
   * Only the Annotations with this sentimentMax will be used for training.
   * sentimentMax value must be between 1 and 10 (inclusive).
   *
   * @var int
   */
  public $sentimentMax;

  /**
   * A sentiment is expressed as an integer ordinal, where higher value means a
   * more positive sentiment. The range of sentiments that will be used is
   * between 0 and sentimentMax (inclusive on both ends), and all the values in
   * the range must be represented in the dataset before a model can be created.
   * Only the Annotations with this sentimentMax will be used for training.
   * sentimentMax value must be between 1 and 10 (inclusive).
   *
   * @param int $sentimentMax
   */
  public function setSentimentMax($sentimentMax)
  {
    $this->sentimentMax = $sentimentMax;
  }
  /**
   * @return int
   */
  public function getSentimentMax()
  {
    return $this->sentimentMax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTextSentimentInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlTextSentimentInputs');
