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

class DismissDecision extends \Google\Model
{
  /**
   * The time at which the approval request was dismissed.
   *
   * @var string
   */
  public $dismissTime;
  /**
   * This field will be true if the ApprovalRequest was implicitly dismissed due
   * to inaction by the access approval approvers (the request is not acted on
   * by the approvers before the exiration time).
   *
   * @var bool
   */
  public $implicit;

  /**
   * The time at which the approval request was dismissed.
   *
   * @param string $dismissTime
   */
  public function setDismissTime($dismissTime)
  {
    $this->dismissTime = $dismissTime;
  }
  /**
   * @return string
   */
  public function getDismissTime()
  {
    return $this->dismissTime;
  }
  /**
   * This field will be true if the ApprovalRequest was implicitly dismissed due
   * to inaction by the access approval approvers (the request is not acted on
   * by the approvers before the exiration time).
   *
   * @param bool $implicit
   */
  public function setImplicit($implicit)
  {
    $this->implicit = $implicit;
  }
  /**
   * @return bool
   */
  public function getImplicit()
  {
    return $this->implicit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DismissDecision::class, 'Google_Service_AccessApproval_DismissDecision');
