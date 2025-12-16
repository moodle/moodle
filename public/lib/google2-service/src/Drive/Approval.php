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

namespace Google\Service\Drive;

class Approval extends \Google\Collection
{
  /**
   * Approval status has not been set or was set to an invalid value.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The approval process has started and not finished.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The approval process is finished and the target was approved.
   */
  public const STATUS_APPROVED = 'APPROVED';
  /**
   * The approval process was cancelled before it finished.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  /**
   * The approval process is finished and the target was declined.
   */
  public const STATUS_DECLINED = 'DECLINED';
  protected $collection_key = 'reviewerResponses';
  /**
   * The Approval ID.
   *
   * @var string
   */
  public $approvalId;
  /**
   * Output only. The time time the approval was completed.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. The time the approval was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time that the approval is due.
   *
   * @var string
   */
  public $dueTime;
  protected $initiatorType = User::class;
  protected $initiatorDataType = '';
  /**
   * This is always drive#approval.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. The most recent time the approval was modified.
   *
   * @var string
   */
  public $modifyTime;
  protected $reviewerResponsesType = ReviewerResponse::class;
  protected $reviewerResponsesDataType = 'array';
  /**
   * Output only. The status of the approval at the time this resource was
   * requested.
   *
   * @var string
   */
  public $status;
  /**
   * Target file id of the approval.
   *
   * @var string
   */
  public $targetFileId;

  /**
   * The Approval ID.
   *
   * @param string $approvalId
   */
  public function setApprovalId($approvalId)
  {
    $this->approvalId = $approvalId;
  }
  /**
   * @return string
   */
  public function getApprovalId()
  {
    return $this->approvalId;
  }
  /**
   * Output only. The time time the approval was completed.
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Output only. The time the approval was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The time that the approval is due.
   *
   * @param string $dueTime
   */
  public function setDueTime($dueTime)
  {
    $this->dueTime = $dueTime;
  }
  /**
   * @return string
   */
  public function getDueTime()
  {
    return $this->dueTime;
  }
  /**
   * The user that requested the Approval.
   *
   * @param User $initiator
   */
  public function setInitiator(User $initiator)
  {
    $this->initiator = $initiator;
  }
  /**
   * @return User
   */
  public function getInitiator()
  {
    return $this->initiator;
  }
  /**
   * This is always drive#approval.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. The most recent time the approval was modified.
   *
   * @param string $modifyTime
   */
  public function setModifyTime($modifyTime)
  {
    $this->modifyTime = $modifyTime;
  }
  /**
   * @return string
   */
  public function getModifyTime()
  {
    return $this->modifyTime;
  }
  /**
   * The responses made on the Approval by reviewers.
   *
   * @param ReviewerResponse[] $reviewerResponses
   */
  public function setReviewerResponses($reviewerResponses)
  {
    $this->reviewerResponses = $reviewerResponses;
  }
  /**
   * @return ReviewerResponse[]
   */
  public function getReviewerResponses()
  {
    return $this->reviewerResponses;
  }
  /**
   * Output only. The status of the approval at the time this resource was
   * requested.
   *
   * Accepted values: STATUS_UNSPECIFIED, IN_PROGRESS, APPROVED, CANCELLED,
   * DECLINED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Target file id of the approval.
   *
   * @param string $targetFileId
   */
  public function setTargetFileId($targetFileId)
  {
    $this->targetFileId = $targetFileId;
  }
  /**
   * @return string
   */
  public function getTargetFileId()
  {
    return $this->targetFileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Approval::class, 'Google_Service_Drive_Approval');
