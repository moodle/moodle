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

namespace Google\Service\AccessApproval;

class ApproveDecision extends \Google\Model
{
  /**
   * The time at which approval was granted.
   *
   * @var string
   */
  public $approveTime;
  /**
   * True when the request has been auto-approved.
   *
   * @var bool
   */
  public $autoApproved;
  /**
   * The time at which the approval expires.
   *
   * @var string
   */
  public $expireTime;
  /**
   * If set, denotes the timestamp at which the approval is invalidated.
   *
   * @var string
   */
  public $invalidateTime;
  /**
   * True when the request has been approved by the customer's defined policy.
   *
   * @var bool
   */
  public $policyApproved;
  protected $signatureInfoType = SignatureInfo::class;
  protected $signatureInfoDataType = '';

  /**
   * The time at which approval was granted.
   *
   * @param string $approveTime
   */
  public function setApproveTime($approveTime)
  {
    $this->approveTime = $approveTime;
  }
  /**
   * @return string
   */
  public function getApproveTime()
  {
    return $this->approveTime;
  }
  /**
   * True when the request has been auto-approved.
   *
   * @param bool $autoApproved
   */
  public function setAutoApproved($autoApproved)
  {
    $this->autoApproved = $autoApproved;
  }
  /**
   * @return bool
   */
  public function getAutoApproved()
  {
    return $this->autoApproved;
  }
  /**
   * The time at which the approval expires.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * If set, denotes the timestamp at which the approval is invalidated.
   *
   * @param string $invalidateTime
   */
  public function setInvalidateTime($invalidateTime)
  {
    $this->invalidateTime = $invalidateTime;
  }
  /**
   * @return string
   */
  public function getInvalidateTime()
  {
    return $this->invalidateTime;
  }
  /**
   * True when the request has been approved by the customer's defined policy.
   *
   * @param bool $policyApproved
   */
  public function setPolicyApproved($policyApproved)
  {
    $this->policyApproved = $policyApproved;
  }
  /**
   * @return bool
   */
  public function getPolicyApproved()
  {
    return $this->policyApproved;
  }
  /**
   * The signature for the ApprovalRequest and details on how it was signed.
   *
   * @param SignatureInfo $signatureInfo
   */
  public function setSignatureInfo(SignatureInfo $signatureInfo)
  {
    $this->signatureInfo = $signatureInfo;
  }
  /**
   * @return SignatureInfo
   */
  public function getSignatureInfo()
  {
    return $this->signatureInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApproveDecision::class, 'Google_Service_AccessApproval_ApproveDecision');
