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

class GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePair extends \Google\Model
{
  /**
   * Source sentence in the sentence pair.
   *
   * @var string
   */
  public $sourceSentence;
  /**
   * Target sentence in the sentence pair.
   *
   * @var string
   */
  public $targetSentence;

  /**
   * Source sentence in the sentence pair.
   *
   * @param string $sourceSentence
   */
  public function setSourceSentence($sourceSentence)
  {
    $this->sourceSentence = $sourceSentence;
  }
  /**
   * @return string
   */
  public function getSourceSentence()
  {
    return $this->sourceSentence;
  }
  /**
   * Target sentence in the sentence pair.
   *
   * @param string $targetSentence
   */
  public function setTargetSentence($targetSentence)
  {
    $this->targetSentence = $targetSentence;
  }
  /**
   * @return string
   */
  public function getTargetSentence()
  {
    return $this->targetSentence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePair::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPromptSpecReferenceSentencePair');
