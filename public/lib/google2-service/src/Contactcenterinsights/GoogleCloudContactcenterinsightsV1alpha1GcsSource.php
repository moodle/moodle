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

class GoogleCloudContactcenterinsightsV1alpha1GcsSource extends \Google\Model
{
  /**
   * Cloud Storage URI that points to a file that contains the conversation
   * audio.
   *
   * @var string
   */
  public $audioUri;
  /**
   * Immutable. Cloud Storage URI that points to a file that contains the
   * conversation transcript.
   *
   * @var string
   */
  public $transcriptUri;

  /**
   * Cloud Storage URI that points to a file that contains the conversation
   * audio.
   *
   * @param string $audioUri
   */
  public function setAudioUri($audioUri)
  {
    $this->audioUri = $audioUri;
  }
  /**
   * @return string
   */
  public function getAudioUri()
  {
    return $this->audioUri;
  }
  /**
   * Immutable. Cloud Storage URI that points to a file that contains the
   * conversation transcript.
   *
   * @param string $transcriptUri
   */
  public function setTranscriptUri($transcriptUri)
  {
    $this->transcriptUri = $transcriptUri;
  }
  /**
   * @return string
   */
  public function getTranscriptUri()
  {
    return $this->transcriptUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1GcsSource::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1GcsSource');
