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

class RejectAccountRequest extends \Google\Model
{
  /**
   * The name of the approval being rejected. If absent and there is only one
   * approval possible, that approval will be rejected. If absent and there are
   * many approvals possible, the request will fail with a 400 Bad Request.
   * Optional.
   *
   * @var string
   */
  public $approvalName;
  /**
   * Free form text string explaining the rejection reason. Max allowed length:
   * 256 bytes. Longer strings will be truncated.
   *
   * @var string
   */
  public $reason;

  /**
   * The name of the approval being rejected. If absent and there is only one
   * approval possible, that approval will be rejected. If absent and there are
   * many approvals possible, the request will fail with a 400 Bad Request.
   * Optional.
   *
   * @param string $approvalName
   */
  public function setApprovalName($approvalName)
  {
    $this->approvalName = $approvalName;
  }
  /**
   * @return string
   */
  public function getApprovalName()
  {
    return $this->approvalName;
  }
  /**
   * Free form text string explaining the rejection reason. Max allowed length:
   * 256 bytes. Longer strings will be truncated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RejectAccountRequest::class, 'Google_Service_CloudCommercePartnerProcurementService_RejectAccountRequest');
