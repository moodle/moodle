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

namespace Google\Service\CloudCommercePartnerProcurementService;

class Approval extends \Google\Model
{
  /**
   * Sentinel value; do not use.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The approval is pending response from the provider. The approval state can
   * transition to Account.Approval.State.APPROVED or
   * Account.Approval.State.REJECTED.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The approval has been granted by the provider.
   */
  public const STATE_APPROVED = 'APPROVED';
  /**
   * The approval has been rejected by the provider. A provider may choose to
   * approve a previously rejected approval, so is it possible to transition to
   * Account.Approval.State.APPROVED.
   */
  public const STATE_REJECTED = 'REJECTED';
  /**
   * Output only. The name of the approval.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. An explanation for the state of the approval.
   *
   * @var string
   */
  public $reason;
  /**
   * Output only. The state of the approval.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The last update timestamp of the approval.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The name of the approval.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. An explanation for the state of the approval.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Output only. The state of the approval.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, APPROVED, REJECTED
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
   * Optional. The last update timestamp of the approval.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Approval::class, 'Google_Service_CloudCommercePartnerProcurementService_Approval');
