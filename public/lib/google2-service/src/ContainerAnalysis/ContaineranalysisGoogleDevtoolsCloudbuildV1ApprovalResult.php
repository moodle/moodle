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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1ApprovalResult extends \Google\Model
{
  /**
   * Default enum type. This should not be used.
   */
  public const DECISION_DECISION_UNSPECIFIED = 'DECISION_UNSPECIFIED';
  /**
   * Build is approved.
   */
  public const DECISION_APPROVED = 'APPROVED';
  /**
   * Build is rejected.
   */
  public const DECISION_REJECTED = 'REJECTED';
  /**
   * Output only. The time when the approval decision was made.
   *
   * @var string
   */
  public $approvalTime;
  /**
   * Output only. Email of the user that called the ApproveBuild API to approve
   * or reject a build at the time that the API was called.
   *
   * @var string
   */
  public $approverAccount;
  /**
   * Optional. An optional comment for this manual approval result.
   *
   * @var string
   */
  public $comment;
  /**
   * Required. The decision of this manual approval.
   *
   * @var string
   */
  public $decision;
  /**
   * Optional. An optional URL tied to this manual approval result. This field
   * is essentially the same as comment, except that it will be rendered by the
   * UI differently. An example use case is a link to an external job that
   * approved this Build.
   *
   * @var string
   */
  public $url;

  /**
   * Output only. The time when the approval decision was made.
   *
   * @param string $approvalTime
   */
  public function setApprovalTime($approvalTime)
  {
    $this->approvalTime = $approvalTime;
  }
  /**
   * @return string
   */
  public function getApprovalTime()
  {
    return $this->approvalTime;
  }
  /**
   * Output only. Email of the user that called the ApproveBuild API to approve
   * or reject a build at the time that the API was called.
   *
   * @param string $approverAccount
   */
  public function setApproverAccount($approverAccount)
  {
    $this->approverAccount = $approverAccount;
  }
  /**
   * @return string
   */
  public function getApproverAccount()
  {
    return $this->approverAccount;
  }
  /**
   * Optional. An optional comment for this manual approval result.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * Required. The decision of this manual approval.
   *
   * Accepted values: DECISION_UNSPECIFIED, APPROVED, REJECTED
   *
   * @param self::DECISION_* $decision
   */
  public function setDecision($decision)
  {
    $this->decision = $decision;
  }
  /**
   * @return self::DECISION_*
   */
  public function getDecision()
  {
    return $this->decision;
  }
  /**
   * Optional. An optional URL tied to this manual approval result. This field
   * is essentially the same as comment, except that it will be rendered by the
   * UI differently. An example use case is a link to an external job that
   * approved this Build.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1ApprovalResult::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1ApprovalResult');
