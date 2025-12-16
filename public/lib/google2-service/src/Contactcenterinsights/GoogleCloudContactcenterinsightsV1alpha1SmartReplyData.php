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

class GoogleCloudContactcenterinsightsV1alpha1SmartReplyData extends \Google\Model
{
  /**
   * The system's confidence score that this reply is a good match for this
   * conversation, ranging from 0.0 (completely uncertain) to 1.0 (completely
   * certain).
   *
   * @var 
   */
  public $confidenceScore;
  /**
   * Map that contains metadata about the Smart Reply and the document from
   * which it originates.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The name of the answer record. Format:
   * projects/{project}/locations/{location}/answerRecords/{answer_record}
   *
   * @var string
   */
  public $queryRecord;
  /**
   * The content of the reply.
   *
   * @var string
   */
  public $reply;

  public function setConfidenceScore($confidenceScore)
  {
    $this->confidenceScore = $confidenceScore;
  }
  public function getConfidenceScore()
  {
    return $this->confidenceScore;
  }
  /**
   * Map that contains metadata about the Smart Reply and the document from
   * which it originates.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The name of the answer record. Format:
   * projects/{project}/locations/{location}/answerRecords/{answer_record}
   *
   * @param string $queryRecord
   */
  public function setQueryRecord($queryRecord)
  {
    $this->queryRecord = $queryRecord;
  }
  /**
   * @return string
   */
  public function getQueryRecord()
  {
    return $this->queryRecord;
  }
  /**
   * The content of the reply.
   *
   * @param string $reply
   */
  public function setReply($reply)
  {
    $this->reply = $reply;
  }
  /**
   * @return string
   */
  public function getReply()
  {
    return $this->reply;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1SmartReplyData::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1SmartReplyData');
