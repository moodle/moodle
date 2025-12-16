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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1AnnotationBoundary extends \Google\Model
{
  /**
   * The index in the sequence of transcribed pieces of the conversation where
   * the boundary is located. This index starts at zero.
   *
   * @var int
   */
  public $transcriptIndex;
  /**
   * The word index of this boundary with respect to the first word in the
   * transcript piece. This index starts at zero.
   *
   * @var int
   */
  public $wordIndex;

  /**
   * The index in the sequence of transcribed pieces of the conversation where
   * the boundary is located. This index starts at zero.
   *
   * @param int $transcriptIndex
   */
  public function setTranscriptIndex($transcriptIndex)
  {
    $this->transcriptIndex = $transcriptIndex;
  }
  /**
   * @return int
   */
  public function getTranscriptIndex()
  {
    return $this->transcriptIndex;
  }
  /**
   * The word index of this boundary with respect to the first word in the
   * transcript piece. This index starts at zero.
   *
   * @param int $wordIndex
   */
  public function setWordIndex($wordIndex)
  {
    $this->wordIndex = $wordIndex;
  }
  /**
   * @return int
   */
  public function getWordIndex()
  {
    return $this->wordIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1AnnotationBoundary::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1AnnotationBoundary');
