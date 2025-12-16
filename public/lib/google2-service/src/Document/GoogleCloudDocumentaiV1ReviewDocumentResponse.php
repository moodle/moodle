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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1ReviewDocumentResponse extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The review operation is rejected by the reviewer.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * The review operation is succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The Cloud Storage uri for the human reviewed document if the review is
   * succeeded.
   *
   * @var string
   */
  public $gcsDestination;
  /**
   * The reason why the review is rejected by reviewer.
   *
   * @var string
   */
  public $rejectionReason;
  /**
   * The state of the review operation.
   *
   * @var string
   */
  public $state;

  /**
   * The Cloud Storage uri for the human reviewed document if the review is
   * succeeded.
   *
   * @param string $gcsDestination
   */
  public function setGcsDestination($gcsDestination)
  {
    $this->gcsDestination = $gcsDestination;
  }
  /**
   * @return string
   */
  public function getGcsDestination()
  {
    return $this->gcsDestination;
  }
  /**
   * The reason why the review is rejected by reviewer.
   *
   * @param string $rejectionReason
   */
  public function setRejectionReason($rejectionReason)
  {
    $this->rejectionReason = $rejectionReason;
  }
  /**
   * @return string
   */
  public function getRejectionReason()
  {
    return $this->rejectionReason;
  }
  /**
   * The state of the review operation.
   *
   * Accepted values: STATE_UNSPECIFIED, REJECTED, SUCCEEDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1ReviewDocumentResponse::class, 'Google_Service_Document_GoogleCloudDocumentaiV1ReviewDocumentResponse');
