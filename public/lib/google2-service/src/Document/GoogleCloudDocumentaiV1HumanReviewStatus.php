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

class GoogleCloudDocumentaiV1HumanReviewStatus extends \Google\Model
{
  /**
   * Human review state is unspecified. Most likely due to an internal error.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Human review is skipped for the document. This can happen because human
   * review isn't enabled on the processor or the processing request has been
   * set to skip this document.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Human review validation is triggered and passed, so no review is needed.
   */
  public const STATE_VALIDATION_PASSED = 'VALIDATION_PASSED';
  /**
   * Human review validation is triggered and the document is under review.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Some error happened during triggering human review, see the state_message
   * for details.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The name of the operation triggered by the processed document. This field
   * is populated only when the state is `HUMAN_REVIEW_IN_PROGRESS`. It has the
   * same response type and metadata as the long-running operation returned by
   * ReviewDocument.
   *
   * @var string
   */
  public $humanReviewOperation;
  /**
   * The state of human review on the processing request.
   *
   * @var string
   */
  public $state;
  /**
   * A message providing more details about the human review state.
   *
   * @var string
   */
  public $stateMessage;

  /**
   * The name of the operation triggered by the processed document. This field
   * is populated only when the state is `HUMAN_REVIEW_IN_PROGRESS`. It has the
   * same response type and metadata as the long-running operation returned by
   * ReviewDocument.
   *
   * @param string $humanReviewOperation
   */
  public function setHumanReviewOperation($humanReviewOperation)
  {
    $this->humanReviewOperation = $humanReviewOperation;
  }
  /**
   * @return string
   */
  public function getHumanReviewOperation()
  {
    return $this->humanReviewOperation;
  }
  /**
   * The state of human review on the processing request.
   *
   * Accepted values: STATE_UNSPECIFIED, SKIPPED, VALIDATION_PASSED,
   * IN_PROGRESS, ERROR
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
  /**
   * A message providing more details about the human review state.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1HumanReviewStatus::class, 'Google_Service_Document_GoogleCloudDocumentaiV1HumanReviewStatus');
